<?php
require_once __DIR__ . '/../../../config/config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario']['id_usuario'])) {
    echo json_encode([]);
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

try {
    $stmt = $conexion->prepare("SELECT mensaje FROM notificaciones WHERE id_usuario_receptor = ? ORDER BY fecha DESC LIMIT 10");
    $stmt->execute([$id_usuario]);
    $notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($notificaciones);
} catch (PDOException $e) {
    echo json_encode([]);
}
