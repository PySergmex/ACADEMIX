<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

// Solo admin
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

// -----------------------------------------
// Estadísticas principales
// -----------------------------------------

// Total Administradores
$admins = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE id_rol = 1")->fetchColumn();

// Total Profesores
$profes = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE id_rol = 2")->fetchColumn();

// Total Estudiantes
$alumnos = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE id_rol = 3")->fetchColumn();

// Total Materias
$materias = $pdo->query("SELECT COUNT(*) FROM materias")->fetchColumn();

//Estatus de usuario
$estatusData = $pdo->query("
    SELECT e.estatus_usuario_descripcion, COUNT(*) AS total
    FROM usuarios u
    INNER JOIN cat_estatus_usuario e ON e.id_estatus_usuario = u.id_estatus_usuario
    GROUP BY e.id_estatus_usuario
")->fetchAll(PDO::FETCH_ASSOC);

//Promedio de usuario
//Gráfico de barras
$promedios = $pdo->query("
    SELECT m.materia_nombre, 
           AVG(h.historial_calificacion_final) AS promedio
    FROM historial_materias h
    INNER JOIN materias m ON m.id_materia = h.id_materia
    GROUP BY h.id_materia
    ORDER BY promedio DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - AcademiX</title>
    <!-- ICONO -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/imgs/logo-ico.png?v=1">
    <!--Bootsrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
     <!--Iconos Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="admin-dashboard">
<!--Topbar Admin-->
<?php include "../../../includes/topbar_admin.php"; ?>

<div class="d-flex">
    
    <?php $pagina_activa = "dashboard"; ?>
    <!--Sidebar Admin-->
    <?php include "../../../includes/sidebar_admin.php"; ?>

    <main class="content-area">

        <h2 class="mb-4">Dashboard del Administrador</h2>

        <!-- CARDS PRINCIPALES -->
        <div class="row g-4 mb-4">

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $admins ?></div>
                    <div class="text-muted">Administradores</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $profes ?></div>
                    <div class="text-muted">Profesores</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $alumnos ?></div>
                    <div class="text-muted">Estudiantes</div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card p-4 text-center shadow-sm">
                    <div class="fs-1 fw-bold"><?= $materias ?></div>
                    <div class="text-muted">Materias</div>
                </div>
            </div>

        </div>

        <!-- GRÁFICOS -->
        <div class="row g-4">

            <!-- DONUT ESTATUS -->
            <div class="col-md-6">
                <div class="card p-4 shadow-sm">
                    <h5 class="mb-3">Estatus de Usuarios</h5>
                    <canvas id="chartEstatus"></canvas>
                </div>
            </div>

            <!-- PROMEDIOS MATERIAS -->
            <div class="col-md-6">
                <div class="card p-4 shadow-sm">
                    <h5 class="mb-3">Promedio por Materia</h5>
                    <canvas id="chartPromedios"></canvas>
                </div>
            </div>

        </div>

    </main>

</div>
    <!--Footer-->
    <?php include "../../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
    <!--ChartJS-->
    <script type="module">
    import { iniciarContadores, cargarGraficaEstatus, cargarGraficaPromedios } from "<?= BASE_URL ?>assets/js/chart.js";

// Activar contador
iniciarContadores();

// Cargar Donut
cargarGraficaEstatus(
    "chartEstatus",
    <?= json_encode(array_column($estatusData, "estatus_usuario_descripcion")) ?>,
    <?= json_encode(array_column($estatusData, "total")) ?>
);

// Cargar barras
cargarGraficaPromedios(
    "chartPromedios",
    <?= json_encode(array_column($promedios, "materia_nombre")) ?>,
    <?= json_encode(array_map('floatval', array_column($promedios, "promedio"))) ?>
);
</script>

</body>
</html>
