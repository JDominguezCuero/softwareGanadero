<?php
require_once 'config/conexion.php';

function actualizarperfil($nombre, $usuario, $contraseña, $direccion, 
$correo, $telefono, $departamento, $municipio) {
    global $conn;

    $sql = "UPDATE usuario SET
         nombre_completo = ?,
         contraseña = ?, 
         direccion = ?,
         correo = ?,
         telefono = ?,
         departamento = ?,
         municipio = ?,
         WHERE id = 1"; 


    $stmt = $conn->prepare($sql);
    return $stmt->execute([$nombre, $usuario, $contraseña, $direccion,
    $correo, $telefono, $departamento, $municipio]);
    }
    

    
?>
