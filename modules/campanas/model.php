<?php
// modules/campanas/model.php

/**
 * Obtiene todas las campañas activas.
 *
 * @param PDO $conexion Objeto de conexión a la base de datos.
 * @param int $limit Límite de campañas a devolver (opcional).
 * @param string $order_by Campo por el que ordenar (opcional).
 * @return array Lista de campañas.
 */
function obtenerCampanasActivas(PDO $conexion, int $limit = 0, string $order_by = 'fecha_publicacion DESC'): array {
    $query = "
        SELECT
            c.id_campana,
            c.titulo,
            c.descripcion,
            c.fecha_evento,
            c.ubicacion,
            c.imagen_url,
            c.fecha_publicacion,
            c.estado,
            u.nombre_usuario,
            u.correo_usuario,
            u.telefono_usuario,
            u.direccion_usuario -- Asegúrate de que este campo exista en tu tabla usuarios
        FROM
            campanas c
        JOIN
            usuarios u ON c.id_usuario = u.id_usuario
        WHERE
            c.estado = 'activa'
        ORDER BY
            " . $order_by; // Se asume que order_by es seguro
    if ($limit > 0) {
        $query .= " LIMIT :limit";
    }

    try {
        $stmt = $conexion->prepare($query);
        if ($limit > 0) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error en obtenerCampanasActivas: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los detalles de una campaña específica por su ID.
 *
 * @param PDO $conexion Objeto de conexión a la base de datos.
 * @param int $id_campana ID de la campaña.
 * @return array|null Detalles de la campaña o null si no se encuentra.
 */
function obtenerCampanaPorId(PDO $conexion, int $id_campana): ?array {
    $query = "
        SELECT
            c.id_campana,
            c.titulo,
            c.descripcion,
            c.fecha_evento,
            c.ubicacion,
            c.imagen_url,
            c.fecha_publicacion,
            c.estado,
            u.nombre_usuario,
            u.correo_usuario,
            u.telefono_usuario,
            u.direccion_usuario -- Asegúrate de que este campo exista en tu tabla usuarios
        FROM
            campanas c
        JOIN
            usuarios u ON c.id_usuario = u.id_usuario
        WHERE
            c.id_campana = :id_campana
    ";
    try {
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id_campana', $id_campana, PDO::PARAM_INT);
        $stmt->execute();
        $campana = $stmt->fetch(PDO::FETCH_ASSOC);
        return $campana ?: null;
    } catch (PDOException $e) {
        error_log("Error en obtenerCampanaPorId: " . $e->getMessage());
        return null;
    }
}

/**
 * Crea una nueva campaña.
 *
 * @param PDO $conexion Objeto de conexión a la base de datos.
 * @param array $datos Datos de la campaña (titulo, descripcion, fecha_evento, ubicacion, imagen_url, id_usuario).
 * @return int|false El ID de la nueva campaña insertada o false en caso de error.
 */
function crearCampana(PDO $conexion, array $datos) {
    $query = "
        INSERT INTO campanas (id_usuario, titulo, descripcion, fecha_evento, ubicacion, imagen_url)
        VALUES (:id_usuario, :titulo, :descripcion, :fecha_evento, :ubicacion, :imagen_url)
    ";
    try {
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id_usuario', $datos['id_usuario'], PDO::PARAM_INT);
        $stmt->bindParam(':titulo', $datos['titulo'], PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
        $stmt->bindParam(':fecha_evento', $datos['fecha_evento'], PDO::PARAM_STR); // Formato YYYY-MM-DD HH:MM:SS
        $stmt->bindParam(':ubicacion', $datos['ubicacion'], PDO::PARAM_STR);
        $stmt->bindParam(':imagen_url', $datos['imagen_url'], PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return $conexion->lastInsertId();
        }
        return false;
    } catch (PDOException $e) {
        error_log("Error al crear campaña: " . $e->getMessage());
        return false;
    }
}

// Funciones como actualizarCampana, eliminarCampana, etc..

?>