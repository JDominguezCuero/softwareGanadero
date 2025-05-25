<?php
session_start();
require_once(__DIR__ . '/model.php');
require_once __DIR__ . '/../../config/config.php';

global $conexion;

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php?login=error&reason=nologin");
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

switch ($action) {
    case 'configurar':
        $nombre = $_POST['nombre'] ?? '';
        $tipo_animal = $_POST['tipo_animal'] ?? '';
        $cantidad = intval($_POST['cantidad'] ?? 0);
        $edad = intval($_POST['edad'] ?? 0);

        if (!empty(trim($nombre)) && $tipo_animal && $cantidad > 0 && $edad > 0) {
            $id_tipo = AnimalModel::obtenerIdTipoPorNombre($conexion, $tipo_animal);

            if (!$id_tipo) {
                die("Tipo de animal no válido.");
            }

            $_SESSION['nombre_animal'] = $nombre;
            $_SESSION['tipo_animal'] = $tipo_animal;
            $_SESSION['cantidad_animales'] = $cantidad;
            $_SESSION['edad_animales'] = $edad;
            $id_usuario = $_SESSION['id_usuario'];

            $animales_existentes = AnimalModel::obtenerAnimalesPorUsuario($conexion, $id_usuario);
            $total_existentes = count($animales_existentes);

            for ($i = 0; $i < $cantidad; $i++) {
                $numero = $total_existentes + $i + 1;
                $nombreAnimal = ucfirst($nombre) . ' - ' . $numero;
                AnimalModel::crearAnimal($conexion, $nombreAnimal, $id_tipo, $edad, $id_usuario);
            }

            header("Location: controller.php?action=mostrar");
            exit;
        } else {
            $_SESSION['error'] = 'Tipo de animal no válido.';
            header("Location: views/configuracion.php");
            exit;
        }
        break;

    case 'alimentar':
    case 'medicar':
    case 'bañar':
        $id_animal = intval($_POST['id_animal'] ?? 0);
        if ($id_animal > 0) {
            $campo = [
                'alimentar' => 'alimentacion',
                'medicar'   => 'salud',
                'bañar'   => 'higiene'
            ][$action];
            $incremento = $action === 'medicar' ? 15 : 20;

            AnimalModel::actualizarEstado($conexion, $id_animal, $campo, $incremento);
            $animal_actualizado = AnimalModel::obtenerPorId($conexion, $id_animal);

            if ($isAjax) {
                echo json_encode([
                    'success' => true,
                    'animal' => $animal_actualizado
                ]);
                exit;
            } else {
                header("Location: controller.php?action=mostrar");
                exit;
            }
        }
        if ($isAjax) {
            echo json_encode(['success' => false, 'error' => 'ID inválido']);
            exit;
        }
        break;

    case 'editar':
        $respuesta = [
            'success' => false,
            'mensaje' => '',
        ];

        try {
            $id_animal = intval($_POST['id_animal'] ?? $_GET['id_animal'] ?? 0);
            $nombre = $_POST['nombre'] ?? $_GET['nombre'] ?? '';

            if ($id_animal > 0 && !empty($nombre)) {
                $resultado = AnimalModel::editarAnimal($conexion, $id_animal, $nombre);
                if ($resultado) {
                    $respuesta['success'] = true;
                    $respuesta['mensaje'] = 'Nombre del animal actualizado correctamente.';
                } else {
                    $respuesta['mensaje'] = 'Error al actualizar el nombre en la base de datos.';
                }
            } else {
                $respuesta['mensaje'] = 'Datos inválidos para editar.';
            }
        } catch (Exception $e) {
            error_log("Error al editar el animal: " . $e->getMessage());
            $respuesta['mensaje'] = 'Ocurrió un error al procesar la solicitud.';
        }

        // Si es una petición AJAX, enviamos JSON
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode($respuesta);
            exit;
        } else {
            // Aquí podrías pasar $respuesta a una vista o redireccionar
            // Por ahora, redirigimos y puedes mostrar mensajes en sesiones si lo deseas
            $_SESSION['respuesta'] = $respuesta;
            header("Location: controller.php?action=mostrar");
            exit;
        }
        break;

    case 'eliminar':
        $id_animal = intval($_POST['id_animal'] ?? $_GET['id_animal'] ?? 0);
        if ($id_animal > 0) {
            $resultado = AnimalModel::eliminarAnimal($conexion, $id_animal);
            if ($isAjax) {
                echo json_encode(['success' => $resultado]);
                exit;
            } else {
                header("Location: controller.php?action=mostrar");
                exit;
            }
        } else {
            if ($isAjax) {
                echo json_encode(['success' => false, 'error' => 'ID inválido']);
                exit;
            }
        }
        break;

    case 'mostrar':
            $id_usuario = $_SESSION['id_usuario'];
            $animales_originales = AnimalModel::obtenerAnimalesPorUsuario($conexion, $id_usuario); // Obtener los animales con tipo_nombre inicial

            $factor_tiempo = $_SESSION['factor_tiempo'] ?? 1;
            $animales_finales = []; // Renombramos para mayor claridad

            foreach ($animales_originales as $animal) {
                // Intentar aplicar decremento
                $animal_despues_decremento = AnimalModel::aplicarDecrementoPorTiempo($conexion, $animal['id_animal'], $factor_tiempo);
                
                if ($animal_despues_decremento === false || !is_array($animal_despues_decremento)) {
                    error_log("DEBUG: Fallo en aplicarDecrementoPorTiempo para ID " . ($animal['id_animal'] ?? 'desconocido') . ". Usando datos originales.");
                    $animales_finales[] = $animal; // Usa el animal original si hay un problema
                } else {
                    // Si la función devuelve un animal (actualizado o no), lo usamos.
                    $animales_finales[] = $animal_despues_decremento;
                }
            }
            
            $animales = $animales_finales;

            include 'views/simulador.php';
        break;

     case 'set_time_factor':
            $factor = intval($_POST['factor'] ?? 1);
            $_SESSION['factor_tiempo'] = max(1, $factor);

            $respuesta = ['success' => true, 'factor' => $_SESSION['factor_tiempo']];

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode($respuesta);
                exit;
            } else {
                // Para peticiones no AJAX, redirige a la vista principal
                $_SESSION['respuesta'] = $respuesta;
                header("Location: controller.php?action=mostrar");
                exit;
            }
        break;

    default:
        header("Location: configuracion.php");
        exit;
}