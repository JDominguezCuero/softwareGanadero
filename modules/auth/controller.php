<?php
session_start();
require_once 'model.php';
require_once '../../public/assets/validaciones.php';
require_once __DIR__ . '/../../config/config.php';

require '../../public/assets/lib/PHPMailer/src/PHPMailer.php';
require '../../public/assets/lib/PHPMailer/src/SMTP.php';
require '../../public/assets/lib/PHPMailer/src/Exception.php';

const UPLOAD_DIR = __DIR__ . '/../../modules/auth/perfiles/';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'listar':
            $usuarios = obtenerUsuario($conexion);
            $roles = obtenerRoles($conexion);

            $msg = $_GET['msg'] ?? null;

            include(__DIR__ . '/views/gestionUsuario.php');
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try{
                 $correo = $_POST['correoElectronicoLogin'] ?? '';
                 $contrasena = $_POST['contrasenaLogin'] ?? '';
                 $mensjError = "";

                 $usuario = obtenerUsuarioPorCorreo($correo);

                if ($usuario && password_verify($contrasena, $usuario['contrasena_usuario'])) {

                    if ($usuario['estado'] == 'Activo'){
                        // Inicio de sesión exitoso
                        $_SESSION['usuario'] = $usuario['nombre_usuario'];
                        $_SESSION['nombre'] = $usuario['nombreCompleto'];
                        $_SESSION['id_usuario'] = $usuario['id_usuario'];
                        $_SESSION['correo_usuario'] = $usuario['correo_usuario'];
                        $_SESSION['rol'] = $usuario['id_rol'];
                        $_SESSION['url_Usuario'] = $usuario['imagen_url_Usuario'];

                        header("Location: ../../public/index_controller.php");
                        exit;

                    } else{
                        // Usuario no encontrado o contraseña incorrecta
                        $mensjError = "El usuario no se encuentra activo, por favor contactese con el administrador";
                        throw new Exception($mensjError);
                        exit;    
                    }
                    
                } else {
                    // Usuario no encontrado o contraseña incorrecta
                    $mensjError = "Usuario o contraseña incorrecta";
                    throw new Exception($mensjError);
                    exit;
                }   
            }
            catch(Exception $e) {
                header("Location: views/autenticacion.php?login=1&error=" . $mensjError);
                exit;
            }            
        } else {
            include 'views/autenticacion.php';
        }
        break;

    case 'registro':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $mensjError = "";

                $nombre = $_POST['nombreCompleto'] ?? '';
                $correo = $_POST['correoElectronico'] ?? '';
                $usuario = $_POST['usuario'] ?? '';
                $contrasena = $_POST['contrasena'] ?? '';   
                $imagen_url = '/../../modules/auth/perfiles/profileDefault.png';            

                // Validaciones reutilizando funciones
                if (!camposNoVacios([$nombre, $correo, $usuario, $contrasena])) {
                    $mensjError = "Todos los campos son obligatorios.";
                    throw new Exception($mensjError);
                }
                if (!validarEmail($correo)) {
                    $mensjError = "Correo electrónico no válido.";
                    throw new Exception($mensjError);
                }
                if (!validarPassword($contrasena)) {
                    $mensjError = "La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.";
                    throw new Exception($mensjError);
                }
                if (obtenerUsuarioPorCorreo($correo)) {
                    $mensjError = "El correo electrónico ya está registrado.";
                    throw new Exception($mensjError);
                }

                // Registrar
                if (registrarUsuario($nombre, $correo, $usuario, $contrasena, $imagen_url)) {
                    header("Location: views/autenticacion.php?success=1");
                    exit;
                } else {
                    $mensjError = "Error al registrar usuario.";
                    throw new Exception($mensjError);
                }

            } catch (Exception $e) {
                header("Location: views/autenticacion.php?success=2&error=" . $mensjError);
                exit;
            }
        } else {
            include 'views/autenticacion.php';
        }
    break;

    case 'agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $mensjError = '';

                // Captura de datos del formulario
                $nombre = $_POST['nombreCompleto'] ?? '';
                $usuario = $_POST['nombre_usuario'] ?? '';
                $correo = $_POST['correo_usuario'] ?? '';
                $telefono = $_POST['telefono_usuario'] ?? '';
                $rol_id = $_POST['rol_id'] ?? '';
                $direccion = $_POST['direccion_usuario'] ?? '';
                $estado = $_POST['estado'] ?? '';
                $contrasena = $_POST['contrasena'] ?? '';
                $imagen_url = ''; // Inicializar imagen_url

                // Validaciones básicas
                if (!camposNoVacios([$nombre, $usuario, $correo, $telefono, $rol_id, $direccion, $estado, $contrasena])) {
                    $mensjError = "Todos los campos son obligatorios.";
                    throw new Exception($mensjError);
                }
                if (!validarEmail($correo)) {
                    $mensjError = "Correo electrónico no válido.";
                    throw new Exception($mensjError);
                }
                if (!validarPassword($contrasena)) {
                    $mensjError = "La contraseña debe tener mínimo 5 caracteres, una mayúscula, una minúscula, un número y un carácter especial.";
                    throw new Exception($mensjError);
                }
                if (obtenerUsuarioPorCorreo($correo)) {
                    $mensjError = "El correo electrónico ya está registrado.";
                    throw new Exception($mensjError);
                }

                // Lógica de carga de imagen
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['imagen']['tmp_name'];
                    $fileName = $_FILES['imagen']['name'];
                    $fileSize = $_FILES['imagen']['size'];
                    $fileType = $_FILES['imagen']['type'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    // Validar extensiones permitidas
                    $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        // Generar un nombre único para el archivo
                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                        $destPath = UPLOAD_DIR . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            // Ruta relativa o URL pública de la imagen para guardar en la BD
                            $imagen_url = BASE_URL . '/modules/auth/perfiles/' . $newFileName;
                        } else {
                            $mensjError = "Error al mover el archivo subido.";
                        }
                    } else {
                        $mensjError = "Tipo de archivo de imagen no permitido.";
                    }
                }

                if (!empty($mensjError)) {
                    header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                    exit;
                }

                if (empty($imagen_url)) {
                    $imagen_url = BASE_URL . '/modules/auth/perfiles/profileDefault.png';
                }

                // Registrar el usuario
                if (agregarUsuario($conexion, $nombre, $usuario, $correo, $contrasena, $direccion, $estado, $imagen_url, $rol_id, $telefono)) {
                    $_SESSION['url_Usuario'] = $imagen_url; 
                    $roles = obtenerRoles($conexion);  
                     $mensaje =  "Usuario registrado correctamente.";
                    header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                    exit;
                } else {
                    throw new Exception("Error al registrar el usuario en la base de datos.");
                }

            } catch (Exception $e) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($e->getMessage()));
                exit;
            }
        } else {
            header("Location: controller.php?accion=listar&inv=1&error=" . urlencode("Método no permitido."));
            exit;
        }
    break;

    case 'editar':
        $id = $_POST['id_usuario'] ?? $_GET['id_usuario'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
            try {
                $mensjError = '';

                // Captura de datos del formulario
                $nombre = $_POST['nombreCompleto'] ?? '';
                $usuario = $_POST['nombre_usuario'] ?? '';
                $correo = $_POST['correo_usuario'] ?? '';
                $telefono = $_POST['telefono_usuario'] ?? '';
                $rol_id = $_POST['rol_id'] ?? ''; // si lo estás usando
                $direccion = $_POST['direccion_usuario'] ?? '';
                $estado = $_POST['estado'] ?? '';
                $contrasena = $_POST['contrasena'] ?? '';
                $imagen_url = $_POST['imagen_url_actual'] ?? '';

                // Validación básica
                if (!camposNoVacios([$nombre, $usuario, $correo, $telefono, $direccion, $estado])) {
                    throw new Exception("Todos los campos son obligatorios.");
                }

                if (!validarEmail($correo)) {
                    throw new Exception("Correo electrónico no válido.");
                }

                // Validación de contraseña si se quiere cambiar
                if (!empty($contrasena) && !validarPassword($contrasena)) {
                    throw new Exception("La contraseña debe tener mínimo 5 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
                }

                // Procesar nueva imagen (si se cargó)
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['imagen']['tmp_name'];
                    $fileName = $_FILES['imagen']['name'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                        $destPath = UPLOAD_DIR . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            $imagen_url = BASE_URL . '/modules/auth/perfiles/' . $newFileName;
                        } else {
                            throw new Exception("Error al mover la nueva imagen.");
                        }
                    } else {
                        throw new Exception("Tipo de archivo no permitido para la imagen.");
                    }
                }

                if (empty($imagen_url)) {
                    $imagen_url = BASE_URL . '/modules/auth/perfiles/profileDefault.png';
                }

                // Ejecutar la actualización del usuario
                $resultado = actualizarUsuario($conexion, $id, $nombre, $usuario, $correo, $direccion, $estado, $imagen_url, $rol_id, $telefono, 
                    $contrasena // Si es vacío, la función decide si mantener la actual
                );

                if ($resultado) {
                    $_SESSION['url_Usuario'] = $imagen_url; 
                    $mensaje = "Usuario actualizado correctamente.";
                    header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                    exit;
                } else {
                    throw new Exception("Error al actualizar el usuario en la base de datos.");
                }

            } catch (Exception $e) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($e->getMessage()));
                exit;
            }

        } else if ($id) {
            $item = obtenerUsuarioPorId($conexion, $id);
            $roles = obtenerRoles($conexion);
            include __DIR__ . '/views/usuarios.php';
        } else {
            $mensjError = "ID de usuario no proporcionado.";
            header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
            exit;
        }
    break;

    case 'eliminar':
        global $conexion;

        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $resultado = eliminarUsuario($conexion, $id);
            if ($resultado) {
                $mensaje = "Usuario eliminado correctamente.";
                header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                exit;
            } else {
                $mensjError = "Error al eliminar el usuario.";
            }
        } else {
            $mensjError = "ID del usuario no proporcionado para eliminar.";
        }

        if (!empty($mensjError)) {
            header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
            exit;
        }
    break;

    case 'restablecer':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $token = $_POST['token'] ?? '';
                $correo = $_POST['email'] ?? '';
                $nuevaContrasena = $_POST['contrasenaNueva'] ?? '';
                $confirmarContrasena = $_POST['confirmarContrasena'] ?? '';
                $mensjError = "";

                if ($nuevaContrasena != $confirmarContrasena) {
                    $mensjError = "Las contraseñas deben coincidir";
                    throw new Exception($mensjError);
                }
                if (!camposNoVacios([$nuevaContrasena])) {
                    $mensjError = "Todos los campos son obligatorios.";
                    throw new Exception($mensjError);
                }
                if (!validarPassword($nuevaContrasena)) {
                    $mensjError = "La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.";
                    throw new Exception($mensjError);
                }
                $usuario = obtenerUsuarioPorCorreo($correo);
                if (!$usuario) {
                    $mensjError = "El correo electrónico no está registrado.";
                    throw new Exception($mensjError);
                }
                
                if (actualizarContrasena($correo, $nuevaContrasena)) {
                    header("Location: views/nueva_contrasena.php?success=1");
                    exit;
                } else {
                    $mensjError = "Error al actualizar la contraseña.";
                    throw new Exception($mensjError);
                }
            } catch (Exception $e) {
                header("Location: views/nueva_contrasena.php?success=2&error=$mensjError&token=$token&email=$correo");
                exit;
            }
        } else {
            include 'views/nueva_contrasena.php';
        }
        break;

        case 'enviarEnlaceRestablecimiento':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                try{
                    $correo = $_POST['email'] ?? '';

                    if (!validarEmail($correo)) {
                        $mensjError = "Correo inválido.";
                        throw new Exception($mensjError);
                    }

                    $usuario = obtenerUsuarioPorCorreo($correo);
                    if (!$usuario) {
                        $mensjError = "Correo no registrado.";
                        throw new Exception($mensjError);
                    }

                    $token = generarTokenRestablecimiento($correo);
                    if (enviarCorreoRestablecimiento($correo, $token)) {
                        header("Location: views/autenticacion.php?enviado=1");
                    } else {
                        $mensjError = "No se pudo enviar el correo.";
                        throw new Exception($mensjError);
                    }
                } catch (Exception $e) {
                    header("Location: views/autenticacion.php?enviado=2&error=" . $mensjError);
                    exit;
                }
            }
            else {
                include 'views/autenticacion.php';
            }
            break;

            case 'mostrarFormularioNuevaContrasena':
                $token = $_GET['token'] ?? '';
                $email = $_GET['email'] ?? '';
                
                // Validar si el token es válido (debe estar en la base de datos y no haber expirado)
                if (!tokenEsValido($token)) {
                    // Redirigir con error si es inválido
                    header("Location: views/autenticacion.php?enviado=2&error=Solicitud Expirada");
                    exit;
                }

                include 'views/nueva_contrasena.php';
            break;

    case 'logout':
        session_destroy();
        header("Location: views/autenticacion.php?logout=ok");
        break;

    default:
        include 'views/autenticacion.php'; // Mostrar formulario por defecto
    break;
}

