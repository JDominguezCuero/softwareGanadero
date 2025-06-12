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

function registrarUsuario($nombreCompleto, $correo, $usuario, $contrasena, $idRol = 3, $estado = 'Activo') {
    global $conexion;

    $sql = "INSERT INTO usuarios (nombreCompleto, correo_usuario, nombre_usuario, contrasena_usuario, id_rol)
            VALUES (:nombreCompleto, :correo, :usuario, :contrasena, :idRol, :estado)";
    
    $stmt = $conexion->prepare($sql);
    return $stmt->execute([
        ':nombreCompleto' => $nombreCompleto,
        ':correo' => $correo,
        ':usuario' => $usuario,
        ':contrasena' => password_hash($contrasena, PASSWORD_DEFAULT),
        ':idRol' => $idRol,
        ':estado' => $estado
    ]);
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