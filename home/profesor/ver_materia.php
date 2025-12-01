<?php
session_start();
require_once "../../includes/conexion.php";
require_once "../../includes/config.php";

/*Validar solo profesores*/
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = $_SESSION["id_usuario"];

/*Validar ID*/
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id_materia = intval($_GET["id"]);

/*Obtener los datos de la materia*/
try {
    $sql = "
        SELECT 
            m.id_materia,
            m.materia_nombre,
            m.materia_descripcion,
            m.materia_horario,
            m.materia_fecha_creacion
        FROM materias m
        WHERE m.id_materia = :id
          AND m.id_usuario_maestro = :maestro
        LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":id" => $id_materia,
        ":maestro" => $id_maestro
    ]);

    $materia = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$materia) {
        die("<h3 style='color:red; padding:20px;'>No tienes permiso para ver esta materia.</h3>");
    }

} catch (PDOException $e) {
    die("Error al cargar la materia: " . $e->getMessage());
}

/*Alumnos Inscritos*/
$sqlInscritos = "
    SELECT 
        i.id_inscripcion,
        u.usuario_nombres,
        u.usuario_apellido_paterno,
        u.usuario_correo,
        i.inscripcion_fecha_solicitud
    FROM inscripciones i
    INNER JOIN usuarios u ON i.id_usuario_estudiante = u.id_usuario
    WHERE i.id_materia = :id
      AND i.id_estatus_inscripcion = 2  -- Aprobado
    ORDER BY u.usuario_nombres
";
$stmt = $pdo->prepare($sqlInscritos);
$stmt->execute([":id" => $id_materia]);
$inscritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*Solicitudes Pendientes*/
$sqlPendientes = "
    SELECT 
        i.id_inscripcion,
        u.usuario_nombres,
        u.usuario_apellido_paterno,
        u.usuario_correo,
        i.inscripcion_fecha_solicitud
    FROM inscripciones i
    INNER JOIN usuarios u ON i.id_usuario_estudiante = u.id_usuario
    WHERE i.id_materia = :id
      AND i.id_estatus_inscripcion = 1  -- Pendiente
    ORDER BY i.inscripcion_fecha_solicitud ASC
";
$stmt = $pdo->prepare($sqlPendientes);
$stmt->execute([":id" => $id_materia]);
$pendientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($materia["materia_nombre"]) ?> - Profesor | AcademiX</title>
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
    <?php include "../../includes/topbar_profesor.php"; ?>
    <div class="d-flex">
     <!--Sidebar Profesor-->
        <?php 
            $pagina_activa = "materias";
            include "../../includes/sidebar_profesor.php"; 
        ?>
        <!-- CONTENIDO -->
        <main class="content-area">

            <h3 class="fw-bold mb-4">
                <?= htmlspecialchars($materia["materia_nombre"]) ?>
            </h3>

            <!-- INFO DE LA MATERIA -->
            <div class="card p-4 mb-4 fade-in">
                <h5 class="fw-bold mb-2">Información de la materia</h5>

                <p><strong>Descripción:</strong><br>
                    <?= nl2br(htmlspecialchars($materia["materia_descripcion"])) ?>
                </p>

                <p><strong>Horario:</strong><br>
                    <?= htmlspecialchars($materia["materia_horario"] ?: "Sin horario") ?>
                </p>

                <p><strong>Creada el:</strong><br>
                    <?= $materia["materia_fecha_creacion"] ?>
                </p>


                <div class="d-flex justify-content-between mt-3">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>

                    <a href="editar_materia.php?id=<?= $materia["id_materia"] ?>"
                       class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Editar materia
                    </a>
                </div>

            </div>

            <!-- SOLICITUDES PENDIENTES -->
            <div class="card p-4 mb-4 fade-in">
                <h5 class="fw-bold mb-3">Solicitudes de inscripción</h5>

                <?php if (empty($pendientes)): ?>
                    <p class="text-muted">No hay solicitudes pendientes.</p>
                <?php else: ?>

                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Correo</th>
                                <th>Fecha solicitud</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($pendientes as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p["usuario_nombres"] . " " . $p["usuario_apellido_paterno"]) ?></td>

                                <td><?= htmlspecialchars($p["usuario_correo"]) ?></td>

                                <td><?= $p["inscripcion_fecha_solicitud"] ?></td>

                                <td class="text-center">
                                    <a href="procesar_inscripcion.php?id=<?= $p['id_inscripcion'] ?>&accion=aprobar"
                                        class="btn btn-sm btn-success me-2">
                                        <i class="bi bi-check-circle"></i>
                                    </a>

                                    <a href="procesar_inscripcion.php?id=<?= $p['id_inscripcion'] ?>&accion=rechazar"
                                        class="btn btn-sm btn-danger">
                                        <i class="bi bi-x-circle"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                <?php endif; ?>
            </div>

            <!-- ALUMNOS INSCRITOS -->
            <div class="card p-4 fade-in">
                <h5 class="fw-bold mb-3">Alumnos inscritos</h5>

                <?php if (empty($inscritos)): ?>
                    <p class="text-muted">Aún no hay alumnos inscritos en esta materia.</p>
                <?php else: ?>

                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Correo</th>
                                <th>Inscrito desde</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($inscritos as $i): ?>
                            <tr>
                                <td>
                                    <?= htmlspecialchars($i["usuario_nombres"] . " " . $i["usuario_apellido_paterno"]) ?>
                                </td>

                                <td><?= htmlspecialchars($i["usuario_correo"]) ?></td>

                                <td><?= $i["inscripcion_fecha_solicitud"] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>

                <?php endif; ?>
                                
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

