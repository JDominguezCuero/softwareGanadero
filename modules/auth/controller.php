<?php
session_start();
require_once 'model.php';
require_once '../../public/assets/validaciones.php';
require_once __DIR__ . '/../../config/config.php';

require '../../public/assets/lib/PHPMailer/src/PHPMailer.php';
require '../../public/assets/lib/PHPMailer/src/SMTP.php';
require '../../public/assets/lib/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$accion = $_GET['accion'] ?? '';

switch ($accion) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try{
                 $correo = $_POST['correoElectronicoLogin'] ?? '';
                 $contrasena = $_POST['contrasenaLogin'] ?? '';
                 $mensjError = "";

                 $usuario = obtenerUsuarioPorCorreo($correo);

                if ($usuario && password_verify($contrasena, $usuario['contrasena_usuario'])) {
                    // Inicio de sesión exitoso
                    $_SESSION['usuario'] = $usuario['nombre_usuario'];
                    $_SESSION['nombre'] = $usuario['nombreCompleto'];
                    $_SESSION['id_usuario'] = $usuario['id_usuario'];
                    $_SESSION['correo_usuario'] = $usuario['correo_usuario'];
                    $_SESSION['rol'] = $usuario['id_rol'];

                    header("Location: ../../public/index_controller.php");
                    exit;
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
                if (registrarUsuario($nombre, $correo, $usuario, $contrasena)) {
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