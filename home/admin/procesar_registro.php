<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

// Solo admin
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

// Validar datos enviados
$nombres    = trim($_POST["nombres"] ?? "");
$ap_paterno = trim($_POST["ap_paterno"] ?? "");
$ap_materno = trim($_POST["ap_materno"] ?? "");
$correo     = trim($_POST["correo"] ?? "");
$password   = trim($_POST["password"] ?? "");
$rol        = intval($_POST["rol"] ?? 0);
$estatus    = intval($_POST["estatus"] ?? 0);

// Comprobar correo duplicado
try {
    $sql = "SELECT id_usuario FROM usuarios WHERE usuario_correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":correo", $correo);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        header("Location: " . BASE_URL . "home/admin/registrar_usuario.php?error=correo_duplicado");
        exit;
    }

    // Guardar usuario
    $insert = $pdo->prepare("
        INSERT INTO usuarios 
        (usuario_nombres, usuario_apellido_paterno, usuario_apellido_materno,
         usuario_correo, usuario_password, id_rol, id_estatus_usuario)
        VALUES (:n, :p, :m, :c, :pass, :r, :e)
    ");

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $insert->execute([
        ":n"    => $nombres,
        ":p"    => $ap_paterno,
        ":m"    => $ap_materno,
        ":c"    => $correo,
        ":pass" => $passwordHash,
        ":r"    => $rol,
        ":e"    => $estatus
    ]);

    header("Location: " . BASE_URL . "home/admin/index.php?registro=ok");
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
