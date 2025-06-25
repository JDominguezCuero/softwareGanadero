<?php
session_start();
require_once(__DIR__ . '/model.php');
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: ../../public/index_controller.php?login=error&reason=nologin");
    exit;
}

$current_user_id = $_SESSION['id_usuario']; // ID del usuario logueado

$accion = $_GET['accion'] ?? $_POST['accion'] ?? 'listar';
$mensjError = ""; // Para mensajes de error que se puedan pasar a la vista

try {
    switch ($accion) {
        case 'listar':
            $notificaciones = obtenerNotificacionesPorUsuario($conexion, $current_user_id);
            $msg = $_GET['msg'] ?? null; 

            include(__DIR__ . '/views/notificacion.php');
        break;

        case 'insertar':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $id_usuario_receptor = $data['ids'] ?? [];
                $id_emisor = $_SESSION['id_usuario'];
                $id_producto = $data['id_producto'];

                if (!empty($id_usuario_receptor) && is_array($id_usuario_receptor)) {
                    if (insertarNotificacion($conexion, $id_usuario_receptor, $id_emisor, $id_producto)) {
                        echo json_encode(['success' => true, 'message' => 'Notificaciones marcadas como leídas.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Fallo al marcar notificaciones como leídas.']);
                    }


                } else {
                    echo json_encode(['success' => false, 'message' => 'IDs de notificación no válidos.']);
                }

            } else {
            echo json_encode(['success' => false, 'message' => 'Método no permitido para marcar como leído.']);
            }
        break;

        case 'marcarComoLeido':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $ids = $data['ids'] ?? [];

                if (!empty($ids) && is_array($ids)) {
                    if (marcarComoLeido($conexion, $ids)) {
                        echo json_encode(['success' => true, 'message' => 'Notificaciones marcadas como leídas.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Fallo al marcar notificaciones como leídas.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'IDs de notificación no válidos.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido para marcar como leído.']);
            }
            break;

        case 'eliminar':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                $ids = $data['ids'] ?? [];

                if (!empty($ids) && is_array($ids)) {
                    if (eliminarNotificaciones($conexion, $ids)) {
                        echo json_encode(['success' => true, 'message' => 'Notificaciones eliminadas correctamente.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Fallo al eliminar notificaciones.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'IDs de notificación no válidos para eliminar.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido para eliminar.']);
            }
            break;

        case 'eliminarTodas':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (eliminarTodasNotificacionesUsuario($conexion, $current_user_id)) {
                    echo json_encode(['success' => true, 'message' => 'Todas las notificaciones eliminadas correctamente.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Fallo al eliminar todas las notificaciones.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido para eliminar todas.']);
            }
            break;

        default:
            header("Location: controller.php?accion=listar");
            exit;
            break;
    }

} catch (Exception $e) {
    error_log("Error en Controller de Notificaciones: " . $e->getMessage());

    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        echo json_encode(['success' => false, 'message' => 'Ocurrió un error interno del servidor: ' . $e->getMessage()]);
    } else {
        $mensajeUsuario = "Ocurrió un error inesperado en el servidor. Contacte al administrador.";
        if (strpos($e->getMessage(), 'Unknown column') !== false || strpos($e->getMessage(), 'Base table or view not found') !== false) {
            $mensajeUsuario = "Hubo un problema con la base de datos. Verifica la estructura.";
        }
        header("Location: controller.php?accion=listar&error=" . urlencode($mensajeUsuario));
    }
    exit;
}

$conexion = null;
?>