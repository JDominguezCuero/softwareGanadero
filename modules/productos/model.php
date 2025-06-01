<?php
// products/model.php
require_once __DIR__ . '/../../config/config.php'; // Asegúrate de que config.php establezca la conexión PDO

/**
 * Obtiene todos los productos de la base de datos.
 * @param PDO $conexion La conexión a la base de datos (PDO).
 * @return array Un array de productos.
 */
function obtenerProductos($conexion) {
    $sql = "SELECT
                p.id_producto,
                p.nombre_producto,
                p.descripcion_producto,
                p.precio_unitario,
                p.cantidad,
                p.imagen_url,
                p.categoria_id,
                cp.nombre_categoria,
                p.estado_oferta,
                p.precio_anterior,
                p.id_usuario,                 
                u.nombre_usuario,             
                u.telefono_usuario,           
                u.correo_usuario              
            FROM
                ProductosGanaderos p
            LEFT JOIN
                CategoriasProducto cp ON p.categoria_id = cp.id_categoria
            LEFT JOIN
                Usuarios u ON p.id_usuario = u.id_usuario  -- ¡Añade este JOIN!
            ORDER BY
                p.nombre_producto ASC";
    $stmt = $conexion->query($sql);
    if (!$stmt) {
        throw new Exception("Error al obtener los productos: " . implode(":", $conexion->errorInfo()));
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosPorUsuario($conexion, $id_usuario) {
    // MODIFICAR ESTA CONSULTA
    $stmt = $conexion->prepare("
                SELECT
                    p.id_producto,
                    p.nombre_producto,
                    p.descripcion_producto,
                    p.precio_unitario,
                    p.cantidad,
                    p.imagen_url,
                    p.categoria_id,
                    cp.nombre_categoria,
                    p.estado_oferta,
                    p.precio_anterior
                FROM
                    ProductosGanaderos p
                LEFT JOIN
                    CategoriasProducto cp ON p.categoria_id = cp.id_categoria
                WHERE p.id_usuario = ?
                ORDER BY
                    p.nombre_producto ASC
            ");
    $stmt->execute([$id_usuario]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtiene un producto por su ID.
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id El ID del producto.
 * @return array|null El producto encontrado o null si no existe.
 */
function obtenerProductoPorId($conexion, $id) {
    $stmt = $conexion->prepare("
        SELECT
            p.id_producto,
            p.nombre_producto,
            p.descripcion_producto,
            p.precio_unitario,
            p.cantidad,
            p.imagen_url,
            p.categoria_id,
            cp.nombre_categoria, -- Agregamos el nombre de la categoría aquí también
            p.estado_oferta,
            p.precio_anterior
        FROM
            ProductosGanaderos p
        LEFT JOIN
            CategoriasProducto cp ON p.categoria_id = cp.id_categoria
        WHERE p.id_producto = :id
    ");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Crea un nuevo producto en la base de datos.
 * @param PDO $conexion La conexión a la base de datos.
 * @param string $nombre Nombre del producto.
 * @param string $descripcion Descripción del producto.
 * @param float $precio Precio del producto.
 * @param int $stock Cantidad en stock.
 * @param string $imagen_url URL de la imagen del producto.
 * @param int $categoria_id ID de la categoría del producto.
 * @param bool $estado_oferta Si el producto está en oferta (1 o 0).
 * @param float|null $precio_anterior Precio anterior (solo si hay oferta).
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function crearProducto($conexion, $nombre, $descripcion, $precio, $stock, $imagen_url, $categoria_id, $estado_oferta, $precio_anterior, $id_usuario) {
    $stmt = $conexion->prepare("
        INSERT INTO ProductosGanaderos (nombre_producto, descripcion_producto, precio_unitario, cantidad, imagen_url, categoria_id, estado_oferta, precio_anterior, id_usuario)
        VALUES (:nombre, :descripcion, :precio, :stock, :imagen_url, :categoria_id, :estado_oferta, :precio_anterior, :id_usuario)
    ");
    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':descripcion', $descripcion);
    $stmt->bindValue(':precio', $precio);
    $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
    $stmt->bindValue(':imagen_url', $imagen_url); // Nuevo campo
    $stmt->bindValue(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt->bindValue(':estado_oferta', $estado_oferta, PDO::PARAM_INT);
    $stmt->bindValue(':precio_anterior', $precio_anterior);
    $stmt->bindValue(':id_usuario', $id_usuario);
    return $stmt->execute();
}

/**
 * Actualiza un producto existente en la base de datos.
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id ID del producto a actualizar.
 * @param string $nombre Nombre del producto.
 * @param string $descripcion Descripción del producto.
 * @param float $precio Precio del producto.
 * @param int $stock Cantidad en stock.
 * @param string $imagen_url URL de la imagen del producto.
 * @param int $categoria_id ID de la categoría del producto.
 * @param bool $estado_oferta Si el producto está en oferta (1 o 0).
 * @param float|null $precio_anterior Precio anterior (solo si hay oferta).
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function actualizarProducto($conexion, $id, $nombre, $descripcion, $precio, $stock, $imagen_url, $categoria_id, $estado_oferta, $precio_anterior) {
    $stmt = $conexion->prepare("
        UPDATE ProductosGanaderos
        SET nombre_producto = :nombre, descripcion_producto = :descripcion, precio_unitario = :precio, 
            cantidad = :stock, imagen_url = :imagen_url, categoria_id = :categoria_id,
            estado_oferta = :estado_oferta, precio_anterior = :precio_anterior
        WHERE id_producto = :id
    ");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':descripcion', $descripcion);
    $stmt->bindValue(':precio', $precio);
    $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
    $stmt->bindValue(':imagen_url', $imagen_url); // Nuevo campo
    $stmt->bindValue(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt->bindValue(':estado_oferta', $estado_oferta, PDO::PARAM_INT);
    $stmt->bindValue(':precio_anterior', $precio_anterior);
    return $stmt->execute();
}

/**
 * Elimina un producto de la base de datos.
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id El ID del producto a eliminar.
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function eliminarProducto($conexion, $id) {
    // Si hay tablas asociadas (ej. VentasProductos), elimínalas primero
    $stmt1 = $conexion->prepare("DELETE FROM VentasProductos WHERE id_producto = :id");
    $stmt1->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt1->execute();

    $stmt = $conexion->prepare("DELETE FROM ProductosGanaderos WHERE id_producto = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}

/**
 * Obtiene todas las categorías de productos de la base de datos.
 * @param PDO $conexion La conexión a la base de datos (PDO).
 * @return array Un array de categorías.
 */
function obtenerCategorias($conexion) {
    try {
        $stmt = $conexion->prepare("SELECT id_categoria, nombre_categoria FROM CategoriasProducto ORDER BY nombre_categoria ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // En un entorno de producción, podrías loggear el error sin mostrarlo al usuario.
        // error_log("Error al obtener categorías: " . $e->getMessage());
        return []; // Retorna un array vacío en caso de error
    }
}

function obtenerProductosPorCategoria($conexion, $categoria_id) {
    $sql = "SELECT
                p.id_producto,
                p.nombre_producto,
                p.descripcion_producto,
                p.precio_unitario,
                p.cantidad,
                p.imagen_url,
                p.categoria_id,
                cp.nombre_categoria,
                p.estado_oferta,
                p.precio_anterior,
                p.id_usuario,
                u.nombre_usuario,
                -- u.telefono_usuario,
                u.correo_usuario
            FROM
                ProductosGanaderos p
            LEFT JOIN
                CategoriasProducto cp ON p.categoria_id = cp.id_categoria
            LEFT JOIN
                Usuarios u ON p.id_usuario = u.id_usuario
            WHERE
                p.categoria_id = :categoria_id
            ORDER BY
                p.nombre_producto ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':categoria_id', $categoria_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosEnOferta($conexion) {
   $sql = "SELECT
                p.id_producto,
                p.nombre_producto,
                p.descripcion_producto,
                p.precio_unitario,
                p.cantidad,
                p.imagen_url,
                p.categoria_id,
                cp.nombre_categoria,
                p.estado_oferta,
                p.precio_anterior,
                p.id_usuario,               
                u.nombre_usuario,            
                u.telefono_usuario,           
                u.correo_usuario              
            FROM
                ProductosGanaderos p
            LEFT JOIN
                CategoriasProducto cp ON p.categoria_id = cp.id_categoria
            LEFT JOIN
                Usuarios u ON p.id_usuario = u.id_usuario 
            WHERE
                p.estado_oferta = 1
            ORDER BY
                cp.nombre_categoria ASC, p.nombre_producto ASC"; 
    $stmt = $conexion->query($sql);
    if (!$stmt) {
        error_log("Error al obtener productos en oferta: " . implode(":", $conexion->errorInfo()));
        throw new Exception("Error al obtener productos en oferta: " . $conexion->errorInfo()[2]);
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProductosPopulares($conexion, $limit = 8) {
    $sql = "SELECT
                p.id_producto,
                p.nombre_producto,
                p.descripcion_producto,
                p.precio_unitario,
                p.cantidad,
                p.imagen_url,
                p.categoria_id,
                cp.nombre_categoria,
                p.estado_oferta,
                p.precio_anterior,
                p.id_usuario,
                u.nombre_usuario,
                -- u.telefono_usuario,
                u.correo_usuario
            FROM
                ProductosGanaderos p
            LEFT JOIN
                CategoriasProducto cp ON p.categoria_id = cp.id_categoria
            LEFT JOIN
                Usuarios u ON p.id_usuario = u.id_usuario
            ORDER BY
                p.id_producto DESC -- O una columna como 'fecha_creacion' si la tienes
            LIMIT :limit";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtiene las categorías más populares (ej. por número de productos vendidos).
 * Incluye también la categoría de ofertas si existe.
 * @param PDO $conexion Objeto de conexión a la base de datos.
 * @param int $limit Número de categorías populares a retornar (sin contar la de oferta).
 * @return array Un array de IDs de categorías populares.
 */
function obtenerCategoriasMasPopulares(PDO $conexion, int $limit = 3): array {
    $populares_ids = [];

    try {
        // Suponiendo que tienes una tabla de pedidos/ventas que relaciona productos
        // con categorías. Esta consulta es un EJEMPLO, adáptala a tu esquema de DB.
        $sql = "
            SELECT
                cp.id_categoria,
                cp.nombre_categoria,
                COUNT(dp.id_detalle_pedido) AS total_ventas
            FROM
                productosganaderos p
            JOIN
                CategoriasProducto cp ON p.categoria_id = cp.id_categoria
            LEFT JOIN
                DetallesPedido dp ON p.id_producto = dp.id_producto -- Asumiendo DetallesPedido
            GROUP BY
                cp.id_categoria, cp.nombre_categoria
            ORDER BY
                total_ventas DESC, cp.nombre_categoria ASC -- Ordena por ventas, luego alfabéticamente
            LIMIT :limit;
        ";
        $stmt = $conexion->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $populares_ids[] = [
                'id' => $row['id_categoria'],
                'nombre' => $row['nombre_categoria']
            ];
        }

    } catch (PDOException $e) {
        error_log("Error al obtener categorías más populares: " . $e->getMessage());
        return [];
    }

    return $populares_ids;
}

/**
 * Obtiene productos populares basados en una lista de IDs de categoría.
 * @param PDO $conexion Objeto de conexión a la base de datos.
 * @param array $category_ids Un array de IDs de categorías.
 * @param int $limit_per_category Límite de productos por cada categoría.
 * @return array Un array asociativo donde la clave es el nombre de la categoría y el valor es un array de productos.
 */
function obtenerProductosPopularesPorCategorias(PDO $conexion, array $category_info, int $limit_per_category = 5): array {
    $productos_populares_por_categoria = [];

    if (empty($category_info)) {
        return [];
    }

    foreach ($category_info as $cat) {
        $cat_id = $cat['id'];
        $cat_nombre = $cat['nombre'];

        try {
            // Selecciona productos de la categoría actual, ordenados por alguna métrica de popularidad
            // (ej. fecha de publicación, o si tuvieras ventas_acumuladas, etc.)
            // Incluimos datos del usuario para el renderProductItems
            $sql = "
                SELECT
                    p.id_producto,
                    p.nombre_producto,
                    p.descripcion_producto,
                    p.precio_unitario,
                    p.precio_anterior,
                    p.imagen_url,
                    p.estado_oferta,
                    u.nombre_usuario,
                    u.telefono_usuario,
                    u.correo_usuario,
                    cp.nombre_categoria
                FROM
                    productosganaderos p
                JOIN
                    CategoriasProducto cp ON p.categoria_id = cp.id_categoria
                LEFT JOIN
                    Usuarios u ON p.id_usuario = u.id_usuario
                WHERE
                    p.categoria_id = :id_categoria
                ORDER BY
                    p.fecha_publicacion DESC
                LIMIT :limit_per_category;
            ";
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(':id_categoria', $cat_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit_per_category', $limit_per_category, PDO::PARAM_INT);
            $stmt->execute();
            $productos_populares_por_categoria[$cat_nombre] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener productos para categoría {$cat_nombre} (ID: {$cat_id}): " . $e->getMessage());
            $productos_populares_por_categoria[$cat_nombre] = []; // Asegura que la categoría exista pero vacía
        }
    }

    return $productos_populares_por_categoria;
}
?>