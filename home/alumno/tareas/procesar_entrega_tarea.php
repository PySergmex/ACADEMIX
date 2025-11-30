<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_alumno = (int) $_SESSION["id_usuario"];

/* Validación básica */
if (!isset($_POST["id_tarea"])) {
    header("Location: index.php?error=faltan_datos");
    exit;
}

$id_tarea    = (int) $_POST["id_tarea"];
$comentario  = trim($_POST["comentario"] ?? "");

/* Verificar que aún no exista una entrega para esta tarea y este alumno */
$stmt = $pdo->prepare("
    SELECT id_entrega
    FROM entregas
    WHERE id_tarea = :t
      AND id_usuario_estudiante = :e
");
$stmt->execute([
    ":t" => $id_tarea,
    ":e" => $id_alumno
]);

if ($stmt->fetch()) {
    header("Location: index.php?error=ya_entregada");
    exit;
}



/* Insertar entrega con los nombres de columnas correctos */
$sql = "
    INSERT INTO entregas 
        (id_tarea,
         id_usuario_estudiante,
         entrega_fecha,
         entrega_ruta_archivo,
         entrega_observaciones)
    VALUES
        (:t, :e, NOW(), :ruta, :obs)
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":t"    => $id_tarea,
    ":e"    => $id_alumno,
    ":ruta" => $ruta_archivo,
    ":obs"  => $comentario
]);

header("Location: index.php?ok=entregada");
exit;
