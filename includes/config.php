<?php
/*Redireccionamiento*/

// URL base del proyecto (modificar si cambia la carpeta)
define("BASE_URL", "http://localhost/academix/");

// Ruta física absoluta (para incluir archivos sin errores)
define("BASE_PATH", __DIR__ . "/..");

// Ajustes opcionales para evitar errores de sesión doble
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
