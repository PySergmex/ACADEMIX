<?php
// Verifica variable para marcar sección activa
if (!isset($pagina_activa)) {
    $pagina_activa = '';
}

require_once __DIR__ . "/config.php";
?>

<aside class="sidebar-admin d-flex flex-column align-items-center pt-4">

    <!-- Módulo: Materias / Inscripción -->
    <a href="<?= BASE_URL ?>home/alumno/index.php" 
       class="sidebar-icon <?= ($pagina_activa == 'materias') ? 'active' : '' ?>">
        <i class="bi bi-journal-plus"></i>
    </a>

    <!-- Módulo: Tareas -->
    <a href="<?= BASE_URL ?>home/alumno/tareas/index.php" 
       class="sidebar-icon <?= ($pagina_activa == 'tareas') ? 'active' : '' ?>">
        <i class="bi bi-list-check"></i>
    </a>

    <!-- Módulo: Calificaciones -->
    <a href="<?= BASE_URL ?>home/alumno/calificaciones/index.php" 
       class="sidebar-icon <?= ($pagina_activa == 'calificaciones') ? 'active' : '' ?>">
        <i class="bi bi-clipboard-data"></i>
    </a>

    <!-- Dashboard -->
    <a href="<?= BASE_URL ?>home/alumno/dashboard/index.php" 
       class="sidebar-icon <?= ($pagina_activa == 'dashboard') ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i>
    </a>
<!-- PROYECTO REALIZADO BY: SERGIO EDUARDO CERVANTES MATA-->
</aside>



