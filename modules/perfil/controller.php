<?php
require_once "modelo.php";

if ($_SERVE['Editar_Perfil'] ==="POST") {
    $nombre_completo =  $_POST['nombre_completo'];
    $nombre_usuario = $_POST['nombre_usuario'];
    $contraseña = $_POST['Contraseña'];
    $direccion = $_POST['Direccion'];
    $correo = $_POST['Correo'];
    $telefono =  $_POST['Telefono_Movil'];
    $departamento = $_POST['Departamento'];
    $municipio = $_POST['Municipio'];

    // llamar al modelo para actualizar los datos

    $resultado = actualizarperfil($nombre_completo, $nombre_usuario, $contraseña, $direccion,
$correo, $telefono, $departamento, $municipio);
   if ($resultado) {
    echo "Perfil Actualizado Correctamente.";}
    else {
        echo "Error al Actualizar Perfil.";
    }

}
?>