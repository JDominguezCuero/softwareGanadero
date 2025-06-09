<?php

require_once(__DIR__ . '../../../../config/config.php')

?>
        
        <button class="dark-toggle" onclick="toggleDarkMode()">Modo Oscuro</button>
        
<!-- =================================
           HEADER MENU
        ================================== -->
        <div class="hm-header">

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


                        <div class="hm-icon-cart">
                            <a href="#">
                                <i class="las la-shopping-cart"></i>
                                <span>0</span>
                            </a>
                        </div>

                        <div class="icon-menu">
                            <button type="button"><i class="fas fa-bars"></i></button>
                        </div>

                    </nav>

                </div>
            </div>

        </div>

        <!-- =================================
           HEADER MENU Movil
        ================================== -->
        <div class="header-menu-movil">
            <button class="cerrar-menu"><i class="fas fa-times"></i></button>
            <ul>
                <li><a href="index_controller.php">Productos</a></li>
                <li><a href="productos_controller.php">Catalogo de Productos</a></li>
                <li><a href="../modules/campanas/controller.php">Campañas</a></li>
                <li><a href="nosotros.php">Nosotros</a></li>
                <li><a href="contacto.php">Contacto</a></li>
            </ul>
        </div>

