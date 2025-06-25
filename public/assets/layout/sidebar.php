<?php
    require_once(__DIR__ . '../../../../config/config.php');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Leer el estado del sidebar de la cookie (si existe)
    // Por defecto, asumimos expandido si no hay cookie o la cookie es 'false'
    $isSidebarCollapsed = (isset($_COOKIE['sidebar_collapsed']) && $_COOKIE['sidebar_collapsed'] === 'true');

    $sidebarWidthClass = $isSidebarCollapsed ? 'w-20' : 'w-64';
    $userProfileHiddenClass = $isSidebarCollapsed ? 'hidden' : '';
    $textHiddenClass = $isSidebarCollapsed ? 'hidden' : ''; // Para los spans de texto
    $toggleIcon = $isSidebarCollapsed ? 'chevrons-right' : 'chevrons-left';
?>  

<aside id="sidebar" class="bg-green-900 text-white <?= $sidebarWidthClass ?> transition-all duration-300 flex flex-col p-4 h-full fixed top-0 left-0 h-screen overflow-y-auto z-50">
    <div class="flex justify-between items-center mb-6">
        <button id="toggleBtn" class="text-white hover:text-gray-200">
            <i data-lucide="<?= $toggleIcon ?>"></i>
        </button>
    </div>
    <div id="userProfile" class="text-center <?= $userProfileHiddenClass ?>">
        <img src="<?php echo htmlspecialchars($_SESSION['url_Usuario'] ?? ''); ?>" class="w-20 h-20 rounded-full mx-auto mb-2">
        <h4 class="text-lg font-semibold" style="color:white;"><?php echo htmlspecialchars($_SESSION['nombre'] ?? ''); ?></h4>
        <p class="text-sm text-gray-300">@<?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?></p>
        <button class="bg-green-700 mt-4 px-4 py-2 rounded hover:bg-green-800" style="color:white;">Editar</button>
    </div>

    <nav class="mt-10 space-y-4">
        <a href="<?= BASE_URL ?>/public/index_controller.php" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="home"></i>
            <span id="textInicio" class="<?= $textHiddenClass ?>">Inicio</span>
        </a>        
        <a href="<?= BASE_URL ?>/modules/notificaciones/controller.php?accion=listar" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="bell-dot"></i>
            <span id="textNotificacion" class="<?= $textHiddenClass ?>">Notificaciones</span>
        </a>
        <a href="<?= BASE_URL ?>/modules/productos/controller.php?accion=listar" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="store"></i>
            <span id="textProducto" class="<?= $textHiddenClass ?>">Productos</span>
        </a>
        <a href="<?= BASE_URL ?>/modules/inventario/controller.php?accion=listar" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="box"></i>
            <span id="textInventario" class="<?= $textHiddenClass ?>">Inventario</span>
        </a>
        <a href="<?= BASE_URL ?>/modules/simulador/views/menuPrincipal.php" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
            <i data-lucide="activity"></i>
            <span id="textSimulacion" class="<?= $textHiddenClass ?>">Simulación</span>
        </a>
        
        
        <?php
            if (isset($_SESSION['usuario']) && $_SESSION['rol'] == 1) {
            echo '<a href="'. BASE_URL . '/modules/auth/controller.php?accion=listar" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded" style="color:white;">
                    <i data-lucide="user"></i>
                    <span id="textPerfil" class="<?= $textHiddenClass ?>">Administrar Usuarios</span>
                </a>';
            }
        ?>
    </nav>

    <div class="mt-auto pt-4">
        <a href="<?= BASE_URL ?>/modules/inicio/controller.php?accion=logout" class="flex items-center space-x-2 hover:bg-red-700 p-2 rounded bg-red-600" style="color:white;">
            <i data-lucide="log-out"></i>
            <span id="textCerrarSesion" class="<?= $textHiddenClass ?>">Cerrar Sesión</span>
        </a>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();

        // Haz estas variables opcionales
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggleBtn'); 
        const userProfile = document.getElementById('userProfile'); 
        const texts = ['textInicio', 'textProducto', 'textInventario', 'textSimulacion', 'textPerfil', 'textCerrarSesion', 'textNotificacion'];
        
        const mainContent = document.getElementById('mainContent');
        const mainHeader = document.getElementById('mainHeader'); // Este es el que nos da problemas si no está

        // Determinar si el sidebar existe (si el usuario está logueado y se incluyó)
        const isSidebarPresent = !!sidebar; 

        if (!mainContent) { // Solo es crucial que mainContent exista
            console.error("Error: El elemento mainContent no fue encontrado. El layout podría no funcionar correctamente.");
            return;
        }

        // Función para aplicar el estado del layout
        function applyLayoutState(isCollapsed = true) { 
            let sidebarWidth = 0;
            let marginLeftForContent = 0;

            if (isSidebarPresent) {
                // Lógica para el sidebar si está presente
                const toggleIconSvg = toggleBtn ? toggleBtn.querySelector('svg') : null; // Comprueba si toggleBtn existe
                if (!toggleIconSvg) {
                    console.warn("Advertencia: No se encontró el elemento SVG (el ícono de Lucide) dentro del botón de toggle.");
                    // No hacemos 'return' aquí para que el resto del layout pueda seguir ajustándose.
                }

                if (isCollapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    if (userProfile) userProfile.classList.add('hidden'); // Comprueba si userProfile existe
                    texts.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.classList.add('hidden');
                    });
                    if (toggleIconSvg) toggleIconSvg.setAttribute('data-lucide', 'chevrons-right');
                    sidebarWidth = 80; 
                } else {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    if (userProfile) userProfile.classList.remove('hidden'); // Comprueba si userProfile existe
                    texts.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.classList.remove('hidden');
                    });
                    if (toggleIconSvg) toggleIconSvg.setAttribute('data-lucide', 'chevrons-left');
                    sidebarWidth = 256; 
                }
                marginLeftForContent = sidebarWidth; 
                lucide.createIcons(); 
            } else {
                // Si el sidebar NO está presente
                marginLeftForContent = 0;
            }
            
            // Ajustar el margen izquierdo del main
            mainContent.style.marginLeft = `${marginLeftForContent}px`;

            // Ajustar la posición y ancho del header SOLO SI EXISTE
            if (mainHeader) { 
                mainHeader.style.left = `${marginLeftForContent}px`;
                mainHeader.style.width = `calc(100% - ${marginLeftForContent}px)`;
            }
        }

        // Aplicar el estado inicial del layout al cargar la página
        if (isSidebarPresent) {
            const isSidebarCollapsedInitial = (document.cookie.split('; ').find(row => row.startsWith('sidebar_collapsed=')) || '=false').split('=')[1] === 'true';
            applyLayoutState(isSidebarCollapsedInitial);
        } else {
            applyLayoutState(false); 
        }
        
        // Manejar el clic del botón de toggle solo si el sidebar y el botón están presentes
        if (isSidebarPresent && toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const isCurrentlyCollapsed = sidebar.classList.contains('w-20');
                const newState = !isCurrentlyCollapsed;

                applyLayoutState(newState);

                document.cookie = "sidebar_collapsed=" + newState + "; path=/; max-age=" + (365 * 24 * 60 * 60);
            });
        }
    });
</script>