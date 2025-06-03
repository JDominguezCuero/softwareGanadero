<?php
// public/campanas_controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once(__DIR__ . '/model.php');
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/includes/render_campana_items_function.php'; // Lo crearemos en el siguiente paso
// Opcional: Si vas a reutilizar renderProductItems, podrías crear un renderCampanaItems

global $conexion;

if (!isset($conexion) || !$conexion instanceof PDO) {
    error_log("Error: La conexión a la base de datos no está disponible en campanas_controller.php. Verifique config.php.");
    die("Lo sentimos, hay un problema técnico al cargar las campañas. Por favor, intente más tarde.");
}

$campanas_listado = [];
$campana_detalle = null;

try {
    // Lógica para mostrar una campaña individual (si se pasa un ID)
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id_campana = (int)$_GET['id'];
        $campana_detalle = obtenerCampanaPorId($conexion, $id_campana);

        if ($campana_detalle) {
            // Incluir la vista de detalle de campaña
            include __DIR__ . '/views/campana_detalle.php';
        } else {
            // Campaña no encontrada
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 Campaña no encontrada</h1><p>La campaña que buscas no existe o ha sido eliminada.</p>";
        }
    } else {
        // Lógica para listar todas las campañas
        $campanas_listado = obtenerCampanasActivas($conexion, 0, 'fecha_evento ASC'); // Obtener todas las campañas activas, ordenadas por fecha
        
        // Incluir la vista de listado de campañas
        include __DIR__ . '/views/campanas_lista.php';
    }

} catch (Exception $e) {
    error_log("Error en campanas_controller.php: " . $e->getMessage());
    // Puedes redirigir a una página de error o mostrar un mensaje amigable
    echo "Lo sentimos, hubo un problema al cargar las campañas.";
}

$conexion = null; // Cierra la conexión a la base de datos
?>