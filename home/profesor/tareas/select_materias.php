<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

/* Validar que sea profesor */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = (int) $_SESSION["id_usuario"];

/* Obtener materias asignadas */
try {
    $sql = "
        SELECT 
            id_materia,
            materia_nombre,
            materia_descripcion
        FROM materias
        WHERE id_usuario_maestro = :id_maestro
        ORDER BY materia_nombre ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id_maestro", $id_maestro, PDO::PARAM_INT);
    $stmt->execute();
    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al cargar materias: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seleccionar materia | Profesor - AcademiX</title>
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
        $pagina_activa = "tareas";
        include "../../../includes/sidebar_profesor.php";
    ?>

    <main class="content-area p-4">
        <!--Alertas-->
        <?php include "../../../includes/alertas_profesor.php"; ?>

        <h1 class="mb-1 h3">Tareas por materia</h1>
        <p class="text-muted mb-4">Selecciona una materia para administrar sus tareas.</p>

        <div class="card fade-in">
            <div class="card-body">

                <?php if (empty($materias)): ?>
                    <p class="text-muted mb-0">No tienes materias asignadas por el momento.</p>
                <?php else: ?>

                    <ul class="list-group">
                        <?php foreach ($materias as $m): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><?= htmlspecialchars($m["materia_nombre"]) ?></strong><br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($m["materia_descripcion"] ?: "Sin descripción") ?>
                                    </small>
                                </div>

                                <a href="index.php?id_materia=<?= (int) $m["id_materia"] ?>"
                                   class="btn btn-primary">
                                    <i class="bi bi-journal-text me-1"></i>
                                    Ver tareas
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                <?php endif; ?>

            </div>
        </div>

    </main>

</div>
    <!-- Footer -->
    <?php include "../../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>


