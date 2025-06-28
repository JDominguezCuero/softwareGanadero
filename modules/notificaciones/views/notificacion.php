<?php
$notificaciones = $notificaciones ?? [];

require_once(__DIR__ . '../../../../config/config.php');

if (isset($_GET['inv']) && $_GET['inv'] == 1 && isset($_GET['error'])) {
    $mensaje = json_encode($_GET['error']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('❌ Error', $mensaje, 'error');
        });
    </script>";
} else if (isset($_GET['msg'])) { // El !empty($inventario) en tu ejemplo original era confuso aquí.
    $mensajeExitoso = json_encode($_GET['msg']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('✅ Operación Exitosa', $mensajeExitoso, 'success');
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificaciones Ganaderas</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/notificaciones/views/css/estyle.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>   
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/principal.css"> 
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">
</head>
<body class="min-h-screen flex bg-gray-100">
    <div class="flex min-h-screen w-full">
        <?php include '../../public/assets/layout/sidebar.php'; ?>
    
        <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">

            <div class="notifications-container">
                <div class="notifications-list">
                    <h2>Tus Notificaciones</h2>
                    <div class="notifications-actions">
                        <button id="selectAllBtn">Seleccionar Todo</button>
                        <button id="deleteSelectedBtn" disabled>Eliminar Seleccionadas</button>
                        <button id="deleteAllBtn">Eliminar Todas</button>
                    </div>
                    <div id="notificationListContent">
                        <?php if (empty($notificaciones)): ?>
                            <p class="no-notifications">No tienes notificaciones por el momento.</p>
                        <?php else: ?>
                            <?php foreach ($notificaciones as $notificacion): ?>
                                <?php
                                $leidoClass = $notificacion['leido'] ? 'read' : 'unread';
                                $imagenProducto = !empty($notificacion['imagen_url']) ? htmlspecialchars($notificacion['imagen_url']) : 'placeholder.jpg'; // Imagen por defecto
                                $nombreProducto = !empty($notificacion['nombre_producto']) ? htmlspecialchars($notificacion['nombre_producto']) : 'Producto Desconocido';
                                $precioProducto = isset($notificacion['precio_unitario']) ? '$' . number_format($notificacion['precio_unitario'], 0, ',', '.') : 'N/A';
                                $emisorNombre = htmlspecialchars($notificacion['emisor_nombre'] ?? 'Usuario Desconocido');
                                $emisorCorreo = htmlspecialchars($notificacion['emisor_correo'] ?? 'N/A');
                                $emisorTelefono = htmlspecialchars($notificacion['emisor_telefono'] ?? 'N/A');

                                ?>
                                <div class="notification-item <?= $leidoClass ?>"
                                    data-id="<?= $notificacion['id_notificacion'] ?>"
                                    data-mensaje="<?= htmlspecialchars($notificacion['mensaje']) ?>"
                                    data-fecha="<?= $notificacion['fecha'] ?>"
                                    data-producto-nombre="<?= $nombreProducto ?>"
                                    data-producto-descripcion="<?= htmlspecialchars($notificacion['descripcion_producto'] ?? 'Sin descripción.') ?>"
                                    data-producto-imagen="<?= $imagenProducto ?>"
                                    data-producto-precio="<?= $precioProducto ?>"
                                    data-producto-id="<?= $notificacion['id_producto'] ?? '' ?>"
                                    data-id-usuario-emisor="<?= htmlspecialchars($notificacion['id_usuario_emisor'] ?? '') ?>"
                                    data-emisor-nombre="<?= $emisorNombre ?>"
                                    data-emisor-correo="<?= $emisorCorreo ?>"
                                    data-emisor-telefono="<?= $emisorTelefono ?>"
                                    data-tipo-notificacion="<?= htmlspecialchars($notificacion['tipo_notificacion'] ?? 'interes') ?>">
                                    <input type="checkbox" class="notification-checkbox">
                                    <div class="notification-content">
                                        <span class="notification-title"><?= $nombreProducto ?> - Notificación</span>
                                        <span class="notification-date"><?= date('d/m/Y H:i', strtotime($notificacion['fecha'])) ?></span>
                                        <p class="notification-preview"><?= substr(htmlspecialchars($notificacion['mensaje']), 0, 70) ?>...</p>
                                    </div>
                                    <button class="delete-single-btn" data-id="<?= $notificacion['id_notificacion'] ?>">X</button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="notification-detail">
                    <h2>Detalles de la Notificación</h2>
                    <div id="detailContent" class="no-selection">
                        <p>Selecciona una notificación para ver los detalles.</p>
                    </div>
                </div>
            </div>

        </main>
        
    </div>

    <?php include '../../modules/auth/layout/mensajesModal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>'; 
    </script>

    <script src="<?= BASE_URL ?>/modules/notificaciones/views/js/notificaciones.js"></script>

    
</body>
</html>