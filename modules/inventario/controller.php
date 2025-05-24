<?php
session_start();
require_once(__DIR__ . '/model.php');
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php?login=error&reason=nologin");
    exit;
}

$accion = $_GET['accion'] ?? 'listar';
$mensjError = "";

try {
    switch ($accion) {
        case 'listar':
            global $conexion;
            $inventario = obtenerInventario($conexion);
            $msg = $_GET['msg'] ?? null;

            include(__DIR__ . '/views/inventario.php');
            break;
        
        case 'consultar':
            global $conexion;
            $id = $_GET['id'] ?? null;

            if ($id) {
                $item = obtenerItemPorId($conexion, $id);
                if ($item) {
                    include __DIR__ . '/views/inventario.php';
                } else {
                    echo "Producto no encontrado.";
                }
            } else {
                echo "ID no proporcionado.";
            }

            break;    

        case 'agregar':
            global $conexion;
            $nombre = $_POST['producto'] ?? '';
            $cantidad = $_POST['cantidad'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $precio = $_POST['precioUnitario'] ?? 0;

            if (!empty($nombre) && !empty($cantidad) && !empty($descripcion) && $precio > 0) {
                crearItem($conexion, $nombre, $cantidad, $descripcion, $precio);
            }

            $mensaje = "Producto agregado correctamente";
            header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));

            exit;
            break;

        case 'editar':
            global $conexion;
            $id = $_POST['id_producto'] ?? $_GET['id_producto'] ?? null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = $_POST['producto'] ?? '';
                $cantidad = $_POST['cantidad'] ?? 0;
                $descripcion = $_POST['descripcion'] ?? '';
                $precio = $_POST['precioUnitario'] ?? 0;
                actualizarItem($conexion, $id, $nombre, $cantidad, $descripcion, $precio);

                $mensaje = "Producto actualizado correctamente";
                header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));

                exit;
            } else {
                $item = obtenerItemPorId($conexion, $id);
                include __DIR__ . '/views/inventario.php';
            }

            break;

        case 'eliminar':
            global $conexion;
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                eliminarItem($conexion, $id);
            }

            $mensaje = "Producto eliminado correctamente";
            header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));

            exit;
           break;



        default:
            header("Location: controller.php?accion=listar");
            exit;
            break;
            
    }

} catch (Exception $e) {
    error_log($e->getMessage()); // Guarda el error técnico en el log

    // Mensaje más amigable para el usuario
    $errorMsg = $e->getMessage();
    
    if (str_contains($errorMsg, 'Unknown column')) {
        $mensajeUsuario = "Hubo un problema con la base de datos. Verifica que las columnas existan correctamente.";
    } else {
        $mensajeUsuario = "Ocurrió un error inesperado. Contacte al administrador.";
    }

    header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensajeUsuario));
    exit;
}
?>