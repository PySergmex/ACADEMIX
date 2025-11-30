<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

/* ==========================================
   VALIDAR SOLO PROFESORES
========================================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = (int) $_SESSION["id_usuario"];

/* ==========================================
   OBTENER MATERIAS DEL PROFESOR + ALUMNOS POR MATERIA
========================================== */
try {
    $sqlMaterias = "
        SELECT 
            m.id_materia,
            m.materia_nombre,
            m.materia_descripcion,
            COUNT(DISTINCT i.id_usuario_estudiante) AS total_alumnos
        FROM materias m
        LEFT JOIN inscripciones i 
            ON i.id_materia = m.id_materia
           AND i.id_estatus_inscripcion = 2
        WHERE m.id_usuario_maestro = :id_maestro
        GROUP BY m.id_materia
        ORDER BY m.materia_nombre ASC
    ";

    $stmt = $pdo->prepare($sqlMaterias);
    $stmt->execute([":id_maestro" => $id_maestro]);
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar materias: " . $e->getMessage());
}

/* ==========================================
   ESTADÍSTICAS PRINCIPALES
========================================== */
$totalMaterias = count($materias);
$totalAlumnos  = 0;

$idsMaterias = [];
foreach ($materias as $m) {
    $totalAlumnos += (int) $m["total_alumnos"];
    $idsMaterias[] = (int) $m["id_materia"];
}

/* Total de tareas en las materias del maestro */
$totalTareas = 0;
if (!empty($idsMaterias)) {
    $placeholders = implode(',', array_fill(0, count($idsMaterias), '?'));
    $sqlTareas = "SELECT COUNT(*) FROM tareas WHERE id_materia IN ($placeholders)";
    $stmtT = $pdo->prepare($sqlTareas);
    $stmtT->execute($idsMaterias);
    $totalTareas = (int) $stmtT->fetchColumn();
}

/* ==========================================
   PROMEDIO POR MATERIA (PONDERADO)
   - Para cada alumno de la materia:
*/
$promediosMaterias = [];   // id_materia => promedio_materia
$labelsMaterias    = [];   // nombres para la gráfica
$valuesMaterias    = [];   // valores numéricos para la gráfica

foreach ($materias as $m) {
    $idMateria    = (int) $m["id_materia"];
    $nombreMateria = $m["materia_nombre"];

    // Obtener alumnos inscritos en esta materia
    $sqlAlumnos = "
        SELECT DISTINCT id_usuario_estudiante
        FROM inscripciones
        WHERE id_materia = :materia
          AND id_estatus_inscripcion = 2
    ";
    $stmtAl = $pdo->prepare($sqlAlumnos);
    $stmtAl->execute([":materia" => $idMateria]);
    $alumnosMateria = $stmtAl->fetchAll(PDO::FETCH_COLUMN);

    if (empty($alumnosMateria)) {
        // Sin alumnos => sin promedio
        $promediosMaterias[$idMateria] = null;
        continue;
    }

    $sumPromediosAlumnos = 0;
    $alumnosConCalif     = 0;

    foreach ($alumnosMateria as $idAlumno) {
        // Calificaciones y ponderaciones de las tareas de esta materia para este alumno
        $sqlCal = "
            SELECT 
                c.calificacion_valor,
                t.tarea_ponderacion
            FROM calificaciones c
            INNER JOIN tareas t ON t.id_tarea = c.id_tarea
            WHERE c.id_usuario_estudiante = :alumno
              AND t.id_materia = :materia
        ";

        $stmtCal = $pdo->prepare($sqlCal);
        $stmtCal->execute([
            ":alumno"  => $idAlumno,
            ":materia" => $idMateria
        ]);

        $rows = $stmtCal->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            continue; // este alumno no tiene calificaciones aún
        }

        $promAlumno = 0;
        foreach ($rows as $row) {
            $valor       = (float) $row["calificacion_valor"];   // 0-100
            $ponderacion = (float) $row["tarea_ponderacion"];    // porcentaje, ej. 1 = 1%

            // Aporte de la tarea al promedio del alumno
            $promAlumno += ($valor * $ponderacion) / 100;
        }

        $sumPromediosAlumnos += $promAlumno;
        $alumnosConCalif++;
    }

    if ($alumnosConCalif > 0) {
        $promedioMateria = $sumPromediosAlumnos / $alumnosConCalif;
        $promediosMaterias[$idMateria] = $promedioMateria;

        $labelsMaterias[] = $nombreMateria;
        $valuesMaterias[] = (float) $promedioMateria;
    } else {
        $promediosMaterias[$idMateria] = null;
    }
}

/* Promedio general (promedio de promedios de materias con datos) */
$promedioGeneral = 0;
$materiasConProm = array_filter($promediosMaterias, function($v) {
    return $v !== null;
});

if (count($materiasConProm) > 0) {
    $promedioGeneral = array_sum($materiasConProm) / count($materiasConProm);
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Profesor - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="prof-dashboard">

<?php include "../../../includes/topbar_profesor.php"; ?>

<div class="d-flex">

    <?php 
        $pagina_activa = "dashboard";
        include "../../../includes/sidebar_profesor.php"; 
    ?>

    <main class="content-area p-4">

        <?php include "../../../includes/alertas_profesor.php"; ?>

        <h2 class="mb-4">Dashboard del Profesor</h2>

        <!-- CARDS PRINCIPALES -->
        <div class="row g-4 mb-4">

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $totalMaterias ?></div>
                    <div class="text-muted">Materias</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $totalAlumnos ?></div>
                    <div class="text-muted">Alumnos inscritos</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $totalTareas ?></div>
                    <div class="text-muted">Tareas creadas</div>
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

        <!-- GRÁFICO PROMEDIO POR MATERIA -->
        <div class="row g-4">
            <div class="col-md-12">
                <div class="card p-4 shadow-sm">
                    <h5 class="mb-3">Promedio por materia</h5>

                    <?php if (empty($labelsMaterias)): ?>
                        <p class="text-muted mb-0">
                            Aún no hay datos suficientes de calificaciones para mostrar la gráfica.
                        </p>
                    <?php else: ?>
                        <canvas id="chartPromedios"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </main>

</div>

    <!-- FOOTER GLOBAL -->
    <?php include "../../../includes/footer.php"; ?>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>

    <!-- ChartJS helpers (mismo patrón que admin) -->
    <script type="module">
    import { iniciarContadores, cargarGraficaPromedios } from "<?= BASE_URL ?>assets/js/chart.js";

    iniciarContadores();

    <?php if (!empty($labelsMaterias)): ?>
    cargarGraficaPromedios(
        "chartPromedios",
        <?= json_encode($labelsMaterias) ?>,
        <?= json_encode(array_map('floatval', $valuesMaterias)) ?>

    );
    <?php endif; ?>
    </script>

</body>
</html>
