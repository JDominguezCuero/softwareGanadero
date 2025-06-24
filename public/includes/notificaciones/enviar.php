<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../../config/config.php';
session_start();

header('Content-Type: application/json');

// Validar sesión
if (!isset($_SESSION['usuario']['id_usuario'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
    exit;
}

// Obtener datos JSON del body
$data = json_decode(file_get_contents('php://input'), true);

// Extraer valores
$id_receptor = $data['receptor_id'] ?? null;
$id_producto = $data['id_producto'] ?? null;
$mensaje = $data['mensaje'] ?? null;
$id_emisor = $_SESSION['usuario']['id_usuario'];

// Verificar datos
if ($id_receptor && $mensaje && $id_emisor) {
    try {
        $stmt = $conexion->prepare("
            INSERT INTO notificaciones (
                id_usuario_emisor,
                id_usuario_receptor,
                mensaje,
                leido,
                fecha
            ) VALUES (?, ?, ?, 0, NOW())
        ");
        $stmt->execute([$id_emisor, $id_receptor, $mensaje]);

        echo json_encode(['status' => 'ok']);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Datos incompletos o sesión expirada'
    ]);
}
