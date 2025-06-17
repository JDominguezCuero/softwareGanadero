<?php
// products/model.php
require_once __DIR__ . '/../../config/config.php';

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
 * Obtiene los detalles completos de un producto por su ID, incluyendo la información del vendedor y la categoría.
 * @param PDO $conexion Objeto de conexión PDO.
 * @param int $id El ID del producto a buscar.
 * @return array|null Un array asociativo con los detalles del producto y del vendedor, o null si no se encuentra.
 */
function obtenerProductoPorId(PDO $conexion, int $id): ?array {
    $stmt = $conexion->prepare("
        SELECT
            p.id_producto,
            p.nombre_producto,
            p.descripcion_producto,   -- Nombre de tu columna de descripción completa
            p.precio_unitario,        -- Nombre de tu columna de precio
            p.cantidad AS stock,      -- Nombre de tu columna de cantidad, aliased as 'stock' for JS
            p.imagen_url,
            p.id_usuario,             -- ¡El ID del usuario/vendedor sigue siendo importante aquí!
            cp.nombre_categoria,
            p.estado_oferta,
            p.precio_anterior,
            p.fecha_publicacion,      -- Asegúrate de incluir la fecha de publicación si la necesitas
            u.nombre_usuario,         -- Datos del Vendedor
            u.telefono_usuario,
            u.correo_usuario,
            u.direccion_usuario       -- Si tienes esta columna en tu tabla Usuarios
        FROM
            ProductosGanaderos p
        LEFT JOIN
            CategoriasProducto cp ON p.categoria_id = cp.id_categoria
        LEFT JOIN
            Usuarios u ON p.id_usuario = u.id_usuario -- ¡Aquí está el JOIN a la tabla de Usuarios!
        WHERE
            p.id_producto = :id_producto
    ");
    $stmt->bindValue(':id_producto', $id, PDO::PARAM_INT);
    $stmt->execute();
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si el producto se encuentra, añade el campo 'es_nuevo' si lo necesitas para otras lógicas
    if ($producto && isset($producto['fecha_publicacion'])) {
        $fecha_publicacion = new DateTime($producto['fecha_publicacion']);
        $fecha_actual = new DateTime();
        $intervalo = $fecha_publicacion->diff($fecha_actual);
        $producto['es_nuevo'] = ($intervalo->days <= 30); // 30 días como umbral
    } else if ($producto) {
        $producto['es_nuevo'] = false; // Si no hay fecha, no es nuevo
    }

    return $producto;
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
    $stmt1 = $conexion->prepare("DELETE FROM VentasProductos WHERE id_producto = :id");
    $stmt1->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt1->execute();

    $stmt = $conexion->prepare("DELETE FROM ProductosGanaderos WHERE id_producto = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
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
            $productos_populares_por_categoria[$cat_nombre] = [];
        }
    }

    return $productos_populares_por_categoria;
}

/**
 * Obtiene todas las categorías de productos.
 * @param PDO $conexion Objeto de conexión a la base de datos.
 * @return array Lista de categorías.
 */
function obtenerCategorias(PDO $conexion): array {
    try {
        $stmt = $conexion->query("SELECT id_categoria, nombre_categoria FROM CategoriasProducto ORDER BY nombre_categoria ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener categorías: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene todos los productos con opciones de filtrado y ordenación.
 * @param PDO $conexion La conexión a la base de datos.
 * @param int|null $id_categoria ID de la categoría a filtrar.
 * @param float|null $precio_min Precio mínimo.
 * @param float|null $precio_max Precio máximo.
 * @param string|null $busqueda Texto para buscar en nombre o descripción.
 * @param string $ordenar_por Criterio de ordenación (fecha_reciente, precio_asc, precio_desc, nombre_asc).
 * @return array Lista de productos.
 */
function obtenerTodosLosProductosConFiltros(
    PDO $conexion,
    ?int $id_categoria = null,
    // ?int $estado_oferta = 0,
    ?float $precio_min = null,
    ?float $precio_max = null,
    ?string $busqueda = null,
    string $ordenar_por = 'fecha_reciente'
): array {
    $sql = "
        SELECT
            p.id_producto,
            p.nombre_producto,
            p.descripcion_producto,
            p.precio_unitario,
            p.precio_anterior,
            p.imagen_url,
            p.estado_oferta,
            p.fecha_publicacion,
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
        WHERE 1=1 -- Cláusula siempre verdadera para facilitar la adición de condiciones
    ";

    $params = [];

    // --- Aplicar Filtros ---
    if ($id_categoria !== null && $id_categoria > 0) {
        $sql .= " AND p.categoria_id = :id_categoria";
        $params[':id_categoria'] = $id_categoria;
    }

    // if ($estado_oferta !== null && $estado_oferta > 0) {
    //     $sql .= " AND p.estado_oferta = :estado_oferta";
    //     $params[':estado_oferta'] = (int)$estado_oferta;
    // }

    if ($precio_min !== null && $precio_min >= 0) {
        $sql .= " AND p.precio_unitario >= :precio_min";
        $params[':precio_min'] = $precio_min;
    }

    if ($precio_max !== null && $precio_max > 0) {
        $sql .= " AND p.precio_unitario <= :precio_max";
        $params[':precio_max'] = $precio_max;
    }

    if ($busqueda !== null && $busqueda !== '') {
        $sql .= " AND (p.nombre_producto LIKE :busqueda OR p.descripcion_producto LIKE :busqueda_desc)";
        $params[':busqueda'] = '%' . $busqueda . '%';
        $params[':busqueda_desc'] = '%' . $busqueda . '%'; // Para buscar también en descripción
    }

    // --- Aplicar Ordenación ---
    switch ($ordenar_por) {
        case 'precio_asc':
            $sql .= " ORDER BY p.precio_unitario ASC";
            break;
        case 'precio_desc':
            $sql .= " ORDER BY p.precio_unitario DESC";
            break;
        case 'nombre_asc':
            $sql .= " ORDER BY p.nombre_producto ASC";
            break;
        case 'fecha_reciente':
        default:
            $sql .= " ORDER BY p.fecha_publicacion DESC";
            break;
    }

    try {
        $stmt = $conexion->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lógica para determinar si es "nuevo" (misma que en renderProductItems)
        foreach ($productos as &$producto) {
            if (isset($producto['fecha_publicacion'])) {
                $fecha_publicacion = new DateTime($producto['fecha_publicacion']);
                $fecha_actual = new DateTime();
                $intervalo = $fecha_publicacion->diff($fecha_actual);
                $producto['es_nuevo'] = ($intervalo->days <= 30); // 30 días como umbral de "nuevo"
            } else {
                $producto['es_nuevo'] = false;
            }
        }

        return $productos;

    } catch (PDOException $e) {
        error_log("Error en obtenerTodosLosProductosConFiltros: " . $e->getMessage());
        return [];
    }
}
?>