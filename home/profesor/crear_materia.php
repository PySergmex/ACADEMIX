<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

/* ============================
   VALIDAR SOLO PROFESORES
============================ */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = (int) $_SESSION["id_usuario"];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear materia | Profesor - AcademiX</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">

</head>

<body class="prof-dashboard">

<?php include "../../includes/topbar_profesor.php"; ?>

<div class="d-flex">

    <?php 
        $pagina_activa = "materias";
        include "../../includes/sidebar_profesor.php"; 
    ?>

    <main class="content-area p-4">

        <?php include "../../includes/alertas_profesor.php"; ?>

        <h3 class="mb-3">Crear nueva materia</h3>

        <div class="card admin-form-card shadow-sm">
            <div class="card-body">

                <form action="procesar_crear_materia.php" method="POST">
                    <!-- El id del maestro se toma de la sesión en procesar_crear_materia.php -->

                    <!-- NOMBRE -->
                    <div class="mb-3">
                        <label class="form-label">Nombre de la materia</label>
                        <input 
                            type="text" 
                            name="materia_nombre" 
                            class="form-control" 
                            placeholder="Ej. Matemáticas I"
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
                            placeholder="Descripción breve de la materia..."
                        ></textarea>
                    </div>

                    <!-- DÍAS DE CLASE -->
                    <div class="mb-3">
                        <label class="form-label">Días de clase</label><br>

                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="dias[]" value="Lun">
                            <label class="form-check-label">Lun</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="dias[]" value="Mar">
                            <label class="form-check-label">Mar</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="dias[]" value="Mie">
                            <label class="form-check-label">Mie</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="dias[]" value="Jue">
                            <label class="form-check-label">Jue</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="dias[]" value="Vie">
                            <label class="form-check-label">Vie</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="dias[]" value="Sáb">
                            <label class="form-check-label">Sáb</label>
                        </div>

                        <div class="form-text">
                            Puedes seleccionar uno o varios días.
                        </div>
                    </div>

                    <!-- HORARIO -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Hora de inicio</label>
                            <input 
                                type="time" 
                                name="hora_inicio" 
                                class="form-control" 
                                required
                            >
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Hora de fin</label>
                            <input 
                                type="time" 
                                name="hora_fin" 
                                class="form-control" 
                                required
                            >
                        </div>
                    </div>

                    <div class="form-text mb-3">
                        El horario se generará automáticamente. Ejemplo: <strong>Lun-Mar 08:00-10:00</strong>
                    </div>

                    <!-- BOTONES -->
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-square me-1"></i> Crear materia
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </main>

</div>

<?php include "../../includes/footer.php"; ?>

<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS global -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

</body>
</html>



