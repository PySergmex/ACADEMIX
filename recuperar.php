<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

<div class="card p-4" style="width: 350px;">

    <h4 class="mb-3 text-center">Recuperar contraseña</h4>

    <?php include "includes/alertas_login.php"; ?>

    <form action="includes/procesar_recuperar.php" method="POST">
        <label class="form-label">Correo registrado</label>
        <input type="email" name="correo" class="form-control" required>

        <button class="btn btn-primary w-100 mt-3">Continuar</button>
    </form>

    <a href="index.php" class="d-block mt-3 text-center">Volver al login</a>

</div>

</body>
</html>

