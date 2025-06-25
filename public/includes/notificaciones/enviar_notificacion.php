<?php
require_once __DIR__ . '/../../../config/config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$id_emisor = $_SESSION['id_usuario'];
$nombre_emisor = $_SESSION['nombre']; // Cambia si usas otro campo
$id_receptor = $data['id_vendedor'];
$id_producto = $data['id_producto'];

$mensaje = "$nombre_emisor está interesado en tu producto (ID: $id_producto).";

try {
    $stmt = $conexion->prepare("INSERT INTO notificaciones (id_usuario_emisor, id_usuario_receptor, mensaje, leido) VALUES (?, ?, ?, 0)");
    $stmt->execute([$id_emisor, $id_receptor, $mensaje]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
