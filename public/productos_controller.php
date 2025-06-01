<?php
// public/productos_controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../modules/productos/model.php';
require_once __DIR__ . '/includes/render_product_items_function.php'; // Asegúrate de que esta ruta sea correcta para tu función

global $conexion;

if (!isset($conexion) || !$conexion instanceof PDO) {
    error_log("Error: La conexión a la base de datos no está disponible. Verifique config.php.");
    die("Lo sentimos, hay un problema técnico. Por favor, intente más tarde.");
}

// --- Captura de Variables de Filtro ---
$filtro_categoria_id = $_GET['categoria'] ?? null;
if ($filtro_categoria_id !== null && is_numeric($filtro_categoria_id)) {
    $filtro_categoria_id = (int)$filtro_categoria_id;
} else {
    $filtro_categoria_id = null;
}

$filtro_precio_min = $_GET['precio_min'] ?? null;
if ($filtro_precio_min !== null && is_numeric($filtro_precio_min)) {
    $filtro_precio_min = (float)$filtro_precio_min;
} else {
    $filtro_precio_min = null;
}

$filtro_precio_max = $_GET['precio_max'] ?? null;
if ($filtro_precio_max !== null && is_numeric($filtro_precio_max)) {
    $filtro_precio_max = (float)$filtro_precio_max;
} else {
    $filtro_precio_max = null;
}

$filtro_busqueda = trim($_GET['buscar'] ?? '');
$ordenar_por = $_GET['ordenar_por'] ?? 'fecha_reciente';

// --- Obtener TODAS las categorías disponibles para llenar el select SIEMPRE ---
$todas_las_categorias = obtenerCategorias($conexion);

// --- Determinar qué categorías mostrar y obtener sus productos ---
$productos_por_categoria_en_listado = [];
$categorias_a_mostrar_en_secciones = [];

if ($filtro_categoria_id !== null) {
    // Si se filtró por una categoría específica, solo muestra esa sección
    foreach ($todas_las_categorias as $cat) {
        if ($cat['id_categoria'] === $filtro_categoria_id) { // Usa === para comparar el tipo también
            $categorias_a_mostrar_en_secciones[] = $cat;
            break;
        }
    }
} else {
    // Si no hay filtro de categoría, muestra todas las categorías en secciones separadas
    $categorias_a_mostrar_en_secciones = $todas_las_categorias;
}

try {
    foreach ($categorias_a_mostrar_en_secciones as $categoria) {
        $id_cat = $categoria['id_categoria'];
        $nombre_cat = $categoria['nombre_categoria'];

        // Obtener productos para la categoría actual, aplicando los filtros relevantes
        $productos_en_esta_categoria = obtenerTodosLosProductosConFiltros(
            $conexion,
            $id_cat,
            $filtro_precio_min,
            $filtro_precio_max,
            $filtro_busqueda,
            $ordenar_por
        );

        // SOLO agrega la categoría a la lista si tiene productos después de aplicar TODOS los filtros
        if (!empty($productos_en_esta_categoria)) {
            $productos_por_categoria_en_listado[$nombre_cat] = $productos_en_esta_categoria;
        }
    }
} catch (Exception $e) {
    error_log("Error al obtener productos por categoría en productos_controller.php: " . $e->getMessage());
}
include __DIR__ . '/productos_lista.php';

$conexion = null;
?>