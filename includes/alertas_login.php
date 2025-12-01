<?php
if (!isset($_SESSION)) session_start();

/*Alertas logins*/

function alertaLogin($tipo, $mensaje) {
    echo "
        <div class='alert alert-$tipo alert-dismissible fade show' role='alert'>
            $mensaje
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
}

/* --- LOGIN: Error --- */
if (!empty($_SESSION["error_login"])) {
    alertaLogin("danger", $_SESSION["error_login"]);
    unset($_SESSION["error_login"]);
}

/* --- SIGN UP: Error --- */
if (!empty($_SESSION["error_signup"])) {
    alertaLogin("danger", $_SESSION["error_signup"]);
    unset($_SESSION["error_signup"]);
}

/* --- SIGN UP: Registro exitoso --- */
if (!empty($_SESSION["success_signup"])) {
    alertaLogin("success", $_SESSION["success_signup"]);
    unset($_SESSION["success_signup"]);
}

/* --- RECUPERAR PASSWORD: Error --- */
if (!empty($_SESSION["error_recuperar"])) {
    alertaLogin("danger", $_SESSION["error_recuperar"]);
    unset($_SESSION["error_recuperar"]);
}

/* --- RESTABLECER: Error --- */
if (!empty($_SESSION["error_reset"])) {
    alertaLogin("danger", $_SESSION["error_reset"]);
    unset($_SESSION["error_reset"]);
}

/* --- RESTABLECER: Éxito --- */
if (!empty($_SESSION["success_reset"])) {
    alertaLogin("success", $_SESSION["success_reset"]);
    unset($_SESSION["success_reset"]);
}
?>
