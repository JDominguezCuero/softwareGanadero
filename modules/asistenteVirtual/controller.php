<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../../config/config.php';

require_once __DIR__ . '/../../modules/auth/model.php';
require_once __DIR__ . '/../../modules/productos/model.php';
require_once '../../public/assets/validaciones.php';

// Asegúrate de que BASE_URL esté definida en config.php
// Ejemplo: define('BASE_URL', 'http://localhost/LoginADSO');

// Define UPLOAD_DIR
const UPLOAD_DIR = __DIR__ . '/../../public/assets/images/productos/';

// No envíes el header application/json hasta que estés seguro de que el proceso es JSON
// Esto se moverá al final, o dentro de cada case si es necesario
// header('Content-Type: application/json');

// Inicializar la respuesta con un estado de falla por defecto
$response = ['success' => false, 'message' => '', 'continueToPublish' => false];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Método de solicitud no permitido para esta acción.';
    header('Content-Type: application/json'); // Enviar el header aquí si es un error temprano
    echo json_encode($response);
    exit;
}

$action = $_POST['action'] ?? null; // Intentamos obtener la acción primero de $_POST (FormData)
$payload = [];

// Determine si la solicitud es JSON o FormData
// Si el Content-Type es application/json, entonces el cuerpo de la petición es JSON
// De lo contrario, asumimos que es FormData y los datos están en $_POST y $_FILES
if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== false) {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('JSON inválido recibido: ' . $input);
        $response['message'] = 'Datos JSON inválidos recibidos.';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    $action = $data['action'] ?? $action; // Preferir la acción del JSON si está presente
    $payload = $data['payload'] ?? $data; // Usar el payload o todo el JSON si no hay payload anidado
} else {
    // Si no es JSON, los datos de texto vienen en $_POST
    $payload = $_POST;
}