function enviarCorreoRestablecimiento($correo, $token) {
    $mail = new PHPMailer(true);
    
    try {
        $correo = htmlspecialchars($correo, ENT_QUOTES, 'UTF-8');
        
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jsdmngzc@gmail.com';
        $mail->Password = 'uhcj wqsm ntvy ixxr'; // Usa una app password de Gmail
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        // Remitente y destinatario
        $mail->setFrom('jsdmngzc@gmail.com', 'PROGRAN SOFTWARE GANADERO');
        $mail->addAddress($correo);

        // Formato HTML
        $mail->isHTML(true);
        $mail->Subject = 'Restablecimiento de cuenta - PROGAN';

        // RUTA ABSOLUTA para incrustar la imagen (IMPORTANTE)
        $mail->AddEmbeddedImage(__DIR__ . '/../../public/assets/images/email4.png', 'logo_cid');
        $logo_url = 'cid:logo_cid';

        $link = "http://localhost/LoginADSO/modules/auth/controller.php?accion=mostrarFormularioNuevaContrasena&token=$token&email=" . urlencode($correo);

        $mail->Body = "
                    <html>
                    <head>
                    <style>
                        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
                        .container {
                        background-color: #fff;
                        border-radius: 8px;
                        padding: 30px;
                        margin: auto;
                        max-width: 600px;
                        box-shadow: 0 0 10px rgba(0,0,0,0.1);
                        text-align: center;
                        }
                        .logo {
                        width: 400px;
                        margin-bottom: 20px;
                        }
                        .btn {
                        background-color: #28a745;
                        color: white !important;
                        padding: 14px 25px;
                        text-decoration: none;
                        border-radius: 5px;
                        font-weight: bold;
                        display: inline-block;
                        margin-top: 20px;
                        }
                        .footer {
                        font-size: 12px;
                        color: #777;
                        margin-top: 30px;
                        }
                    </style>
                    </head>
                    <body>
                    <div class='container'>
                        <img src='$logo_url' alt='PROGAN Logo' class='logo' />
                        <h2 style='color: #54046d;'>Solicitud de restablecimiento de contraseña</h2>
                        <p>Hola, hemos recibido una solicitud para restablecer la contraseña asociada a este correo.</p>
                        <p>Haz clic en el siguiente botón para continuar con el proceso:</p>
                        <a href='$link' class='btn'>Restablecer contraseña</a>
                        <p style='margin-top: 25px;'>Si tú no realizaste esta solicitud, puedes ignorar este mensaje.</p>
                        <div class='footer'>
                        <p>© " . date('Y') . " PROGAN - Todos los derechos reservados</p>
                        </div>
                    </div>
                    </body>
                    </html>
                    ";

        // Enviar correo
        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Error al enviar correo: " . $mail->ErrorInfo);
        return false;
    }
}


function tokenEsValido($token) {
    global $conexion;

    $stmt = $conexion->prepare("SELECT correo_usuario, token_expiracion FROM usuarios WHERE token_recuperacion = ?");
    $stmt->execute([$token]);
    $resultado = $stmt->fetch();

    if ($resultado) {
        $fechaExp = strtotime($resultado['token_expiracion']);
        $ahora = time();

        return $fechaExp > $ahora;
    }

    return false;
}

?>