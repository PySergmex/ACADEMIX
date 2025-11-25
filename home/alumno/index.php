<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

/* ===========================
   VALIDAR SOLO ALUMNOS
=========================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 3) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_alumno = $_SESSION["id_usuario"];

/* ===========================
   CONSULTAR MATERIAS INSCRITAS
=========================== */
$sql = "
    SELECT 
        i.id_inscripcion,
        m.id_materia,
        m.materia_nombre,
        m.materia_horario,
        u.usuario_nombres AS maestro,
        i.id_estatus_inscripcion
    FROM inscripciones i
    INNER JOIN materias m ON m.id_materia = i.id_materia
    INNER JOIN usuarios u ON u.id_usuario = m.id_usuario_maestro
    WHERE i.id_usuario_estudiante = :id
    ORDER BY m.materia_nombre ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id_alumno]);
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Materias | Alumno - AcademiX</title>

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
            $pagina_activa = "materias";
            include "../../includes/sidebar_alumno.php";
        ?>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="content-area">

            <?php include "../../includes/alertas_alumno.php"; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold mb-0">Mis materias</h3>

                <a href="alumno_inscrip_materia.php" class="btn btn-primary">
                    <i class="bi bi-journal-plus me-1"></i> Agregar materia
                </a>
            </div>

            <!-- Buscador -->
            <div class="row g-2 mb-3">
                <div class="col-sm-4 col-md-3">
                    <input 
                        type="text"
                        name="busqueda"
                        class="form-control"
                        placeholder="Buscar materia"
                    >
                </div>
            </div>

            <!-- TABLA -->
            <div class="card fade-in">
                <div class="table-responsive">

                    <table class="table table-hover align-middle w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Materia</th>
                                <th>Horario</th>
                                <th>Maestro</th>
                                <th>Estatus</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (empty($materias)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Aún no estás inscrito en ninguna materia.
                                </td>
                            </tr>

                        <?php else: ?>
                            <?php foreach ($materias as $index => $m): ?>

                                <?php 
                                switch ($m["id_estatus_inscripcion"]) {
                                    case 1: 
                                        $estatus = "<span class='badge bg-warning'>Pendiente</span>";
                                        $btn_retirar = true;
                                        break;

                                    case 2: 
                                        $estatus = "<span class='badge bg-success'>Aprobada</span>";
                                        $btn_retirar = false;
                                        break;

                                    case 3: 
                                        $estatus = "<span class='badge bg-danger'>Rechazada</span>";
                                        $btn_retirar = true;
                                        break;

                                    default: 
                                        $estatus = "<span class='badge bg-secondary'>Desconocido</span>";
                                        $btn_retirar = false;
                                        break;
                                }
                                ?>

                                <tr>
                                    <td><?= $index + 1 ?></td>

                                    <td>
                                        <strong><?= htmlspecialchars($m["materia_nombre"]) ?></strong>
                                    </td>

                                    <td><?= htmlspecialchars($m["materia_horario"] ?: "Sin horario") ?></td>

                                    <td><?= htmlspecialchars($m["maestro"]) ?></td>

                                    <td><?= $estatus ?></td>

                                    <td class="text-center">

                                        <!-- VER -->
                                        <a href="ver_alumno_materia.php?id=<?= $m['id_materia'] ?>" 
                                           class="btn btn-sm btn-outline-info me-1">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- RETIRAR (si aplica) -->
                                        <?php if ($btn_retirar): ?>
                                            <a href="retirar_solicitud.php?id=<?= $m['id_inscripcion'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('¿Seguro que deseas retirar la solicitud?');">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
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
