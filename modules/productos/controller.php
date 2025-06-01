<?php
// products/controller.php
session_start();
require_once(__DIR__ . '/model.php'); // Asegúrate de que el modelo esté cargado
require_once __DIR__ . '/../../config/config.php'; // Asegúrate de que config.php establezca la conexión PDO

// Directorio donde se guardarán las imágenes
// Asegúrate de que este directorio exista y tenga permisos de escritura.
const UPLOAD_DIR = __DIR__ . '/../../public/assets/images/productos/';

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php?login=error&reason=nologin");
    exit;
}

$accion = $_GET['accion'] ?? 'listar';
$mensjError = "";

try {
    switch ($accion) {
        case 'listar':
            global $conexion;
            $id_usuario = $_SESSION['id_usuario'];
            $productos = obtenerProductosPorUsuario($conexion, $id_usuario); // Asume que tienes esta función en tu model.php
            $msg = $_GET['msg'] ?? null;

             $categorias = obtenerCategorias($conexion);

            include(__DIR__ . '/views/productos.php'); // La vista principal de productos
            break;

        case 'agregar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $precio = $_POST['precio'] ?? 0;
                $stock = $_POST['stock'] ?? 0;
                $categoria_id = $_POST['categoria_id'] ?? null;
                $nombre_categoria = $_POST['nombre_categoria'] ?? null;
                $estado_oferta = isset($_POST['estado_oferta']) ? 1 : 0;
                $precio_anterior = $estado_oferta ? ($_POST['precio_anterior'] ?? null) : null;
                $imagen_url = ''; // Inicializar imagen_url
                $id_usuario = $_SESSION['id_usuario'];

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
                            $imagen_url = BASE_URL . '/public/assets/images/productos/' . $newFileName;
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

                // Asegúrate de que la función crearProducto en tu modelo acepte la nueva imagen_url
                if (!empty($nombre) && $precio > 0 && $stock >= 0 && !empty($categoria_id)) {
                    // crearProducto($conexion, $nombre, $precio, $stock, $descripcion, $imagen_url, $categoria_id, $estado_oferta, $precio_anterior);
                    // Actualiza tu modelo para que reciba la imagen_url
                    // Ejemplo de llamada (ajusta según tu model.php)
                    $resultado = crearProducto($conexion, $nombre, $descripcion, $precio, $stock, $imagen_url, $categoria_id, $estado_oferta, $precio_anterior, $id_usuario);
                    if ($resultado) {
                        $mensaje = "Producto agregado correctamente.";
                        header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                        exit;
                    } else {
                        $mensjError = "Error al agregar el producto en la base de datos.";
                    }
                } else {
                    $mensjError = "Faltan datos obligatorios o son inválidos para agregar el producto.";
                }
            } else {
                $mensjError = "Método no permitido.";
            }
            if (!empty($mensjError)) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                exit;
            }
            break;

        case 'editar':
            $id = $_POST['id_producto'] ?? $_GET['id_producto'] ?? null;

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
                $nombre = $_POST['nombre'] ?? '';
                $descripcion = $_POST['descripcion'] ?? '';
                $precio = $_POST['precio'] ?? 0;
                $stock = $_POST['stock'] ?? 0;
                $categoria_id = $_POST['categoria_id'] ?? null;
                $nombre_categoria = $_POST['nombre_categoria'] ?? null;
                $estado_oferta = isset($_POST['estado_oferta']) ? 1 : 0;
                $precio_anterior = $estado_oferta ? ($_POST['precio_anterior'] ?? null) : null;
                $imagen_url = $_POST['imagen_url_actual'] ?? ''; // Valor por defecto: la URL actual

                // Lógica para actualizar imagen (solo si se sube una nueva)
                if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['imagen']['tmp_name'];
                    $fileName = $_FILES['imagen']['name'];
                    $fileSize = $_FILES['imagen']['size'];
                    $fileType = $_FILES['imagen']['type'];
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));

                    $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
                    if (in_array($fileExtension, $allowedfileExtensions)) {
                        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                        $destPath = UPLOAD_DIR . $newFileName;

                        if (move_uploaded_file($fileTmpPath, $destPath)) {
                            // Si se subió con éxito, actualiza la imagen_url
                            $imagen_url = BASE_URL . '/public/assets/images/productos/' . $newFileName;
                        } else {
                            $mensjError = "Error al mover el nuevo archivo subido.";
                        }
                    } else {
                        $mensjError = "Tipo de archivo de imagen no permitido para la actualización.";
                    }
                }

                if (!empty($mensjError)) {
                    header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                    exit;
                }

                if (!empty($nombre) && $precio > 0 && $stock >= 0 && !empty($categoria_id)) {
                    // Actualiza tu modelo para que reciba la imagen_url
                    $resultado = actualizarProducto($conexion, $id, $nombre, $descripcion, $precio, $stock, $imagen_url, $categoria_id, $estado_oferta, $precio_anterior);
                    if ($resultado) {
                        $mensaje = "Producto actualizado correctamente.";
                        header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                        exit;
                    } else {
                        $mensjError = "Error al actualizar el producto en la base de datos.";
                    }
                } else {
                    $mensjError = "Faltan datos obligatorios o son inválidos para actualizar el producto.";
                }
            } else if ($id) {
                // Si es un GET para cargar el formulario de edición
                $item = obtenerProductoPorId($conexion, $id); // Asume que tienes esta función en tu model.php
                if (!$item) {
                    $mensjError = "Producto no encontrado para editar.";
                }
                $categorias = obtenerCategorias($conexion);
                include __DIR__ . '/views/productos.php'; // Se incluye la vista principal que contendrá el modal
            } else {
                $mensjError = "ID de producto no proporcionado para editar.";
            }

            if (!empty($mensjError)) {
                header("Location: controller.php?accion=listar&inv=1&error=" . urlencode($mensjError));
                exit;
            }
            break;

        case 'eliminar':
            global $conexion;
            if (isset($_GET['id'])) {
                $id = intval($_GET['id']);
                // Opcional: Obtener la imagen_url antes de eliminar el producto para luego borrar el archivo
                // $producto_a_eliminar = obtenerProductoPorId($conexion, $id);
                $resultado = eliminarProducto($conexion, $id); // Asume que tienes esta función
                if ($resultado) {
                    // Opcional: unlink(ruta_del_archivo_imagen_antiguo);
                    $mensaje = "Producto eliminado correctamente.";
                    header("Location: controller.php?accion=listar&msg=" . urlencode($mensaje));
                    exit;
                } else {
                    $mensjError = "Error al eliminar el producto.";
                }
            } else {
                $mensjError = "ID de producto no proporcionado para eliminar.";
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
    error_log("Error en Products Controller: " . $e->getMessage());

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