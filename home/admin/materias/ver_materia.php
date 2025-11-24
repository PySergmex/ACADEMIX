<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

// Solo admin
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php");
    exit;
}

$sql = "
    SELECT 
        m.*,
        u.usuario_nombres,
        u.usuario_apellido_paterno,
        u.usuario_correo
    FROM materias m
    LEFT JOIN usuarios u ON u.id_usuario = m.id_usuario_maestro
    WHERE m.id_materia = :id
    LIMIT 1
";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);
$materia = $stmt->fetch();

if (!$materia) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_no_encontrada");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle de materia - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>
<body class="admin-dashboard">

    <?php include "../../../includes/topbar_admin.php"; ?>

    <div class="d-flex">
        <?php $pagina_activa = 'materias'; ?>
        <?php include "../../../includes/sidebar_admin.php"; ?>

        <main class="content-area p-4">
            <h3 class="mb-4 fw-bold">Detalle de la materia</h3>

            <div class="admin-form-card">

                <div class="mb-3">
                    <label class="form-label text-muted">Nombre</label>
                    <div class="fw-bold fs-5">
                        <?= htmlspecialchars($materia['materia_nombre']); ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Descripción</label>
                    <div>
                        <?= nl2br(htmlspecialchars($materia['materia_descripcion'])); ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Maestro</label>
                    <div>
                        <?php if ($materia["usuario_nombres"]): ?>
                            <?= htmlspecialchars($materia["usuario_nombres"] . " " . $materia["usuario_apellido_paterno"]); ?>
                            <br>
                            <small class="text-muted">
                                <?= htmlspecialchars($materia["usuario_correo"]); ?>
                            </small>
                        <?php else: ?>
                            <span class="text-muted">Sin maestro asignado</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted">Horario</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($materia['materia_horario'] ?: 'Sin horario definido'); ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted">Fecha de creación</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($materia['materia_fecha_creacion']); ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>

                    <a href="editar_materia.php?id=<?= $materia['id_materia']; ?>" class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Editar
                    </a>
                </div>

            </div>
        </main>
    </div>

    <!-- FOOTER GLOBAL -->
    <?php include "../../../includes/footer.php"; ?>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>

