<?php
session_start();
header('Content-Type: application/json');

$response = [
    'loggedIn' => false,
    'userName' => null
];

if (isset($_SESSION['id_usuario']) && !empty($_SESSION['id_usuario'])) {
    $response['loggedIn'] = true;
    // Envía el primer nombre del usuario para el saludo
    if (isset($_SESSION['nombre'])) {
        $fullName = $_SESSION['nombre'];
        $response['userName'] = explode(' ', $fullName)[0]; // Obtener solo el primer nombre
    }
}

echo json_encode($response);
?>