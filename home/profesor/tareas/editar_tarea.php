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
   VALIDAR ID
========================================== */
if (!isset($_GET["id"])) {
    header("Location: ../select_materias.php");
    exit;
}

$id_tarea = intval($_GET["id"]);
$id_maestro = $_SESSION["id_usuario"];

/* ==========================================
   OBTENER TAREA
========================================== */
$stmt = $pdo->prepare("
    SELECT 
        t.*,
        m.materia_nombre,
        m.id_materia,
        m.id_usuario_maestro
    FROM tareas t
    INNER JOIN materias m ON m.id_materia = t.id_materia
    WHERE t.id_tarea = :id
");
$stmt->execute([":id" => $id_tarea]);
$tarea = $stmt->fetch(PDO::FETCH_ASSOC);

/* Validar propiedad */
if (!$tarea || $tarea["id_usuario_maestro"] != $id_maestro) {
    die("No tienes permiso para editar esta tarea.");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar tarea | Profesor - AcademiX</title>

    <link rel="stylesheet" href="../../../assets/css/profesor.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="prof-dashboard">

<?php include "../../../includes/topbar_profesor.php"; ?>

<div class="d-flex">

<?php 
    $pagina_activa = "tareas";
    include "../../../includes/sidebar_profesor.php";
?>

<main class="content-area p-4">
<!--Alertas-->
<?php include "../../../includes/alertas_profesor.php"; ?>
    <h3 class="fw-bold">Editar tarea</h3>
    <p class="text-muted mb-4"><?= htmlspecialchars($tarea["materia_nombre"]); ?></p>

    <div class="admin-form-card">

        <form method="POST" action="procesar_editar_tarea.php" enctype="multipart/form-data">

            <input type="hidden" name="id_tarea" value="<?= $id_tarea ?>">
            <input type="hidden" name="id_materia" value="<?= $tarea["id_materia"] ?>">

            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control"
                       value="<?= htmlspecialchars($tarea["tarea_titulo"]) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="4"
                          required><?= htmlspecialchars($tarea["tarea_descripcion"]) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha límite</label>
                <input type="datetime-local" name="fecha_limite" class="form-control"
                       value="<?= date('Y-m-d\TH:i', strtotime($tarea["tarea_fecha_limite"])) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Ponderación (%)</label>
                <input type="number" name="ponderacion" class="form-control"
                       step="0.01" min="0" max="100"
                       value="<?= $tarea["tarea_ponderacion"] ?>" required>
            </div>

            <!-- ARCHIVO ADJUNTO -->
            <div class="mb-3">
                <label class="form-label">Archivo adjunto</label>

                <?php if (!empty($tarea["tarea_archivo"])): ?>
                    <p class="text-muted">
                        Archivo actual: 
                        <a href="../../../uploads/tareas/<?= $tarea['tarea_archivo'] ?>" target="_blank">
                            <?= $tarea["tarea_archivo"] ?>
                        </a>
                    </p>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="eliminar_archivo" value="1">
                        <label class="form-check-label text-danger">
                            Eliminar archivo actual
                        </label>
                    </div>
                <?php endif; ?>

                <input type="file" name="archivo" class="form-control">
                <small class="text-muted">Opcional. PDF, imágenes, DOCX, ZIP, etc.</small>
            </div>

            <!-- BOTONES -->
            <div class="d-flex justify-content-between mt-4">
                <a href="index.php?id_materia=<?= $tarea['id_materia'] ?>" 
                   class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Cancelar
                </a>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar cambios
                </button>
            </div>

        </form>

    </div>

</main>

</div>
     <!--JS-->                               
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
