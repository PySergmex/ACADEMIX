<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

/* Validar profesor */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* Validar materia */
if (!isset($_GET["id_materia"])) {
    header("Location: select_materias.php");
    exit;
}

$id_maestro  = (int) $_SESSION["id_usuario"];
$id_materia = (int) $_GET["id_materia"];

/* Confirmar que la materia pertenece al maestro */
$sql = "
    SELECT materia_nombre, materia_descripcion
    FROM materias
    WHERE id_materia = :id_materia
      AND id_usuario_maestro = :id_maestro
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":id_materia" => $id_materia,
    ":id_maestro" => $id_maestro
]);

$materia = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$materia) {
    die("No tienes permiso para ver esta materia.");
}

/* Buscador */
$busqueda = $_GET["busqueda"] ?? "";
$param = "%".$busqueda."%";

/* Consultar tareas */
if ($busqueda !== "") {

    $sql = "
        SELECT *
        FROM tareas
        WHERE id_materia = :id
          AND (tarea_titulo LIKE :b1 OR tarea_descripcion LIKE :b2)
        ORDER BY tarea_fecha_creacion DESC
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":id" => $id_materia,
        ":b1" => $param,
        ":b2" => $param
    ]);

} else {

    $sql = "
        SELECT *
        FROM tareas
        WHERE id_materia = :id
        ORDER BY tarea_fecha_creacion DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $id_materia]);
}

$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tareas | Profesor - AcademiX</title>
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
            <!--Seleccionar Otra Materia--->
            <a href="select_materias.php" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left"></i> Seleccionar otra materia
            </a>

            <h1 class="h3 mb-1">
                <?= htmlspecialchars($materia["materia_nombre"]) ?>
            </h1>

            <p class="text-muted mb-4">
                <?= htmlspecialchars($materia["materia_descripcion"] ?: "Sin descripción") ?>
            </p>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 mb-0">Tareas</h2>

                <a href="crear_tarea.php?id_materia=<?= $id_materia ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    Nueva tarea
                </a>
            </div>

            <!-- Buscador -->
            <form class="row g-2 mb-3" method="get">
                <input type="hidden" name="id_materia" value="<?= $id_materia ?>">

                <div class="col-sm-4 col-md-3">
                    <input 
                        type="text"
                        name="busqueda"
                        class="form-control"
                        placeholder="Buscar tarea"
                        value="<?= htmlspecialchars($busqueda) ?>"
                    >
                </div>

                <div class="col-sm-3 col-md-2">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="bi bi-search"></i>
                        Buscar
                    </button>
                </div>

                <?php if ($busqueda !== ""): ?>
                    <div class="col-sm-3 col-md-2">
                        <a href="index.php?id_materia=<?= $id_materia ?>" class="btn btn-link">
                            Limpiar
                        </a>
                    </div>
                <?php endif; ?>
            </form>

            <!-- Formulario -->
            <div class="card fade-in">
                <div class="card-body table-responsive">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Título</th>
                                <th>Fecha límite</th>
                                <th>Ponderación</th>
                                <th>Creada</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($tareas)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No hay tareas registradas.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tareas as $t): ?>
                                    <tr>
                                        <td><?= $t["id_tarea"] ?></td>

                                        <td>
                                            <strong><?= htmlspecialchars($t["tarea_titulo"]) ?></strong><br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars(substr($t["tarea_descripcion"], 0, 60)) ?>...
                                            </small>
                                        </td>

                                        <td><?= $t["tarea_fecha_limite"] ?></td>

                                        <td><?= $t["tarea_ponderacion"] ?>%</td>

                                        <td><?= $t["tarea_fecha_creacion"] ?></td>

                                        <td class="text-center">
                                            <a href="ver_tarea.php?id=<?= $t['id_tarea'] ?>" 
                                               class="btn btn-sm btn-outline-info me-1">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="editar_tarea.php?id=<?= $t['id_tarea'] ?>" 
                                               class="btn btn-sm btn-outline-warning me-1">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>

                                            <a href="eliminar_tarea.php?id=<?= $t['id_tarea'] ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('¿Eliminar esta tarea?');">
                                                <i class="bi bi-trash"></i>
                                            </a>
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
    <!-- Footer -->
    <?php include "../../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
    <!--Buscador-->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        if (typeof iniciarBuscadorEnTiempoReal === "function") {
            iniciarBuscadorEnTiempoReal(
                "input[name='busqueda']",
                "table"
            );
        }
    });
    </script>

</body>
</html>


