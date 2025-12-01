<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_tarea = isset($_GET["id_tarea"]) ? intval($_GET["id_tarea"]) : 0;
$id_alumno = isset($_GET["id_alumno"]) ? intval($_GET["id_alumno"]) : 0;
$id_maestro = $_SESSION["id_usuario"];

if ($id_tarea <= 0 || $id_alumno <= 0) {
    header("Location: ../index.php");
    exit;
}

$q = $pdo->prepare("
    SELECT 
        t.tarea_titulo,
        m.id_materia,
        m.materia_nombre
    FROM tareas t
    INNER JOIN materias m ON m.id_materia = t.id_materia
    WHERE t.id_tarea = :tarea
      AND m.id_usuario_maestro = :maestro
");
$q->execute([
    ":tarea" => $id_tarea,
    ":maestro" => $id_maestro
]);

$tarea = $q->fetch(PDO::FETCH_ASSOC);

if (!$tarea) {
    header("Location: ../index.php?error=no_permitido");
    exit;
}

$id_materia = $tarea["id_materia"];

$q = $pdo->prepare("
    SELECT 
        u.usuario_nombres,
        u.usuario_apellido_paterno,
        u.usuario_correo,
        e.id_entrega,
        e.entrega_ruta_archivo,
        e.entrega_fecha,
        e.entrega_observaciones,
        c.id_calificacion,
        c.calificacion_valor,
        c.calificacion_comentario
    FROM usuarios u
    INNER JOIN entregas e ON e.id_usuario_estudiante = u.id_usuario
    LEFT JOIN calificaciones c ON c.id_tarea = e.id_tarea AND c.id_usuario_estudiante = u.id_usuario
    WHERE e.id_tarea = :tarea AND u.id_usuario = :alumno
");
$q->execute([
    ":tarea" => $id_tarea,
    ":alumno" => $id_alumno
]);

$info = $q->fetch(PDO::FETCH_ASSOC);

if (!$info) {
    header("Location: ver_tarea.php?id=" . $id_tarea);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificar entrega | Profesor - AcademiX</title>
    <!-- ICONO -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/imgs/logo-ico.png?v=1">
    <!--Bootsrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
     <!--Iconos Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="prof-dashboard">
<!--Topbar Profesor-->
<?php include "../../../includes/topbar_profesor.php"; ?>

<div class="d-flex">
    <!--Sidebar Profesor-->
    <?php
        $pagina_activa = "tareas";
        include "../../../includes/sidebar_profesor.php";
    ?>

    <main class="content-area p-4">
        <!--Alertas-->
        <?php include "../../../includes/alertas_profesor.php"; ?>

        <h3 class="mb-1"><?= htmlspecialchars($tarea["tarea_titulo"]) ?></h3>
        <p class="text-muted"><?= htmlspecialchars($tarea["materia_nombre"]) ?></p>

        <a href="ver_tarea.php?id=<?= $id_tarea ?>" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Volver
        </a>

        <div class="admin-form-card">

            <h5 class="fw-bold mb-3">Alumno</h5>
            <p>
                <strong><?= htmlspecialchars($info["usuario_nombres"] . " " . $info["usuario_apellido_paterno"]) ?></strong><br>
                <small class="text-muted"><?= htmlspecialchars($info["usuario_correo"]) ?></small>
            </p>

            <h5 class="fw-bold mt-4 mb-3">Entrega</h5>

            <?php if ($info["id_entrega"]): ?>
                <p>
                    <small class="text-muted"><?= $info["entrega_fecha"] ?></small>
                </p>

                <?php if (!empty($info["entrega_observaciones"])): ?>
                    <p><em><?= htmlspecialchars($info["entrega_observaciones"]) ?></em></p>
                <?php endif; ?>

            <?php else: ?>
                <p class="text-muted">Sin entrega registrada.</p>
            <?php endif; ?>

            <hr class="my-4">

            <h5 class="fw-bold mb-3">Calificación</h5>

            <form method="POST" action="procesar_calificacion.php">

                <input type="hidden" name="id_tarea" value="<?= $id_tarea ?>">
                <input type="hidden" name="id_alumno" value="<?= $id_alumno ?>">

                <div class="mb-3">
                    <label class="form-label">Calificación (0 - 100)</label>
                    <input 
                        type="number" 
                        name="valor" 
                        class="form-control"
                        min="0" max="100"
                        value="<?= $info["calificacion_valor"] ?? "" ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Comentario</label>
                    <textarea 
                        name="comentario"
                        class="form-control"
                        rows="3"><?= htmlspecialchars($info["calificacion_comentario"] ?? "") ?></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="ver_tarea.php?id=<?= $id_tarea ?>" class="btn btn-outline-secondary">
                        Cancelar
                    </a>

                    <button class="btn btn-primary">
                        Guardar calificación
                    </button>
                </div>

            </form>

        </div>

    </main>

</div>
    <!-- Footer -->
    <?php include "../../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
