<?php
// config/db_connect.php

// -- CONFIGURACIÓN DE LA BASE DE DATOS --
// Modifica estos valores según tu configuración local.
$host = 'localhost';         // Generalmente 'localhost' o '127.0.0.1'
$db   = 'hotel_gestion_db'; // El nombre de la base de datos que creamos
$user = 'root';              // Tu usuario de MySQL (por defecto 'root' en XAMPP)
$pass = '';                  // Tu contraseña de MySQL (por defecto vacía en XAMPP)
$charset = 'utf8mb4';        // Conjunto de caracteres para soportar emojis y caracteres especiales

// -- NO MODIFICAR DE AQUÍ HACIA ABAJO --

// DSN (Data Source Name): Cadena de conexión para PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de configuración para PDO (PHP Data Objects)
$options = [
    // Reportar errores como excepciones, lo que permite usar bloques try/catch
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Devolver los resultados como arrays asociativos (ej: $row['nombre_columna'])
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // Desactivar la emulación de sentencias preparadas para mayor seguridad
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Intentar crear una nueva instancia de PDO para conectar a la base de datos
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Si la conexión falla, se detiene la ejecución y se muestra un mensaje de error.
    // En un entorno de producción, esto debería registrarse en un archivo de log en lugar de mostrarse al usuario.
    http_response_code(500); // Internal Server Error
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>