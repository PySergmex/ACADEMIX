<?php
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión completa
session_destroy();

// Redirigir al login principal (index.php)
header("Location: index.php");
exit;
?>
