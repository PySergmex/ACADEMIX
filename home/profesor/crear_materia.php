<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

/* Validar que sea profesor */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = $_SESSION["id_usuario"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar materia | Profesor - AcademiX</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
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
    <main class="content-area p-4">
        <!--Alertas-->
        <?php include "../../includes/alertas_profesor.php"; ?>

        <h3 class="mb-4 fw-bold">Registrar nueva materia</h3>

        <div class="admin-form-card">

            <form action="procesar_crear_materia.php" method="POST">

                <input type="hidden" name="id_usuario_maestro" value="<?= $id_maestro ?>">

                <!-- NOMBRE -->
                <div class="mb-3">
                    <label class="form-label">Nombre de la materia</label>
                    <input 
                        type="text" 
                        name="materia_nombre" 
                        class="form-control" 
                        placeholder="Ejemplo: Física 1"
                        required
                    >
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea 
                        name="materia_descripcion" 
                        class="form-control" 
                        rows="3"
                        placeholder="Describe brevemente la materia..."
                    ></textarea>
                </div>

                <!-- DÍAS -->
                <div class="mb-3">
                    <label class="form-label">Días de clase</label>

                    <div class="d-flex flex-wrap gap-3 pt-1">
                        <label><input type="checkbox" name="dias[]" value="Lun"> Lun</label>
                        <label><input type="checkbox" name="dias[]" value="Mar"> Mar</label>
                        <label><input type="checkbox" name="dias[]" value="Mie"> Mie</label>
                        <label><input type="checkbox" name="dias[]" value="Jue"> Jue</label>
                        <label><input type="checkbox" name="dias[]" value="Vie"> Vie</label>
                        <label><input type="checkbox" name="dias[]" value="Sáb"> Sáb</label>
                    </div>

                    <small class="text-muted">
                        Puedes seleccionar uno o varios días.
                    </small>
                </div>

                <!-- HORARIO -->
                <div class="row g-3 mb-4">

                    <div class="col-md-6">
                        <label class="form-label">Hora de inicio</label>
                        <select name="hora_inicio" class="form-select">
                            <option value="">Selecciona...</option>
                            <option>07:00</option>
                            <option>08:00</option>
                            <option>09:00</option>
                            <option>10:00</option>
                            <option>11:00</option>
                            <option>12:00</option>
                            <option>13:00</option>
                            <option>14:00</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Hora de fin</label>
                        <select name="hora_fin" class="form-select">
                            <option value="">Selecciona...</option>
                            <option>08:00</option>
                            <option>09:00</option>
                            <option>10:00</option>
                            <option>11:00</option>
                            <option>12:00</option>
                            <option>13:00</option>
                            <option>14:00</option>
                            <option>15:00</option>
                        </select>
                    </div>

                    <small class="text-muted ps-3">
                        El horario se formará automáticamente. Ejemplo:
                        <strong>Lun-Mar 08:00-10:00</strong>
                    </small>
                </div>

                <!-- BOTONES -->
                <div class="d-flex justify-content-between mt-3">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-journal-plus"></i> Guardar materia
                    </button>
                </div>

            </form>

        </div>

    </main>

</div>
    <!-- FOOTER GLOBAL -->
    <?php include "../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
