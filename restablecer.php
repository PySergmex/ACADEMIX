<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AcademiX - Recuperar contraseña</title>

    <!-- ICONO -->
    <link rel="icon" type="image/x-icon" href="assets/imgs/logo-ico.png?v=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100">

    <main class="w-100 d-flex justify-content-center">
        <div class="card p-4" style="width: 350px;">

            <h4 class="mb-3 text-center">Recuperar contraseña</h4>

            <?php include "includes/alertas_login.php"; ?>

            <form action="includes/procesar_recuperar.php" method="POST">
                <label class="form-label" for="correo">Correo registrado</label>
                <input
                    type="email"
                    id="correo"
                    name="correo"
                    class="form-control"
                    required
                >

                <button type="submit" class="btn btn-primary w-100 mt-3">
                    Continuar
                </button>
            </form>

            <a href="index.php" class="d-block mt-3 text-center">
                Volver al login
            </a>

        </div>
    </main>
    <!-- PROYECTO REALIZADO BY: SERGIO EDUARDO CERVANTES MATA-->
</body>
</html>

