<?php

function alertAlumno($tipo, $mensaje) {
    echo "
        <div class='alert alert-$tipo alert-dismissible fade show premium-alert' role='alert'>
            <i class='bi bi-info-circle me-2'></i> $mensaje
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
        </div>
    ";
}

/*Solicitudes de inscripccion*/
if (isset($_GET["materia"])) {

    switch ($_GET["materia"]) {

        case "solicitud_ok":
            alertAlumno("success", "Tu solicitud de inscripción fue enviada. Espera la respuesta del profesor.");
            break;

        case "ya_inscrito":
            alertAlumno("info", "Ya tienes una solicitud o inscripción registrada para esta materia.");
            break;
    }
}

/*Errores generales*/
if (isset($_GET["error"])) {

    switch ($_GET["error"]) {

        case "materia_no_encontrada":
            alertAlumno("danger", "La materia no existe o no está disponible.");
            break;

        case "error_bd":
            alertAlumno("danger", "Ocurrió un error al procesar tu solicitud. Intenta de nuevo.");
            break;

        case "no_permitido":
            alertAlumno("warning", "No tienes permiso para realizar esta acción.");
            break;
    }
}
/*Entrega de tareas*/
if (isset($_GET["ok"]) && $_GET["ok"] === "entregada") {
    alertAlumno("success", "Tarea entregada correctamente.");
}

if (isset($_GET["error"]) && $_GET["error"] === "ya_entregada") {
    alertAlumno("warning", "Ya habías entregado esta tarea.");
}

