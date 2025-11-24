<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/* ==========================================
   1. SOLO PROFESORES
========================================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = (int) $_SESSION["id_usuario"];

/* ==========================================
   2. VALIDAR DATOS
========================================== */
if (!isset($_POST["id_inscripcion"]) || !isset($_POST["accion"])) {
    header("Location: index.php?error=solicitud_invalida");
    exit;
}

$id_inscripcion = (int) $_POST["id_inscripcion"];
$accion = $_POST["accion"]; // aprobar / rechazar

/* Solo permitimos estas acciones */
if (!in_array($accion, ["aprobar", "rechazar"])) {
    header("Location: index.php?error=solicitud_invalida");
    exit;
}

/* ==========================================
   3. VALIDAR QUE LA INSCRIPCIÓN SEA DEL MAESTRO
========================================== */
$sql = "
    SELECT 
        i.id_inscripcion,
        i.id_materia,
        m.id_usuario_maestro
    FROM inscripciones i
    INNER JOIN materias m ON m.id_materia = i.id_materia
    WHERE i.id_inscripcion = :id
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id_inscripcion]);
$inscripcion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inscripcion) {
    header("Location: index.php?error=solicitud_no_existe");
    exit;
}

/* Esta inscripción NO pertenece al maestro autenticado */
if ($inscripcion["id_usuario_maestro"] != $id_maestro) {
    header("Location: index.php?error=no_autorizado");
    exit;
}

/* ==========================================
   4. ACTUALIZAR ESTATUS
========================================== */
$estatusNuevo = ($accion === "aprobar") ? 2 : 3; 
// 2 = Aprobado, 3 = Rechazado

$sql = "
    UPDATE inscripciones
    SET 
        id_estatus_inscripcion = :estatus,
        id_usuario_resolvio = :maestro,
        inscripcion_fecha_resolucion = NOW()
    WHERE id_inscripcion = :id
";
$stmt = $pdo->prepare($sql);

$ok = $stmt->execute([
    ":estatus" => $estatusNuevo,
    ":maestro" => $id_maestro,
    ":id" => $id_inscripcion
]);

if ($ok) {
    if ($accion === "aprobar") {
        header("Location: index.php?solicitud=aprobada");
    } else {
        header("Location: index.php?solicitud=rechazada");
    }
} else {
    header("Location: index.php?error=error_actualizar");
}

exit;

?>
