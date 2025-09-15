<?php
// uploads/cotizaciones.php
require_once("../config/conexion.php");

// Detectar conexión PDO
$db = null;
if (isset($conexion) && $conexion instanceof PDO) {
    $db = $conexion;
} elseif (isset($pdo) && $pdo instanceof PDO) {
    $db = $pdo;
} else {
    die("Error: no se encontró una conexión PDO en conexion.php");
}

// Mensajes
$success_msg = '';
$error_msg = '';

if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'success':
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $success_msg = "Cotización #$id creada exitosamente";
            break;
        case 'deleted':
            $success_msg = "Cotización eliminada correctamente";
            break;
    }
}

if (isset($_GET['error'])) {
    $error_msg = $_GET['error'];
}

// Eliminar cotización si se solicita
if (isset($_GET['eliminar'])) {
    $id_eliminar = (int)$_GET['eliminar'];
    try {
        // Eliminar detalles primero
        $sql = "DELETE FROM detalle_cotizacion WHERE id_cotizacion = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_eliminar]);
        
        // Eliminar cotización
        $sql = "DELETE FROM cotizaciones WHERE id_cotizacion = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id_eliminar]);
        
        header("Location: cotizaciones.php?msg=deleted");
        exit;
    } catch (Exception $e) {
        $error_msg = "Error al eliminar: " . $e->getMessage();
    }
}

// Obtener todas las cotizaciones
$sql = "SELECT c.*, 
               COUNT(d.id_detalle) as cantidad_productos,
               GROUP_CONCAT(d.nombre_producto SEPARATOR ', ') as productos
        FROM cotizaciones c
        LEFT JOIN detalle_cotizacion d ON c.id_cotizacion = d.id_cotizacion
        GROUP BY c.id_cotizacion
        ORDER BY c.fecha DESC, c.id_cotizacion DESC";
$stmt = $db->prepare($sql);
$stmt->execute();
$cotizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Cotizaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table th { background-color: #f8f9fa; }
        .badge-estado { font-size: 0.8em; }
        .btn-group-sm .btn { padding: 0.25rem 0.5rem; font-size: 0.8rem; }
    </style>
</head>
<body class="bg-light">

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-file-invoice"></i> Gestión de Cotizaciones</h2>
                <a href="cotizacion_form.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Nueva Cotización
                </a>
            </div>

            <!-- Mensajes -->
            <?php if ($success_msg): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success_msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error_msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tabla de Cotizaciones -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Listado de Cotizaciones</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($cotizaciones)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay cotizaciones registradas</h5>
                            <p class="text-muted">Comienza creando tu primera cotización</p>
                            <a href="cotizacion_form.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primera Cotización
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="8%">ID</th>
                                        <th width="20%">Cliente</th>
                                        <th width="18%">Colegio</th>
                                        <th width="12%">Promoción</th>
                                        <th width="8%">Fecha</th>
                                        <th width="10%">Total</th>
                                        <th width="8%">Estado</th>
                                        <th width="6%">Productos</th>
                                        <th width="10%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cotizaciones as $cot): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary">#<?= $cot['id_cotizacion'] ?></span>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($cot['cliente']) ?></strong>
                                                <?php if ($cot['seccion']): ?>
                                                    <br><small class="text-muted">Sección: <?= htmlspecialchars($cot['seccion']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($cot['colegio']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($cot['promocion']) ?>
                                            </td>
                                            <td>
                                                <small><?= date('d/m/Y', strtotime($cot['fecha'])) ?></small>
                                            </td>
                                            <td>
                                                <strong class="text-success">S/. <?= number_format($cot['total'], 2) ?></strong>
                                                <?php if ($cot['adelanto'] > 0): ?>
                                                    <br><small class="text-info">Adelanto: S/. <?= number_format($cot['adelanto'], 2) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = 'bg-secondary';
                                                switch($cot['estado']) {
                                                    case 'pendiente': $badge_class = 'bg-warning text-dark'; break;
                                                    case 'confirmado': $badge_class = 'bg-info'; break;
                                                    case 'completado': $badge_class = 'bg-success'; break;
                                                    case 'cancelado': $badge_class = 'bg-danger'; break;
                                                }
                                                ?>
                                                <span class="badge <?= $badge_class ?> badge-estado">
                                                    <?= ucfirst(htmlspecialchars($cot['estado'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?= $cot['cantidad_productos'] ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="cotizaciones_ver.php?id=<?= $cot['id_cotizacion'] ?>" 
                                                       class="btn btn-outline-primary" 
                                                       title="Ver cotización">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="cotizacion_form.php?id=<?= $cot['id_cotizacion'] ?>" 
                                                       class="btn btn-outline-warning" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="?eliminar=<?= $cot['id_cotizacion'] ?>" 
                                                       class="btn btn-outline-danger" 
                                                       title="Eliminar"
                                                       onclick="return confirm('¿Está seguro de eliminar esta cotización?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if (!empty($cotizaciones)): ?>
                    <div class="card-footer text-muted">
                        <small>
                            <i class="fas fa-info-circle"></i> 
                            Total de cotizaciones: <?= count($cotizaciones) ?>
                        </small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>