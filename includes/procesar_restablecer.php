<?php
session_start();
require_once "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

$id_usuario = intval($_POST["id_usuario"]);
$password   = trim($_POST["password"]);
$password2  = trim($_POST["password2"]);

/*Validación de contraeñas*/
if ($password !== $password2) {
    $_SESSION["error_reset"] = "Las contraseñas no coinciden.";
    header("Location: ../restablecer.php?id=" . $id_usuario);
    exit;
}

if (strlen($password) < 6) {
    $_SESSION["error_reset"] = "La contraseña debe tener al menos 6 caracteres.";
    header("Location: ../restablecer.php?id=" . $id_usuario);
    exit;
}

if (!preg_match('/[0-9]/', $password)) {
    $_SESSION["error_reset"] = "La contraseña debe incluir al menos un número.";
    header("Location: ../restablecer.php?id=" . $id_usuario);
    exit;
}

try {

    /*Validar que el usuario exista y sea estudiante = id=3*/
    $sqlRol = "SELECT id_rol FROM usuarios WHERE id_usuario = :id LIMIT 1";
    $stmtRol = $pdo->prepare($sqlRol);
    $stmtRol->bindParam(":id", $id_usuario);
    $stmtRol->execute();

    if ($stmtRol->rowCount() === 0) {
        $_SESSION["error_reset"] = "El usuario no existe.";
        header("Location: ../index.php");
        exit;
    }

    $usuario = $stmtRol->fetch();

    // Bloquear si no es estudiante
    if ($usuario["id_rol"] != 3) {
        $_SESSION["error_reset"] = "Solo los estudiantes pueden recuperar contraseña.";
        header("Location: ../index.php");
        exit;
    }

    /*Actualizar contraseña*/
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sqlUpdate = "UPDATE usuarios 
                  SET usuario_password = :pass 
                  WHERE id_usuario = :id";

    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->bindParam(":pass", $hash);
    $stmtUpdate->bindParam(":id", $id_usuario);
    $stmtUpdate->execute();

    $_SESSION["success_reset"] = "Contraseña actualizada. ¡Ya puedes iniciar sesión!";
    header("Location: ../index.php");
    exit;

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
