<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

// Solo admin
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($id <= 0) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_sin_id");
    exit;
}

// Verificar si tiene inscripciones o tareas ligadas
try {
    // Inscripciones
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM inscripciones WHERE id_materia = :id");
    $stmt->execute([":id" => $id]);
    $inscripciones = (int)$stmt->fetchColumn();

    // Tareas
    $stmt = $pdo->prepare("SELECT COUNT(*) AS total FROM tareas WHERE id_materia = :id");
    $stmt->execute([":id" => $id]);
    $tareas = (int)$stmt->fetchColumn();

    if ($inscripciones > 0 || $tareas > 0) {
        header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_tiene_relaciones");
        exit;
    }

    // Eliminar
    $stmt = $pdo->prepare("DELETE FROM materias WHERE id_materia = :id");
    $stmt->execute([":id" => $id]);

    if ($stmt->rowCount() > 0) {
        header("Location: " . BASE_URL . "home/admin/materias/index.php?materia_delete=ok");
    } else {
        header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_no_encontrada");
    }
    exit;

} catch (PDOException $e) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_error_delete");
    exit;
}
