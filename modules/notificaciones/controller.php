<?php
session_start();

error_log("--------------------------------------------------");
error_log("DEBUG: Peticion al controlador de notificaciones iniciada.");

$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'N/A';
error_log("DEBUG: Metodo de solicitud: " . $requestMethod);

$accion = $_GET['accion'] ?? 'N/A';
error_log("DEBUG: Accion solicitada: " . $accion);

// Captura el cuerpo crudo de la petición POST (JSON)
$request_body_raw = file_get_contents('php://input');
error_log("DEBUG: Cuerpo de la peticion POST (RAW): " . ($request_body_raw ? $request_body_raw : "[VACIO]"));

// Decodifica el JSON
$data = json_decode($request_body_raw, true); // <--- Asegúrate de usar $data aquí, como tu código original
error_log("DEBUG: Datos decodificados (JSON): " . print_r($data, true));

// Si json_decode falla, puedes añadir más logs
if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("DEBUG: ERROR JSON_DECODE: " . json_last_error_msg());
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once(__DIR__ . '/model.php');
require_once __DIR__ . '/../../config/config.php';

// Incluir PHPMailer (ajusta estas rutas si son diferentes)
require __DIR__ . '/../../public/assets/lib/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../public/assets/lib/PHPMailer/src/SMTP.php';
require __DIR__ . '/../../public/assets/lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: ../../public/index_controller.php?login=error&reason=nologin");
    exit;
}

$current_user_id = $_SESSION['id_usuario'];
$accion = $_GET['accion'] ?? $_POST['accion'] ?? 'listar';
$mensjError = "";

// $conexion ya debería estar disponible aquí por el require_once de database.php

