<?php
// inventario/controller.php
session_start();
require_once(__DIR__ . '/model.php'); // Asegúrate de que el modelo esté cargado
require_once __DIR__ . '/../../config/config.php'; // Asegúrate de que config.php establezca la conexión PDO

// Verifica si hay sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php?login=error&reason=nologin");
    exit;
}

$accion = $_GET['accion'] ?? 'listar';
$mensjError = ""; // Variable para mensajes de error del controlador

try {
    switch ($accion) {
        case 'listar':
            // La variable $conexion viene de config/config.php
            $inventario = obtenerInventario($conexion);
            $msg = $_GET['msg'] ?? null; // Mensajes de éxito pasados por URL

            include(__DIR__ . '/views/inventario.php');
            break;
        
        case 'consultar':
            // Esta acción parece redundante si 'listar' ya incluye la tabla.
            // Si necesitas ver solo un item individual, la vista tendría que cambiar.
            // Por ahora, solo se obtiene el item y se incluye la vista principal.
            $id = $_GET['id'] ?? null;

            if ($id) {
                $item = obtenerItemPorId($conexion, $id);
                if (!$item) {
                    $mensjError = "Alimento no encontrado.";
                }
            } else {
                $mensjError = "ID de alimento no proporcionado.";
            }
            // Aunque se consulta, la vista sigue siendo la misma que lista todo.
            // Podrías redirigir o mostrar un modal con la información del item.
            // Para simplificar, si hay error, se redirige.
            if (!empty($mensjError)) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                exit;
            }
            include(__DIR__ . '/views/inventario.php'); // Si quieres mostrar el item consultado en la misma vista, asegúrate de que la vista lo maneje.
            break;    

        case 'agregar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = $_POST['nombre'] ?? ''; // Usa 'nombre' del formulario
                $cantidad = $_POST['cantidad'] ?? 0;
                $unidad_medida = $_POST['unidad_medida'] ?? ''; // Nuevo campo
                $fecha_ingreso = $_POST['fecha_ingreso'] ?? ''; // Nuevo campo

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
                // Si se accede directamente por GET, puedes redirigir o mostrar un formulario específico
                // Para este CRUD, normalmente el formulario de agregar está en un modal de la vista listar.
                $mensjError = "Método no permitido para esta acción.";
            }
            // Si hay un error, redirige con el mensaje
            if (!empty($mensjError)) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                exit;
            }
            break;

        case 'editar':
            $id = $_POST['id_alimento'] ?? $_GET['id_alimento'] ?? null; // Usa 'id_alimento'

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $nombre = $_POST['nombre'] ?? ''; // Usa 'nombre'
                $cantidad = $_POST['cantidad'] ?? 0;
                $unidad_medida = $_POST['unidad_medida'] ?? ''; // Nuevo campo
                $fecha_ingreso = $_POST['fecha_ingreso'] ?? ''; // Nuevo campo

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
            } else if ($id) { // Si es un GET para cargar el formulario de edición
                $item = obtenerItemPorId($conexion, $id);
                if (!$item) {
                    $mensjError = "Alimento no encontrado para editar.";
                }
                // Si el item no se encuentra, $mensjError se llenará y la redirección ocurrirá abajo.
                // Si se encuentra, la vista lo usará para pre-llenar el modal.
                include __DIR__ . '/views/inventario.php'; // La vista de listar también contendrá el modal de edición
            } else {
                $mensjError = "ID de alimento no proporcionado para editar.";
            }

            // Si hay un error, redirige con el mensaje
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

            // Si hay un error, redirige con el mensaje
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
    error_log("Error en Controller de Inventario: " . $e->getMessage()); // Guarda el error técnico en el log

    // Mensaje más amigable para el usuario
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