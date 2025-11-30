<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

/* ============================
   VALIDAR SOLO ALUMNOS
============================ */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_alumno = (int) $_SESSION["id_usuario"];

/* ============================
   ESTADÍSTICAS PRINCIPALES
============================ */

/* Materias pendientes vs aprobadas */
$sqlEstatus = "
    SELECT
        SUM(CASE WHEN id_estatus_inscripcion = 1 THEN 1 ELSE 0 END) AS pendientes,
        SUM(CASE WHEN id_estatus_inscripcion = 2 THEN 1 ELSE 0 END) AS aprobadas
    FROM inscripciones
    WHERE id_usuario_estudiante = :alumno_estatus
";
$stmt = $pdo->prepare($sqlEstatus);
$stmt->execute([":alumno_estatus" => $id_alumno]);
$estatusMaterias = $stmt->fetch(PDO::FETCH_ASSOC);

$materiasPendientes = (int) ($estatusMaterias["pendientes"] ?? 0);
$materiasAprobadas  = (int) ($estatusMaterias["aprobadas"] ?? 0);

/* Tareas completadas vs pendientes (en materias aprobadas) */
$sqlTareasStats = "
    SELECT
        SUM(CASE WHEN e.id_entrega IS NULL THEN 1 ELSE 0 END) AS pendientes,
        SUM(CASE WHEN e.id_entrega IS NOT NULL THEN 1 ELSE 0 END) AS completadas
    FROM tareas t
    INNER JOIN inscripciones i 
        ON i.id_materia = t.id_materia
       AND i.id_usuario_estudiante = :alumno_insc
       AND i.id_estatus_inscripcion = 2
    LEFT JOIN entregas e 
        ON e.id_tarea = t.id_tarea
       AND e.id_usuario_estudiante = :alumno_entrega
";
$stmt = $pdo->prepare($sqlTareasStats);
$stmt->execute([
    ":alumno_insc"    => $id_alumno,
    ":alumno_entrega" => $id_alumno
]);
$tareasStats = $stmt->fetch(PDO::FETCH_ASSOC);

$tareasPendientes  = (int) ($tareasStats["pendientes"] ?? 0);
$tareasCompletadas = (int) ($tareasStats["completadas"] ?? 0);

/* Próximos horarios (máx 5 materias) */
$sqlHorarios = "
    SELECT 
        m.materia_nombre, 
        m.materia_horario
    FROM inscripciones i
    INNER JOIN materias m ON m.id_materia = i.id_materia
    WHERE i.id_usuario_estudiante = :alumno_horarios
      AND i.id_estatus_inscripcion = 2
    ORDER BY m.materia_nombre
    LIMIT 5
";
$stmt = $pdo->prepare($sqlHorarios);
$stmt->execute([":alumno_horarios" => $id_alumno]);
$horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ============================
   PROMEDIO POR MATERIA (ALUMNO)
============================ */

/* Materias del alumno */
$sqlMaterias = "
    SELECT 
        m.id_materia,
        m.materia_nombre
    FROM inscripciones i
    INNER JOIN materias m ON m.id_materia = i.id_materia
    WHERE i.id_usuario_estudiante = :alumno_materias
      AND i.id_estatus_inscripcion = 2
    ORDER BY m.materia_nombre ASC
";
$stmt = $pdo->prepare($sqlMaterias);
$stmt->execute([":alumno_materias" => $id_alumno]);
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labelsMaterias      = [];
$valuesMaterias      = [];
$promediosMaterias   = [];   // id_materia => calif materia
$paraPromedioGeneral = [];   // solo materias con calificación

foreach ($materias as $m) {
    $id_materia    = (int) $m["id_materia"];
    $nombreMateria = $m["materia_nombre"];

    $sqlCal = "
        SELECT 
            c.calificacion_valor,
            t.tarea_ponderacion
        FROM calificaciones c
        INNER JOIN tareas t ON t.id_tarea = c.id_tarea
        WHERE c.id_usuario_estudiante = :alumno_calif
          AND t.id_materia = :materia
    ";
    $qCal = $pdo->prepare($sqlCal);
    $qCal->execute([
        ":alumno_calif" => $id_alumno,
        ":materia"      => $id_materia
    ]);
    $rows = $qCal->fetchAll(PDO::FETCH_ASSOC);

    $califMateria = 0;
    $tieneCalif   = false;

    foreach ($rows as $row) {
        if ($row["calificacion_valor"] !== null && $row["tarea_ponderacion"] !== null) {
            $tieneCalif = true;
            $valor       = (float) $row["calificacion_valor"];   // 0–100
            $ponderacion = (float) $row["tarea_ponderacion"];    // %
            $califMateria += ($valor * $ponderacion) / 100;
        }
    }

    // Siempre agregamos la materia a la gráfica
    $labelsMaterias[]              = $nombreMateria;
    $valuesMaterias[]              = $tieneCalif ? $califMateria : 0;
    $promediosMaterias[$id_materia] = $tieneCalif ? $califMateria : null;

    // Para el promedio general solo contamos las que SÍ tienen calificaciones
    if ($tieneCalif) {
        $paraPromedioGeneral[] = $califMateria;
    }
}

