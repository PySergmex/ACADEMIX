<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

/* Solo administradores */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$idAdmin = (int)$_SESSION["id_usuario"];
$id      = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

/* Validación básica */
if ($id <= 0) {
    header("Location: " . BASE_URL . "home/admin/index.php?error=sin_id");
    exit;
}

/* Evitar que un admin se elimine a sí mismo */
if ($id === $idAdmin) {
    header("Location: " . BASE_URL . "home/admin/index.php?error=no_autoborrar");
    exit;
}

/* VERIFICAR SI ES ADMIN — NO SE PUEDE ELIMINAR */
try {
    $q = $pdo->prepare("SELECT id_rol FROM usuarios WHERE id_usuario = :id LIMIT 1");
    $q->bindParam(":id", $id, PDO::PARAM_INT);
    $q->execute();

    $usr = $q->fetch();

    if (!$usr) {
        header("Location: " . BASE_URL . "home/admin/index.php?error=usuario_no_encontrado");
        exit;
    }

    if ((int)$usr["id_rol"] === 1) {
        // No borrar administradores
        header("Location: " . BASE_URL . "home/admin/index.php?delete=admin_denegado");
        exit;
    }

} catch (PDOException $e) {
    header("Location: " . BASE_URL . "home/admin/index.php?error=consulta_fail");
    exit;
}

/* Eliminar usuario normal */
try {
    $sql = "DELETE FROM usuarios WHERE id_usuario = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        header("Location: " . BASE_URL . "home/admin/index.php?delete=ok");
    } else {
        header("Location: " . BASE_URL . "home/admin/index.php?error=usuario_no_encontrado");
    }

    exit;

} catch (PDOException $e) {
    header("Location: " . BASE_URL . "home/admin/index.php?error=eliminar_fail");
    exit;
}

