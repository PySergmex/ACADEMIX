<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

/* ===========================
   VALIDAR SOLO ALUMNOS
=========================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* ===========================
   VALIDAR DATOS
=========================== */
if (!isset($_POST["id_materia"])) {
    header("Location: alumno_inscrip_materia.php?error=materia_no_encontrada");
    exit;
}

$id_alumno  = (int) $_SESSION["id_usuario"];
$id_materia = (int) $_POST["id_materia"];

if ($id_materia <= 0) {
    header("Location: alumno_inscrip_materia.php?error=materia_no_encontrada");
    exit;
}

/* ===========================
   VERIFICAR QUE LA MATERIA EXISTA Y ESTÉ ACTIVA
=========================== */
$sql = "
    SELECT id_materia
    FROM materias
    WHERE id_materia = :id
      AND materia_activa = 1
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id_materia]);
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    header("Location: alumno_inscrip_materia.php?error=materia_no_encontrada");
    exit;
}

/* ===========================
   VERIFICAR SI YA EXISTE INSCRIPCIÓN
   (Pendiente, Aprobada o Rechazada)
=========================== */
$sql = "
    SELECT id_inscripcion
    FROM inscripciones
    WHERE id_usuario_estudiante = :alumno
      AND id_materia = :materia
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":alumno" => $id_alumno,
    ":materia" => $id_materia
]);

$existe = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existe) {
    header("Location: index.php?materia=ya_inscrito");
    exit;
}

/* ===========================
   CREAR SOLICITUD (PENDIENTE)
=========================== */
try {
    $sql = "
        INSERT INTO inscripciones
        (id_usuario_estudiante, id_materia, id_estatus_inscripcion)
        VALUES (:alumno, :materia, 1)
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":alumno"  => $id_alumno,
        ":materia" => $id_materia
    ]);

    /* Redirigir a la lista de materias del alumno */
    header("Location: index.php?materia=solicitud_ok");
    exit;

} catch (PDOException $e) {

    header("Location: alumno_inscrip_materia.php?error=error_bd");
    exit;
}
