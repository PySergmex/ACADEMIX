<?php
session_start();

require_once "../includes/config.php";
require_once "../includes/conexion.php";

// Solo aceptar POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$correo   = trim($_POST["correo"] ?? "");
$password = trim($_POST["password"] ?? "");

// Buscar el usuario por correo
try {
    $sql = "SELECT * FROM usuarios WHERE usuario_correo = :correo LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":correo", $correo, PDO::PARAM_STR);
    $stmt->execute();

    // Si no existe
    if ($stmt->rowCount() === 0) {
        $_SESSION["error_login"] = "Correo o contraseña incorrectos.";
        header("Location: " . BASE_URL . "index.php");
        exit;
    }

    $usuario = $stmt->fetch();

    // Validación de contraseña
    if (!password_verify($password, $usuario["usuario_password"])) {
        $_SESSION["error_login"] = "Correo o contraseña incorrectos.";
        header("Location: " . BASE_URL . "index.php");
        exit;
    }

    // Crear sesión
    $_SESSION["id_usuario"] = $usuario["id_usuario"];

    $_SESSION["nombre_completo"] =
        $usuario["usuario_nombres"] . " " .
        $usuario["usuario_apellido_paterno"] . " " .
        $usuario["usuario_apellido_materno"];

    $_SESSION["rol_id"] = $usuario["id_rol"];
    $_SESSION["usuario_correo"] = $usuario["usuario_correo"];

    // Redirigir según rol
    switch ($usuario["id_rol"]) {
        case 1:
            header("Location: " . BASE_URL . "home/admin/index.php");
            break;
        case 2:
            header("Location: " . BASE_URL . "home/profesor/index.php");
            break;
        case 3:
            header("Location: " . BASE_URL . "home/alumno/index.php");
            break;
        default:
            header("Location: " . BASE_URL . "index.php");
            break;
    }

    exit;

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
