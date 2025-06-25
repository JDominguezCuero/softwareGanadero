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
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/inventario/css/estilosInventario.css">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>    
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
                                ?>
                                <div class="notification-item <?= $leidoClass ?>"
                                    data-id="<?= $notificacion['id_notificacion'] ?>"
                                    data-mensaje="<?= htmlspecialchars($notificacion['mensaje']) ?>"
                                    data-fecha="<?= $notificacion['fecha'] ?>"
                                    data-producto-nombre="<?= $nombreProducto ?>"
                                    data-producto-descripcion="<?= htmlspecialchars($notificacion['descripcion_producto'] ?? 'Sin descripción.') ?>"
                                    data-producto-imagen="<?= $imagenProducto ?>"
                                    data-producto-precio="<?= $precioProducto ?>"
                                    data-producto-id="<?= $notificacion['id_producto'] ?? '' ?>">
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
       document.addEventListener('DOMContentLoaded', () => {
        const notificationListContent = document.getElementById('notificationListContent');
        const detailContent = document.getElementById('detailContent');
        const selectAllBtn = document.getElementById('selectAllBtn');
        const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
        const deleteAllBtn = document.getElementById('deleteAllBtn');

        // Función para actualizar el estado del botón de eliminar seleccionadas
        const updateDeleteSelectedButton = () => {
            const checkedCheckboxes = document.querySelectorAll('.notification-checkbox:checked');
            deleteSelectedBtn.disabled = checkedCheckboxes.length === 0;
        };

        // Función para manejar las solicitudes AJAX
        async function sendAjaxRequest(action, data, method = 'POST') { // Añadimos 'action' y 'method'
            const url = `<?= BASE_URL ?>/modules/notificaciones/controller.php?accion=${action}`; // Apunta al controlador
            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });
                if (!response.ok) {
                    // Si la respuesta no es 2xx, lanza un error con el estado HTTP
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return await response.json();
            } catch (error) {
                console.error('Error en la solicitud AJAX:', error);
                alert('Hubo un error al procesar tu solicitud. Por favor, inténtalo de nuevo.');
                return { success: false, message: 'Error de conexión o servidor.' };
            }
        }

        // Manejar clics en los elementos de notificación para mostrar detalles
        notificationListContent.addEventListener('click', async (event) => {
            const item = event.target.closest('.notification-item');
            if (item) {
                // No procesar clics en el checkbox o el botón de eliminar individual
                if (event.target.classList.contains('notification-checkbox') || event.target.classList.contains('delete-single-btn')) {
                    return;
                }

                // Desactivar cualquier notificación activa previamente
                document.querySelectorAll('.notification-item.active').forEach(activeItem => {
                    activeItem.classList.remove('active');
                });

                // Marcar la notificación actual como activa
                item.classList.add('active');

                const id = item.dataset.id;
                const mensaje = item.dataset.mensaje;
                const fecha = item.dataset.fecha;
                const productoNombre = item.dataset.productoNombre;
                const productoDescripcion = item.dataset.productoDescripcion;
                const productoImagen = item.dataset.productoImagen;
                const productoPrecio = item.dataset.productoPrecio;
                const productoId = item.dataset.productoId;

                detailContent.classList.remove('no-selection');
                detailContent.innerHTML = `
                    <h3>${productoNombre || 'Detalle de Notificación'}</h3>
                    <span class="detail-date">${new Date(fecha).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                    ${productoImagen && productoImagen !== 'placeholder.jpg' ? `<img src="${productoImagen}" alt="${productoNombre}" class="product-image">` : ''}
                    <p><strong>Precio:</strong> ${productoPrecio}</p>
                    <p><strong>Mensaje:</strong> ${mensaje}</p>
                    <p><strong>Descripción del Producto:</strong> ${productoDescripcion}</p>
                    ${productoId ? `<p><a href="/productos/detalle.php?id=${productoId}" target="_blank">Ver Producto</a></p>` : ''}
                `;

                // Marcar como leído si no está ya leído
                if (item.classList.contains('unread')) {
                    // La petición ahora va al controlador
                    const response = await sendAjaxRequest('marcarComoLeido', { ids: [id] });
                    if (response.success) {
                        item.classList.remove('unread');
                        console.log(`Notificación ${id} marcada como leída en la DB.`);
                    } else {
                        console.error('Fallo al marcar como leído:', response.message);
                    }
                }
            }
        });

        // Manejar selección de checkboxes (sin cambios)
        notificationListContent.addEventListener('change', (event) => {
            if (event.target.classList.contains('notification-checkbox')) {
                const item = event.target.closest('.notification-item');
                if (event.target.checked) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }
                updateDeleteSelectedButton();
            }
        });

        // Seleccionar/Deseleccionar todas las notificaciones (sin cambios en JS, solo el evento)
        selectAllBtn.addEventListener('click', () => {
            const allCheckboxes = document.querySelectorAll('.notification-checkbox');
            const allSelected = Array.from(allCheckboxes).every(cb => cb.checked);

            allCheckboxes.forEach(cb => {
                cb.checked = !allSelected;
                const item = cb.closest('.notification-item');
                if (cb.checked) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }
            });
            updateDeleteSelectedButton();
        });

        // Eliminar notificación individual (botón 'X')
        notificationListContent.addEventListener('click', async (event) => {
            if (event.target.classList.contains('delete-single-btn')) {
                event.stopPropagation(); // Evitar que el clic se propague al item principal
                const notificationId = event.target.dataset.id;
                if (confirm(`¿Estás seguro de que quieres eliminar esta notificación (ID: ${notificationId})?`)) {
                    // La petición ahora va al controlador
                    const response = await sendAjaxRequest('eliminar', { ids: [notificationId] });
                    if (response.success) {
                        console.log(`Eliminando notificación con ID: ${notificationId}`);
                        const itemToRemove = event.target.closest('.notification-item');
                        const isActive = itemToRemove.classList.contains('active');
                        itemToRemove.remove();
                        updateDeleteSelectedButton();

                        if (isActive) {
                            detailContent.innerHTML = '<p>Selecciona una notificación para ver los detalles.</p>';
                            detailContent.classList.add('no-selection');
                        }
                        if (!notificationListContent.querySelector('.notification-item')) {
                            notificationListContent.innerHTML = '<p class="no-notifications">No tienes notificaciones por el momento.</p>';
                        }
                    } else {
                        console.error('Fallo al eliminar notificación individual:', response.message);
                    }
                }
            }
        });

        // Eliminar notificaciones seleccionadas
        deleteSelectedBtn.addEventListener('click', async () => {
            const checkedCheckboxes = document.querySelectorAll('.notification-checkbox:checked');
            if (checkedCheckboxes.length > 0) {
                if (confirm(`¿Estás seguro de que quieres eliminar ${checkedCheckboxes.length} notificaciones seleccionadas?`)) {
                    const idsToDelete = [];
                    const activeId = document.querySelector('.notification-item.active')?.dataset.id;
                    checkedCheckboxes.forEach(cb => {
                        const item = cb.closest('.notification-item');
                        idsToDelete.push(item.dataset.id);
                    });

                    // La petición ahora va al controlador
                    const response = await sendAjaxRequest('eliminar', { ids: idsToDelete });
                    if (response.success) {
                        console.log(`Eliminando notificaciones con IDs: ${idsToDelete.join(', ')}`);
                        checkedCheckboxes.forEach(cb => cb.closest('.notification-item').remove());
                        updateDeleteSelectedButton();

                        if (idsToDelete.includes(activeId)) { // Si la notificación activa fue eliminada
                            detailContent.innerHTML = '<p>Selecciona una notificación para ver los detalles.</p>';
                            detailContent.classList.add('no-selection');
                        }
                        if (!notificationListContent.querySelector('.notification-item')) {
                            notificationListContent.innerHTML = '<p class="no-notifications">No tienes notificaciones por el momento.</p>';
                        }
                    } else {
                        console.error('Fallo al eliminar notificaciones seleccionadas:', response.message);
                    }
                }
            }
        });

        // Eliminar todas las notificaciones
        deleteAllBtn.addEventListener('click', async () => {
            if (confirm('¿Estás seguro de que quieres eliminar TODAS tus notificaciones? Esta acción es irreversible.')) {
                // El ID del usuario se obtiene del lado del servidor en el controlador.
                // No necesitamos enviarlo desde aquí, a menos que el controlador lo espere explícitamente.
                // Para este ejemplo, el controlador ya lo saca de la sesión.
                const response = await sendAjaxRequest('eliminarTodas', {}); // Petición sin body si el user ID está en sesión
                if (response.success) {
                    console.log('Eliminando todas las notificaciones.');
                    notificationListContent.innerHTML = '<p class="no-notifications">No tienes notificaciones por el momento.</p>';
                    detailContent.innerHTML = '<p>Selecciona una notificación para ver los detalles.</p>';
                    detailContent.classList.add('no-selection');
                    updateDeleteSelectedButton();
                } else {
                    console.error('Fallo al eliminar todas las notificaciones:', response.message);
                }
            }
        });

        // Inicializar el estado del botón al cargar
        updateDeleteSelectedButton();
    });
    </script>
</body>
</html>