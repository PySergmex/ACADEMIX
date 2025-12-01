<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/*Validar Profesor*/
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/*Validar datos*/
if (!isset($_POST["id_tarea"], $_POST["id_materia"])) {
    header("Location: ../select_materias.php");
    exit;
}

$id_tarea   = intval($_POST["id_tarea"]);
$id_materia = intval($_POST["id_materia"]);
$id_maestro = $_SESSION["id_usuario"];

/*Validar pertenencia*/
$stmt = $pdo->prepare("
    SELECT t.*, m.id_usuario_maestro
    FROM tareas t
    INNER JOIN materias m ON m.id_materia = t.id_materia
    WHERE t.id_tarea = :id
");
$stmt->execute([":id" => $id_tarea]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data || $data["id_usuario_maestro"] != $id_maestro) {
    die("No tienes permiso para modificar esta tarea.");
}

$archivo_anterior = $data["tarea_archivo"];

/*Capturar datos de formulario*/
$titulo      = trim($_POST["titulo"]);
$descripcion = trim($_POST["descripcion"]);
$fecha_limite = trim($_POST["fecha_limite"]);
$ponderacion = floatval($_POST["ponderacion"]);

/*Validación de campos*/
if (empty($titulo) || empty($descripcion) || empty($fecha_limite)) {
    header("Location: editar_tarea.php?id=$id_tarea&error=datos_incompletos");
    exit;
}

/*Actualizar Tarea*/
$stmt = $pdo->prepare("
    UPDATE tareas
    SET tarea_titulo = :t,
        tarea_descripcion = :d,
        tarea_fecha_limite = :f,
        tarea_ponderacion = :p,
        tarea_archivo = :a
    WHERE id_tarea = :id
");

$stmt->execute([
    ":t" => $titulo,
    ":d" => $descripcion,
    ":f" => $fecha_limite,
    ":p" => $ponderacion,
    ":a" => $nuevo_archivo,
    ":id" => $id_tarea
]);

/*Redirección*/
header("Location: index.php?id_materia=$id_materia&exito=tarea_editada");
exit;

?>
