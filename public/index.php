<?php

if (isset($_GET['login']) && $_GET['login'] == 'error' && isset($_GET['reason'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('🤖 Mensaje del Sistema', 'Sesión del usuario cerrada', 'error');
        });
    </script>";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home Master Store</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/principal.css"> 
    <link rel="stylesheet" href="assets/css/detalleProducto.css">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>    
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body class="min-h-screen flex bg-gray-100">
    <div class="flex min-h-screen w-full">
        <?php
            if (isset($_SESSION['usuario'])) {
                include 'assets/layout/sidebar.php';
            }
        ?>
            
        <?php include 'assets/layout/header.php'; ?>
        
        <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">   
            
            <div class="hm-wrapper">
                            
                <header>
                    <div class="carousel">
                        <img src="assets/images/index1.png" alt="Ganadería 1">
                        <img src="assets/images/index2.png" alt="Ganadería 2">
                        <img src="assets/images/index3.png" alt="Ganadería 3">
                        <img src="assets/images/index4.png" alt="Ganadería 4">
                    </div>
                    <div class="header-content">
                        <h1>Nuestros Productos del Campo</h1>
                        <p>Descubre y adquiere todo lo que necesitas para tu finca. Simplifica tus compras con nuestra plataforma online, diseñada para tu comodidad y la eficiencia de tu producción.</p>
                    </div>
                </header>

                <div class="hm-page-block">
                    <div class="container">
                        <div class="header-title">
                            <h1 data-aos="fade-up" data-aos-duration="3000">Categorías</h1>
                        </div>

                        <div class="hm-grid-category">
                            <div class="grid-item" data-aos="fade-up" data-aos-duration="1000">
                                <a href="#">
                                    <img src="assets/images/c-1.png" alt="">
                                    <div class="c-info">
                                        <h3>Todo en Productos Frescos</h3>
                                    </div>
                                </a>
                            </div>
                            <div class="grid-item" data-aos="fade-up" data-aos-duration="1500">
                                <a href="#">
                                    <img src="assets/images/c-2.png" alt="">
                                    <div class="c-info">
                                        <h3>Todo en Lácteos y Huevos</h3>
                                    </div>
                                </a>
                            </div>
                            <div class="grid-item" data-aos="fade-up" data-aos-duration="2000">
                                <a href="#">
                                    <img src="assets/images/c-3.png" alt="">
                                    <div class="c-info">
                                        <h3>Lo Mejor en Carnes y Embutidos</h3>
                                    </div>
                                </a>
                            </div>
                            <div class="grid-item" data-aos="fade-up" data-aos-duration="2000">
                                <a href="#">
                                    <img src="assets/images/c-4.png" alt="">
                                    <div class="c-info">
                                        <h3>Alimentos para Animales</h3>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hm-page-block all-products-section bg-fondo">
                    <div class="container">
                        <div class="header-title" data-aos="fade-up">
                            <h1>Todos los Productos Publicados</h1>
                            <a href="productos_controller.php" class="view-all-btn">Ver Todos <i class="las la-angle-right"></i></a>
                        </div>

                        <div class="carousel-container product-carousel-all" data-aos="fade-up">
                            <button class="carousel-btn prev-btn"><i class="las la-angle-left"></i></button>
                            <div class="carousel-track">

                                <?php
                                    // Solo mostramos una cantidad limitada de productos aquí, el resto en la página "Ver Todos"
                                    $productos_a_mostrar_en_carrusel = array_slice($todos_los_productos, 0, 10); // Mostrar solo 10 productos en el carrusel
                                    if (!empty($productos_a_mostrar_en_carrusel)) {
                                        renderProductItems($productos_a_mostrar_en_carrusel);
                                    } else {
                                        echo '<p class="text-center">No hay productos disponibles en este momento.</p>';
                                    }
                                ?>
                                
                            </div>
                            <button class="carousel-btn next-btn"><i class="las la-angle-right"></i></button>
                        </div>
                    </div>
                </div>

                <div class="hm-page-block">
                    <div class="container">
                        <div class="header-title" data-aos="fade-up">
                            <h1>Productos Populares</h1>
                        </div>

                        <ul class="hm-tabs" data-aos="fade-up">
                            <?php $first_tab = true; ?>
                            <?php foreach ($productos_populares_tabs as $categoria_nombre => $productos_list): ?>
                                <li class="hm-tab-link <?= $first_tab ? 'active' : '' ?>" data-tab="tab-<?= htmlspecialchars(str_replace(' ', '-', strtolower($categoria_nombre))) ?>">
                                    <?= htmlspecialchars($categoria_nombre) ?>
                                </li>
                                <?php $first_tab = false; ?>
                            <?php endforeach; ?>
                            <?php if (empty($productos_populares_tabs)): ?>
                                <li class="hm-tab-link active" data-tab="tab-default">Sin categorías populares</li>
                            <?php endif; ?>
                        </ul>

                        <?php $first_content = true; ?>
                        <?php if (!empty($productos_populares_tabs)): ?>
                            <?php foreach ($productos_populares_tabs as $categoria_nombre => $productos_list): ?>
                                <div class="tabs-content <?= $first_content ? 'active' : '' ?>" id="tab-<?= htmlspecialchars(str_replace(' ', '-', strtolower($categoria_nombre))) ?>" data-aos="fade-up">
                                    <?php if (!empty($productos_list)): ?>
                                        <?php $show_old_price_for_this_tab = ($categoria_nombre === 'En Oferta'); ?>
                                        <div class="carousel-container">
                                            <button class="carousel-btn prev-btn"><i class="las la-angle-left"></i></button>
                                            <div class="carousel-track">
                                                <?php renderProductItems($productos_list, $show_old_price_for_this_tab); ?>
                                            </div>
                                            <button class="carousel-btn next-btn"><i class="las la-angle-right"></i></button>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-center">No hay productos disponibles en la categoría "<?= htmlspecialchars($categoria_nombre) ?>" en este momento.</p>
                                    <?php endif; ?>
                                </div>
                                <?php $first_content = false; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="tabs-content active" id="tab-default" data-aos="fade-up">
                                <p class="text-center">No se encontraron productos populares ni categorías disponibles.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php include 'assets/layout/flooter.php'; ?>

            </div>
        </main>
    </div>

    
    <div id="productDetailModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span> <div id="modal-body-content">
                <div class="product-detail-loading">Cargando detalles del producto...</div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="assets/js/tienda_online.js"></script>
    <script src="assets/js/product_modal.js"></script> </body>
    
    <?php include '../../modules/auth/layout/mensajesModal.php'; ?>

    <script>
        AOS.init({
            duration: 1200,
        });

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled');
            } else {
                localStorage.setItem('darkMode', 'disabled');
            }
        }

        function applyDarkModeOnLoad() {
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
            }
        }
        document.addEventListener('DOMContentLoaded', applyDarkModeOnLoad);

        // Lógica para manejar las pestañas (tabs)
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.hm-tab-link');
            const tabContents = document.querySelectorAll('.tabs-content');

            tabLinks.forEach(link => {
                link.addEventListener('click', function() {
                    tabLinks.forEach(item => item.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));

                    this.classList.add('active');
                    const targetTabId = this.getAttribute('data-tab');
                    const targetContent = document.getElementById(targetTabId);
                    if (targetContent) {
                        targetContent.classList.add('active');
                    }
                    // Re-inicializar AOS para el contenido de la pestaña si es necesario
                    AOS.refresh();
                });
            });

            // Activar la primera pestaña por defecto al cargar la página si no hay una activa
            const activeTab = document.querySelector('.hm-tab-link.active');
            if (!activeTab && tabLinks.length > 0) {
                tabLinks[0].classList.add('active');
                const firstTabContent = document.getElementById(tabLinks[0].getAttribute('data-tab'));
                if (firstTabContent) {
                    firstTabContent.classList.add('active');
                }
            }
        });

        // Lógica para los carruseles horizontales
        document.addEventListener('DOMContentLoaded', function() {
            const carouselContainers = document.querySelectorAll('.carousel-container');

            carouselContainers.forEach(container => {
                const carouselTrack = container.querySelector('.carousel-track');
                const prevBtn = container.querySelector('.prev-btn');
                const nextBtn = container.querySelector('.next-btn');

                if (!carouselTrack || !prevBtn || !nextBtn) {
                    console.warn("Elementos del carrusel no encontrados en el contenedor:", container);
                    return; // Salir si los elementos no se encuentran
                }

                const productItem = carouselTrack.querySelector('.product-item');
                let scrollAmount = 300; // Valor por defecto

                // Intenta calcular el scrollAmount dinámicamente si hay un product-item
                if (productItem) {
                    const itemStyle = getComputedStyle(productItem);
                    const itemWidth = parseFloat(itemStyle.width);
                    const itemMarginRight = parseFloat(itemStyle.marginRight);
                    // O el gap si lo tienes definido con grid-gap
                    const gap = parseFloat(getComputedStyle(carouselTrack).gap || 0);

                    // Desplazar aproximadamente 3 elementos o un valor fijo si no se puede calcular
                    // Puedes ajustar '3' a la cantidad de elementos que quieres ver por scroll.
                    scrollAmount = (itemWidth + itemMarginRight + gap) * 3;
                    if (isNaN(scrollAmount) || scrollAmount === 0) {
                        scrollAmount = 300; // Fallback
                    }
                }

                nextBtn.addEventListener('click', () => {
                    carouselTrack.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                });

                prevBtn.addEventListener('click', () => {
                    carouselTrack.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>

</body>
</html>