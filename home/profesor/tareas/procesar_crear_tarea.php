<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

/* Validar sesión de profesor */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = (int) $_SESSION["id_usuario"];

/* Validar datos */
$id_materia   = isset($_POST["id_materia"]) ? (int) $_POST["id_materia"] : 0;
$titulo       = trim($_POST["titulo"] ?? "");
$descripcion  = trim($_POST["descripcion"] ?? "");
$fecha_limite = trim($_POST["fecha_limite"] ?? "");
$ponderacion  = isset($_POST["ponderacion"]) ? (int) $_POST["ponderacion"] : 0;

if ($id_materia <= 0 || $titulo === "" || $descripcion === "" || $fecha_limite === "" || $ponderacion <= 0) {
    header("Location: crear_tarea.php?id_materia=$id_materia&error=faltan_datos");
    exit;
}

/* Validar que la materia pertenezca al maestro */
$sql = "
    SELECT id_materia
    FROM materias
    WHERE id_materia = :materia
      AND id_usuario_maestro = :maestro
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":materia" => $id_materia,
    ":maestro" => $id_maestro
]);

if ($stmt->rowCount() === 0) {
    header("Location: ../index.php");
    exit;
}

/* Insertar tarea */
$sql = "
    INSERT INTO tareas (
        id_materia,
        id_usuario_creador,
        tarea_titulo,
        tarea_descripcion,
        tarea_fecha_limite,
        tarea_ponderacion
    )
    VALUES (
        :materia,
        :creador,
        :titulo,
        :descripcion,
        :limite,
        :ponderacion
    )
";

$stmt = $pdo->prepare($sql);
$ok = $stmt->execute([
    ":materia"      => $id_materia,
    ":creador"      => $id_maestro,
    ":titulo"       => $titulo,
    ":descripcion"  => $descripcion,
    ":limite"       => $fecha_limite,
    ":ponderacion"  => $ponderacion
]);

if ($ok) {
    header("Location: index.php?id_materia=$id_materia&exito=tarea_creada");
    exit;
}

header("Location: crear_tarea.php?id_materia=$id_materia&error=error_bd");
exit;
