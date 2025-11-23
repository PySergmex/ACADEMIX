<?php
// Función simple para generar alertas dentro del panel admin
function mostrarAlerta($tipo, $mensaje) {
    echo "
        <div class='alert alert-$tipo alert-dismissible fade show premium-alert' role='alert'>
            <i class='bi bi-info-circle me-2'></i> $mensaje
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
}

/* ===============================
   ÉXITO: EDITAR USUARIO
   =============================== */
if (isset($_GET["edit"]) && $_GET["edit"] === "ok") {
    mostrarAlerta("success", "El usuario fue actualizado correctamente.");
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof showToast === "function") {
                showToast("Usuario actualizado correctamente ✔", "success");
            }
            // Quitar parámetros de la URL
            window.history.replaceState({}, "", window.location.pathname);
        });
    </script>
    <?php
}

/* ===============================
   ÉXITO: REGISTRAR USUARIO
   =============================== */
if (isset($_GET["registro"]) && $_GET["registro"] === "ok") {
    mostrarAlerta("success", "Usuario registrado correctamente 🎉");
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof showToast === "function") {
                showToast("Usuario registrado correctamente 🎉", "success");
            }
            window.history.replaceState({}, "", window.location.pathname);
        });
    </script>
    <?php
}

/* ===============================
   ERRORES COMUNES
   =============================== */
if (isset($_GET["error"])) {

    switch ($_GET["error"]) {

        case "usuario_no_encontrado":
            mostrarAlerta("danger", "El usuario no existe.");
            break;

        case "correo_duplicado":
            mostrarAlerta("warning", "Este correo ya está en uso.");
            break;

        case "sin_id":
            mostrarAlerta("danger", "Solicitud inválida.");
            break;

        case "error_actualizar":
            mostrarAlerta("danger", "Ocurrió un error al actualizar.");
            break;

        default:
            mostrarAlerta("secondary", "Ha ocurrido un error inesperado.");
    }
}
?>
