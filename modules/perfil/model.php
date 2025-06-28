<?php
require_once __DIR__ . '/../../config/config.php';

/**
 * Obtiene los datos de un usuario por su ID, incluyendo el rol asociado.
 * @param PDO $conexion La conexión a la base de datos (PDO).
 * @param int $userId El ID del usuario a buscar.
 * @return array|false Los datos del usuario como un array asociativo, o false si no se encuentra.
 */
function obtenerUsuarioPorId($conexion, $userId) {
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
            WHERE
                u.id_usuario = ?";
    
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta obtenerUsuarioPorId: " . implode(":", $conexion->errorInfo()));
    }
    
    $stmt->bindParam(1, $userId, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
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



?>