    <?php
    require_once __DIR__ . '/../../config/config.php';

    function obtenerInventario($conexion) {
        $sql = "SELECT * FROM ProductosGanaderos";
        $stmt = $conexion->query($sql); // Ejecuta la consulta

        // Obtiene todos los resultados como un array asociativo
        $inventario = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $inventario;
    }

    function obtenerItemPorId($conexion, $id) {
        $stmt = $conexion->prepare("SELECT * FROM ProductosGanaderos WHERE id_producto = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function crearItem($conexion, $nombre, $cantidad, $descripcion, $precio) {
        $stmt = $conexion->prepare("
            INSERT INTO ProductosGanaderos (nombre_producto, cantidad, descripcion_producto, precio_unitario)
            VALUES (:nombre, :cantidad, :descripcion, :precio)
        ");

        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':precio', $precio);
        return $stmt->execute();
    }

    function actualizarItem($conexion, $id, $nombre, $cantidad, $descripcion, $precio) {
        $stmt = $conexion->prepare("
            UPDATE ProductosGanaderos
            SET nombre_producto = :nombre, cantidad = :cantidad, descripcion_producto = :descripcion ,precio_unitario = :precio
            WHERE id_producto = :id
        ");

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
        $stmt->bindValue(':descripcion', $descripcion);
        $stmt->bindValue(':precio', $precio);    
        return $stmt->execute();
    }

    function eliminarItem($conexion, $id) {
        // Eliminar ventas asociadas
        $stmt1 = $conexion->prepare("DELETE FROM VentasProductos WHERE id_producto = :id");
        $stmt1->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt1->execute();
        
        $stmt = $conexion->prepare("DELETE FROM ProductosGanaderos WHERE id_producto = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
