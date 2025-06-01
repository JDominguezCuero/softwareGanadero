<?php
// public/productos.php (ESTE ES EL CONTROLADOR)

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Si necesitas sesiones para algo en esta página

require_once __DIR__ . '/../config/config.php'; // Para la conexión a la base de datos
require_once __DIR__ . '/../modules/productos/model.php'; // Tu modelo de productos
require_once __DIR__ . '/../modules/categorias/model.php'; // Si tienes un modelo para categorías

global $conexion;

if (!isset($conexion) || !$conexion instanceof PDO) {
    error_log("Error: La conexión a la base de datos no está disponible en public/productos.php.");
    die("Lo sentimos, hay un problema técnico con la base de datos.");
}

// --- 1. Lógica para filtros y ordenamiento ---
$filtros = [
    'buscar' => $_GET['buscar'] ?? null,
    'categoria_id' => $_GET['categoria'] ?? null,
    'precio_min' => $_GET['precio_min'] ?? null,
    'precio_max' => $_GET['precio_max'] ?? null,
    'ofertas_solo' => isset($_GET['ofertas']) && $_GET['ofertas'] === 'true'
];
$orden = $_GET['orden'] ?? 'nombre_asc';

$productos_filtrados = [];
$categorias_para_filtro = [];

try {
    // 2. Obtener todas las categorías para el filtro
    $categorias_para_filtro = obtenerCategorias($conexion); // Asegúrate de que esta función existe en tu modelo de categorías

    // 3. Obtener los productos aplicando filtros y ordenamiento
    // Necesitas una función en tu model.php que pueda manejar esto.
    // Si no la tienes, esta es una buena oportunidad para crearla.
    // Por ahora, para que empiece a mostrar algo, puedes usar obtenerProductos sin filtros si lo deseas.
    // Idealmente, deberías tener una función como obtenerProductosConFiltrosYOrden($conexion, $filtros, $orden);
    // Si no, por el momento, para que aparezcan los productos:
    $productos_filtrados = obtenerProductos($conexion); // Usa la función obtenerProductos que ya modificaste para incluir datos del vendedor.

} catch (Exception $e) {
    error_log("Error en public/productos.php al obtener datos: " . $e->getMessage());
    // En caso de error, $productos_filtrados se mantiene como un array vacío.
}

// --- 4. Incluir la vista ---
// Ahora incluye la vista que me mostraste
include __DIR__ . '/views/productos_lista.php';

// Cerrar la conexión si es necesario
$conexion = null;
?>