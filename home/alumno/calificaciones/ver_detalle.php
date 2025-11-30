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

/* ============================
   VALIDAR MATERIA
============================ */
$id_materia = isset($_GET["id_materia"]) ? intval($_GET["id_materia"]) : 0;
$id_alumno  = (int) $_SESSION["id_usuario"];

if ($id_materia <= 0) {
    header("Location: index.php");
    exit;
}

/* ============================
   VALIDAR INSCRIPCIÓN DEL ALUMNO
============================ */
$sql = "
    SELECT 
        m.materia_nombre,
        m.materia_horario
    FROM materias m
    INNER JOIN inscripciones i 
        ON i.id_materia = m.id_materia
       AND i.id_usuario_estudiante = :alumno
       AND i.id_estatus_inscripcion = 2
    WHERE m.id_materia = :materia
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":alumno"  => $id_alumno,
    ":materia" => $id_materia
]);
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    header("Location: index.php?error=materia_no_encontrada");
    exit;
}

/* ============================
   OBTENER TAREAS + ENTREGA + CALIFICACIÓN
============================ */
$sqlTareas = "
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

$stmt = $pdo->prepare($sqlTareas);
$stmt->execute([
    ":alumno_entrega" => $id_alumno,
    ":alumno_calif"   => $id_alumno,
    ":materia"        => $id_materia
]);

$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ============================
   CALIFICACIÓN TOTAL ACUMULADA
   suma(calif * ponderacion / 100)
============================ */
$calificacionTotal = 0;

foreach ($tareas as $t) {
    if ($t["calificacion_valor"] !== null && $t["tarea_ponderacion"] !== null) {
        $valor       = (float) $t["calificacion_valor"];   // 0-100
        $ponderacion = (float) $t["tarea_ponderacion"];    // porcentaje tarea
        $calificacionTotal += ($valor * $ponderacion) / 100;
    }
}

/* ============================
   MARCAR SECCIÓN ACTIVA
============================ */
$pagina_activa = 'calificaciones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de calificaciones | Alumno - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="alumno-dashboard">

<?php include "../../../includes/topbar_alumno.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR ALUMNO -->
    <?php include "../../../includes/sidebar_alumno.php"; ?>

    <!-- CONTENIDO -->
    <main class="content-area p-4">

        <?php 
        if (file_exists("../../../includes/alertas_alumno.php")) {
            include "../../../includes/alertas_alumno.php"; 
        }
        ?>

        <h3 class="fw-bold mb-1">
            Mis calificaciones — <?= htmlspecialchars($materia["materia_nombre"]) ?>
        </h3>

        <p class="text-muted mb-2">
            Horario: 
            <strong><?= htmlspecialchars($materia["materia_horario"] ?: "Sin horario definido") ?></strong>
        </p>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>

            <div>
                <span class="me-2 text-muted">Calificación total acumulada:</span>
                <span class="badge bg-primary fs-6">
                    <?= number_format($calificacionTotal, 2) ?>
                </span>
            </div>
        </div>

        <div class="card fade-in">
            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tarea</th>
                            <th>Fecha límite</th>
                            <th>Ponderación (%)</th>
                            <th>Entrega</th>
                            <th>Calificación</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($tareas)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Aún no hay tareas registradas en esta materia.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tareas as $t): ?>
                                <tr>
                                    <!-- Tarea -->
                                    <td>
                                        <strong><?= htmlspecialchars($t["tarea_titulo"]) ?></strong>
                                    </td>

                                    <!-- Fecha límite -->
                                    <td><?= htmlspecialchars($t["tarea_fecha_limite"]) ?></td>

                                    <!-- Ponderación -->
                                    <td>
                                        <?= htmlspecialchars(
                                            $t["tarea_ponderacion"] !== null 
                                                ? $t["tarea_ponderacion"] . "%" 
                                                : "—"
                                        ) ?>
                                    </td>

                                    <!-- Entrega -->
                                    <td>
                                        <?php if ($t["entrega_fecha"]): ?>
                                            <span class="badge bg-success">Entregado</span><br>
                                            <small><?= htmlspecialchars($t["entrega_fecha"]) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Sin entregar</span>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Calificación -->
                                    <td>
                                        <?php if ($t["calificacion_valor"] !== null): ?>
                                            <strong><?= htmlspecialchars($t["calificacion_valor"]) ?></strong>
                                            <?php
                                                $valor       = (float) $t["calificacion_valor"];
                                                $ponderacion = (float) $t["tarea_ponderacion"];
                                                $aporte      = ($ponderacion > 0) ? ($valor * $ponderacion) / 100 : 0;
                                            ?>
                                            <br>
                                            <small class="text-muted">
                                                Aporte: <?= number_format($aporte, 2) ?>
                                            </small>
                                            <?php if (!empty($t["calificacion_comentario"])): ?>
                                                <br>
                                                <small class="text-muted">
                                                    <?= htmlspecialchars($t["calificacion_comentario"]) ?>
                                                </small>
                                            <?php endif; ?>
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
<!-- Footer -->
<?php include "../../../includes/footer.php"; ?>
<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS global -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
