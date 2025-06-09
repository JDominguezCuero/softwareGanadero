<?php
    require_once(__DIR__ . '../../../../config/config.php');
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<!-- Sidebar -->
    <aside id="sidebar" class="bg-green-900 text-white w-64 transition-all duration-300 flex flex-col p-4 h-full">
            <div class="flex justify-between items-center mb-6">
                <button id="toggleBtn" onclick="toggleSidebar()" class="text-white hover:text-gray-200">
                    <i data-lucide="chevrons-left"></i>
                </button>
            </div>    
            <div id="userProfile" class="text-center">
                <img src="https://bootdey.com/img/Content/avatar/avatar6.png" class="w-20 h-20 rounded-full mx-auto mb-2">
                <h4 class="text-lg font-semibold"><?php echo htmlspecialchars($_SESSION['nombre']); ?></h4>
                <p class="text-sm text-gray-300">@<?php echo htmlspecialchars($_SESSION['usuario']); ?></p>
                <button class="bg-green-700 mt-4 px-4 py-2 rounded hover:bg-green-800">Editar</button>
            </div>

            <nav class="mt-10 space-y-4">
                <a href="<?= BASE_URL ?>/public/index_controller.php" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded">
                    <i data-lucide="home"></i>
                    <span id="textInicio">Inicio</span>
                </a>
                <a href="<?= BASE_URL ?>/modules/productos/controller.php?accion=listar" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded">
                    <i data-lucide="store"></i>
                    <span id="textProducto">Productos</span>
                </a>
                <a href="<?= BASE_URL ?>/modules/inventario/controller.php?accion=listar" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded">
                    <i data-lucide="box"></i>
                    <span id="textInventario">Inventario</span>
                </a>
                <a href="<?= BASE_URL ?>/modules/simulador/views/menuPrincipal.php" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded">
                    <i data-lucide="activity"></i>
                    <span id="textSimulacion">Simulación</span>
                </a>
                <a href="<?= BASE_URL ?>/modules/simulador/views/menuPrincipal.php" class="flex items-center space-x-2 hover:bg-green-800 p-2 rounded">
                    <i data-lucide="user"></i>
                    <span id="textPerfil">Administrar Usuarios</span>
                </a>
            </nav>

            <!-- Botón en la parte de abajo -->
            <div class="mt-auto pt-4">
                <a href="<?= BASE_URL ?>/modules/inicio/controller.php?accion=logout" class="flex items-center space-x-2 hover:bg-red-700 p-2 rounded bg-red-600">
                    <i data-lucide="log-out"></i>
                    <span id="textCerrarSesion">Cerrar Sesión</span>
                </a>
            </div>
    </aside>

      <!-- SIDEBAR -->
    <script>
        lucide.createIcons();

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const isCollapsed = sidebar.classList.contains('w-64');
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('w-20');

            const userProfile = document.getElementById('userProfile');
            if (isCollapsed) {
                userProfile.classList.add('hidden');
            } else {
                userProfile.classList.remove('hidden');
            }

            const texts = ['textInicio', 'textProducto', 'textInventario', 'textSimulacion', 'textPerfil', 'textCerrarSesion'];
            texts.forEach(id => {
                const el = document.getElementById(id);
                if (isCollapsed) {
                    el.classList.add('hidden');
                } else {
                    el.classList.remove('hidden');
                }
            });

            const toggleBtn = document.getElementById('toggleBtn');
            const toggleIcon = toggleBtn.querySelector('i[data-lucide]');
            const newIcon = isCollapsed ? 'chevrons-right' : 'chevrons-left';
            toggleIcon.setAttribute('data-lucide', newIcon);
            lucide.createIcons();
        }

    </script>
