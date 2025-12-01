<?php
session_start();
require_once "../../../includes/conexion.php";
require_once "../../../includes/config.php";

/*Validar solo profesores*/
if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol_id"] != 2) {
    header("Location: " . BASE_URL . "index.php");
    exit;
}

/*Validar Materia*/
$id_materia = isset($_GET["id_materia"]) ? intval($_GET["id_materia"]) : 0;
$id_maestro = (int) $_SESSION["id_usuario"];

if ($id_materia <= 0) {
    header("Location: index.php");
    exit;
}

/*Obtener los datos de la materia*/
$sql = "
    SELECT materia_nombre 
    FROM materias 
    WHERE id_materia = :id 
      AND id_usuario_maestro = :maestro
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ":id"      => $id_materia,
    ":maestro" => $id_maestro
]);

$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    header("Location: index.php?error=no_permitido");
    exit;
}

/*Buscar alumnos*/
$busqueda = $_GET["busqueda"] ?? "";
$param = "%" . $busqueda . "%";

/*Alumnos inscritos*/
if ($busqueda !== "") {
    // Con filtro por nombre / apellido paterno / correo
    $sql = "
        SELECT 
            u.id_usuario,
            u.usuario_nombres,
            u.usuario_apellido_paterno,
            u.usuario_correo
        FROM inscripciones i
        INNER JOIN usuarios u ON u.id_usuario = i.id_usuario_estudiante
        WHERE i.id_materia = :materia
          AND i.id_estatus_inscripcion = 2
          AND (
                u.usuario_nombres          LIKE :b
             OR u.usuario_apellido_paterno LIKE :b
             OR u.usuario_correo          LIKE :b
          )
        ORDER BY u.usuario_nombres ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":materia" => $id_materia,
        ":b"       => $param
    ]);

} else {
    // Sin filtro (todos los alumnos inscritos)
    $sql = "
        SELECT 
            u.id_usuario,
            u.usuario_nombres,
            u.usuario_apellido_paterno,
            u.usuario_correo
        FROM inscripciones i
        INNER JOIN usuarios u ON u.id_usuario = i.id_usuario_estudiante
        WHERE i.id_materia = :materia
          AND i.id_estatus_inscripcion = 2
        ORDER BY u.usuario_nombres ASC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([":materia" => $id_materia]);
}

$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*Obtener promedios*/
$promedios = [];

foreach ($alumnos as $a) {

    // Traemos calificación y ponderación de cada tarea del alumno
    $sqlCal = "
        SELECT 
            c.calificacion_valor,
            t.tarea_ponderacion
        FROM calificaciones c
        INNER JOIN tareas t ON t.id_tarea = c.id_tarea
        WHERE c.id_usuario_estudiante = :alumno
          AND t.id_materia = :materia
    ";

    $qCal = $pdo->prepare($sqlCal);
    $qCal->execute([
        ":alumno"  => $a["id_usuario"],
        ":materia" => $id_materia
    ]);

    $rows = $qCal->fetchAll(PDO::FETCH_ASSOC);

    if (count($rows) > 0) {
        $acumulado = 0;

        foreach ($rows as $row) {
            $valor       = (float) $row["calificacion_valor"];  
            $ponderacion = (float) $row["tarea_ponderacion"];   

            $acumulado += ($valor * $ponderacion) / 100;
        }
        $promedios[$a["id_usuario"]] = number_format($acumulado, 2);
    } else {
        $promedios[$a["id_usuario"]] = null;
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Alumnos | Profesor - AcademiX</title>
    <!-- ICONO -->
    <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/imgs/logo-ico.png?v=1">
    <!--Bootsrap-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
     <!--Iconos Bootstrap-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- CSS tablero -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/tablero.css">
</head>

<body class="prof-dashboard">
<!--Topbar Profesor-->
<?php include "../../../includes/topbar_profesor.php"; ?>

<div class="d-flex">

    <!--Sidebar Profesor-->
    <?php 
        $pagina_activa = "calificaciones";
        include "../../../includes/sidebar_profesor.php"; 
    ?>

    <main class="content-area p-4">
        <!--Alertas-->
        <?php include "../../../includes/alertas_profesor.php"; ?>

        <h3 class="mb-1 fw-bold">
            Alumnos — <?= htmlspecialchars($materia["materia_nombre"]) ?>
        </h3>

        <a href="index.php" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Volver
        </a>

        <!-- BUSCADOR -->
        <form class="row g-2 mb-3" method="get">
            <!-- Mantener id_materia en la URL al buscar -->
            <input type="hidden" name="id_materia" value="<?= (int) $id_materia ?>">

            <div class="col-sm-4 col-md-3">
                <input 
                    type="text" 
                    name="busqueda"
                    class="form-control"
                    placeholder="Buscar alumno por nombre o correo"
                    value="<?= htmlspecialchars($busqueda) ?>"
                >
            </div>

            <div class="col-sm-3 col-md-2">
                <button class="btn btn-outline-secondary w-100">
                    <i class="bi bi-search"></i> Buscar
                </button>
            </div>

            <?php if ($busqueda !== ""): ?>
            <div class="col-sm-3 col-md-2">
                <a href="ver_alumnos.php?id_materia=<?= (int) $id_materia ?>" class="btn btn-link text-decoration-none">
                    Limpiar filtro
                </a>
            </div>
            <?php endif; ?>
        </form>

        <div class="card fade-in">
            <div class="card-body table-responsive">

                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Alumno</th>
                            <th>Correo</th>
                            <th>Promedio</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($alumnos)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    No hay alumnos inscritos en esta materia.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($alumnos as $a): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($a["usuario_nombres"] . " " . $a["usuario_apellido_paterno"]) ?>
                                        </strong>
                                    </td>

                                    <td><?= htmlspecialchars($a["usuario_correo"]) ?></td>

                                    <td>
                                        <?php 
                                            $p = $promedios[$a["id_usuario"]];
                                            echo $p !== null 
                                                ? "<span class='badge bg-primary'>$p</span>"
                                                : "<span class='text-muted'>Sin calificaciones</span>";
                                        ?>
                                    </td>

                                    <td class="text-center">
                                        <a href="ver_detalle.php?id_materia=<?= $id_materia ?>&id_alumno=<?= $a['id_usuario'] ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Detalle
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>

                </table>

            </div>
        </div>

    </main>

</div>
<!--Footer-->
<?php include "../../../includes/footer.php"; ?>
<!-- JS Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<!-- JS global -->
<script src="<?= BASE_URL ?>assets/js/main.js"></script>

    <!-- Buscador en tiempo real -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        iniciarBuscadorEnTiempoReal(
            "input[name='busqueda']",
            "table"
        );
    });
    </script>
</body>
</html>

