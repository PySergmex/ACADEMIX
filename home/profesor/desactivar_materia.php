<?php
session_start();
require_once "../../includes/conexion.php";
require_once "../../includes/config.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id_materia = (int) $_GET["id"];
$id_maestro = (int) $_SESSION["id_usuario"];

/* Confirmar que la materia es del maestro */
$sql = "
    SELECT id_materia
    FROM materias
    WHERE id_materia = :id
      AND id_usuario_maestro = :maestro
      AND materia_activa = 1
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":id"      => $id_materia,
    ":maestro" => $id_maestro
]);

if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
    header("Location: index.php?error=materia_no_encontrada");
    exit;
}

try {
    $sql = "
        UPDATE materias
        SET materia_activa = 0
        WHERE id_materia = :id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $id_materia]);

    header("Location: index.php?materia=desactivada");
    exit;

} catch (PDOException $e) {
    header("Location: index.php?error=error_bd");
    exit;
}