/* Promedio general del alumno = promedio de materias con calificación */
$promedioGeneral = 0;
if (count($paraPromedioGeneral) > 0) {
    $promedioGeneral = array_sum($paraPromedioGeneral) / count($paraPromedioGeneral);
}

/* ============================
   MARCAR SECCIÓN ACTIVA
============================ */
$pagina_activa = "dashboard";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Alumno - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="alumno-dashboard">

<?php include "../../../includes/topbar_alumno.php"; ?>

<div class="d-flex">

    <?php include "../../../includes/sidebar_alumno.php"; ?>

    <main class="content-area p-4">

        <?php 
        if (file_exists("../../../includes/alertas_alumno.php")) {
            include "../../../includes/alertas_alumno.php"; 
        }
        ?>

        <h2 class="mb-4">Dashboard del Alumno</h2>

        <!-- CARDS PRINCIPALES -->
        <div class="row g-4 mb-4">

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $materiasAprobadas ?></div>
                    <div class="text-muted">Materias inscritas</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $materiasPendientes ?></div>
                    <div class="text-muted">Materias pendientes</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $tareasPendientes ?></div>
                    <div class="text-muted">Tareas pendientes</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold">
                        <?= number_format($promedioGeneral, 2) ?>
                    </div>
                    <div class="text-muted">Promedio general</div>
                </div>
            </div>

        </div>

        <!-- GRÁFICAS + HORARIOS -->
        <div class="row g-4 mb-4">

            <!-- PROMEDIO POR MATERIA (BARRAS) -->
            <div class="col-md-8">
                <div class="card p-4 shadow-sm h-100">
                    <h5 class="mb-3">Promedio por materia</h5>
                    <?php if (empty($labelsMaterias)): ?>
                        <p class="text-muted mb-0">
                            Aún no hay calificaciones registradas para mostrar la gráfica.
                        </p>
                    <?php else: ?>
                        <canvas id="chartPromedios"></canvas>
                    <?php endif; ?>
                </div>
            </div>

            <!-- TAREAS (DONUT) + HORARIOS -->
            <div class="col-md-4 d-flex flex-column gap-4">

                <!-- Donut tareas -->
                <div class="card p-4 shadow-sm">
                    <h5 class="mb-3">Tareas (completadas vs pendientes)</h5>
                    <?php if (($tareasPendientes + $tareasCompletadas) === 0): ?>
                        <p class="text-muted mb-0">
                            Aún no tienes tareas registradas.
                        </p>
                    <?php else: ?>
                        <canvas id="chartTareas"></canvas>
                    <?php endif; ?>
                </div>

                <!-- Próximas clases -->
                <div class="card p-3 shadow-sm">
                    <h6 class="mb-2">Horarios de mis materias</h6>
                    <small class="text-muted d-block mb-2">Próximas clases</small>

                    <?php if (!empty($horarios)): ?>
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($horarios as $h): ?>
                                <li class="d-flex justify-content-between align-items-center py-1 border-bottom small">
                                    <span><?= htmlspecialchars($h["materia_nombre"]) ?></span>
                                    <span class="text-muted">
                                        <?= htmlspecialchars($h["materia_horario"] ?: "Sin horario") ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted small mb-0">No tienes materias inscritas aún.</p>
                    <?php endif; ?>
                </div>

            </div>

        </div>

    </main>

</div>

<?php include "../../../includes/footer.php"; ?>

<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS global -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

<!-- Chart helpers -->
<script type="module">
import { iniciarContadores, cargarGraficaEstatus, cargarGraficaPromedios } from "<?= BASE_URL ?>assets/js/chart.js";

iniciarContadores();

// Barras: promedio por materia
<?php if (!empty($labelsMaterias)): ?>
cargarGraficaPromedios(
    "chartPromedios",
    <?= json_encode($labelsMaterias) ?>,
    <?= json_encode(array_map('floatval', $valuesMaterias)) ?>
);
<?php endif; ?>

// Donut: tareas completadas vs pendientes
<?php if (($tareasPendientes + $tareasCompletadas) > 0): ?>
cargarGraficaEstatus(
    "chartTareas",
    <?= json_encode(["Pendientes", "Completadas"]) ?>,
    <?= json_encode([$tareasPendientes, $tareasCompletadas]) ?>
);
<?php endif; ?>
</script>

</body>
</html>
