<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

/* ===========================
   VALIDAR SOLO ALUMNOS
=========================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_alumno = (int) $_SESSION["id_usuario"];

/* ===========================
   CONSULTAR TAREAS DE MATERIAS
   CON INSCRIPCIÓN APROBADA
=========================== */
$sql = "
    SELECT 
        t.id_tarea,
        t.tarea_titulo,
        t.tarea_fecha_limite,
        t.tarea_ponderacion,
        m.id_materia,
        m.materia_nombre,
        e.id_entrega,
        e.entrega_fecha
    FROM inscripciones i
    INNER JOIN materias m ON m.id_materia = i.id_materia
    INNER JOIN tareas t ON t.id_materia = m.id_materia
    LEFT JOIN entregas e 
        ON e.id_tarea = t.id_tarea
       AND e.id_usuario_estudiante = :alumno
    WHERE i.id_usuario_estudiante = :alumno
      AND i.id_estatus_inscripcion = 2
    ORDER BY t.tarea_fecha_limite ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":alumno" => $id_alumno]);
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis tareas | Alumno - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!--CSS-->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="alumno-dashboard">

    <!-- TOPBAR -->
    <?php include "../../includes/topbar_alumno.php"; ?>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <?php 
            $pagina_activa = "tareas";
            include "../../includes/sidebar_alumno.php";
        ?>

        <!-- CONTENIDO -->
        <main class="content-area">

            <?php include "../../includes/alertas_alumno.php"; ?>

            <h3 class="fw-bold mb-3">Mis tareas</h3>
            <p class="text-muted mb-4">
                Aquí se muestran las tareas de tus materias aprobadas.
            </p>

            <!-- Buscador -->
            <div class="row g-2 mb-3">
                <div class="col-sm-4 col-md-3">
                    <input 
                        type="text"
                        name="busqueda"
                        class="form-control"
                        placeholder="Buscar por materia o tarea"
                    >
                </div>
            </div>

            <!-- TABLA -->
            <div class="card fade-in">
                <div class="card-body table-responsive">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Tarea</th>
                                <th>Fecha límite</th>
                                <th>Ponderación</th>
                                <th>Entrega</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (empty($tareas)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Por ahora no tienes tareas asignadas.
                                </td>
                            </tr>

                        <?php else: ?>
                            <?php foreach ($tareas as $t): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($t["materia_nombre"]) ?></strong>
                                </td>

                                <td><?= htmlspecialchars($t["tarea_titulo"]) ?></td>

                                <td><?= htmlspecialchars($t["tarea_fecha_limite"]) ?></td>

                                <td><?= (float) $t["tarea_ponderacion"] ?>%</td>

                                <td>
                                    <?php if ($t["id_entrega"]): ?>
                                        <span class="badge bg-success">
                                            Entregada el <?= htmlspecialchars($t["entrega_fecha"]) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin entregar</span>
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

    <!-- FOOTER -->
    <?php include "../../includes/footer.php"; ?>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>

    <!-- Buscador en tiempo real -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        if (typeof iniciarBuscadorEnTiempoReal === "function") {
            iniciarBuscadorEnTiempoReal("input[name='busqueda']", "table");
        }
    });
    </script>

</body>
</html>
