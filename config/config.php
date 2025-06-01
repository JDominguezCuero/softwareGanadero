<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'ganaderiasimulador');
define('DB_USER', 'root');
define('DB_PASS', '');

define('BASE_URL', '/LoginADSO');

try {
    // Cadena de conexión
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $opciones = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Lanzar excepciones en errores
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Devolver arrays asociativos
        PDO::ATTR_EMULATE_PREPARES => false,                // Usar sentencias reales
    ];

    // Crear la instancia de conexión PDO
    $conexion = new PDO($dsn, DB_USER, DB_PASS, $opciones);
    
} catch (PDOException $e) {
    // Manejo de error
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>