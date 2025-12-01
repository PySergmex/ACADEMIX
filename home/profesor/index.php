<?php
session_start();
require_once "../../includes/conexion.php";
require_once "../../includes/config.php";
/* Solo profesores */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}
$id_maestro = (int) $_SESSION["id_usuario"];
// Buscador (para que el value del input no se pierda)
$busqueda = trim($_GET["busqueda"] ?? "");
/* Consultar materias del maestro (solo activas) */
try {
    $sql = "
        SELECT 
            id_materia,
            materia_nombre,
            materia_descripcion,
            materia_horario,
            materia_fecha_creacion
        FROM materias
        WHERE id_usuario_maestro = :id_maestro
          AND materia_activa = 1
        ORDER BY materia_fecha_creacion DESC
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
    <title>Mis Materias - Profesor | AcademiX</title>
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
        <main class="content-area p-4">
            <!--Alertas-->
            <?php include "../../includes/alertas_profesor.php"; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0 fw-bold">Mis materias</h3>

                <a href="crear_materia.php" class="btn btn-primary">
                    <i class="bi bi-journal-plus me-1"></i>
                    Nueva materia
                </a>
            </div>
            <!-- BUSCADOR -->
            <div class="row g-2 mb-3">
                <div class="col-sm-4 col-md-3">
                    <input 
                        type="text"
                        name="busqueda"
                        class="form-control"
                        placeholder="Buscar materia"
                        value="<?= htmlspecialchars($busqueda) ?>"
                    >
                </div>
            </div>
            <!-- TABLA DE MATERIAS -->
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
                                        No tienes materias asignadas.
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

                                        <td>
                                            <?= htmlspecialchars($m["materia_horario"] ?: "Sin horario") ?>
                                        </td>

                                        <td><?= htmlspecialchars($m["materia_fecha_creacion"]) ?></td>

                                        <td class="text-center">
                                            <a href="ver_materia.php?id=<?= (int) $m['id_materia'] ?>"
                                               class="btn btn-sm btn-outline-info me-1">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="editar_materia.php?id=<?= (int) $m['id_materia'] ?>"
                                               class="btn btn-sm btn-outline-warning me-1">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            
                                            <a href="desactivar_materia.php?id=<?= (int) $m['id_materia'] ?>"
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="return confirm('¿Desactivar esta materia?');">
                                                <i class="bi bi-slash-circle"></i>
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
    <!--Footer-->
    <?php include "../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
    <!-- Buscadr JS-->
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

