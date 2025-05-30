<?php
require_once __DIR__ . '/../../config/config.php';

function listarAnimales($conexion) {
    $sql = "SELECT * FROM animales";
    $stmt = $conexion->query($sql); // Ejecuta la consulta

    // Obtiene todos los resultados como un array asociativo
    $animales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $animales;
}

?>