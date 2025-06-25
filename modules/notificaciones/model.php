<?php
// notificaciones/model.php
require_once __DIR__ . '/../../config/config.php'; // Asegúrate de que esta ruta sea correcta para tu conexión PDO

/**
 * Obtiene las notificaciones para un usuario receptor específico,
 * incluyendo los detalles del producto asociado.
 *
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id_usuario_receptor El ID del usuario para el que se obtienen las notificaciones.
 * @return array Un array de notificaciones con detalles del producto.
 */
function obtenerNotificacionesPorUsuario($conexion, $id_usuario_receptor) {
    $sql = "
        SELECT
            n.id AS id_notificacion,
            n.id_usuario_emisor,
            n.id_usuario_receptor,
            n.id_producto,
            n.mensaje,
            n.leido,
            n.fecha,
            pg.nombre_producto,
            pg.descripcion_producto,
            pg.imagen_url,
            pg.precio_unitario
        FROM
            notificaciones n
        LEFT JOIN
            productosganaderos pg ON n.id_producto = pg.id_producto
        WHERE
            n.id_usuario_receptor = :id_usuario_receptor
        ORDER BY
            n.fecha DESC;
    ";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta para obtener notificaciones: " . implode(":", $conexion->errorInfo()));
    }
    $stmt->bindValue(':id_usuario_receptor', $id_usuario_receptor, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Marca una o varias notificaciones como leídas.
 *
 * @param PDO $conexion La conexión a la base de datos.
 * @param array $ids_notificaciones Un array de IDs de notificaciones a marcar como leídas.
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function marcarComoLeido($conexion, array $ids_notificaciones) {
    if (empty($ids_notificaciones)) {
        return true; // No hay IDs, no hay nada que hacer.
    }
    $placeholders = implode(',', array_fill(0, count($ids_notificaciones), '?'));
    $sql = "UPDATE notificaciones SET leido = 1 WHERE id IN ($placeholders)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta para marcar como leído: " . implode(":", $conexion->errorInfo()));
    }
    foreach ($ids_notificaciones as $key => $id) {
        $stmt->bindValue(($key + 1), $id, PDO::PARAM_INT);
    }
    return $stmt->execute();
}

/**
 * Elimina una o varias notificaciones de la base de datos.
 *
 * @param PDO $conexion La conexión a la base de datos.
 * @param array $ids_notificaciones Un array de IDs de notificaciones a eliminar.
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function eliminarNotificaciones($conexion, array $ids_notificaciones) {
    if (empty($ids_notificaciones)) {
        return true; // No hay IDs, no hay nada que hacer.
    }
    $placeholders = implode(',', array_fill(0, count($ids_notificaciones), '?'));
    $sql = "DELETE FROM notificaciones WHERE id IN ($placeholders)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta para eliminar notificaciones: " . implode(":", $conexion->errorInfo()));
    }
    foreach ($ids_notificaciones as $key => $id) {
        $stmt->bindValue(($key + 1), $id, PDO::PARAM_INT);
    }
    return $stmt->execute();
}

/**
 * Elimina todas las notificaciones de un usuario receptor.
 *
 * @param PDO $conexion La conexión a la base de datos.
 * @param int $id_usuario_receptor El ID del usuario cuyas notificaciones se eliminarán.
 * @return bool True en caso de éxito, false en caso de fallo.
 */
function eliminarTodasNotificacionesUsuario($conexion, $id_usuario_receptor) {
    $sql = "DELETE FROM notificaciones WHERE id_usuario_receptor = :id_usuario_receptor";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta para eliminar todas las notificaciones: " . implode(":", $conexion->errorInfo()));
    }
    $stmt->bindValue(':id_usuario_receptor', $id_usuario_receptor, PDO::PARAM_INT);
    return $stmt->execute();
}


function insertarNotificacion($conexion, $id_usuario_receptor, $id_emisor, $id_producto){
    $sql = $conexion->prepare("
    INSERT INTO notificaciones (id_usuario_emisor, id_usuario_receptor, mensaje, leido, id_producto) 
    VALUES (:id_emisor, :id_receptor, 'Esta interesado en tu producto', 0, :id_producto)");
    if (!$sql) {
        throw new Exception("Error al preparar la consulta para eliminar todas las notificaciones: " . implode(":", $conexion->errorInfo()));
    }    
    $sql->bindValue(':id_emisor', $id_emisor);
    $sql->bindValue(':id_receptor', $id_usuario_receptor);
    $sql->bindValue(':id_producto', $id_producto);

    return $sql->execute();


}
