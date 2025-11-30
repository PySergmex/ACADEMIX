<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/* ===========================
   VALIDAR SOLO PROFESORES
=========================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id_maestro = (int) $_SESSION["id_usuario"];

/* ===========================
   BUSCADOR
=========================== */
$busqueda = $_GET["busqueda"] ?? "";
$param = "%" . $busqueda . "%";

/* ===========================
   CONSULTAR MATERIAS DEL PROFESOR
=========================== */
try {

    if ($busqueda !== "") {
        // Con filtro de búsqueda
        $sql = "
            SELECT 
                id_materia,
                materia_nombre,
                materia_descripcion,
                materia_horario,
                materia_fecha_creacion
            FROM materias
            WHERE id_usuario_maestro = :id
              AND materia_activa = 1
              AND (
                    materia_nombre      LIKE :b
                 OR materia_descripcion LIKE :b
                 OR materia_horario     LIKE :b
              )
            ORDER BY materia_fecha_creacion DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(":id", $id_maestro, PDO::PARAM_INT);
        $stmt->bindValue(":b",  $param,      PDO::PARAM_STR);
        $stmt->execute();

    } else {
        // Sin filtro
        $sql = "
            SELECT 
                id_materia,
                materia_nombre,
                materia_descripcion,
                materia_horario,
                materia_fecha_creacion
            FROM materias
            WHERE id_usuario_maestro = :id
              AND materia_activa = 1
            ORDER BY materia_fecha_creacion DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([":id" => $id_maestro]);
    }

    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al consultar materias: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Calificaciones | Profesor - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="prof-dashboard">

<!-- TOPBAR -->
<?php include "../../../includes/topbar_profesor.php"; ?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <?php
        $pagina_activa = "calificaciones";
        include "../../../includes/sidebar_profesor.php";
    ?>

    <!-- CONTENIDO -->
    <main class="content-area p-4">

        <?php include "../../../includes/alertas_profesor.php"; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold mb-0">Alumnos / Calificaciones</h3>
        </div>

        <!-- BUSCADOR -->
        <form class="row g-2 mb-3" method="get">
            <div class="col-sm-4 col-md-3">
                <input 
                    type="text" 
                    name="busqueda"
                    class="form-control"
                    placeholder="Buscar materia"
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
                <a href="index.php" class="btn btn-link text-decoration-none">Limpiar filtro</a>
            </div>
            <?php endif; ?>
        </form>

        <!-- TABLA MATERIAS -->
        <div class="card fade-in">
            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Materia</th>
                            <th>Horario</th>
                            <th>Creada</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($materias)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No tienes materias activas.
                                </td>
                            </tr>

                        <?php else: ?>
                            <?php foreach ($materias as $m): ?>
                                <tr>
                                    <td><?= (int) $m["id_materia"] ?></td>

                                    <td>
                                        <strong><?= htmlspecialchars($m["materia_nombre"]) ?></strong><br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($m["materia_descripcion"] ?: "Sin descripción") ?>
                                        </small>
                                    </td>

                                    <td><?= htmlspecialchars($m["materia_horario"] ?: "Sin horario") ?></td>

                                    <td><?= htmlspecialchars($m["materia_fecha_creacion"]) ?></td>

                                    <td class="text-center">
                                        <a href="ver_alumnos.php?id_materia=<?= (int) $m['id_materia'] ?>"
                                           class="btn btn-sm btn-primary">
                                            <i class="bi bi-people"></i> Ver alumnos
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

<!-- FOOTER -->
<?php include "../../../includes/footer.php"; ?>
<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS global -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

<!-- Buscador en tiempo real -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    iniciarBuscadorEnTiempoReal(
        "input[name='busqueda']",
        "table"
    );
});
</script>

</body>
</html>
