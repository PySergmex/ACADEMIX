<?php
if (!isset($_SESSION)) session_start();
require_once __DIR__ . "/config.php";
?>

<header class="topbar d-flex justify-content-between align-items-center px-4">

    <!-- LOGO -->
    <div class="d-flex align-items-center">
        <img src="<?= BASE_URL ?>assets/imgs/LOGO-NAV.png" class="logo-dashboard" alt="AcademiX">
    </div>

    <!-- DATOS DEL PROFESOR -->
    <div class="d-flex align-items-center gap-3">

        <span class="bienvenido-text">
            Profesor: <?= htmlspecialchars($_SESSION["nombre_completo"] ?? ""); ?>
        </span>

        <div class="dropdown">
            <button class="btn btn-user-avatar p-0 dropdown-toggle" data-bs-toggle="dropdown">
                <div class="user-avatar">
                    <img src="<?= BASE_URL ?>assets/imgs/USER.png" alt="User">
                </div>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <li class="px-3 small text-muted">
                    <?= htmlspecialchars($_SESSION["usuario_correo"] ?? ""); ?>
                </li>

                <li><hr class="dropdown-divider"></li>

                <li>
                    <a href="<?= BASE_URL ?>logout.php" class="dropdown-item text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>

    </div>

</header>
