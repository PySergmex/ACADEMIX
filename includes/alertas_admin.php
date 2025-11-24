<?php
// ============================================================
//  SISTEMA DE ALERTAS ADMIN — SOLO ALERTAS BOOTSTRAP
// ============================================================

function mostrarAlerta($tipo, $mensaje) {
    echo "
        <div class='alert alert-$tipo alert-dismissible fade show premium-alert' role='alert'>
            <i class='bi bi-info-circle me-2'></i> $mensaje
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
}

/* =============================== 
   USUARIOS: Editar
=============================== */
if (isset($_GET["edit"]) && $_GET["edit"] === "ok") {
    mostrarAlerta("success", "El usuario fue actualizado correctamente.");
}

/* =============================== 
   USUARIOS: Registro
=============================== */
if (isset($_GET["registro"]) && $_GET["registro"] === "ok") {
    mostrarAlerta("success", "Usuario registrado correctamente 🎉");
}

/* =============================== 
   USUARIOS: Eliminación
=============================== */
if (isset($_GET["delete"]) && $_GET["delete"] === "ok") {
    mostrarAlerta("success", "Usuario eliminado correctamente.");
}

if (isset($_GET["delete"]) && $_GET["delete"] === "admin_denegado") {
    mostrarAlerta("warning", "No puedes eliminar usuarios administradores.");
}

/* =============================== 
   USUARIOS: Errores
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

/* =============================== 
   MATERIAS: ÉXITOS
=============================== */
if (isset($_GET["materia_registro"]) && $_GET["materia_registro"] === "ok") {
    mostrarAlerta("success", "Materia registrada correctamente 🎓");
}

if (isset($_GET["materia_edit"]) && $_GET["materia_edit"] === "ok") {
    mostrarAlerta("success", "Materia actualizada correctamente.");
}

if (isset($_GET["materia_delete"]) && $_GET["materia_delete"] === "ok") {
    mostrarAlerta("success", "Materia eliminada correctamente.");
}

/* =============================== 
   MATERIAS: Errores
=============================== */
if (isset($_GET["error_materia"])) {

    switch ($_GET["error_materia"]) {

        case "materia_datos":
            mostrarAlerta("danger", "Faltan datos para registrar o editar la materia.");
            break;

        case "materia_sin_id":
            mostrarAlerta("danger", "Solicitud inválida para materias.");
            break;

        case "materia_no_encontrada":
            mostrarAlerta("danger", "La materia no existe.");
            break;

        case "materia_tiene_relaciones":
            mostrarAlerta("warning", "No se puede eliminar la materia porque tiene inscripciones o tareas relacionadas.");
            break;

        case "materia_error_registro":
            mostrarAlerta("danger", "Ocurrió un error al registrar la materia.");
            break;

        case "materia_error_editar":
            mostrarAlerta("danger", "Ocurrió un error al editar la materia.");
            break;

        case "materia_error_delete":
            mostrarAlerta("danger", "Ocurrió un error al eliminar la materia.");
            break;
    }
}
?>