try {
    switch ($action) {
        case 'confirmRegister':
            header('Content-Type: application/json'); // Asegurarse de que sea JSON para esta respuesta
            $nombre = $payload['nombreCompleto'] ?? '';
            $correo = $payload['correo'] ?? '';
            $contrasena = $payload['contrasena'] ?? '';

            if (!camposNoVacios([$nombre, $correo, $contrasena])) {
                throw new Exception("Nombre, correo y contraseña son obligatorios para el registro.");
            }
            if (!validarEmail($correo)) {
                throw new Exception("El formato del correo electrónico no es válido.");
            }
            if (!validarPassword($contrasena)) {
                throw new Exception("La contraseña no cumple con los requisitos de seguridad. Debe tener mínimo 5 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
            }

            if (obtenerUsuarioPorCorreo($correo)) {
                throw new Exception("El correo electrónico ya se encuentra registrado. Por favor, intenta iniciar sesión.");
            }

            $usuario_default = "botUser_" . uniqid();
            $telefono_default = '';
            $rol_id_default = 3;
            $direccion_default = '';
            $estado_default = 'Activo';
            $imagen_url_default = BASE_URL . '/modules/auth/perfiles/profileDefault.png';

            if (agregarUsuario($conexion, $nombre, $usuario_default, $correo, $contrasena, $direccion_default, $estado_default, $imagen_url_default, $rol_id_default, $telefono_default)) {
                $response['success'] = true;
                $response['message'] = '¡Genial! Tu cuenta ha sido creada exitosamente. ¡Bienvenido/a!';
                $response['userName'] = explode(' ', $nombre)[0];

                $usuarioRegistrado = obtenerUsuarioPorCorreo($correo);
                if ($usuarioRegistrado) {
                    $_SESSION['usuario'] = $usuarioRegistrado['nombre_usuario'];
                    $_SESSION['nombre'] = $usuarioRegistrado['nombreCompleto'];
                    $_SESSION['id_usuario'] = $usuarioRegistrado['id_usuario'];
                    $_SESSION['correo_usuario'] = $usuarioRegistrado['correo_usuario'];
                    $_SESSION['rol'] = $usuarioRegistrado['id_rol'];
                    $_SESSION['url_Usuario'] = $usuarioRegistrado['imagen_url_Usuario'];

                    if (isset($payload['intendedAction']) && $payload['intendedAction'] === 'publishProduct') {
                        $response['continueToPublish'] = true;
                    }
                }
            } else {
                throw new Exception("No se pudo registrar el usuario en la base de datos.");
            }
            break;

        case 'confirmLogin':
            header('Content-Type: application/json'); // Asegurarse de que sea JSON para esta respuesta
            $correo = $payload['correo'] ?? $payload['loginCorreo'] ?? '';
            $contrasena = $payload['contrasena'] ?? $payload['loginContrasena'] ?? '';
            $intendedAction = $payload['intendedAction'] ?? null;

            if (empty($correo) || empty($contrasena)) {
                throw new Exception("Por favor, ingresa tu correo y contraseña para iniciar sesión.");
            }

            $usuario = obtenerUsuarioPorCorreo($correo);

            if ($usuario && password_verify($contrasena, $usuario['contrasena_usuario'])) {
                if ($usuario['estado'] == 'Activo') {
                    $_SESSION['usuario'] = $usuario['nombre_usuario'];
                    $_SESSION['nombre'] = $usuario['nombreCompleto'];
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['correo_usuario'] = $usuario['correo_usuario'];
                    $_SESSION['rol'] = $usuario['id_rol'];
                    $_SESSION['url_Usuario'] = $usuario['imagen_url_Usuario'];

                    $response['success'] = true;
                    $response['message'] = '¡Bienvenido/a de nuevo! Has iniciado sesión correctamente.';
                    $response['userName'] = explode(' ', $usuario['nombreCompleto'])[0];

                    if ($intendedAction === 'publishProduct') {
                        $response['continueToPublish'] = true;
                    }
                } else {
                    throw new Exception("El usuario no se encuentra activo, por favor contactese con el administrador.");
                }
            } else {
                throw new Exception("Usuario o contraseña incorrecta.");
            }
            break;

        case 'confirmPublish':
            header('Content-Type: application/json'); // Asegurarse de que sea JSON para esta respuesta
            $mensjError = '';
            $imagen_url = '';

            // Los datos de texto como nombreProducto y precioProducto vendrán en $payload (que es $_POST)
            $nombreProducto = $payload['nombreProducto'] ?? '';
            $precioProducto = $payload['precioProducto'] ?? 0;

            $id_usuario = $_SESSION['id_usuario'] ?? null;

            if (!$id_usuario) {
                throw new Exception('Usuario no autenticado para publicar producto.'); // Lanza una excepción para que el catch la maneje
            }

            // Lógica de carga de imagen - USA $_FILES directamente
            if (isset($_FILES['imagenProducto']) && $_FILES['imagenProducto']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['imagenProducto']['tmp_name'];
                $fileName = $_FILES['imagenProducto']['name'];
                // No necesitas fileSize o fileType a menos que hagas validaciones por ellos
                // $fileSize = $_FILES['imagenProducto']['size'];
                // $fileType = $_FILES['imagenProducto']['type'];

                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg', 'webp'];
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = UPLOAD_DIR . $newFileName;

                    if (!is_dir(UPLOAD_DIR)) {
                        mkdir(UPLOAD_DIR, 0775, true); // Crea el directorio recursivamente
                    }

                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imagen_url = BASE_URL . '/public/assets/images/productos/' . $newFileName;
                    } else {
                        throw new Exception("Error al mover el archivo subido al servidor.");
                    }
                } else {
                    throw new Exception("Tipo de archivo de imagen no permitido. Solo se aceptan JPG, GIF, PNG, JPEG, WEBP.");
                }
            } else {
                // Manejar errores específicos de la subida de archivo
                switch ($_FILES['imagenProducto']['error'] ?? UPLOAD_ERR_NO_FILE) {
                    case UPLOAD_ERR_INI_SIZE:
                    case UPLOAD_ERR_FORM_SIZE:
                        throw new Exception("El archivo es demasiado grande.");
                    case UPLOAD_ERR_PARTIAL:
                        throw new Exception("La subida del archivo fue parcial.");
                    case UPLOAD_ERR_NO_FILE:
                        throw new Exception("No se ha seleccionado ninguna imagen para subir.");
                    default:
                        throw new Exception("Ocurrió un error desconocido al subir la imagen. Código: " . ($_FILES['imagenProducto']['error'] ?? 'N/A'));
                }
            }

            // Validaciones de datos del producto
            if (!empty($nombreProducto) && $precioProducto > 0) {
                // Proporciona valores por defecto o adquiere estos datos desde el chatbot si es necesario
                $stock = 1;
                $categoria_id = 1; // Asigna una categoría por defecto o pide al usuario
                $descripcion = 'Publicado desde el chatbot.'; // Descripción por defecto o pide al usuario
                $estado_oferta = 0; // 0 para no oferta, 1 para oferta. Decide si el chatbot preguntará esto
                $precio_anterior = null; // null si no es oferta

                // Asegúrate de que $conexion esté disponible (desde config.php)
                if (crearProducto($conexion, $nombreProducto, $descripcion, $precioProducto, $stock, $imagen_url, $categoria_id, $estado_oferta, $precio_anterior, $id_usuario)) {
                    $response['success'] = true;
                    $response['message'] = 'Producto publicado correctamente.';
                } else {
                    throw new Exception("Error al guardar el producto en la base de datos.");
                }
            } else {
                throw new Exception("Faltan datos obligatorios (nombre, precio) o son inválidos para publicar el producto.");
            }
            break;

        default:
            $response['message'] = 'Acción de chatbot no reconocida o no implementada.';
            break;
    }
} catch (Exception $e) {
    error_log("Error en Chatbot Controller: " . $e->getMessage()); // Para depuración en el servidor
    $response['message'] = $e->getMessage();
}

// Finalmente, envía la respuesta JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;

?>