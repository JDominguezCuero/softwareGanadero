<?php
require_once __DIR__ . '/../../../config/config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['usuario'])) {
    echo json_encode([]);
    exit;
}

$id_usuario = $_SESSION['usuario']['id_usuario'];

$sql = "SELECT mensaje, fecha FROM notificaciones 
        WHERE id_usuario_receptor = :id 
        ORDER BY fecha DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id_usuario]);
$notificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($notificaciones);
?>
