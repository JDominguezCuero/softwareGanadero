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
        $stmt = $conexion->prepare("
            SELECT a.*, t.nombre AS tipo_nombre 
            FROM animales a
            JOIN tiposanimales t ON a.id_tipo = t.id_tipo
            WHERE a.id_animal = ?
        ");
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

        $stmt = $conexion->prepare("UPDATE animales SET $campo = ?, last_updated_at = NOW() WHERE id_animal = ?");
        return $stmt->execute([$nuevoValor, $id]);
    }

    public static function crearAnimal($conexion, $nombre, $id_tipo, $edad, $id_usuario) {
        $stmt = $conexion->prepare("
            INSERT INTO animales (nombre, id_tipo, edad, id_usuario, alimentacion, salud, higiene, produccion, last_updated_at)
            VALUES (?, ?, ?, ?, 25, 25, 25, 0, NOW())
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
            SET nombre = ?, last_updated_at = NOW()
            WHERE id_animal = ?
        ");
        return $stmt->execute([$nombre, $id_animal]);
    }

    public static function eliminarAnimal($conexion, $id_animal) {
        $stmt = $conexion->prepare("DELETE FROM animales WHERE id_animal = ?");
        return $stmt->execute([$id_animal]);
    }

    /**
     * Calcula y aplica el decremento de estados para un animal específico
     * basado en el tiempo transcurrido desde la última actualización.
     *
     * @param PDO $conexion Conexión a la base de datos.
     * @param int $id_animal El ID del animal.
     * @param int $factor_tiempo El factor por el cual se acelera el tiempo (1 para normal, 2 para x2, etc.)
     * @return array|false El animal actualizado (con tipo_nombre) o false si no se encuentra.
    */

    public static function aplicarDecrementoPorTiempo($conexion, $id_animal, $factor_tiempo = 1) {
        $animal = self::obtenerPorId($conexion, $id_animal); // Asegurarse de que obtenerPorId ya trae tipo_nombre

        if (!$animal) {
            error_log("Animal no encontrado para decremento. ID: " . $id_animal);
            return false;
        }

        $lastUpdated = new DateTime($animal['last_updated_at']);
        $now = new DateTime();
        $interval = $now->diff($lastUpdated);

        // Convertimos el intervalo a minutos.
        $minutos_pasados = $interval->days * 24 * 60 + $interval->h * 60 + $interval->i;

        // --- DEFINICIÓN DE LAS VARIABLES DE DECREMENTO AQUÍ ---
        // Asegúrate de que estas líneas estén presentes y correctas.
        $decremento_por_minuto_alimentacion = 0.03333;
        $decremento_por_minuto_higiene = 0.03333;
        $decremento_por_minuto_salud = 0.01666;
        $decremento_por_minuto_produccion = 0.01666;
        // --- FIN DE DEFINICIÓN ---

        if ($minutos_pasados < 1) { // Si no ha pasado al menos un minuto
            return $animal; // No hay decremento que aplicar todavía, devolvemos el animal actual
        }

        // Aplicamos el factor de tiempo a los minutos pasados para simular aceleración
        $minutos_simulados = $minutos_pasados * $factor_tiempo;

        // Calcula los nuevos valores, asegurándose de que no bajen de 0
        $nueva_alimentacion = max(0, $animal['alimentacion'] - ($minutos_simulados * $decremento_por_minuto_alimentacion));
        $nueva_higiene = max(0, $animal['higiene'] - ($minutos_simulados * $decremento_por_minuto_higiene));
        $nueva_salud = max(0, $animal['salud'] - ($minutos_simulados * $decremento_por_minuto_salud));
        $nueva_produccion = max(0, $animal['produccion'] - ($minutos_simulados * $decremento_por_minuto_produccion));

        // Para evitar actualizaciones a la DB si no hay cambios significativos.
        // Convertimos a int para comparar y luego guardar en la DB.
        $nueva_alimentacion_int = (int)round($nueva_alimentacion);
        $nueva_higiene_int = (int)round($nueva_higiene);
        $nueva_salud_int = (int)round($nueva_salud);
        $nueva_produccion_int = (int)round($nueva_produccion);

        // Solo actualizamos si hay algún cambio real en los valores enteros
        if ($nueva_alimentacion_int !== $animal['alimentacion'] ||
            $nueva_higiene_int !== $animal['higiene'] ||
            $nueva_salud_int !== $animal['salud'] ||
            $nueva_produccion_int !== $animal['produccion']) {

            $stmt = $conexion->prepare("
                UPDATE animales
                SET 
                    alimentacion = ?,
                    higiene = ?,
                    salud = ?,
                    produccion = ?,
                    last_updated_at = NOW()
                WHERE id_animal = ?
            ");

            $ejecutado = $stmt->execute([
                $nueva_alimentacion_int,
                $nueva_higiene_int,
                $nueva_salud_int,
                $nueva_produccion_int,
                $id_animal
            ]);

            if ($ejecutado) {
                // Retorna el animal con los datos actualizados desde la BD, incluyendo tipo_nombre
                return self::obtenerPorId($conexion, $id_animal); 
            } else {
                error_log("Fallo al actualizar el animal en la base de datos: ID " . $id_animal);
                return $animal; // Devuelve el animal original si falla la actualización
            }
        } else {
            // No hubo cambios significativos, devolvemos el animal original
            return $animal;
        }
    }

}