<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

// Solo admin
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

// Datos básicos
$idMateria   = isset($_POST["id_materia"]) ? (int)$_POST["id_materia"] : 0;
$nombre      = trim($_POST["materia_nombre"] ?? "");
$descripcion = trim($_POST["materia_descripcion"] ?? "");
$idMaestro   = isset($_POST["id_usuario_maestro"]) ? (int)$_POST["id_usuario_maestro"] : 0;

$dias        = $_POST["dias"] ?? [];
$horaInicio  = trim($_POST["hora_inicio"] ?? "");
$horaFin     = trim($_POST["hora_fin"] ?? "");

if ($idMateria <= 0) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_sin_id");
    exit;
}

if ($nombre === "" || $idMaestro <= 0) {
    header("Location: editar_materia.php?id=" . $idMateria . "&error=materia_datos");
    exit;
}

// Obtener materia actual para conservar horario si no se cambia
$sql = "SELECT materia_horario FROM materias WHERE id_materia = :id LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $idMateria]);
$actual = $stmt->fetch();

if (!$actual) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_no_encontrada");
    exit;
}

$horarioFinal = $actual["materia_horario"];

// Si el admin seleccionó días y horas, construimos un nuevo horario
if (!empty($dias) && $horaInicio !== "" && $horaFin !== "") {
    $diasString  = implode("-", $dias);
    $horarioFinal = $diasString . " " . $horaInicio . "-" . $horaFin;
}

try {
    $sqlUpdate = "
        UPDATE materias
        SET materia_nombre      = :nombre,
            materia_descripcion = :descripcion,
            materia_horario     = :horario,
            id_usuario_maestro  = :id_maestro
        WHERE id_materia = :id
    ";

    $stmt = $pdo->prepare($sqlUpdate);
    $stmt->execute([
        ":nombre"      => $nombre,
        ":descripcion" => $descripcion,
        ":horario"     => $horarioFinal,
        ":id_maestro"  => $idMaestro,
        ":id"          => $idMateria
    ]);

    header("Location: " . BASE_URL . "home/admin/materias/index.php?materia_edit=ok");
    exit;

} catch (PDOException $e) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_error_editar");
    exit;
}
