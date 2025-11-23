<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

/* ============================================
   PROTEGER SOLO ADMIN
============================================ */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$busqueda = trim($_GET["busqueda"] ?? "");
$param = "%" . $busqueda . "%";

/* ============================================
   CONSULTA DE MATERIAS
============================================ */
try {

    if ($busqueda !== "") {

        $sql = "
            SELECT 
                m.id_materia,
                m.materia_nombre,
                m.materia_descripcion,
                m.materia_horario,
                m.materia_fecha_creacion,
                u.usuario_nombres,
                u.usuario_apellido_paterno,
                u.usuario_correo
            FROM materias m
            LEFT JOIN usuarios u 
                ON m.id_usuario_maestro = u.id_usuario
            WHERE m.materia_nombre LIKE :b
               OR m.materia_descripcion LIKE :b
               OR CONCAT(u.usuario_nombres, ' ', u.usuario_apellido_paterno) LIKE :b
            ORDER BY m.materia_fecha_creacion DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":b", $param);
        $stmt->execute();

    } else {

        $sql = "
            SELECT 
                m.id_materia,
                m.materia_nombre,
                m.materia_descripcion,
                m.materia_horario,
                m.materia_fecha_creacion,
                u.usuario_nombres,
                u.usuario_apellido_paterno,
                u.usuario_correo
            FROM materias m
            LEFT JOIN usuarios u 
                ON m.id_usuario_maestro = u.id_usuario
            ORDER BY m.materia_fecha_creacion DESC
        ";

        $stmt = $pdo->query($sql);
    }

    $materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error al consultar materias: " . $e->getMessage());
}

// Activar sección en sidebar
$pagina_activa = "materias";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Materias - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS Admin -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>

<body class="admin-dashboard">

    <!-- TOPBAR -->
    <?php include "../../../includes/topbar_admin.php"; ?>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <?php include "../../../includes/sidebar_admin.php"; ?>

        <!-- CONTENIDO -->
        <main class="content-area">

            <!-- Alertas -->
            <?php include "../../../includes/alertas_admin.php"; ?>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Materias del sistema</h3>

                <a href="<?= BASE_URL ?>home/admin/materias/registrar_materia.php" 
                   class="btn btn-primary">
                    <i class="bi bi-journal-plus me-1"></i> Nueva materia
                </a>
            </div>

            <!-- BUSCADOR -->
            <form class="row g-2 mb-3" method="get">
                <div class="col-sm-4 col-md-3">
                    <input 
                        type="text" 
                        name="busqueda" 
                        class="form-control"
                        placeholder="Buscar por nombre o maestro"
                        value="<?= htmlspecialchars($busqueda); ?>"
                    >
                </div>

                <div class="col-sm-2">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>

                <?php if ($busqueda !== ""): ?>
                <div class="col-sm-3 col-md-2">
                    <a href="index.php" class="btn btn-link text-decoration-none">
                        Limpiar filtro
                    </a>
                </div>
                <?php endif; ?>
            </form>

            <!-- TABLA -->
            <div class="card">
                <div class="card-body table-responsive">

                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Materia</th>
                                <th>Maestro</th>
                                <th>Horario</th>
                                <th>Creada</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($materias)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        No se encontraron materias.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($materias as $m): ?>
                                <tr>
                                    <td><?= $m["id_materia"]; ?></td>

                                    <td>
                                        <strong><?= htmlspecialchars($m["materia_nombre"]); ?></strong><br>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($m["materia_descripcion"]); ?>
                                        </small>
                                    </td>

                                    <!-- Maestro -->
                                    <td>
                                        <?php if ($m["usuario_nombres"]): ?>
                                            <?= htmlspecialchars($m["usuario_nombres"] . " " . $m["usuario_apellido_paterno"]); ?>
                                            <br>
                                            <small class="text-muted">
                                                <?= htmlspecialchars($m["usuario_correo"]); ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="text-muted">Sin maestro asignado</span>
                                        <?php endif; ?>
                                    </td>

                                    <td><?= htmlspecialchars($m["materia_horario"]); ?></td>

                                    <td><?= $m["materia_fecha_creacion"]; ?></td>

                                    <td class="text-center">
                                        <a href="ver_materia.php?id=<?= $m['id_materia']; ?>"
                                           class="btn btn-sm btn-outline-info me-1">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <a href="editar_materia.php?id=<?= $m['id_materia']; ?>"
                                           class="btn btn-sm btn-outline-warning me-1">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <a href="eliminar_materia.php?id=<?= $m['id_materia']; ?>"
                                           onclick="return confirm('¿Seguro que deseas eliminar esta materia?');"
                                           class="btn btn-sm btn-outline-danger">
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

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {
        if (typeof iniciarBuscadorEnTiempoReal === "function") {
            iniciarBuscadorEnTiempoReal("input[name='busqueda']", "table");
        }
    });
    </script>

</body>
</html>
