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
    SELECT 
        u.*,
        r.rol_nombre,
        e.estatus_usuario_descripcion AS estatus
    FROM usuarios u
    INNER JOIN cat_roles r ON r.id_rol = u.id_rol
    INNER JOIN cat_estatus_usuario e ON e.id_estatus_usuario = u.id_estatus_usuario
    WHERE u.id_usuario = :id
");
$stmt->execute([":id" => $id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header("Location: index.php?error=usuario_no_encontrado");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil del usuario | Administrador - AcademiX</title>

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

            <h3 class="fw-bold mb-4">Perfil del usuario</h3>

            <div class="admin-form-card p-4">

                <!-- CAMPO -->
                <div class="mb-3">
                    <label class="form-label text-muted">Nombre completo</label>
                    <div class="fw-bold fs-5">
                        <?= htmlspecialchars(
                            $usuario["usuario_nombres"] . " " .
                            $usuario["usuario_apellido_paterno"] . " " .
                            $usuario["usuario_apellido_materno"]
                        ); ?>
                    </div>
                </div>

                <!-- CAMPO -->
                <div class="mb-3">
                    <label class="form-label text-muted">Correo electrónico</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($usuario["usuario_correo"]); ?>
                    </div>
                </div>

                <!-- CAMPO -->
                <div class="mb-3">
                    <label class="form-label text-muted">Rol</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($usuario["rol_nombre"]); ?>
                    </div>
                </div>

                <!-- CAMPO -->
                <div class="mb-3">
                    <label class="form-label text-muted">Estatus</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($usuario["estatus"]); ?>
                    </div>
                </div>

                <!-- CAMPO -->
                <div class="mb-3">
                    <label class="form-label text-muted">Fecha de creación</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($usuario["usuario_fecha_creacion"]); ?>
                    </div>
                </div>

                <!-- CAMPO -->
                <div class="mb-4">
                    <label class="form-label text-muted">Última actualización</label>
                    <div class="fw-semibold">
                        <?= htmlspecialchars($usuario["usuario_fecha_actualizacion"]); ?>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>

                    <a href="editar_usuario.php?id=<?= $usuario["id_usuario"]; ?>"
                       class="btn btn-primary">
                        <i class="bi bi-pencil-square"></i> Editar usuario
                    </a>
                </div>

            </div>

        </main>
    </div>

    <!-- FOOTER -->
    <?php include "../../includes/footer.php"; ?>

    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>

</body>
</html>
