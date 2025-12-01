<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/*Validar solo profesores*/
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = $_SESSION["id_usuario"];
$busqueda = $_GET["busqueda"] ?? "";
$param = "%" . $busqueda . "%";

/*Consulta Maestros*/
try {

    $sql = "
        SELECT 
            i.id_inscripcion,
            i.inscripcion_fecha_solicitud,
            i.id_estatus_inscripcion,
            est.estatus_inscripcion_descripcion AS estatus,
            
            m.id_materia,
            m.materia_nombre,

            u.id_usuario AS id_estudiante,
            u.usuario_nombres,
            u.usuario_apellido_paterno,
            u.usuario_correo

        FROM inscripciones i
        INNER JOIN materias m ON m.id_materia = i.id_materia
        INNER JOIN usuarios u ON u.id_usuario = i.id_usuario_estudiante
        INNER JOIN cat_estatus_inscripcion est ON est.id_estatus_inscripcion = i.id_estatus_inscripcion
        WHERE m.id_usuario_maestro = :id
    ";

    /*Filtro de busqueda*/
    if ($busqueda !== "") {
        $sql .= "
            AND (
                u.usuario_nombres LIKE :b
                OR u.usuario_apellido_paterno LIKE :b
                OR u.usuario_correo LIKE :b
                OR m.materia_nombre LIKE :b
            )
        ";
    }

    $sql .= " ORDER BY i.inscripcion_fecha_solicitud DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id_maestro, PDO::PARAM_INT);

    if ($busqueda !== "") {
        $stmt->bindParam(":b", $param, PDO::PARAM_STR);
    }

    $stmt->execute();
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar solicitudes: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Solicitudes de inscripción - Profesor | AcademiX</title>
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
            $pagina_activa = "inscripciones";
            include "../../../includes/sidebar_profesor.php";
        ?>

        <main class="content-area p-4">

            <!--Alertas-->
            <?php include "../../../includes/alertas_profesor.php"; ?>

            <h3 class="mb-3">Solicitudes de inscripción</h3>

            <!-- BUSCADOR -->
            <form class="row g-2 mb-3" method="get">
                <div class="col-sm-4 col-md-3">
                    <input 
                        type="text" 
                        name="busqueda" 
                        class="form-control"
                        placeholder="Buscar alumno o materia"
                        value="<?= htmlspecialchars($busqueda) ?>"
                    >
                </div>

                <div class="col-sm-3 col-md-2">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                <?php if ($busqueda !== ""): ?>
                <div class="col-sm-3 col-md-2">
                    <a href="index.php" class="btn btn-link text-decoration-none">Limpiar</a>
                </div>
                <?php endif; ?>
            </form>

            <!-- TABLA -->
            <div class="card">
                <div class="card-body table-responsive">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Alumno</th>
                                <th>Materia</th>
                                <th>Fecha solicitud</th>
                                <th>Estatus</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($solicitudes)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No hay solicitudes de inscripción.
                                    </td>
                                </tr>

                            <?php else: ?>
                                <?php foreach ($solicitudes as $s): ?>
                                <tr>
                                    <td><?= $s["id_inscripcion"] ?></td>

                                    <td>
                                        <strong><?= htmlspecialchars($s["usuario_nombres"] . " " . $s["usuario_apellido_paterno"]) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($s["usuario_correo"]) ?></small>
                                    </td>

                                    <td><?= htmlspecialchars($s["materia_nombre"]) ?></td>

                                    <td><?= $s["inscripcion_fecha_solicitud"] ?></td>

                                    <td>
                                        <?php
                                            $badge = "secondary";
                                            if ($s["id_estatus_inscripcion"] == 1) $badge = "warning";
                                            if ($s["id_estatus_inscripcion"] == 2) $badge = "success";
                                            if ($s["id_estatus_inscripcion"] == 3) $badge = "danger";
                                        ?>
                                        <span class="badge bg-<?= $badge ?>">
                                            <?= htmlspecialchars($s["estatus"]) ?>
                                        </span>
                                    </td>

                                    <td class="text-center">

                                        <?php if ($s["id_estatus_inscripcion"] == 1): ?>
                                            <!-- FORMULARIO APROBAR -->
                                            <form method="POST" action="procesar_inscripcion.php" class="d-inline">
                                                <input type="hidden" name="id_inscripcion" value="<?= $s['id_inscripcion'] ?>">
                                                <input type="hidden" name="accion" value="aprobar">
                                                <button class="btn btn-sm btn-success" title="Aprobar">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>

                                            <!-- FORMULARIO RECHAZAR -->
                                            <form method="POST" action="procesar_inscripcion.php" class="d-inline">
                                                <input type="hidden" name="id_inscripcion" value="<?= $s['id_inscripcion'] ?>">
                                                <input type="hidden" name="accion" value="rechazar">
                                                <button class="btn btn-sm btn-danger" title="Rechazar">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small">Sin acciones</span>
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
    <?php include "../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
