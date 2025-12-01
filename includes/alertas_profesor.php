<?php

function alertProfesor($tipo, $mensaje) {
    echo "
        <div class='alert alert-$tipo alert-dismissible fade show premium-alert' role='alert'>
            <i class='bi bi-info-circle me-2'></i> $mensaje
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
}

/*Inscripciones*/
if (isset($_GET["inscripcion"])) {
    switch ($_GET["inscripcion"]) {
        case "aprobada":
            alertProfesor("success", "La inscripción fue aprobada correctamente.");
            break;
        case "rechazada":
            alertProfesor("warning", "La inscripción fue rechazada.");
            break;
    }
}

/*Tareas*/
if (isset($_GET["exito"])) {
    switch ($_GET["exito"]) {
        case "tarea_creada":
            alertProfesor("success", "Tarea creada correctamente.");
            break;
        case "tarea_editada":
            alertProfesor("success", "La tarea fue actualizada.");
            break;
        case "tarea_eliminada":
            alertProfesor("success", "La tarea fue eliminada correctamente.");
            break;
        case "materia_editada": // ya existía
            alertProfesor("success", "La materia fue actualizada correctamente.");
            break;
    }
}

/*Calificaciones*/
if (isset($_GET["ok"])) {
    switch ($_GET["ok"]) {
        case "creado":
            alertProfesor("success", "Calificación registrada.");
            break;
        case "editado":
            alertProfesor("success", "Calificación actualizada.");
            break;
    }
}

/*Materias*/
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

/*Errores de materias*/
if (isset($_GET["error"])) {
    switch ($_GET["error"]) {
        case "faltan_datos":
            alertProfesor("danger", "Faltan datos para completar la acción.");
            break;
        case "materia_datos":
            alertProfesor("danger", "Faltan datos para registrar o editar la materia.");
            break;
        case "materia_no_encontrada":
            alertProfesor("danger", "La materia no existe o no pertenece a tu cuenta.");
            break;
        case "error_bd":
            alertProfesor("danger", "Ocurrió un error al guardar la información.");
            break;
        case "no_permitido":
            alertProfesor("warning", "No tienes permiso para realizar esta acción.");
            break;
        case "tarea_no_encontrada":
            alertProfesor("danger", "La tarea no existe.");
            break;
        case "calificacion_no_encontrada":
            alertProfesor("danger", "La calificación no existe.");
            break;
        case "entrega_no_encontrada":
            alertProfesor("danger", "El alumno no ha entregado esta tarea.");
            break;
    }
}

?>

