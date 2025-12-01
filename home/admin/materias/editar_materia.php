<?php
session_start();
require_once "../../../includes/config.php";
require_once "../../../includes/conexion.php";

// Solo admin
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 1) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
if ($id <= 0) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_sin_id");
    exit;
}

// Obtener materia
$sqlMateria = "
    SELECT m.*, u.usuario_nombres, u.usuario_apellido_paterno
    FROM materias m
    LEFT JOIN usuarios u ON u.id_usuario = m.id_usuario_maestro
    WHERE m.id_materia = :id
    LIMIT 1
";
$stmt = $pdo->prepare($sqlMateria);
$stmt->execute([":id" => $id]);
$materia = $stmt->fetch();

if (!$materia) {
    header("Location: " . BASE_URL . "home/admin/materias/index.php?error=materia_no_encontrada");
    exit;
}

// Obtener maestros disponibles
$sqlMaestros = "
    SELECT id_usuario, usuario_nombres, usuario_apellido_paterno, usuario_correo
    FROM usuarios
    WHERE id_rol = 2
      AND id_estatus_usuario = 1
    ORDER BY usuario_nombres
";
$maestros = $pdo->query($sqlMaestros)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar materia - AcademiX</title>
    <!-- ICONO -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/imgs/logo-ico.png?v=1">
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="admin-dashboard">
    <!--Topbar Admin-->
    <?php include "../../../includes/topbar_admin.php"; ?>
    <!--Sidebar Admin-->
    <div class="d-flex">
        <?php $pagina_activa = 'materias'; ?>
        <?php include "../../../includes/sidebar_admin.php"; ?>
        <!--Alertas-->
        <main class="content-area p-4">
            <?php include "../../../includes/alertas_admin.php"; ?>

            <h3 class="mb-4 fw-bold">Editar materia</h3>

            <div class="admin-form-card">

                <form action="procesar_editar_materia.php" method="POST">
                    <input type="hidden" name="id_materia" value="<?= $materia['id_materia']; ?>">

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label class="form-label">Nombre de la materia</label>
                        <input 
                            type="text" 
                            name="materia_nombre" 
                            class="form-control"
                            value="<?= htmlspecialchars($materia['materia_nombre']); ?>"
                            required
                        >
                    </div>

                    <!-- Descripción -->
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea 
                            name="materia_descripcion" 
                            class="form-control" 
                            rows="3"
                        ><?= htmlspecialchars($materia['materia_descripcion']); ?></textarea>
                    </div>

                    <!-- Maestro -->
                    <div class="mb-3">
                        <label class="form-label">Maestro asignado</label>
                        <select name="id_usuario_maestro" class="form-select" required>
                            <option value="">Selecciona un maestro...</option>
                            <?php foreach ($maestros as $m): ?>
                                <option 
                                    value="<?= $m['id_usuario']; ?>"
                                    <?= ($m['id_usuario'] == $materia['id_usuario_maestro']) ? 'selected' : ''; ?>
                                >
                                    <?= htmlspecialchars($m['usuario_nombres'] . " " . $m['usuario_apellido_paterno']); ?>
                                    (<?= htmlspecialchars($m['usuario_correo']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Horario actual -->
                    <div class="mb-3">
                        <label class="form-label text-muted">Horario actual</label>
                        <div class="fw-semibold">
                            <?= htmlspecialchars($materia['materia_horario'] ?: 'Sin horario definido'); ?>
                        </div>
                    </div>

                    <!-- Actualizar horario (opcional) -->
                    <div class="border rounded p-3 mb-4">
                        <p class="mb-2 fw-semibold">Actualizar horario (opcional)</p>
                        <small class="text-muted d-block mb-2">
                            Si no seleccionas nada, se conservará el horario actual.
                        </small>

                        <div class="mb-3">
                            <label class="form-label">Días</label>
                            <div class="d-flex flex-wrap gap-3">
                                <label><input type="checkbox" name="dias[]" value="Lun"> Lun</label>
                                <label><input type="checkbox" name="dias[]" value="Mar"> Mar</label>
                                <label><input type="checkbox" name="dias[]" value="Mie"> Mie</label>
                                <label><input type="checkbox" name="dias[]" value="Jue"> Jue</label>
                                <label><input type="checkbox" name="dias[]" value="Vie"> Vie</label>
                                <label><input type="checkbox" name="dias[]" value="Sáb"> Sáb</label>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Hora de inicio</label>
                                <select name="hora_inicio" class="form-select">
                                    <option value="">Selecciona...</option>
                                    <option>07:00</option>
                                    <option>08:00</option>
                                    <option>09:00</option>
                                    <option>10:00</option>
                                    <option>11:00</option>
                                    <option>12:00</option>
                                    <option>13:00</option>
                                    <option>14:00</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Hora de fin</label>
                                <select name="hora_fin" class="form-select">
                                    <option value="">Selecciona...</option>
                                    <option>08:00</option>
                                    <option>09:00</option>
                                    <option>10:00</option>
                                    <option>11:00</option>
                                    <option>12:00</option>
                                    <option>13:00</option>
                                    <option>14:00</option>
                                    <option>15:00</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!--Botones-->
                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Guardar cambios
                        </button>
                    </div>

                </form>

            </div>

        </main>
    </div>
    <!--Footer-->
    <?php include "../../../includes/footer.php"; ?>
    <!-- JS Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JS global -->
    <script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
