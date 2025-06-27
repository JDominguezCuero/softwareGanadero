<?php
require_once(__DIR__ . '../../../../config/config.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$isUserLoggedIn = isset($_SESSION['usuario']);

$headerClasses = "hm-header fixed top-0 z-40 transition-all duration-300";

if (!$isUserLoggedIn) {
  $headerClasses .= " left-0 w-full";
} else {
  $headerClasses .= " right-0"; // Mantenemos right-0 para que el JS calcule el left y width
}
?>
    
  
<div id="mainHeader" class="<?= $headerClasses ?>">
    <div class="container">
        <div class="header-menu">

            <div class="hm-logo">
                <a href="<?= BASE_URL ?>/public/index_controller.php">
                <img src="<?= BASE_URL ?>/public/assets/images/logo1.png" alt="">
                </a>
            </div>

            <nav class="hm-menu">
                <ul>
                <li><a href="<?= BASE_URL ?>/public/index_controller.php">Productos</a></li>
                <li><a href="<?= BASE_URL ?>/public/productos_controller.php">Catalogo de Productos</a></li>
                <li><a href="<?= BASE_URL ?>/modules/campanas/controller.php">Campañas</a></li>
                <li><a href="<?= BASE_URL ?>/public/nosotros.php">Nosotros</a></li>
                <li><a href="<?= BASE_URL ?>/public/contacto.php">Contacto</a></li>
                <?php
                    if (!isset($_SESSION['usuario'])) {
                    echo '<li><a href="'. BASE_URL .'/modules/auth/views/autenticacion.php">Ingresar</a></li>';
                    }
                ?>
                </ul>

                <div class="flex items-center space-x-4"> 
                    
                    <div class="hm-icon-cart">
                        <a href="#">
                            <i class="las la-shopping-cart"></i>
                            <span>0</span>
                        </a>
                    </div>
                    
                    <button class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 flex items-center justify-center -mt-2" onclick="toggleDarkMode()">
                        <i class="las la-moon text-3xl text-gray-800 dark:text-gray-200"></i>
                    </button>

                    <!-- <button class="dark-toggle w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200" onclick="toggleDarkMode()">
                        <i class="las la-moon text-3xl text-gray-800 dark:text-gray-200 -mt-0.5"></i>
                    </button> -->


                    <!-- Botón de campana -->
<?php if (isset($_SESSION['usuario'])): ?>
    <button class="notification-btn relative" onclick="showNotifications()">
        <i class="fas fa-bell text-lg"></i>
        <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs font-bold rounded-full h-4 w-4 flex items-center justify-center"></span>
    </button>
<?php endif; ?>

<!-- Panel de Notificaciones -->
<div id="notifications-panel" class="absolute right-5 top-14 bg-white rounded shadow-md w-80 z-50 hidden">
    <div class="p-3 border-b font-bold text-green-800">Notificaciones</div>
    <div id="notifications-container" class="p-3 max-h-80 overflow-y-auto">
        <!-- Aquí se cargan dinámicamente -->
         <div class="notifications-container">
                <div class="notifications-list">
                    <h2>Tus Notificaciones</h2>
                    <div id="notificationListContent">
                        <?php if (empty($obtener_notificaciones)): ?>
                            <p class="no-notifications">No tienes notificaciones por el momento.</p>
                        <?php else: ?>
                            <?php foreach ($obtener_notificaciones as $notificacion): ?>
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
    </div>
</div>
<script src="/LoginADSO/public/assets/js/notificaciones.js"></script>



                    
                    <div class="icon-menu">
                        <button type="button"><i class="fas fa-bars"></i></button>
                    </div>
                </div> 
            </nav>

        </div>
    </div>
</div>

<div class="header-menu-movil">
    <button class="cerrar-menu"><i class="fas fa-times"></i></button>
    <ul>
    <li><a href="index_controller.php">Productos</a></li>
       <li><a href="productos_controller.php">Catalogo de Productos</a></li>
    <li><a href="../modules/campanas/controller.php">Campañas</a></li>
     <li><a href="nosotros.php">Nosotros</a></li>
     <li><a href="contacto.php">Contacto</a></li>
        <?php
            if (!isset($_SESSION['usuario'])) {
            echo '<li><a href="'. BASE_URL .'/modules/auth/views/autenticacion.php">Ingresar</a></li>';
            }
        ?>
    </ul>
</div>