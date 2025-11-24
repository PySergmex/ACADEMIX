<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/* ==========================================
   VALIDAR PROFESOR
========================================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* ==========================================
   VALIDAR DATOS
========================================== */
if (!isset($_POST["id_tarea"], $_POST["id_materia"])) {
    header("Location: ../select_materias.php");
    exit;
}

$id_tarea   = intval($_POST["id_tarea"]);
$id_materia = intval($_POST["id_materia"]);
$id_maestro = $_SESSION["id_usuario"];

/* ==========================================
   VERIFICAR QUE LA TAREA PERTENECE AL PROFESOR
========================================== */
$stmt = $pdo->prepare("
    SELECT t.*, m.id_usuario_maestro
    FROM tareas t
    INNER JOIN materias m ON m.id_materia = t.id_materia
    WHERE t.id_tarea = :id
");
$stmt->execute([":id" => $id_tarea]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data || $data["id_usuario_maestro"] != $id_maestro) {
    die("No tienes permiso para modificar esta tarea.");
}

$archivo_anterior = $data["tarea_archivo"];

/* ==========================================
   CAPTURAR DATOS DEL FORMULARIO
========================================== */
$titulo      = trim($_POST["titulo"]);
$descripcion = trim($_POST["descripcion"]);
$fecha_limite = trim($_POST["fecha_limite"]);
$ponderacion = floatval($_POST["ponderacion"]);

/* ==========================================
   VALIDACIÓN BÁSICA
========================================== */
if (empty($titulo) || empty($descripcion) || empty($fecha_limite)) {
    header("Location: editar_tarea.php?id=$id_tarea&error=datos_incompletos");
    exit;
}

/* ==========================================
   MANEJO DE ARCHIVO
========================================== */
$nuevo_archivo = $archivo_anterior;

$upload_dir = "../../../uploads/tareas/";

/* Crear carpeta si no existe */
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

/* ------------------------------------------
   1. ELIMINAR ARCHIVO (si se pidió)
------------------------------------------- */
if (isset($_POST["eliminar_archivo"]) && $archivo_anterior) {
    $ruta = $upload_dir . $archivo_anterior;
    if (file_exists($ruta)) {
        unlink($ruta);
    }
    $nuevo_archivo = null;
}

/* ------------------------------------------
   2. SUBIR ARCHIVO NUEVO (si se cargó)
------------------------------------------- */
if (!empty($_FILES["archivo"]["name"])) {

    $file = $_FILES["archivo"];

    /* Validar que no haya errores */
    if ($file["error"] === 0) {

        /* Generar nombre único */
        $extension = pathinfo($file["name"], PATHINFO_EXTENSION);
        $archivo_final = uniqid("tarea_", true) . "." . $extension;

        /* Mover archivo */
        if (move_uploaded_file($file["tmp_name"], $upload_dir . $archivo_final)) {

            /* Si había archivo anterior, eliminarlo */
            if ($archivo_anterior) {
                $ruta = $upload_dir . $archivo_anterior;
                if (file_exists($ruta)) {
                    unlink($ruta);
                }
            }

            $nuevo_archivo = $archivo_final;
        }
    }
}

/* ==========================================
   ACTUALIZAR TAREA
========================================== */
$stmt = $pdo->prepare("
    UPDATE tareas
    SET tarea_titulo = :t,
        tarea_descripcion = :d,
        tarea_fecha_limite = :f,
        tarea_ponderacion = :p,
        tarea_archivo = :a
    WHERE id_tarea = :id
");

$stmt->execute([
    ":t" => $titulo,
    ":d" => $descripcion,
    ":f" => $fecha_limite,
    ":p" => $ponderacion,
    ":a" => $nuevo_archivo,
    ":id" => $id_tarea
]);

/* ==========================================
   REDIRIGIR AL LISTADO DE TAREAS
========================================== */
header("Location: index.php?id_materia=$id_materia&exito=tarea_editada");
exit;

?>
