<?php
// public/productos_controller.php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../modules/productos/model.php';
// REMOVIDO: Ya no necesitamos incluir el modelo de usuarios por separado,
// porque la función obtenerProductoPorId ahora trae los datos del vendedor.
// require_once __DIR__ . '/../modules/usuarios/model.php'; 
require_once __DIR__ . '/includes/render_product_items_function.php';

global $conexion;

if (!isset($conexion) || !$conexion instanceof PDO) {
    error_log("Error: La conexión a la base de datos no está disponible. Verifique config.php.");
    die("Lo sentimos, hay un problema técnico. Por favor, intente más tarde.");
}

// --- LÓGICA PARA EL MODAL (SOLICITUD AJAX DE DETALLE DE PRODUCTO) ---
if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    header('Content-Type: application/json'); // La respuesta será JSON
    $id_producto_modal = (int)$_GET['product_id'];
    $response = null;

    try {
        // Obtener detalles del producto, que ahora incluye datos del vendedor
        // La función obtenerProductoPorId en modules/productos/model.php ya trae el JOIN con Usuarios
        $producto = obtenerProductoPorId($conexion, $id_producto_modal);

        if ($producto) {
            // Preparamos los datos para la respuesta JSON.
            // Los campos 'stock', 'nombre_vendedor', 'email_vendedor', 'telefono_vendedor',
            // 'direccion_vendedor' ahora se obtienen directamente del array $producto.
            $response = [
                'id_producto' => $producto['id_producto'],
                'nombre_producto' => $producto['nombre_producto'],
                'descripcion_completa' => $producto['descripcion_producto'], 
                'precio' => $producto['precio_unitario'], 
                'stock' => $producto['stock'], // Asegúrate que tu model alias 'cantidad' AS 'stock'
                'imagen_url' => $producto['imagen_url'],
                'nombre_categoria' => $producto['nombre_categoria'], 
                // Información del Vendedor, obtenida directamente del JOIN
                'nombre_vendedor' => $producto['nombre_usuario'] ?? 'N/A', 
                'email_vendedor' => $producto['correo_usuario'] ?? 'N/A',
                'telefono_vendedor' => $producto['telefono_usuario'] ?? 'N/A',
                'direccion_vendedor' => $producto['direccion_usuario'] ?? 'N/A', // Solo si la tienes en tu tabla Usuarios
            ];

        } else {
            http_response_code(404); // Not Found
            $response = ['error' => 'Producto no encontrado.'];
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        $response = ['error' => 'Error de base de datos: ' . $e->getMessage()];
        error_log("Error AJAX en productos_controller.php: " . $e->getMessage());
    } catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        $response = ['error' => 'Error inesperado: ' . $e->getMessage()];
        error_log("Error AJAX inesperado en productos_controller.php: " . $e->getMessage());
    }

    echo json_encode($response); // Envía la respuesta JSON
    $conexion = null; // Cierra la conexión
    exit(); // Detiene la ejecución del script aquí
}


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

$todas_las_categorias = obtenerCategorias($conexion);

$productos_por_categoria_en_listado = [];
$categorias_a_mostrar_en_secciones = [];

if ($filtro_categoria_id !== null) {
    foreach ($todas_las_categorias as $cat) {
        if ($cat['id_categoria'] === $filtro_categoria_id) {
            $categorias_a_mostrar_en_secciones[] = $cat;
            break;
        }
    }
} else {
    $categorias_a_mostrar_en_secciones = $todas_las_categorias;
}

try {
    foreach ($categorias_a_mostrar_en_secciones as $categoria) {
        $id_cat = $categoria['id_categoria'];
        $nombre_cat = $categoria['nombre_categoria'];

        $productos_en_esta_categoria = obtenerTodosLosProductosConFiltros(
            $conexion,
            $id_cat,
            $filtro_precio_min,
            $filtro_precio_max,
            $filtro_busqueda,
            $ordenar_por
        );

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