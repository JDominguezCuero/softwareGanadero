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
    <link rel="stylesheet" href="assets/css/principal.css"> </head>
<body>

    <div class="hm-wrapper">

        <?php include 'assets/layout/header.php'; ?>
        <button class="dark-toggle" onclick="toggleDarkMode()">Modo Oscuro</button>

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

    <?php
    /**
     * Renders a single product item HTML.
     * @param array $products An array of product data.
     * @param bool $show_old_price If true, displays the 'precio_anterior' with strikethrough.
     */
    function renderProductItems($products, $show_old_price = false) {
        if (empty($products)) {
            echo '<p class="text-center">No hay productos disponibles en esta sección.</p>';
            return;
        }
        foreach ($products as $product) {
            // Asegúrate de que las claves existen antes de usarlas, o proporciona un valor por defecto
            $id_producto = htmlspecialchars($product['id_producto'] ?? '');
            $nombre_producto = htmlspecialchars($product['nombre_producto'] ?? 'Producto Desconocido');
            $imagen_url = htmlspecialchars($product['imagen_url'] ?? 'assets/images/default_product.png'); // Asegúrate de tener una imagen por defecto
            $precio_unitario = number_format($product['precio_unitario'] ?? 0, 2);
            $precio_anterior = number_format($product['precio_anterior'] ?? 0, 2);
            $estado_oferta = ($product['estado_oferta'] ?? 0) == 1;
            $descripcion_corta = htmlspecialchars(substr($product['descripcion_producto'] ?? '', 0, 70)) . (strlen($product['descripcion_producto'] ?? '') > 70 ? '...' : '');

            $nombre_usuario = htmlspecialchars($product['nombre_usuario'] ?? 'N/A');
            $telefono_usuario = htmlspecialchars($product['telefono_usuario'] ?? '');
            $email_usuario = htmlspecialchars($product['correo_usuario'] ?? '');
            ?>
            <div class="product-item">
                <div class="p-portada">
                    <a href="detalle_producto.php?id=<?= $id_producto ?>">
                        <img src="<?= $imagen_url ?>" alt="<?= $nombre_producto ?>">
                    </a>
                    <?php if ($estado_oferta): ?>
                        <span class="stin stin-oferta">Oferta</span>
                    <?php endif; ?>
                </div>
                <div class="p-info">
                    <a href="detalle_producto.php?id=<?= $id_producto ?>">
                        <h3><?= $nombre_producto ?></h3>
                    </a>
                    <p class="descripcion"><?= $descripcion_corta ?></p>
                    <div class="precio">
                        <span>S/ <?= $precio_unitario ?></span>
                        <?php if ($show_old_price && ($product['precio_anterior'] ?? 0) > ($product['precio_unitario'] ?? 0)): ?>
                            <span class="thash">S/ <?= $precio_anterior ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($product['nombre_usuario']) && !empty($product['nombre_usuario'])): ?>
                        <p class="seller-info">Vendido por:
                            <strong><?= $nombre_usuario ?></strong>
                            <?php if (!empty($telefono_usuario)): ?>
                                <br>Tel: <a href="tel:<?= $telefono_usuario ?>"><?= $telefono_usuario ?></a>
                            <?php endif; ?>
                            <?php if (!empty($email_usuario)): ?>
                                <br>Email: <a href="mailto:<?= $email_usuario ?>"><?= $email_usuario ?></a>
                            <?php endif; ?>
                        </p>
                    <?php else: ?>
                        <p class="seller-info">Vendedor no disponible</p>
                    <?php endif; ?>
                    <a href="añadir_carrito.php?id=<?= $id_producto ?>" class="hm-btn btn-primary uppercase">AGREGAR AL CARRITO</a>
                </div>
            </div>
            <?php
        }
    }
    ?>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script src="assets/js/tienda_online.js"></script>

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