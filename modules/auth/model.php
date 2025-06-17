<?php
require_once __DIR__ . '/../../config/config.php';

function obtenerUsuarioPorCorreo($correo) {
    global $conexion;

    $sql = "SELECT * FROM usuarios WHERE correo_usuario = :correo";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();

    return $stmt->fetch(); // Devuelve false si no encuentra
}

/**
 * Obtiene todos los alimentos del inventario.
 * @param PDO $conexion La conexión a la base de datos (PDO).
 * @return array Un array de alimentos.
 */
function obtenerUsuario($conexion) {
    $sql = "SELECT
                u.id_usuario,
                u.nombreCompleto,
                u.nombre_usuario,
                u.correo_usuario,
                u.direccion_usuario,
                u.estado,
                u.contrasena_usuario,
                u.telefono_usuario,
                u.id_rol,
                u.imagen_url_Usuario,
                r.nombre_rol,   
                r.descripcion
            FROM
                usuarios u
            LEFT JOIN
                roles r ON u.id_rol = r.id_rol
            ORDER BY
                u.nombreCompleto ASC";
    $stmt = $conexion->query($sql);
    if (!$stmt) {
        throw new Exception("Error al obtener inventario de alimentos: " . implode(":", $conexion->errorInfo()));
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function registrarUsuario($nombreCompleto, $correo, $usuario, $contrasena, $imagen_url, $idRol = 3, $estado = 'Activo') {
    global $conexion;

    $sql = "INSERT INTO usuarios (nombreCompleto, correo_usuario, nombre_usuario, contrasena_usuario, id_rol, )
            VALUES (:nombreCompleto, :correo, :usuario, :contrasena, :idRol, :estado, :imagenUrl)";
    
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':nombreCompleto' => $nombreCompleto,
        ':correo' => $correo,
        ':usuario' => $usuario,
        ':contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
        ':idRol' => $idRol,
        ':estado' => $estado,
        ':imagenUrl' => $imagen_url
    ]);
}

/**
 * Elimina un producto de la base de datos.
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id El ID del producto a eliminar.
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function eliminarUsuario($conexion, $id) {
    // 1. Obtener los id_animal de los animales asociados al usuario
    $stmtAnimales = $conexion->prepare("SELECT id_animal FROM animales WHERE id_usuario = :id");
    $stmtAnimales->bindValue(':id', $id, PDO::PARAM_INT);
    $stmtAnimales->execute();
    $animalesAEliminar = $stmtAnimales->fetchAll(PDO::FETCH_COLUMN);

    // 2. Eliminar registros de la tabla 'venta' que estén ligados a esos id_animal
    if (!empty($animalesAEliminar)) {
        $placeholders = implode(',', array_fill(0, count($animalesAEliminar), '?'));
        $stmtVenta = $conexion->prepare("DELETE FROM ventas WHERE id_animal IN ($placeholders)");
        foreach ($animalesAEliminar as $key => $idAnimal) {
            $stmtVenta->bindValue(($key + 1), $idAnimal, PDO::PARAM_INT);
        }
        $stmtVenta->execute();
    }

    // 3. Eliminar registros de la tabla 'animales' que estén ligados al id_usuario
    $stmtAnimalesDelete = $conexion->prepare("DELETE FROM animales WHERE id_usuario = :id");
    $stmtAnimalesDelete->bindValue(':id', $id, PDO::PARAM_INT);
    $stmtAnimalesDelete->execute();

    // **NUEVO PASO: Eliminar registros de la tabla 'logsactividades' ligados al id_usuario**
    $stmtLogsActividades = $conexion->prepare("DELETE FROM logsactividades WHERE id_usuario = :id");
    $stmtLogsActividades->bindValue(':id', $id, PDO::PARAM_INT);
    $stmtLogsActividades->execute();

    // 4. Eliminar el usuario de la tabla 'usuarios'
    $stmtUsuarioDelete = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = :id");
    $stmtUsuarioDelete->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmtUsuarioDelete->execute();
}

function agregarUsuario($conexion, $nombre, $usuario, $correo, $contrasena, $direccion, $estado, $imagen_url, $rol_id, $telefono) {

    $sql = "INSERT INTO usuarios (nombreCompleto, nombre_usuario, correo_usuario, contrasena_usuario, direccion_usuario, estado, imagen_url_Usuario, id_rol, telefono_usuario)
            VALUES (:nombreCompleto, :nombreUsuario, :correo, :contrasena, :direccion, :estado, :imagenUrl, :idRol, :telefono)";
    
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':nombreCompleto' => $nombre,
        ':nombreUsuario' => $usuario,
        ':correo' => $correo,
        ':contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
        ':direccion' => $direccion,
        ':estado' => $estado,
        ':imagenUrl' => $imagen_url,
        ':idRol' => $rol_id,
        ':telefono' => $telefono
    ]);
}

/**
 * Actualiza un usuario en la base de datos.
 * @param PDO $conexion Conexión a la base de datos.
 * @param int $id ID del usuario.
 * @param string $nombre Nombre completo.
 * @param string $usuario Nombre de usuario.
 * @param string $correo Correo electrónico.
 * @param string $direccion Dirección.
 * @param int $estado Estado (1: Activo, 2: Inactivo).
 * @param string $imagen_url URL de la imagen.
 * @param int $rol_id ID del rol.
 * @param string $telefono Teléfono.
 * @param string|null $contrasena Contraseña nueva (opcional).
 * @return bool True en caso de éxito, false si falla.
 */
