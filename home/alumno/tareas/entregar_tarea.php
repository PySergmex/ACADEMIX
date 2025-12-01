<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_alumno = (int) $_SESSION["id_usuario"];

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id_tarea = (int) $_GET["id"];

/* Validar que la tarea pertenece a una materia aprobada */
$sql = "
    SELECT 
        t.id_tarea,
        t.tarea_titulo,
        t.tarea_descripcion,
        t.tarea_fecha_limite,
        m.materia_nombre
    FROM tareas t
    INNER JOIN materias m ON m.id_materia = t.id_materia
    INNER JOIN inscripciones i ON i.id_materia = m.id_materia
    WHERE t.id_tarea = :tarea
      AND i.id_usuario_estudiante = :alumno
      AND i.id_estatus_inscripcion = 2
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":tarea"  => $id_tarea,
    ":alumno" => $id_alumno
]);

$tarea = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tarea) {
    die("No tienes permiso para entregar esta tarea.");
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entregar tarea | Alumno</title>
    <!-- ICONO -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/imgs/logo-ico.png?v=1">
    <!--Bootsrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
     <!--Iconos Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="alumno-dashboard">
<!--Topbar alumno-->
<?php include "../../../includes/topbar_alumno.php"; ?>
<!--Sidebar alumno-->
<div class="d-flex">
    <?php 
        $pagina_activa = "tareas";
        include "../../../includes/sidebar_alumno.php"; 
    ?>
    <!--Contenido Principal-->
    <main class="content-area">
        <!--Alertas-->
        <?php include "../../../includes/alertas_alumno.php"; ?>

        <h3 class="mb-3">Entregar tarea</h3>
        <p class="text-muted mb-4">
            <strong>Materia:</strong> <?= htmlspecialchars($tarea["materia_nombre"]) ?><br>
            <strong>Tarea:</strong> <?= htmlspecialchars($tarea["tarea_titulo"]) ?><br>
        </p>

        <div class="alumno-card">

            <form action="procesar_entrega_tarea.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="id_tarea" value="<?= $id_tarea ?>">

                <div class="mb-3">
                    <label class="form-label">Comentario *obligatorio</label>
                    <textarea name="comentario" class="form-control" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>

                    <button class="btn btn-success" type="submit">
                        <i class="bi bi-upload"></i> Enviar entrega
                    </button>
                </div>

            </form>

        </div>

    </main>
</div>
    <!--Footer-->
    <?php include "../../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
