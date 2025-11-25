<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

/* Solo alumnos */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* Validar materia */
if (!isset($_GET["id"])) {
    header("Location: index.php?error=materia_no_valida");
    exit;
}

$id_alumno  = (int) $_SESSION["id_usuario"];
$id_materia = (int) $_GET["id"];

if ($id_materia <= 0) {
    header("Location: index.php?error=materia_no_valida");
    exit;
}

try {
    /* Eliminar SOLO si está en estado pendiente */
    $sql = "
        DELETE FROM inscripciones
        WHERE id_usuario_estudiante = :alumno
          AND id_materia = :materia
          AND id_estatus_inscripcion = 1
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":alumno"  => $id_alumno,
        ":materia" => $id_materia
    ]);

    if ($stmt->rowCount() > 0) {
        header("Location: index.php?inscripcion=cancelada");
        exit;
    } else {
        header("Location: index.php?error=no_permitido");
        exit;
    }

} catch (PDOException $e) {
    header("Location: index.php?error=error_bd");
    exit;
}
