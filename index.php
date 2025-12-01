<?php
session_start();
// Si ya está logueado, enviarlo a su rol correspondiente
if (isset($_SESSION['id_usuario'])) {
    header("Location: home/admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcademiX - Iniciar Sesión</title>

    <!-- ICONO -->
    <link rel="icon" type="image/x-icon" href="assets/imgs/logo-ico.png?v=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS principal -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="vh-100">

    <div class="container-fluid h-100">
        <div class="row h-100">

            <!-- Panel izquierdo -->
            <div class="col-md-6 login-left d-flex flex-column justify-content-center align-items-center text-white">
                <img src="assets/imgs/LOGO.png" alt="Logo de AcademiX" class="logo-img mb-4">
            </div>

            <!-- Panel derecho -->
            <div class="col-md-6 login-right d-flex flex-column justify-content-start align-items-start p-5">

                <div class="w-100 d-flex justify-content-end mb-5">
                    <a href="sign_up.php" class="tab-btn me-2">Sign Up</a>
                    <span class="tab-btn active">Sign In</span>
                </div>

                <h1 class="fw-bold">Sign In</h1>
                <div class="underline mb-3"></div>

                <!-- Alertas globales -->
                <?php include "includes/alertas_login.php"; ?>

                <div class="form-card p-4 mt-3 fade-in">
                    <form action="includes/validar.php" method="POST">

                        <label class="form-label custom-label" for="correo">Correo</label>
                        <input type="email" id="correo" name="correo" class="form-input" required>

                        <label class="form-label custom-label mt-4" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-input" required>

                        <button type="submit" class="btn submit-btn mt-4">SIGN IN</button>

                        <div class="text-end mt-3">
                            <a href="recuperar.php" class="text-decoration-none" style="color:#5146D9; font-weight:500;">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>

                    </form>
                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Loader de carga -->
    <script src="assets/js/loader.js"></script>

    <!-- Animaciones y más funciones -->
    <script src="assets/js/login.js"></script>

    <!-- JS principal -->
    <script src="assets/js/main.js"></script>

    <!-- Div carga de Loader -->
    <div id="loader-overlay" class="loader-overlay d-none">
        <img src="assets/imgs/LOGO.png" class="loader-logo" alt="Logo de AcademiX">
        <h4 class="mt-3 text-white">Cargando...</h4>
    </div>
    <!-- Fin div carga de Loader -->
    <!-- PROYECTO REALIZADO BY: SERGIO EDUARDO CERVANTES MATA-->
</body>
</html>
