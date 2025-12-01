<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

/*Validar solo alumnos*/
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_alumno = (int) $_SESSION["id_usuario"];

/*Obtener materias*/
$sql = "
    SELECT 
        m.id_materia,
        m.materia_nombre,
        m.materia_descripcion,
        m.materia_horario
    FROM inscripciones i
    INNER JOIN materias m ON m.id_materia = i.id_materia
    WHERE i.id_usuario_estudiante = :alumno
      AND i.id_estatus_inscripcion = 2
    ORDER BY m.materia_nombre ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([":alumno" => $id_alumno]);
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*Promedio materia*/
$promedios = []; // id_materia => promedio (float|null)

foreach ($materias as $m) {
    $id_materia = (int) $m["id_materia"];

    $sqlCal = "
        SELECT 
            c.calificacion_valor,
            t.tarea_ponderacion
        FROM calificaciones c
        INNER JOIN tareas t ON t.id_tarea = c.id_tarea
        WHERE c.id_usuario_estudiante = :alumno
          AND t.id_materia = :materia
    ";

    $qCal = $pdo->prepare($sqlCal);
    $qCal->execute([
        ":alumno"  => $id_alumno,
        ":materia" => $id_materia
    ]);

    $rows = $qCal->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        $promedios[$id_materia] = null;
        continue;
    }

    $promAlumno = 0;
    foreach ($rows as $row) {
        $valor       = (float) $row["calificacion_valor"];   // 0-100
        $ponderacion = (float) $row["tarea_ponderacion"];    // porcentaje

        // Aporte de la tarea a la calificación final de la materia
        $promAlumno += ($valor * $ponderacion) / 100;
    }

    $promedios[$id_materia] = $promAlumno;
}

/*Página Activa*/
$pagina_activa = 'calificaciones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis calificaciones | Alumno - AcademiX</title>
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

<div class="d-flex">

    <!--Sidebar alumno-->
    <?php include "../../../includes/sidebar_alumno.php"; ?>

    <!--Contenido Principal-->
    <main class="content-area p-4">

        <?php 
        // si tienes archivo de alertas para alumno
        if (file_exists("../../../includes/alertas_alumno.php")) {
            include "../../../includes/alertas_alumno.php"; 
        }
        ?>

        <h3 class="fw-bold mb-1">Mis calificaciones</h3>
        <p class="text-muted mb-3">
            Selecciona una materia para ver el detalle de tus tareas y calificación acumulada.
        </p>

        <div class="card fade-in">
            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Materia</th>
                            <th>Horario</th>
                            <th>Calificación actual</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($materias)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Aún no estás inscrito en ninguna materia.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($materias as $m): 
                                $id_materia = (int) $m["id_materia"];
                                $prom = $promedios[$id_materia] ?? null;
                            ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($m["materia_nombre"]) ?></strong><br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($m["materia_descripcion"] ?: "Sin descripción") ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($m["materia_horario"] ?: "Sin horario definido") ?>
                                    </td>

                                    <td>
                                        <?php if ($prom !== null): ?>
                                            <span class="badge bg-primary">
                                                <?= number_format($prom, 2) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Sin calificaciones</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="ver_detalle.php?id_materia=<?= $id_materia ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Ver detalle
                                        </a>
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
