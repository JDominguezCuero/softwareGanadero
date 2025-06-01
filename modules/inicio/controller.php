<?php
session_start();
require_once(__DIR__ . '/model.php');
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index_controller.php?login=error&reason=nologin");
    exit;
}

$accion = $_GET['accion'] ?? 'listarAnimales';
$mensjError = "";

try {
    switch ($accion) {
        case 'listarAnimales':
            global $conexion;
            $animales = listarAnimales($conexion);
            $_animal['cantidadAnimales'] = count($animales);

            include(__DIR__ . '/views/bienvenido.php');
        break;


        default:
            header("Location: controller.php?accion=listarAnimales");
            exit;
        break;
            
    }

} catch (Exception $e) {
    error_log($e->getMessage());
    $errorMsg = $e->getMessage();
    
    if (str_contains($errorMsg, 'Unknown column')) {
        $mensajeUsuario = "Hubo un problema con la base de datos. Verifica que las columnas existan correctamente.";
    } else {
        $mensajeUsuario = "Ocurrió un error inesperado. Contacte al administrador.";
    }

    header("Location: controller.php?accion=listarAnimales&inv=1&error=" . urlencode($mensajeUsuario));
    exit;
}





?>