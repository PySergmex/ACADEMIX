<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_tarea = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$id_maestro = $_SESSION["id_usuario"];

if ($id_tarea <= 0) {
    header("Location: ../index.php");
    exit;
}

$q = $pdo->prepare("
    SELECT t.*, m.materia_nombre, m.id_materia
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

$sql = "
    SELECT 
        u.id_usuario,
        u.usuario_nombres,
        u.usuario_apellido_paterno,
        u.usuario_correo,

        e.id_entrega,
        e.entrega_fecha,
        e.entrega_ruta_archivo,
        e.entrega_observaciones,

        c.id_calificacion,
        c.calificacion_valor,
        c.calificacion_comentario

    FROM inscripciones i
    INNER JOIN usuarios u ON u.id_usuario = i.id_usuario_estudiante

    LEFT JOIN entregas e 
        ON e.id_usuario_estudiante = u.id_usuario
       AND e.id_tarea = :tarea1

    LEFT JOIN calificaciones c
        ON c.id_usuario_estudiante = u.id_usuario
       AND c.id_tarea = :tarea2

    WHERE i.id_materia = :materia
    ORDER BY u.usuario_nombres ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":tarea1"  => $id_tarea,
    ":tarea2"  => $id_tarea,
    ":materia" => $id_materia
]);


$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Entregas - Profesor | AcademiX</title>

    <!-- Bootstrap -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" 
        rel="stylesheet">

    <!-- Iconos Bootstrap -->
    <link 
        rel="stylesheet" 
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

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

        <?php include "../../../includes/alertas_profesor.php"; ?>

        <h3 class="mb-1"><?= htmlspecialchars($tarea["tarea_titulo"]) ?></h3>
        <p class="text-muted"><?= htmlspecialchars($tarea["tarea_descripcion"]) ?></p>

        <a href="index.php?id_materia=<?= $id_materia ?>" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Volver
        </a>

        <div class="card">
            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Entrega</th>
                            <th>Calificación</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($alumnos)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">
                                    No hay alumnos inscritos.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($alumnos as $a): ?>
                            <tr>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($a["usuario_nombres"] . " " . $a["usuario_apellido_paterno"]) ?>
                                    </strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($a["usuario_correo"]) ?></small>
                                </td>

                                <td>
                                    <?php if ($a["id_entrega"]): ?>
                                        <a href="<?= BASE_URL . 'uploads/entregas/' . $a['entrega_ruta_archivo'] ?>" 
                                           target="_blank">
                                            Ver archivo
                                        </a><br>
                                        <small class="text-muted">
                                            <?= $a["entrega_fecha"] ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">Sin entrega</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($a["id_calificacion"]): ?>
                                        <strong><?= $a["calificacion_valor"] ?></strong><br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($a["calificacion_comentario"]) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">Sin calificar</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?php if ($a["id_entrega"]): ?>
                                        <a href="calificar_entrega.php?id_tarea=<?= $id_tarea ?>&id_alumno=<?= $a['id_usuario'] ?>"
                                           class="btn btn-sm btn-primary">
                                            <?= $a["id_calificacion"] ? "Editar" : "Calificar" ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Sin entrega</span>
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

</body>
</html>
