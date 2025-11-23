<?php
// Datos de conexión a MySQL
$host = 'localhost';
$dbname = 'academix';
$usuario = 'root';
$clave = 'Server02.';   

try {
    // Conexión con PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $usuario,
        $clave
    );

    // Configuraciones básicas de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Mensaje en caso de error
    die("Error de conexión: " . $e->getMessage());
}