try {
    switch ($accion) {
        case 'listar':
            $notificaciones = obtenerNotificacionesPorUsuario($conexion, $current_user_id);
            $msg = $_GET['msg'] ?? null; 
            include(__DIR__ . '/views/notificacion.php');
        break;

        case 'listarNotificaciones':
            header('Content-Type: application/json'); 
            if ($current_user_id) {
                $notificaciones = obtenerNotificacionesPorUsuario($conexion, $current_user_id);
                echo json_encode($notificaciones); 
            } else {
                echo json_encode([]);
            }
        break;

        case 'insertar':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);

                $id_usuario_receptor = $data['id_vendedor'] ?? null; 
                $id_producto = $data['id_producto'] ?? null;
                $mensaje_comprador = $data['mensaje'] ?? "Estoy interesado en tu producto.";
                $id_emisor = $_SESSION['id_usuario'];

                if (!empty($id_usuario_receptor) && !empty($id_producto) && !empty($id_emisor)) {
                    if (insertarNotificacion($conexion, $id_usuario_receptor, $id_emisor, $id_producto, $mensaje_comprador, 'interes')) {
                        echo json_encode(['success' => true, 'message' => 'Notificación de interés enviada.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Fallo al enviar la notificación de interés.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Datos incompletos para enviar notificación de interés.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido para insertar.']);
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

        case 'enviarRespuestaRapida':
            header('Content-Type: application/json');
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = json_decode(file_get_contents('php://input'), true);
                
                $destinatarioEmail = filter_var($data['destinatarioEmail'] ?? '', FILTER_SANITIZE_EMAIL);
                $destinatarioNombre = htmlspecialchars($data['destinatarioNombre'] ?? 'Interesado');
                $destinatarioId = $data['destinatarioId'] ?? null;
                $destinatarioTelefono = htmlspecialchars($data['destinatarioTelefono'] ?? '');
                $mensajeRespuesta = htmlspecialchars($data['mensaje'] ?? '');
                $nombreProducto = htmlspecialchars($data['nombreProducto'] ?? 'nuestro producto');
                $idProducto = $data['idProducto'] ?? null;

                $vendedorInfo = obtenerDatosUsuario($conexion, $current_user_id);
                $nombreVendedor = htmlspecialchars($vendedorInfo['nombreCompleto'] ?? 'El Vendedor');
                $correoVendedor = htmlspecialchars($vendedorInfo['correo_usuario'] ?? 'noreply@tudominio.com');

                if (empty($destinatarioEmail) || empty($mensajeRespuesta) || !filter_var($destinatarioEmail, FILTER_VALIDATE_EMAIL) || empty($destinatarioId) || empty($idProducto)) {
                    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos para enviar respuesta.']);
                    break;
                }

                $asuntoEmail = "Respuesta de {$nombreVendedor} sobre tu interés en {$nombreProducto}";
                $cuerpoCorreoHTML = "
                    <html>
                    <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 0; }
                        .container { background-color: #fff; border-radius: 8px; padding: 30px; margin: 20px auto; max-width: 600px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: left; }
                        .header { text-align: center; margin-bottom: 20px; }
                        .header img { width: 150px; margin-bottom: 10px; }
                        .message-box { background-color: #f0f8ff; border-left: 5px solid #007bff; padding: 15px; margin: 20px 0; font-style: italic; }
                        .footer { font-size: 12px; color: #777; margin-top: 30px; text-align: center; }
                        .product-info { background-color: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                    </style>
                    </head>
                    <body>
                    <div class='container'>
                        <div class='header'>
                            <img src='" . BASE_URL . "/public/assets/images/email6.png' alt='Logo' style='max-width: 200px;'>
                            <h2>¡Respuesta de {$nombreVendedor}!</h2>
                        </div>
                        <p>Hola <strong>{$destinatarioNombre}</strong>,</p>
                        <p>El vendedor <strong>{$nombreVendedor}</strong> ha respondido a tu interés sobre el producto <strong>'{$nombreProducto}'</strong>:</p>
                        
                        <div class='message-box'>
                            <p>{$mensajeRespuesta}</p>
                        </div>

                        <div class='product-info'>
                            <p><strong>Producto:</strong> {$nombreProducto}</p>
                            <p>Puedes ver más detalles del producto <a href='" . BASE_URL . "/modules/productos/views/detalle_producto.php?id={$idProducto}'>aquí</a>.</p>
                        </div>
                        
                        <p>Saludos cordiales,</p>
                        <p>El equipo de Ganaderos.</p>

                        <div class='footer'>
                            <p>© " . date('Y') . " PROGAN - Todos los derechos reservados</p>
                        </div>
                    </div>
                    </body>
                    </html>";

                $all_success = true;
                $messages = [];

                $emailSent = sendEmailPHPMailer($destinatarioEmail, $destinatarioNombre, $asuntoEmail, $cuerpoCorreoHTML, $correoVendedor, $nombreVendedor);
                if ($emailSent) {
                    $messages[] = 'Correo enviado.';
                } else {
                    $all_success = false;
                    $messages[] = 'Fallo al enviar el correo.';
                }

                $mensajeNotificacionInterna = "Respuesta de {$nombreVendedor} sobre '{$nombreProducto}': \"{$mensajeRespuesta}\"";
                $notificacionInternaCreada = insertarNotificacion($conexion, $destinatarioId, $current_user_id, $idProducto, $mensajeNotificacionInterna, 'respuesta');

                if ($notificacionInternaCreada) {
                    $messages[] = 'Notificación interna para el comprador creada.';
                } else {
                    $all_success = false;
                    $messages[] = 'Fallo al crear notificación interna para el comprador.';
                }

                if (!empty($destinatarioTelefono) && isValidPhoneNumber($destinatarioTelefono)) {
                    $sms_message = "Respuesta de {$nombreVendedor} sobre '{$nombreProducto}': \"{$mensajeRespuesta}\". Revisa tus notificaciones en la app.";
                    $smsSent = sendSMS($destinatarioTelefono, $sms_message);
                    if ($smsSent) {
                        $messages[] = 'SMS enviado.';
                    } else {
                        $messages[] = 'Fallo al enviar SMS.';
                    }
                } else {
                    $messages[] = 'No se envió SMS (teléfono inválido o vacío).';
                }

                echo json_encode(['success' => $all_success, 'message' => implode(' ', $messages)]);

            } else {
                echo json_encode(['success' => false, 'message' => 'Método no permitido para enviar respuesta.']);
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
        header("Location: controller.php?accion=listar&error=" . urlencode($mensajeUsuario));
    }
    exit;
}

// --- Funciones auxiliares ---

function sendEmailPHPMailer($toEmail, $toName, $subject, $bodyHtml, $fromEmail, $fromName) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jsdmngzc@gmail.com';
        $mail->Password = 'uhcj wqsm ntvy ixxr'; // Tu App password de Gmail
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($toEmail, $toName);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $bodyHtml;
        $mail->CharSet = 'UTF-8';
        $mail->AltBody = strip_tags($bodyHtml); 

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar correo (PHPMailer): " . $mail->ErrorInfo . " | Detalles: " . $e->getMessage());
        return false;
    }
}

function sendSMS($phoneNumber, $message) {
    // ESTO ES UN PLACEHOLDER. NECESITAS IMPLEMENTARLO CON UNA API DE TERCEROS (Ej. Twilio)
    // Para depuración, simula el éxito:
    error_log("SMS SIMULADO: Enviado a $phoneNumber con mensaje: \"$message\"");
    return true; 
}

function isValidPhoneNumber($phone) {
    // Validación básica, ajusta según tus necesidades
    return preg_match('/^\+?\d{7,15}$/', $phone); 
}

// Función para obtener los datos de un usuario por su ID
// ASEGÚRATE que esta función o una similar esté disponible (ej. en modules/auth/model.php)
// Si está en otro archivo, ajusta el require_once correspondiente.
function obtenerDatosUsuario($conexion, $id_usuario) {
    $sql = "SELECT nombreCompleto, correo_usuario, telefono_usuario FROM usuarios WHERE id_usuario = :id_usuario LIMIT 1"; 
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>