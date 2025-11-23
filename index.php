<?php
session_start();
require_once __DIR__ . "/includes/config.php";

// Si el usuario ya inició sesión, redirigir al dashboard/admin
if (isset($_SESSION['id_usuario'])) {
    header("Location: " . BASE_URL . "/home/admin/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcademiX - Iniciar Sesión</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>

<body class="vh-100">

    <div class="container-fluid h-100">
        <div class="row h-100">

            <!-- PANEL IZQUIERDO -->
            <div class="col-md-6 login-left d-flex flex-column justify-content-center align-items-center text-white">
                <img src="<?= BASE_URL ?>/assets/imgs/LOGO.png" alt="AcademiX Logo" class="logo-img mb-4">
            </div>

            <!-- PANEL DERECHO -->
            <div class="col-md-6 login-right d-flex flex-column justify-content-start align-items-start p-5">

                <!-- TABS -->
                <div class="w-100 d-flex justify-content-end mb-5">
                    <a href="<?= BASE_URL ?>/sign_up.php" class="tab-btn me-2">Sign Up</a>
                    <a class="tab-btn active">Sign In</a>
                </div>

                <!-- TÍTULO -->
                <h1 class="fw-bold">Sign In</h1>
                <div class="underline mb-3"></div>

                <!-- ALERTAS -->
                <?php if (isset($_SESSION["error_login"])): ?>
                    <div class="alert alert-danger">
                        <?= $_SESSION["error_login"]; unset($_SESSION["error_login"]); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION["success_reset"])): ?>
                    <div class="alert alert-success">
                        <?= $_SESSION["success_reset"]; unset($_SESSION["success_reset"]); ?>
                    </div>
                <?php endif; ?>

                <!-- FORMULARIO -->
                <div class="form-card p-4 mt-3 fade-in">
                    <form action="<?= BASE_URL ?>/includes/validar.php" method="POST">

                        <label class="form-label custom-label">Correo</label>
                        <input type="email" name="correo" class="form-input" required>

                        <label class="form-label custom-label mt-4">Password</label>
                        <input type="password" name="password" class="form-input" required>

                        <button type="submit" class="btn submit-btn mt-4">
                            SIGN IN
                        </button>

                        <!-- Recuperar Contraseña -->
                        <div class="text-end mt-3">
                            <a href="<?= BASE_URL ?>/recuperar.php" 
                               class="text-decoration-none" 
                               style="color:#5146D9; font-weight:500;">
                                ¿Olvidaste tu contraseña?
                            </a>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script src="<?= BASE_URL ?>/assets/js/loader.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/login.js"></script>
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>

    <!-- LOADER -->
    <div id="loader-overlay" class="loader-overlay d-none">
        <img src="<?= BASE_URL ?>/assets/imgs/LOGO.png" class="loader-logo" alt="AcademiX Logo">
        <h4 class="mt-3 text-white">Cargando...</h4>
    </div>

</body>
</html>
