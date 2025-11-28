<?php
// Verifica variable para marcar sección activa
if (!isset($pagina_activa)) {
    $pagina_activa = '';
}

require_once __DIR__ . "/config.php";
?>

<aside class="sidebar d-flex flex-column align-items-center pt-4">

    <!-- Usuarios -->
    <a href="<?= BASE_URL ?>home/admin/index.php"
       class="sidebar-icon <?= ($pagina_activa === 'usuarios') ? 'active' : ''; ?>"
       title="Usuarios">
        <i class="bi bi-people"></i>
    </a>

        <!-- Materias -->
    <a href="<?= BASE_URL ?>home/admin/materias/index.php"
       class="sidebar-icon <?= ($pagina_activa === 'materias') ? 'active' : ''; ?>"
       title="Materias">
        <i class="bi bi-journal-bookmark"></i>
    </a>

    <!-- Dashboard -->
    <a href="<?= BASE_URL ?>home/admin/dashboard/index.php"
       class="sidebar-icon <?= ($pagina_activa === 'dashboard') ? 'active' : ''; ?>"
       title="Dashboard">
        <i class="bi bi-speedometer2"></i>
    </a>


</aside>
