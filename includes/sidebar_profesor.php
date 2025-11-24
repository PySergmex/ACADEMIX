<?php
if (!isset($pagina_activa)) {
    $pagina_activa = '';
}
require_once __DIR__ . "/config.php";
?>

<aside class="sidebar-admin d-flex flex-column align-items-center pt-4">

    <!-- MIS MATERIAS -->
    <a href="<?= BASE_URL ?>home/profesor/index.php"
       class="sidebar-icon <?= ($pagina_activa == 'materias') ? 'active' : ''; ?>"
       title="Mis materias">
        <i class="bi bi-journal-bookmark"></i>
    </a>

    <!-- SOLICITUDES -->
    <a href="<?= BASE_URL ?>home/profesor/inscripciones/index.php"
       class="sidebar-icon <?= ($pagina_activa == 'inscripciones') ? 'active' : ''; ?>"
       title="Solicitudes">
        <i class="bi bi-person-check"></i>
    </a>

    <!-- TAREAS -->
    <a href="<?= BASE_URL ?>home/profesor/tareas/select_materias.php" 
    class="sidebar-icon <?= ($pagina_activa == 'tareas') ? 'active' : ''; ?>">
        <i class="bi bi-journal-check"></i>
    </a>

    <!-- DASHBOARD -->
    <a href="<?= BASE_URL ?>home/profesor/dashboard/index.php"
       class="sidebar-icon <?= ($pagina_activa == 'dashboard') ? 'active' : ''; ?>"
       title="Dashboard">
        <i class="bi bi-speedometer2"></i>
    </a>

</aside>
