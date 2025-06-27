<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../modules/productos/model.php';
require_once __DIR__ . '/includes/render_product_items_function.php';
require_once __DIR__ . '/../modules/notificaciones/model.php';

global $conexion;

if (!isset($conexion) || !$conexion instanceof PDO) {
    error_log("Error: La conexión a la base de datos no está disponible. Verifique config.php.");
    die("Lo sentimos, hay un problema técnico. Por favor, intente más tarde.");
}

$todos_los_productos = [];
$ofertas_por_categoria = [];

$categorias_populares_info = [];
$productos_populares_tabs = [];

try {

    $productos_en_oferta_db = obtenerProductosEnOferta($conexion);
    if (!empty($productos_en_oferta_db)) {
        $ofertas_por_categoria['En Oferta'] = $productos_en_oferta_db;
    }

    $categorias_mas_populares_raw = obtenerCategoriasMasPopulares($conexion, 3);

    if (!empty($productos_en_oferta_db)) {
        $found_oferta_in_popular = false;
        foreach ($categorias_mas_populares_raw as $cat) {
            if (isset($cat['nombre']) && $cat['nombre'] === 'En Oferta') {
                $found_oferta_in_popular = true;
                break;
            }
        }
        if (!$found_oferta_in_popular) {
             array_unshift($categorias_populares_info, ['id' => 0, 'nombre' => 'En Oferta']);
        }
    }

    foreach ($categorias_mas_populares_raw as $cat_info) {
        if (!in_array($cat_info['nombre'], array_column($categorias_populares_info, 'nombre'))) {
            $categorias_populares_info[] = $cat_info;
        }
    }

    $categorias_populares_info = array_slice($categorias_populares_info, 0, 4);

    // --- DEPURACIÓN CLAVE AQUÍ ---
    // echo "DEBUG: Llama a obtenerProductosPopularesPorCategorias con estas categorías:<br>";
    // var_dump($categorias_populares_info); // Muestra qué categorías se le están pasando
    // --- FIN DEPURACIÓN CLAVE ---

    $productos_populares_tabs = obtenerProductosPopularesPorCategorias($conexion, $categorias_populares_info, 5);

    // Asegurar que los productos en oferta se asignen correctamente
    if (isset($productos_populares_tabs['En Oferta'])) {
        $productos_populares_tabs['En Oferta'] = $productos_en_oferta_db;
    }

    // --- DEPURACIÓN CLAVE AQUÍ ---
    // echo "DEBUG: Contenido FINAL de \$productos_populares_tabs ANTES de la vista:<br>";
    foreach ($productos_populares_tabs as $cat_nombre => $productos) {
        // echo "DEBUG:   Categoría '$cat_nombre' tiene " . count($productos) . " productos.<br>";
        // Opcional: var_dump($productos); // Descomentar para ver los productos de cada categoría
    }
    // --- FIN DEPURACIÓN CLAVE ---

    $current_user_id = $_SESSION['id_usuario'];


    $todos_los_productos = obtenerProductos($conexion);

    $obtener_notificaciones = obtenerNotificacionesPorUsuario($conexion, 8);

} catch (Exception $e) {
    error_log("Error en index_controller.php al obtener datos: " . $e->getMessage());
    // echo "ERROR: " . $e->getMessage() . "<br>";
}

include __DIR__ . '/index.php';

$conexion = null;

?>