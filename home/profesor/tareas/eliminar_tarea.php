<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/* ==========================================
   VALIDAR PROFESOR
========================================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* ==========================================
   VALIDAR ID
========================================== */
if (!isset($_GET["id"])) {
    header("Location: ../select_materias.php");
    exit;
}

$id_tarea = intval($_GET["id"]);
$id_maestro = $_SESSION["id_usuario"];

/* ==========================================
   OBTENER TAREA Y VALIDAR QUE SEA DEL PROFESOR
========================================== */
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

/* ==========================================
   ELIMINAR ARCHIVO SI EXISTE
========================================== */
if (!empty($tarea["tarea_archivo"])) {

    $ruta = "../../../uploads/tareas/" . $tarea["tarea_archivo"];

    if (file_exists($ruta)) {
        unlink($ruta);
    }
}

/* ==========================================
   ELIMINAR LA TAREA
========================================== */
try {
    $stmt = $pdo->prepare("DELETE FROM tareas WHERE id_tarea = :id LIMIT 1");
    $stmt->execute([":id" => $id_tarea]);

    header("Location: index.php?id_materia=$id_materia&exito=tarea_eliminada");
    exit;

} catch (PDOException $e) {
    header("Location: index.php?id_materia=$id_materia&error=error_bd");
    exit;
}

