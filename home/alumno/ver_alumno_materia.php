<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";


/*Validar solo alumnos*/
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_alumno = $_SESSION["id_usuario"];

/*Validar ID materia*/
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id_materia = intval($_GET["id"]);

/*Consultar información materia*/
$sql = "
    SELECT 
        m.id_materia,
        m.materia_nombre,
        m.materia_descripcion,
        m.materia_horario,
        u.usuario_nombres,
        u.usuario_apellido_paterno,
        u.usuario_apellido_materno,
        i.id_estatus_inscripcion,
        i.inscripcion_fecha_solicitud
    FROM inscripciones i
    INNER JOIN materias m ON m.id_materia = i.id_materia
    INNER JOIN usuarios u ON u.id_usuario = m.id_usuario_maestro
    WHERE i.id_usuario_estudiante = :alumno
      AND i.id_materia = :materia
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":alumno" => $id_alumno,
    ":materia" => $id_materia
]);

$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    header("Location: index.php?error=materia_no_encontrada");
    exit;
}

/*Status visual*/
switch ($materia["id_estatus_inscripcion"]) {
    case 1: 
        $estatus_html = "<span class='badge bg-warning'>Pendiente</span>";
        break;
    case 2: 
        $estatus_html = "<span class='badge bg-success'>Aprobada</span>";
        break;
    case 3: 
        $estatus_html = "<span class='badge bg-danger'>Rechazada</span>";
        break;
    default:
        $estatus_html = "<span class='badge bg-secondary'>Desconocido</span>";
}

/* Para el nombre completo del maestro */
$nombre_maestro = 
    $materia["usuario_nombres"] . " " .
    $materia["usuario_apellido_paterno"] . " " .
    $materia["usuario_apellido_materno"];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($materia["materia_nombre"]) ?> | Alumno - AcademiX</title>
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
    <?php include "../../includes/topbar_alumno.php"; ?>

    <div class="d-flex">

        <!--Sidebar alumno-->
        <?php 
            $pagina_activa = "materias";
            include "../../includes/sidebar_alumno.php";
        ?>

        <!--Contenido Principal-->
        <main class="content-area">

            <?php include "../../includes/alertas_alumno.php"; ?>

            <h3 class="fw-bold mb-4">
                <?= htmlspecialchars($materia["materia_nombre"]) ?>
            </h3>

            <div class="admin-form-card p-4 col-lg-6 col-md-8">

                <div class="mb-3">
                    <label class="form-label text-muted">Estado de inscripción</label>
                    <div class="fw-semibold">
                        <?= $estatus_html ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Maestro</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($nombre_maestro) ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Horario</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($materia["materia_horario"] ?: "Sin horario") ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Descripción</label>
                    <div>
                        <?= nl2br(htmlspecialchars($materia["materia_descripcion"])) ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted">Fecha de solicitud</label>
                    <div class="fw-semibold">
                        <?= $materia["inscripcion_fecha_solicitud"] ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>

                    <?php if ($materia["id_estatus_inscripcion"] == 2): ?>
                        <a href="tareas/index.php?id=<?= $materia['id_materia'] ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-journal-text"></i> Ver tareas
                        </a>
                    <?php endif; ?>
                </div>

            </div>

        </main>

    </div>
    <!--Footer-->
    <?php include "../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
