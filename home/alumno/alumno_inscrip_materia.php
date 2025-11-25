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

$id_alumno = (int) $_SESSION["id_usuario"];

/* ===========================
   MATERIAS DISPONIBLES
   - Activas
   - En las que el alumno NO tenga inscripción previa
=========================== */
$sql = "
    SELECT 
        m.id_materia,
        m.materia_nombre,
        m.materia_horario,
        u.usuario_nombres,
        u.usuario_apellido_paterno
    FROM materias m
    INNER JOIN usuarios u ON u.id_usuario = m.id_usuario_maestro
    WHERE m.materia_activa = 1
      AND NOT EXISTS (
            SELECT 1 
            FROM inscripciones i
            WHERE i.id_materia = m.id_materia
              AND i.id_usuario_estudiante = :id_alumno
      )
    ORDER BY m.materia_nombre ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([":id_alumno" => $id_alumno]);
$materias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inscribirme a materias | Alumno - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS tablero -->
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
                <h3 class="fw-bold mb-0">Inscribirme a materias</h3>

                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a mis materias
                </a>
            </div>

            <p class="text-muted mb-3">
                Selecciona una materia disponible y envía una solicitud de inscripción. 
                El profesor deberá aprobarla.
            </p>

            <!-- Buscador -->
            <div class="row g-2 mb-3">
                <div class="col-sm-4 col-md-3">
                    <input 
                        type="text"
                        name="busqueda"
                        class="form-control"
                        placeholder="Buscar materia o maestro"
                    >
                </div>
            </div>

            <!-- TABLA DE MATERIAS DISPONIBLES -->
            <div class="alumno-card fade-in">
                <div class="table-responsive">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Materia</th>
                                <th>Maestro</th>
                                <th>Horario</th>
                                <th class="text-center">Acción</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (empty($materias)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No hay materias disponibles para inscribirte en este momento.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($materias as $m): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($m["materia_nombre"]) ?></strong>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($m["usuario_nombres"] . " " . $m["usuario_apellido_paterno"]) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($m["materia_horario"] ?: "Sin horario") ?>
                                    </td>

                                    <td class="text-center">
                                        <form 
                                            method="POST" 
                                            action="procesar_alumno_inscrip.php"
                                            class="d-inline"
                                        >
                                            <input type="hidden" name="id_materia" value="<?= (int) $m['id_materia'] ?>">

                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-journal-plus me-1"></i>
                                                Inscribirme
                                            </button>
                                        </form>
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
