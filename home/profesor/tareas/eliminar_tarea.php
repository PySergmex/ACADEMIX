<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/*Validar Profesor*/
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/*Validar ID*/
if (!isset($_GET["id"])) {
    header("Location: ../select_materias.php");
    exit;
}

$id_tarea = intval($_GET["id"]);
$id_maestro = $_SESSION["id_usuario"];

/*Obtener tarea para validar que sea el profesor*/
$stmt = $pdo->prepare("
    SELECT t.*, m.id_usuario_maestro
    FROM tareas t
    INNER JOIN materias m ON m.id_materia = t.id_materia
    WHERE t.id_tarea = :id
    LIMIT 1
");
$stmt->execute([":id" => $id_tarea]);
$tarea = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarea || $tarea["id_usuario_maestro"] != $id_maestro) {
    die("No tienes permiso para eliminar esta tarea.");
}

$id_materia = $tarea["id_materia"];
/*Eliminar Tarea*/
try {
    $stmt = $pdo->prepare("DELETE FROM tareas WHERE id_tarea = :id LIMIT 1");
    $stmt->execute([":id" => $id_tarea]);

    header("Location: index.php?id_materia=$id_materia&exito=tarea_eliminada");
    exit;

} catch (PDOException $e) {
    header("Location: index.php?id_materia=$id_materia&error=error_bd");
    exit;
}

