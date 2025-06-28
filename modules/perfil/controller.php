<?php

session_start();
require_once 'model.php';
require_once '../../public/assets/validaciones.php';
require_once __DIR__ . '/../../config/config.php';

const UPLOAD_DIR = __DIR__ . '/../../modules/auth/perfiles/';

global $conexion;

$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'listarUsuario':
        $id = $_GET['id_usuario'] ?? $_SESSION['id_usuario'] ?? null;

        if (!$id) {
            $_SESSION['error'] = "ID de usuario no proporcionado para listar el perfil.";
            header("Location: " . BASE_URL . "/modules/perfil/views/perfil.php");
            exit();
        }

        $userData = obtenerUsuarioPorId($conexion, $id);

        if (!$userData) {
            $_SESSION['error'] = "Perfil de usuario no encontrado.";
            header("Location: " . BASE_URL . "/modules/perfil/views/perfil.php");
            exit();
        }

        include __DIR__ . '/views/perfil.php';
    break;

    case 'actualizar':
        $id = $_POST['id_usuario'] ?? $_SESSION['id_usuario'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            try {
                $nombre_completo = trim($_POST['nombreCompleto'] ?? '');
                $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
                $correo = trim($_POST['correo_usuario'] ?? '');
                $telefono_movil = trim($_POST['telefono_usuario'] ?? '');
                $direccion = trim($_POST['direccion_usuario'] ?? '');
                $contrasena = $_POST['contrasena'] ?? '';

                
                $currentUserData = obtenerUsuarioPorId($conexion, $id);
                if (!$currentUserData) {
                    throw new Exception("Usuario a actualizar no encontrado.");
                }
                $rol_id = $currentUserData['id_rol'];
                $estado = $currentUserData['estado'];

                // VALIDACIONES (usando tus funciones de validaciones.php)
                if (!camposNoVacios([$nombre_completo, $nombre_usuario, $correo, $telefono_movil, $direccion])) {
                    throw new Exception("Todos los campos obligatorios deben ser llenados.");
                }
                if (!validarEmail($correo)) {
                    throw new Exception("El formato del correo electrónico no es válido.");
                }
                if (!empty($contrasena) && !validarPassword($contrasena)) {
                    throw new Exception("La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
                }

                $imagen_url = $_POST['imagen_url_actual'] ?? BASE_URL . '/modules/auth/perfiles/profileDefault.png';

                
                if (isset($_FILES['fileFoto']) && $_FILES['fileFoto']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['fileFoto']['tmp_name'];
                    $fileName = $_FILES['fileFoto']['name'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (!in_array($fileExtension, $allowedfileExtensions)) {
                        throw new Exception("Tipo de archivo de imagen no permitido. Solo JPG, JPEG, PNG, GIF.");
                    }

                    // Generar un nombre único para el archivo para evitar colisiones
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $destPath = UPLOAD_DIR . $newFileName;

                    // Asegurarse de que el directorio de subida existe
                    if (!is_dir(UPLOAD_DIR)) {
                        mkdir(UPLOAD_DIR, 0777, true);
                    }
                    
                    if (move_uploaded_file($fileTmpPath, $destPath)) {
                        $imagen_url = BASE_URL . '/modules/auth/perfiles/' . $newFileName;
                    } else {
                        throw new Exception("Error al mover la nueva imagen subida al servidor.");
                    }
                }
                
                $resultado = actualizarUsuario(
                    $conexion,
                    $id,
                    $nombre_completo,
                    $nombre_usuario,
                    $correo,
                    $direccion,
                    $estado,
                    $imagen_url,
                    $rol_id,
                    $telefono_movil,
                    $contrasena
                );

                if ($resultado) {
                    $_SESSION['url_Usuario'] = $imagen_url;
                    $_SESSION['nombre'] = $nombre_completo;
                    $_SESSION['usuario'] = $nombre_usuario;
                    $_SESSION['correo_usuario'] = $correo;
                    $_SESSION['id_usuario'] = $id;

                    $_SESSION['message'] = "Perfil actualizado correctamente.";
                    header("Location: controller.php?accion=listarUsuario&id_usuario=" . $id);
                    exit();
                } else {
                    throw new Exception("Error al actualizar el perfil en la base de datos.");
                }

            } catch (Exception $e) {
                $_SESSION['error'] = 'Error al actualizar: ' . $e->getMessage();
                header("Location: controller.php?accion=listarUsuario&id_usuario=" . $id);
                exit();
            }
        } else {
            $_SESSION['error'] = "Solicitud no válida para actualizar el perfil.";
            header("Location: " . BASE_URL . "/modules/perfil/views/perfil.php");
            exit();
        }
        break;

    default:
        include 'views/perfil.php';
    break;
}

?>