<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/*Validar solo profesores*/
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = (int) $_SESSION["id_usuario"];
$id_materia = isset($_GET["id_materia"]) ? intval($_GET["id_materia"]) : 0;
$id_alumno  = isset($_GET["id_alumno"]) ? intval($_GET["id_alumno"]) : 0;

if ($id_materia <= 0 || $id_alumno <= 0) {
    header("Location: index.php");
    exit;
}

/*Validar materia*/
$sql = "
    SELECT materia_nombre 
    FROM materias 
    WHERE id_materia = :m
      AND id_usuario_maestro = :p
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":m" => $id_materia,
    ":p" => $id_maestro
]);
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    header("Location: index.php?error=no_permitido");
    exit;
}

/*obtener datos*/
$sql = "
    SELECT usuario_nombres, usuario_apellido_paterno, usuario_correo
    FROM usuarios
    WHERE id_usuario = :id
";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id_alumno]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$alumno) {
    header("Location: index.php?error=alumno_no_encontrado");
    exit;
}

/*consulta tareas + calificación*/
$sql = "
    SELECT 
        t.id_tarea,
        t.tarea_titulo,
        t.tarea_fecha_limite,
        t.tarea_ponderacion,

        e.entrega_fecha,
        e.entrega_observaciones,

        c.calificacion_valor,
        c.calificacion_comentario

    FROM tareas t
    
    LEFT JOIN entregas e 
        ON e.id_tarea = t.id_tarea
       AND e.id_usuario_estudiante = :alumno_entrega

    LEFT JOIN calificaciones c
        ON c.id_tarea = t.id_tarea
       AND c.id_usuario_estudiante = :alumno_calif

    WHERE t.id_materia = :materia
    ORDER BY t.tarea_fecha_limite ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":alumno_entrega" => $id_alumno,
    ":alumno_calif"   => $id_alumno,
    ":materia"        => $id_materia
]);

$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del alumno | Profesor - AcademiX</title>
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
        $pagina_activa = "calificaciones";
        include "../../../includes/sidebar_profesor.php"; 
    ?>

    <main class="content-area p-4">
        <!--Alertas-->
        <?php include "../../../includes/alertas_profesor.php"; ?>

        <h3 class="fw-bold mb-1"><?= htmlspecialchars($materia["materia_nombre"]) ?></h3>

        <p class="text-muted mb-2">
            Alumno: 
            <strong><?= htmlspecialchars($alumno["usuario_nombres"] . " " . $alumno["usuario_apellido_paterno"]) ?></strong><br>
            <small><?= htmlspecialchars($alumno["usuario_correo"]) ?></small>
        </p>

        <a href="ver_alumnos.php?id_materia=<?= $id_materia ?>" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Volver
        </a>

        <div class="card fade-in">
            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Fecha límite</th>
                            <th>Entrega</th>
                            <th>Comentario</th>
                            <th>Calificación</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($tareas)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No hay tareas registradas en esta materia.
                                </td>
                            </tr>

                        <?php else: ?>
                            <?php foreach ($tareas as $t): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($t["tarea_titulo"]) ?></strong></td>

                                <td><?= htmlspecialchars($t["tarea_fecha_limite"]) ?></td>

                                <td>
                                    <?php if ($t["entrega_fecha"]): ?>
                                        <span class="badge bg-success">Entregado</span><br>
                                        <small><?= $t["entrega_fecha"] ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Sin entregar</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($t["entrega_observaciones"]): ?>
                                        <?= nl2br(htmlspecialchars($t["entrega_observaciones"])) ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($t["calificacion_valor"] !== null): ?>
                                        <strong><?= $t["calificacion_valor"] ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($t["calificacion_comentario"]) ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">Sin calificar</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>

                </table>

            </div>
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

