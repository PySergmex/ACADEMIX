<?php
session_start();

// Base URL
require_once "includes/config.php";

// Si ya hay sesión activa, redirigir al login
if (isset($_SESSION["id_usuario"])) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcademiX - Crear cuenta</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="vh-100">

<div class="container-fluid h-100">
    <div class="row h-100">

        <!-- Panel izquierdo -->
        <div class="col-md-6 login-left d-flex flex-column justify-content-center align-items-center text-white">
            <img src="assets/imgs/LOGO.png" alt="Logo" class="logo-img mb-4">
        </div>

        <!-- Panel derecho -->
        <div class="col-md-6 p-5">

            <!-- Tabs -->
            <div class="w-100 d-flex justify-content-end mb-5">
                <span class="tab-btn active2 me-2">Sign Up</span>
                <a href="<?= BASE_URL ?>index.php" class="tab-btn">Sign In</a>
            </div>

            <h1 class="fw-bold">Crear cuenta</h1>
            <div class="underline2 mb-3"></div>

            <!-- Alertas -->
            <?php include "includes/alertas_login.php"; ?>

            <div class="form-card1 p-4 fade-in">

                <form action="<?= BASE_URL ?>includes/registrar_usuario.php" method="POST">

                    <label class="form-label custom-label">Nombres</label>
                    <input type="text" name="nombres" class="form-input" required>

                    <label class="form-label custom-label mt-3">Apellido paterno</label>
                    <input type="text" name="ap_paterno" class="form-input" required>

                    <label class="form-label custom-label mt-3">Apellido materno</label>
                    <input type="text" name="ap_materno" class="form-input">

                    <label class="form-label custom-label mt-3">Correo</label>
                    <input type="email" name="correo" class="form-input" required>

                    <label class="form-label custom-label mt-3">Contraseña</label>
                    <input type="password" name="password" class="form-input" required>

                    <label class="form-label custom-label mt-3">Confirmar contraseña</label>
                    <input type="password" name="password2" class="form-input" required>

                    <button type="submit" class="btn submit-btn mt-4">REGISTRAR</button>

                </form>

            </div>
        </div>
    </div>
</div>

</body>
</html>
