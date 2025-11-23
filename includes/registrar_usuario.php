<?php
session_start();
require_once "../includes/config.php";
require_once "../includes/conexion.php";

$nombres          = trim($_POST["nombres"] ?? "");
$apellido_paterno = trim($_POST["ap_paterno"] ?? "");
$apellido_materno = trim($_POST["ap_materno"] ?? "");
$correo           = trim($_POST["correo"] ?? "");
$password         = trim($_POST["password"] ?? "");
$password2        = trim($_POST["password2"] ?? "");

/* Validaciones básicas */
if ($password !== $password2) {
    $_SESSION["error_signup"] = "Las contraseñas no coinciden.";
    header("Location: " . BASE_URL . "sign_up.php");
    exit;
}

if (strlen($password) < 6) {
    $_SESSION["error_signup"] = "La contraseña debe tener al menos 6 caracteres.";
    header("Location: " . BASE_URL . "sign_up.php");
    exit;
}

if (!preg_match('/[0-9]/', $password)) {
    $_SESSION["error_signup"] = "La contraseña debe incluir al menos un número.";
    header("Location: " . BASE_URL . "sign_up.php");
    exit;
}

try {
    /* Validar correo duplicado */
    $sql = "SELECT id_usuario FROM usuarios WHERE usuario_correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION["error_signup"] = "El correo ya está registrado.";
        header("Location: " . BASE_URL . "sign_up.php");
        exit;
    }

    /* Crear usuario */
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sqlInsert = "INSERT INTO usuarios 
        (usuario_nombres, usuario_apellido_paterno, usuario_apellido_materno,
         usuario_correo, usuario_password, id_rol, id_estatus_usuario, usuario_fecha_creacion)
        VALUES
        (:nombres, :ap_pat, :ap_mat, :correo, :password, 3, 1, NOW())";

    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->bindParam(":nombres", $nombres);
    $stmtInsert->bindParam(":ap_pat", $apellido_paterno);
    $stmtInsert->bindParam(":ap_mat", $apellido_materno);
    $stmtInsert->bindParam(":correo", $correo);
    $stmtInsert->bindParam(":password", $passwordHash);
    $stmtInsert->execute();

    /* Confirmación */
    $_SESSION["success_signup"] = "Cuenta creada correctamente. Ya puedes iniciar sesión.";
    header("Location: " . BASE_URL . "sign_up.php");
    exit;

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
