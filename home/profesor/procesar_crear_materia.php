<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";


if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: crear_materia.php");
    exit;
}

$nombre       = trim($_POST["materia_nombre"] ?? "");
$descripcion  = trim($_POST["materia_descripcion"] ?? "");
$dias         = $_POST["dias"] ?? [];
$inicio       = $_POST["hora_inicio"] ?? "";
$fin          = $_POST["hora_fin"] ?? "";
$id_maestro   = (int) $_SESSION["id_usuario"];

// Validar campos obligatorios
if (
    $nombre === "" ||
    empty($dias) ||
    $inicio === "" ||
    $fin === ""
) {
    header("Location: crear_materia.php?error=faltan_datos");
    exit;
}


$listaDias = implode("-", $dias);      // ej: L-M-X
$horario   = $listaDias . " " . $inicio . "-" . $fin;

try {
    $sql = "
        INSERT INTO materias 
        (materia_nombre, materia_descripcion, materia_horario, id_usuario_maestro)
        VALUES (:n, :d, :h, :m)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":n" => $nombre,
        ":d" => $descripcion,
        ":h" => $horario,
        ":m" => $id_maestro
    ]);

    header("Location: index.php?materia=creada");
    exit;


} catch (PDOException $e) {

    header("Location: crear_materia.php?error=error_bd");
    exit;
}
