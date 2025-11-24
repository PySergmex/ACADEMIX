<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_tarea = intval($_POST["id_tarea"] ?? 0);
$id_alumno = intval($_POST["id_alumno"] ?? 0);
$valor = isset($_POST["valor"]) ? floatval($_POST["valor"]) : null;
$comentario = trim($_POST["comentario"] ?? "");
$id_maestro = $_SESSION["id_usuario"];

if ($id_tarea <= 0 || $id_alumno <= 0) {
    header("Location: ../index.php");
    exit;
}

$q = $pdo->prepare("
    SELECT m.id_materia
    FROM tareas t
    INNER JOIN materias m ON m.id_materia = t.id_materia
    WHERE t.id_tarea = :tarea
      AND m.id_usuario_maestro = :maestro
");
$q->execute([
    ":tarea" => $id_tarea,
    ":maestro" => $id_maestro
]);

$materia = $q->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    header("Location: ../index.php?error=no_permitido");
    exit;
}

$q = $pdo->prepare("
    SELECT id_calificacion 
    FROM calificaciones 
    WHERE id_tarea = :tarea AND id_usuario_estudiante = :alumno
");
$q->execute([
    ":tarea" => $id_tarea,
    ":alumno" => $id_alumno
]);

$existe = $q->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    $q = $pdo->prepare("
        UPDATE calificaciones
        SET calificacion_valor = :v,
            calificacion_comentario = :c,
            calificacion_fecha_registro = NOW(),
            id_usuario_registro = :prof
        WHERE id_tarea = :tarea AND id_usuario_estudiante = :alumno
    ");
    $q->execute([
        ":v" => $valor,
        ":c" => $comentario,
        ":prof" => $id_maestro,
        ":tarea" => $id_tarea,
        ":alumno" => $id_alumno
    ]);

    header("Location: ver_tarea.php?id=" . $id_tarea . "&ok=editado");
    exit;

} else {
    $q = $pdo->prepare("
        INSERT INTO calificaciones 
        (id_tarea, id_usuario_estudiante, calificacion_valor, calificacion_comentario, id_usuario_registro)
        VALUES (:tarea, :alumno, :v, :c, :prof)
    ");
    $q->execute([
        ":tarea" => $id_tarea,
        ":alumno" => $id_alumno,
        ":v" => $valor,
        ":c" => $comentario,
        ":prof" => $id_maestro
    ]);

    header("Location: ver_tarea.php?id=" . $id_tarea . "&ok=creado");
    exit;
}
