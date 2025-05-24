<?php

class AnimalModel {

    public static function obtenerTodos($conexion) {
        $stmt = $conexion->prepare("
            SELECT a.*, t.nombre AS tipo_nombre 
            FROM animales a 
            JOIN tiposanimales t ON a.id_tipo = t.id_tipo
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($conexion, $id) {
        $stmt = $conexion->prepare("SELECT * FROM animales WHERE id_animal = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function actualizarEstado($conexion, $id, $campo, $incremento) {
        $camposValidos = ['alimentacion', 'higiene', 'salud', 'produccion'];
        if (!in_array($campo, $camposValidos)) return false;

        $animal = self::obtenerPorId($conexion, $id);
        if (!$animal) return false;

        $valorActual = $animal[$campo];
        $nuevoValor = min(100, $valorActual + $incremento);

        $stmt = $conexion->prepare("UPDATE animales SET $campo = ? WHERE id_animal = ?");
        return $stmt->execute([$nuevoValor, $id]);
    }

    public static function crearAnimal($conexion, $nombre, $id_tipo, $edad, $id_usuario) {
        $stmt = $conexion->prepare("
            INSERT INTO animales (nombre, id_tipo, edad, id_usuario, alimentacion, salud, higiene)
            VALUES (?, ?, ?, ?, 100, 100, 100)
        ");
        return $stmt->execute([$nombre, $id_tipo, $edad, $id_usuario]);
    }

    public static function obtenerIdTipoPorNombre($conexion, $nombreTipo) {
        $stmt = $conexion->prepare("SELECT id_tipo FROM tiposanimales WHERE nombre = ?");
        $stmt->execute([$nombreTipo]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['id_tipo'] ?? null;
    }

    public static function obtenerAnimalesPorUsuario($conexion, $id_usuario) {
        $stmt = $conexion->prepare("
            SELECT a.*, t.nombre AS tipo_nombre 
            FROM animales a
            JOIN tiposanimales t ON a.id_tipo = t.id_tipo
            WHERE a.id_usuario = ?
        ");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function degradarEstados($conexion) {
        $stmt = $conexion->prepare("
            UPDATE animales
            SET 
                alimentacion = GREATEST(alimentacion - 5, 0),
                higiene = GREATEST(higiene - 5, 0),
                salud = GREATEST(salud - 5, 0)
        ");
        return $stmt->execute();
    }

    public static function alimentarAnimal($conexion, $id) {
        $stmt = $conexion->prepare("
            UPDATE animales 
            SET alimentacion = LEAST(alimentacion + 10, 100) 
            WHERE id_animal = ?
        ");
        $stmt->execute([$id]);

        return self::obtenerPorId($conexion, $id);
    }

    public static function editarAnimal($conexion, $id_animal, $nombre) {
        $stmt = $conexion->prepare("
            UPDATE animales 
            SET nombre = ? 
            WHERE id_animal = ?
        ");
        return $stmt->execute([$nombre, $id_animal]);
    }

    public static function eliminarAnimal($conexion, $id_animal) {
        $stmt = $conexion->prepare("DELETE FROM animales WHERE id_animal = ?");
        return $stmt->execute([$id_animal]);
    }
}
