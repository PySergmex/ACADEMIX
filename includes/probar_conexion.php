<?php
// Probar la conexión con la base de datos
require_once 'conexion.php';

try {
    // Consulta simple para confirmar que la conexión funciona
    $stmt = $pdo->query("SELECT 1");
    $resultado = $stmt->fetch();

    echo "<h2>Conexión exitosa a la base de datos 'academix'</h2>";
    echo "<p>La consulta de prueba se ejecutó correctamente.</p>";

} catch (PDOException $e) {
    echo "<h2>Error ejecutando la consulta de prueba</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
