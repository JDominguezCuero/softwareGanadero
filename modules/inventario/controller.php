<?php
// inventario/controller.php
session_start();
require_once(__DIR__ . '/model.php');
require_once __DIR__ . '/../../config/config.php';

// Verifica si hay sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index_controller.php?login=error&reason=nologin");
    exit;
}

$accion = $_GET['accion'] ?? 'listar';
$mensjError = "";

try {
    switch ($accion) {
        case 'listar':
            $inventario = obtenerInventario($conexion);
            $msg = $_GET['msg'] ?? null;

            include(__DIR__ . '/views/inventario.php');
            break;
        
        case 'consultar':
            $id = $_GET['id'] ?? null;

            if ($id) {
                $item = obtenerItemPorId($conexion, $id);
                if (!$item) {
                    $mensjError = "Alimento no encontrado.";
                }
            } else {
                $mensjError = "ID de alimento no proporcionado.";
            }
            if (!empty($mensjError)) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                exit;
            }
            include(__DIR__ . '/views/inventario.php');
            break;    

        case 'agregar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = $_POST['nombre'] ?? ''; 
                $cantidad = $_POST['cantidad'] ?? 0;
                $unidad_medida = $_POST['unidad_medida'] ?? '';
                $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';

                // Validación básica de los datos
                if (!empty($nombre) && $cantidad >= 0 && !empty($unidad_medida) && !empty($fecha_ingreso)) {
                    // Llama a la función del modelo con los nuevos parámetros
                    $resultado = crearItem($conexion, $nombre, $cantidad, $unidad_medida, $fecha_ingreso);
                    if ($resultado) {
                        $mensaje = "Alimento agregado correctamente.";
                        header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                        exit;
                    } else {
                        $mensjError = "Error al agregar el alimento.";
                    }
                } else {
                    $mensjError = "Faltan datos obligatorios o son inválidos para agregar el alimento.";
                }
            } else {
                $mensjError = "Método no permitido para esta acción.";
            }
            if (!empty($mensjError)) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                exit;
            }
            break;

        case 'editar':
            $id = $_POST['id_alimento'] ?? $_GET['id_alimento'] ?? null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $nombre = $_POST['nombre'] ?? '';
                $cantidad = $_POST['cantidad'] ?? 0;
                $unidad_medida = $_POST['unidad_medida'] ?? '';
                $fecha_ingreso = $_POST['fecha_ingreso'] ?? '';

                // Validación básica
                if (!empty($nombre) && $cantidad >= 0 && !empty($unidad_medida) && !empty($fecha_ingreso)) {
                    $resultado = actualizarItem($conexion, $id, $nombre, $cantidad, $unidad_medida, $fecha_ingreso);
                    if ($resultado) {
                        $mensaje = "Alimento actualizado correctamente.";
                        header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                        exit;
                    } else {
                        $mensjError = "Error al actualizar el alimento.";
                    }
                } else {
                    $mensjError = "Faltan datos obligatorios o son inválidos para actualizar el alimento.";
                }
            } else if ($id) {
                $item = obtenerItemPorId($conexion, $id);
                if (!$item) {
                    $mensjError = "Alimento no encontrado para editar.";
                }
                include __DIR__ . '/views/inventario.php'; 
            } else {
                $mensjError = "ID de alimento no proporcionado para editar.";
            }

            
            if (!empty($mensjError)) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                exit;
            }
            break;

        case 'eliminar':
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                $resultado = eliminarItem($conexion, $id);
                if ($resultado) {
                    $mensaje = "Alimento eliminado correctamente.";
                    header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                    exit;
                } else {
                    $mensjError = "Error al eliminar el alimento.";
                }
            } else {
                $mensjError = "ID de alimento no proporcionado para eliminar.";
            }

            if (!empty($mensjError)) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                exit;
            }
            break;

        default:
            header("Location: controller.php?accion=listar");
            exit;
            break;
            
    }

} catch (Exception $e) {
    error_log("Error en Controller de Inventario: " . $e->getMessage());

    $errorMsg = $e->getMessage();
    
    if (str_contains($errorMsg, 'Unknown column') || str_contains($errorMsg, 'Base table or view not found')) {
        $mensajeUsuario = "Hubo un problema con la base de datos (columnas o tabla no encontradas). Verifica la estructura.";
    } else {
        $mensajeUsuario = "Ocurrió un error inesperado en el servidor. Contacte al administrador.";
    }

    header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensajeUsuario));
    exit;
}
?>