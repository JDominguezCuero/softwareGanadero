<?php
// inventario/model.php
require_once __DIR__ . '/../../config/config.php'; // Asegúrate de que config.php establezca la conexión PDO

/**
 * Obtiene todos los alimentos del inventario.
 * @param PDO $conexion La conexión a la base de datos (PDO).
 * @return array Un array de alimentos.
 */
function obtenerInventario($conexion) {
    $sql = "SELECT id_alimento, nombre, cantidad, unidad_medida, fecha_ingreso
            FROM inventarioalimentos
            ORDER BY nombre ASC";
    $stmt = $conexion->query($sql);
    if (!$stmt) {
        throw new Exception("Error al obtener inventario de alimentos: " . implode(":", $conexion->errorInfo()));
    }
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtiene un alimento del inventario por su ID.
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id El ID del alimento.
 * @return array|null El alimento encontrado o null si no existe.
 */
function obtenerItemPorId($conexion, $id) {
    $stmt = $conexion->prepare("SELECT id_alimento, nombre, cantidad, unidad_medida, fecha_ingreso
                                FROM inventarioalimentos
                                WHERE id_alimento = :id");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta para obtener alimento por ID: " . implode(":", $conexion->errorInfo()));
    }
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Crea un nuevo alimento en el inventario.
 * @param PDO $conexion La conexión a la base de datos.
 * @param string $nombre Nombre del alimento.
 * @param int $cantidad Cantidad del alimento.
 * @param string $unidad_medida Unidad de medida (ej. "kg", "litros").
 * @param string $fecha_ingreso Fecha de ingreso del alimento (formato 'YYYY-MM-DD').
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function crearItem($conexion, $nombre, $cantidad, $unidad_medida, $fecha_ingreso) {
    $stmt = $conexion->prepare("
        INSERT INTO inventarioalimentos (nombre, cantidad, unidad_medida, fecha_ingreso)
        VALUES (:nombre, :cantidad, :unidad_medida, :fecha_ingreso)
    ");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta para crear alimento: " . implode(":", $conexion->errorInfo()));
    }

    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt->bindValue(':unidad_medida', $unidad_medida);
    $stmt->bindValue(':fecha_ingreso', $fecha_ingreso);
    return $stmt->execute();
}

/**
 * Actualiza un alimento existente en el inventario.
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id ID del alimento a actualizar.
 * @param string $nombre Nombre del alimento.
 * @param int $cantidad Cantidad del alimento.
 * @param string $unidad_medida Unidad de medida (ej. "kg", "litros").
 * @param string $fecha_ingreso Fecha de ingreso del alimento (formato 'YYYY-MM-DD').
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function actualizarItem($conexion, $id, $nombre, $cantidad, $unidad_medida, $fecha_ingreso) {
    $stmt = $conexion->prepare("
        UPDATE inventarioalimentos
        SET nombre = :nombre, cantidad = :cantidad, unidad_medida = :unidad_medida, fecha_ingreso = :fecha_ingreso
        WHERE id_alimento = :id
    ");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta para actualizar alimento: " . implode(":", $conexion->errorInfo()));
    }

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':nombre', $nombre);
    $stmt->bindValue(':cantidad', $cantidad, PDO::PARAM_INT);
    $stmt->bindValue(':unidad_medida', $unidad_medida);
    $stmt->bindValue(':fecha_ingreso', $fecha_ingreso);
    return $stmt->execute();
}

/**
 * Elimina un alimento del inventario.
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id El ID del alimento a eliminar.
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function eliminarItem($conexion, $id) {
    // Si hay otras tablas que dependen de inventarioalimentos, deberías manejar la eliminación en cascada
    // o eliminar los registros relacionados aquí antes de eliminar el alimento principal.
    // En este caso, tu ejemplo de inventario no mostraba dependencias como 'VentasProductos',
    // por lo que asumo que 'inventarioalimentos' es una tabla independiente para este módulo.

    $stmt = $conexion->prepare("DELETE FROM inventarioalimentos WHERE id_alimento = :id");
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta para eliminar alimento: " . implode(":", $conexion->errorInfo()));
    }
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
?>