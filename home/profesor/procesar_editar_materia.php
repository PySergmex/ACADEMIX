<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

/* Validar que sea profesor */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* Validar datos básicos */
if (!isset($_POST["id_materia"])) {
    header("Location: index.php");
    exit;
}

$id_materia = (int) $_POST["id_materia"];
$id_maestro = (int) $_SESSION["id_usuario"];

$nombre      = trim($_POST["materia_nombre"] ?? "");
$descripcion = trim($_POST["materia_descripcion"] ?? "");

/* Si no hay nombre, no seguimos */
if ($nombre === "") {
    header("Location: editar_materia.php?id=" . $id_materia . "&error=faltan_datos");
    exit;
}

/* Verificar que la materia pertenezca al profesor */
$sql = "
    SELECT 
        id_materia,
        id_usuario_maestro,
        materia_horario
    FROM materias
    WHERE id_materia = :id_materia
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":id_materia" => $id_materia]);
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia || (int)$materia["id_usuario_maestro"] !== $id_maestro) {
    header("Location: index.php?error=no_permitido");
    exit;
}

/* Horario actual por si no se modifica */
$horario_actual = $materia["materia_horario"];

/* Capturar horario nuevo */
$dias        = $_POST["dias"] ?? [];
$hora_inicio = trim($_POST["hora_inicio"] ?? "");
$hora_fin    = trim($_POST["hora_fin"] ?? "");

/*
    Lógica de horario:
    - Si el usuario selecciona días y ambas horas, se actualiza el horario.
    - Si deja todo vacío, se mantiene el horario actual.
*/

$horario_nuevo = $horario_actual;

if (!empty($dias) && $hora_inicio !== "" && $hora_fin !== "") {
    $dias_str = implode("-", $dias);               // Ej: Lun-Mar-Mie
    $horario_nuevo = $dias_str . " " . $hora_inicio . "-" . $hora_fin;
}

/* Actualizar materia */
$sqlUpdate = "
    UPDATE materias
    SET materia_nombre      = :nombre,
        materia_descripcion = :descripcion,
        materia_horario     = :horario
    WHERE id_materia = :id_materia
";

$stmtUpdate = $pdo->prepare($sqlUpdate);

$stmtUpdate->execute([
    ":nombre"      => $nombre,
    ":descripcion" => $descripcion,
    ":horario"     => $horario_nuevo,
    ":id_materia"  => $id_materia
]);

/* Redirigir al detalle de la materia con mensaje */
header("Location: index.php?id=" . $id_materia . "&exito=materia_editada");
exit;
