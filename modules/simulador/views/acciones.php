<?php
session_start();
require_once(__DIR__ . '/../../../config/config.php');

global $conexion;

header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_POST['accion']) || !isset($_POST['id'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'Faltan parámetros.']);
    exit;
}

$accion = $_POST['accion'];
$id = intval($_POST['id']);

$response = ['exito' => false, 'mensaje' => 'Acción no reconocida.'];
file_put_contents('debug_post.txt', print_r($_POST, true));

switch ($accion) {
    case 'alimentar':
        $sql = "UPDATE animales SET alimentacion = LEAST(alimentacion + 10, 100) WHERE id_animal = ?";
        $mensaje = "Animal alimentado correctamente.";
        break;

    case 'banar':
        $sql = "UPDATE animales SET higiene = LEAST(higiene + 10, 100) WHERE id_animal = ?";
        $mensaje = "Animal bañado correctamente.";
        break;

    case 'curar':
        $sql = "UPDATE animales SET salud = LEAST(salud + 10, 100) WHERE id_animal = ?";
        $mensaje = "Animal curado correctamente.";
        break;

    case 'producir':
        $sql = "UPDATE animales SET produccion = produccion + 1 WHERE id_animal = ?";
        $mensaje = "Producción registrada correctamente.";
        break;

    default:
        echo json_encode($response);
        exit;
}

try {
    // Ejecutar la actualización con PDO
    $stmt = $conexion->prepare($sql);
    $stmt->execute([$id]);

    // Obtener estado actualizado
    $sql_select = "SELECT alimentacion, higiene, salud, produccion FROM animales WHERE id_animal = ?";
    $stmt2 = $conexion->prepare($sql_select);
    $stmt2->execute([$id]);
    $result = $stmt2->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'exito' => true,
        'mensaje' => $mensaje,
        'datos' => $result
    ]);
} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al ejecutar la acción: ' . $e->getMessage()
    ]);
}
?>