function actualizarUsuario($conexion, $id, $nombre, $usuario, $correo, $direccion, $estado, $imagen_url, $rol_id, $telefono, $contrasena = null) {
    // Arma la consulta base
    $sql = "
        UPDATE usuarios SET 
            nombreCompleto = :nombreCompleto,
            nombre_usuario = :nombreUsuario,
            correo_usuario = :correo,
            direccion_usuario = :direccion,
            estado = :estado,
            imagen_url_Usuario = :imagenUrl,
            id_rol = :idRol,
            telefono_usuario = :telefono";

    // Si se envió una contraseña nueva, la añadimos al SQL
    if (!empty($contrasena)) {
        $sql .= ", contrasena_usuario = :contrasena";
    }

    $sql .= " WHERE id_usuario = :id";

    $stmt = $conexion->prepare($sql);

    // Enlace de parámetros obligatorios
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':nombreCompleto', $nombre);
    $stmt->bindValue(':nombreUsuario', $usuario);
    $stmt->bindValue(':correo', $correo);
    $stmt->bindValue(':direccion', $direccion);
    $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
    $stmt->bindValue(':imagenUrl', $imagen_url);
    $stmt->bindValue(':idRol', $rol_id, PDO::PARAM_INT);
    $stmt->bindValue(':telefono', $telefono);

    // Solo enlaza la contraseña si fue enviada
    if (!empty($contrasena)) {
        $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);
        $stmt->bindValue(':contrasena', $hashedPassword);
    }

    return $stmt->execute();
}


/**
 * Obtiene todas las categorías de productos.
 * @param PDO $conexion Objeto de conexión a la base de datos.
 * @return array Lista de categorías.
 */
function obtenerRoles(PDO $conexion): array {
    try {
        $stmt = $conexion->query("SELECT id_rol, nombre_rol FROM roles ORDER BY nombre_rol ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener los roles: " . $e->getMessage());
        return [];
    }
}

function actualizarContrasena($correo, $nuevaContrasena) {
    global $conexion;

    $sql = "UPDATE usuarios SET contrasena_usuario = :contrasena WHERE correo_usuario = :correo";
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':contrasena' => password_hash($nuevaContrasena, PASSWORD_DEFAULT),
        ':correo' => $correo
    ]);
}

function generarTokenRestablecimiento($correo) {
    global $conexion;

    $token = bin2hex(random_bytes(16)); // Token seguro
    $expiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

    $sql = "UPDATE usuarios SET token_recuperacion = :token, token_expiracion = :expiracion
            WHERE correo_usuario = :correo";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([
        ':token' => $token,
        ':expiracion' => $expiracion,
        ':correo' => $correo
    ]);

    return $token;
}


?>