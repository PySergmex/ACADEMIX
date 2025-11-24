<?php

function alertProfesor($tipo, $mensaje) {
    echo "
        <div class='alert alert-$tipo alert-dismissible fade show premium-alert' role='alert'>
            <i class='bi bi-info-circle me-2'></i> $mensaje
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
}

/* Inscripciones */
if (isset($_GET["inscripcion"])) {
    if ($_GET["inscripcion"] === "aprobada") {
        alertProfesor("success", "La inscripción fue aprobada correctamente.");
    }
    if ($_GET["inscripcion"] === "rechazada") {
        alertProfesor("warning", "La inscripción fue rechazada.");
    }
}

/* Tareas */
if (isset($_GET["exito"])) {
    if ($_GET["exito"] === "tarea_creada") {
        alertProfesor("success", "Tarea creada correctamente.");
    }
    if ($_GET["exito"] === "tarea_editada") {
        alertProfesor("success", "La tarea fue actualizada.");
    }
    if ($_GET["exito"] === "tarea_eliminada") {
        alertProfesor("success", "La tarea fue eliminada correctamente.");
    }
}

/* Calificaciones */
if (isset($_GET["ok"])) {
    if ($_GET["ok"] === "creado") {
        alertProfesor("success", "Calificación registrada.");
    }
    if ($_GET["ok"] === "editado") {
        alertProfesor("success", "Calificación actualizada.");
    }
}
/* Materias */
if (isset($_GET["materia"])) {
    switch ($_GET["materia"]) {
        case "creada":
            alertProfesor("success", "Materia creada correctamente.");
            break;
        case "editada":
            alertProfesor("success", "Materia actualizada.");
            break;
        case "desactivada":
            alertProfesor("warning", "La materia fue desactivada. Los alumnos ya no podrán inscribirse.");
            break;
    }
}
/* Materias (profesor) */
if (isset($_GET["exito"])) {
    if ($_GET["exito"] === "materia_editada") {
        alertProfesor("success", "La materia fue actualizada correctamente.");
    }
}

/* Errores de materias */
if (isset($_GET["error"])) {
    if ($_GET["error"] === "materia_datos") {
        alertProfesor("danger", "Faltan datos para registrar o editar la materia.");
    }
    if ($_GET["error"] === "materia_no_encontrada") {
        alertProfesor("danger", "La materia no existe o no pertenece a tu cuenta.");
    }
    if ($_GET["error"] === "error_bd") {
        alertProfesor("danger", "Ocurrió un error al guardar la información de la materia.");
    }
}


/* Errores */
if (isset($_GET["error"])) {
    if ($_GET["error"] === "faltan_datos") {
        alertProfesor("danger", "Faltan datos para completar la acción.");
    }
    if ($_GET["error"] === "error_bd") {
        alertProfesor("danger", "Ocurrió un error al guardar la información.");
    }
    if ($_GET["error"] === "no_permitido") {
        alertProfesor("warning", "No tienes permiso para realizar esta acción.");
    }
    if ($_GET["error"] === "tarea_no_encontrada") {
        alertProfesor("danger", "La tarea no existe.");
    }
    if ($_GET["error"] === "calificacion_no_encontrada") {
        alertProfesor("danger", "La calificación no existe.");
    }
    if ($_GET["error"] === "entrega_no_encontrada") {
        alertProfesor("danger", "El alumno no ha entregado esta tarea.");
    }
}
