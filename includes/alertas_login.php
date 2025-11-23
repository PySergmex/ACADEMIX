<?php if (!isset($_SESSION)) session_start(); ?>

<?php
// Muestra alertas de login, registro y recuperación
function mostrarAlertaLogin($tipo, $mensaje) {
    echo "
        <div class='alert alert-$tipo alert-dismissible fade show' role='alert'>
            $mensaje
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
}

// LOGIN: Error de acceso
if (isset($_SESSION["error_login"])) {
    mostrarAlertaLogin("danger", $_SESSION["error_login"]);
    unset($_SESSION["error_login"]);
}

// SIGN UP: Errores
if (isset($_SESSION["error_signup"])) {
    mostrarAlertaLogin("danger", $_SESSION["error_signup"]);
    unset($_SESSION["error_signup"]);
}

// SIGN UP: Registro exitoso
if (isset($_SESSION["success_signup"])) {
    mostrarAlertaLogin("success", $_SESSION["success_signup"]);
    unset($_SESSION["success_signup"]);
}

// RECUPERAR PASSWORD
if (isset($_SESSION["error_recuperar"])) {
    mostrarAlertaLogin("danger", $_SESSION["error_recuperar"]);
    unset($_SESSION["error_recuperar"]);
}

// RESTABLECER PASSWORD: error
if (isset($_SESSION["error_reset"])) {
    mostrarAlertaLogin("danger", $_SESSION["error_reset"]);
    unset($_SESSION["error_reset"]);
}

// RESTABLECER PASSWORD: éxito
if (isset($_SESSION["success_reset"])) {
    mostrarAlertaLogin("success", $_SESSION["success_reset"]);
    unset($_SESSION["success_reset"]);
}
?>
