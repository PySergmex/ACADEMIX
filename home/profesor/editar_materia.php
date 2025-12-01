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

/*Obtener datos de la materia*/
$stmt = $pdo->prepare("
    SELECT 
        id_materia,
        materia_nombre,
        materia_descripcion,
        materia_horario,
        id_usuario_maestro
    FROM materias
    WHERE id_materia = :id
    LIMIT 1
");

$stmt->execute([":id" => $id_materia]);
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia || $materia["id_usuario_maestro"] != $id_maestro) {
    die("<h3 style='padding:20px; color:red;'>No tienes permiso para editar esta materia.</h3>");
}

/*Horario*/
$dias_seleccionados = [];
$hora_inicio = "";
$hora_fin = "";

if (!empty($materia["materia_horario"])) {

    // Ejemplo formato: Lun-Mar 08:00-10:00
    if (preg_match("/^([A-Za-zÁÉÍÓÚ\-]+)\s+(\d\d:\d\d)-(\d\d:\d\d)$/", $materia["materia_horario"], $matches)) {
        $dias = explode("-", $matches[1]);
        $dias_seleccionados = $dias;
        $hora_inicio = $matches[2];
        $hora_fin = $matches[3];
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar materia | Profesor - AcademiX</title>
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

        <!--Contenido Principal-->
        <main class="content-area">

            <h3 class="mb-4 fw-bold">Editar materia</h3>

            <div class="admin-form-card p-4 col-lg-6 col-md-8">

                <form action="procesar_editar_materia.php" method="POST">

                    <input type="hidden" name="id_materia" value="<?= $materia["id_materia"] ?>">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label">Nombre de la materia</label>
                        <input 
                            type="text" 
                            name="materia_nombre" 
                            class="form-control"
                            value="<?= htmlspecialchars($materia["materia_nombre"]) ?>"
                            required
                        >
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea 
                            name="materia_descripcion" 
                            class="form-control" 
                            rows="3"
                        ><?= htmlspecialchars($materia["materia_descripcion"]) ?></textarea>
                    </div>

                    <!-- Días -->
                    <div class="mb-3">
                        <label class="form-label">Días de clase</label>

                        <div class="d-flex flex-wrap gap-3">

                            <?php
                            $dias_posibles = ["Lun", "Mar", "Mie", "Jue", "Vie", "Sáb"];
                            foreach ($dias_posibles as $d):
                            ?>
                                <label>
                                    <input 
                                        type="checkbox" 
                                        name="dias[]"
                                        value="<?= $d ?>"
                                        <?= in_array($d, $dias_seleccionados) ? "checked" : "" ?>
                                    >
                                    <?= $d ?>
                                </label>
                            <?php endforeach; ?>

                        </div>

                        <small class="text-muted">
                            Puedes seleccionar uno o varios días.
                        </small>
                    </div>

                    <!-- Horario -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Hora de inicio</label>
                            <input 
                                type="time"
                                name="hora_inicio"
                                class="form-control"
                                value="<?= $hora_inicio ?>"
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Hora de fin</label>
                            <input 
                                type="time"
                                name="hora_fin"
                                class="form-control"
                                value="<?= $hora_fin ?>"
                            >
                        </div>

                        <small class="text-muted ps-3">
                            El horario se generará automáticamente. Ejemplo: <strong>Lun-Mar 08:00-10:00</strong>
                        </small>
                    </div>

                    <!--Botones-->
                    <div class="d-flex justify-content-between">

                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar cambios
                        </button>

                    </div>

                </form>

            </div>

        </main>

    </div>
    <!--Footer-->
    <?php include "../../includes/footer.php"; ?>
    <!--Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!--JS Global-->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
