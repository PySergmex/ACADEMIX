<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/* ============================
   VALIDAR SOLO PROFESORES
============================ */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* ============================
   VALIDAR ID MATERIA
============================ */
if (!isset($_GET["id_materia"])) {
    header("Location: ../index.php");
    exit;
}

$id_materia = intval($_GET["id_materia"]);
$id_maestro = (int) $_SESSION["id_usuario"];

/* ============================
   VALIDAR PROPIEDAD DE LA MATERIA
============================ */
$stmt = $pdo->prepare("
    SELECT materia_nombre 
    FROM materias 
    WHERE id_materia = :id 
      AND id_usuario_maestro = :m
");
$stmt->execute([
    ":id" => $id_materia,
    ":m"  => $id_maestro
]);
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    die("No tienes permiso para crear tareas en esta materia.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nueva Tarea | AcademiX</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">

    <!-- CSS específico de profesor -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/profesor.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS tablero (layout general) -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="profesor-dashboard">

<div class="layout-wrapper"><!-- CONTIENE sidebar + main -->

    <!-- SIDEBAR -->
    <?php 
        $pagina_activa = "tareas";
        include "../../../includes/sidebar_profesor.php"; 
    ?>

    <!-- PANEL PRINCIPAL -->
    <div class="main-panel">

        <!-- TOPBAR -->
        <?php include "../../../includes/topbar_profesor.php"; ?>

        <!-- CONTENIDO CENTRAL -->
        <main class="content-area p-4">
            <!-- Alertas -->
            <?php include "../../../includes/alertas_profesor.php"; ?>

            <h3 class="mb-3">
                Nueva tarea — 
                <span class="text-primary">
                    <?= htmlspecialchars($materia["materia_nombre"]); ?>
                </span>
            </h3>

            <div class="admin-form-card">

                <form action="procesar_crear_tarea.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_materia" value="<?= $id_materia ?>">

                    <div class="mb-3">
                        <label class="form-label">Título de la tarea</label>
                        <input type="text" name="titulo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fecha límite</label>
                        <input type="datetime-local" name="fecha_limite" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ponderación (%)</label>
                        <input type="number" name="ponderacion" class="form-control" min="1" max="100" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Archivo adjunto (opcional)</label>
                        <input type="file" name="archivo" class="form-control">
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php?id_materia=<?= $id_materia ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>

                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save"></i> Crear tarea
                        </button>
                    </div>
                </form>

            </div>
        </main>

        <!-- FOOTER GLOBAL -->
        <?php include "../../../includes/footer.php"; ?>

    </div><!-- /.main-panel -->
</div><!-- /.layout-wrapper -->

<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<!-- JS global -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

</body>
</html>

