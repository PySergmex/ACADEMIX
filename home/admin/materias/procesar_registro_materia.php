<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

// Solo admin
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

// Datos del formulario
$nombre      = trim($_POST["materia_nombre"] ?? "");
$descripcion = trim($_POST["materia_descripcion"] ?? "");
$idMaestro   = isset($_POST["id_usuario_maestro"]) ? (int)$_POST["id_usuario_maestro"] : 0;

$dias        = $_POST["dias"] ?? [];
$horaInicio  = trim($_POST["hora_inicio"] ?? "");
$horaFin     = trim($_POST["hora_fin"] ?? "");

// Validación sencilla
if ($nombre === "" || $idMaestro <= 0) {
    header("Location: registrar_materia.php?error=materia_datos");
    exit;
}

// Construir el horario
$horarioFinal = "";

if (!empty($dias) && $horaInicio !== "" && $horaFin !== "") {
    // Ejemplo: "Lun-Mar-Mie"
    $diasString = implode("-", $dias);

    // Ejemplo final: "Lun-Mar-Mie 08:00-10:00"
    $horarioFinal = $diasString . " " . $horaInicio . "-" . $horaFin;
}

try {
    $sql = "
        INSERT INTO materias (
            materia_nombre,
            materia_descripcion,
            materia_horario,
            id_usuario_maestro,
            materia_fecha_creacion
        ) VALUES (
            :nombre,
            :descripcion,
            :horario,
            :id_maestro,
            NOW()
        )
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":nombre"      => $nombre,
        ":descripcion" => $descripcion,
        ":horario"     => $horarioFinal,
        ":id_maestro"  => $idMaestro
    ]);

    header("Location: " . BASE_URL . "home/admin/materias/index.php?materia_registro=ok");
    exit;

} catch (PDOException $e) {
    // Podrías loguear el error real en un archivo
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_error_registro");
    exit;
}
