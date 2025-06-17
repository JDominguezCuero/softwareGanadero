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