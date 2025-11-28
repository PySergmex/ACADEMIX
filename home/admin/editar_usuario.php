<?php
session_start();
require_once "../../includes/config.php";
require_once "../../includes/conexion.php";

/* ===========================
   VALIDAR SOLO ADMIN
=========================== */
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/* ===========================
   VALIDAR ID
=========================== */
if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET["id"]);

/* ===========================
   CONSULTAR USUARIO
=========================== */
$stmt = $pdo->prepare("
    SELECT *
    FROM usuarios
    WHERE id_usuario = :id
");
$stmt->execute([":id" => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuario no encontrado.");
}

/* ===========================
   LISTAS
=========================== */
$roles = $pdo->query("SELECT * FROM cat_roles")->fetchAll(PDO::FETCH_ASSOC);
$estatus_list = $pdo->query("SELECT * FROM cat_estatus_usuario")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario | Administrador - AcademiX</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="admin-dashboard">

    <!-- TOPBAR -->
    <?php include "../../includes/topbar_admin.php"; ?>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <?php 
            $pagina_activa = "usuarios";
            include "../../includes/sidebar_admin.php"; 
        ?>

        <!-- CONTENIDO -->
        <main class="content-area">

            <?php include "../../includes/alertas_admin.php"; ?>

            <h3 class="fw-bold mb-4">Editar usuario</h3>

            <div class="admin-form-card">

                <form action="procesar_editar.php" method="POST">

                    <input type="hidden" name="id" value="<?= $usuario['id_usuario'] ?>">

                    <div class="mb-3">
                        <label class="form-label">Nombres</label>
                        <input type="text" name="nombres" class="form-control"
                               value="<?= htmlspecialchars($usuario['usuario_nombres']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Apellido paterno</label>
                        <input type="text" name="ap_paterno" class="form-control"
                               value="<?= htmlspecialchars($usuario['usuario_apellido_paterno']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Apellido materno</label>
                        <input type="text" name="ap_materno" class="form-control"
                               value="<?= htmlspecialchars($usuario['usuario_apellido_materno']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="correo" class="form-control"
                               value="<?= htmlspecialchars($usuario['usuario_correo']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rol</label>
                        <select name="rol" class="form-select" required>
                            <?php foreach ($roles as $r): ?>
                                <option value="<?= $r['id_rol'] ?>"
                                    <?= ($usuario['id_rol'] == $r['id_rol']) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($r['rol_nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Estatus</label>
                        <select name="estatus" class="form-select" required>
                            <?php foreach ($estatus_list as $e): ?>
                                <option value="<?= $e['id_estatus_usuario'] ?>"
                                    <?= ($usuario['id_estatus_usuario'] == $e['id_estatus_usuario']) ? "selected" : "" ?>>
                                    <?= htmlspecialchars($e['estatus_usuario_descripcion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>

                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save"></i> Guardar cambios
                        </button>
                    </div>

                </form>

            </div>

        </main>
    </div>

    <!-- FOOTER GLOBAL -->
    <?php include "../../includes/footer.php"; ?>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>

</body>
</html>
