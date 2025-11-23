<?php
session_start();
require_once "config.php";
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "recuperar.php");
    exit;
}

$correo = trim($_POST["correo"] ?? "");

try {
    // Buscar usuario por correo
    $sql = "SELECT id_usuario, id_rol FROM usuarios WHERE usuario_correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
    $stmt->execute();

    // Si no existe
    if ($stmt->rowCount() === 0) {
        $_SESSION["error_recuperar"] = "El correo no está registrado.";
        header("Location: " . BASE_URL . "recuperar.php");
        exit;
    }

    $usuario = $stmt->fetch();

    // Validar que solo estudiantes puedan recuperar contraseña
    if ($usuario["id_rol"] != 3) {
        $_SESSION["error_recuperar"] = "Solo los estudiantes pueden recuperar contraseña.";
        header("Location: " . BASE_URL . "recuperar.php");
        exit;
    }

    // Si pasa validación → ir a restablecer
    header("Location: " . BASE_URL . "restablecer.php?id=" . $usuario["id_usuario"]);
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
