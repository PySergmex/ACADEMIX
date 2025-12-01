<?php
session_start();

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit;
}

$id_usuario = intval($_GET["id"]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

<div class="card p-4" style="width: 360px;">

    <h4 class="mb-3 text-center">Restablecer contraseña</h4>

    <?php include "includes/alertas_login.php"; ?>

    <form action="includes/procesar_restablecer.php" method="POST">

        <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">

        <label class="form-label">Nueva contraseña</label>
        <input type="password" name="password" class="form-control" required>

        <label class="form-label mt-3">Confirmar contraseña</label>
        <input type="password" name="password2" class="form-control" required>

        <button class="btn btn-primary w-100 mt-3">Guardar</button>

    </form>

    <a href="index.php" class="d-block mt-3 text-center">Volver al login</a>

</div>

</body>
</html>

