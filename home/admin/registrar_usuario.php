<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

// Solo acceso admin
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

// Catálogos
$roles = $pdo->query("SELECT * FROM cat_roles")->fetchAll();
$estatus_list = $pdo->query("SELECT * FROM cat_estatus_usuario")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar usuario - AcademiX</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/admin.css">
</head>

<body class="admin-dashboard">

    <!-- TOPBAR -->
    <?php include "../../includes/topbar_admin.php"; ?>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <?php $pagina_activa = "usuarios"; ?>
        <?php include "../../includes/sidebar_admin.php"; ?>

        <!-- CONTENIDO -->
        <main class="content-area p-4">

            <?php include "../../includes/alertas_admin.php"; ?>

            <h3 class="mb-4">Registrar nuevo usuario</h3>

            <div class="admin-form-card p-4 col-lg-6 col-md-8">

                <form action="<?= BASE_URL ?>home/admin/procesar_registro.php" method="POST">

                    <div class="mb-3">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="nombres" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Apellido paterno</label>
                        <input type="text" name="ap_paterno" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Apellido materno</label>
                        <input type="text" name="ap_materno" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol" class="form-select" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id_rol'] ?>">
                                    <?= htmlspecialchars($r['rol_nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Estatus</label>
                        <select name="estatus" class="form-select" required>
                            <?php foreach ($estatus_list as $e): ?>
                                <option value="<?= $e['id_estatus_usuario'] ?>">
                                    <?= htmlspecialchars($e['estatus_usuario_descripcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="<?= BASE_URL ?>home/admin/index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Registrar usuario
                        </button>
                    </div>

                </form>

            </div>
        </main>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>

</body>
</html>
