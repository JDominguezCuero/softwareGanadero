<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todos los Productos - AgroMarket</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/line-awesome@1.3.0/dist/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="assets/css/principal.css">
    <link rel="stylesheet" href="assets/css/detalleProducto.css">
</head>
<body>

    <?php include 'assets/layout/header.php'; ?>
    <button class="dark-toggle" onclick="toggleDarkMode()">Modo Oscuro</button>

    <div class="hm-page-block">
        <div class="container">
            <div class="header-title" data-aos="fade-up">
                <h1>Catálogo de Productos</h1>
            </div>

            <div class="product-list-container">
                <aside class="filters-sidebar" data-aos="fade-right">
                    <h2>Filtros</h2>
                    <form action="productos_controller.php" method="GET"> 
                        <div class="filter-group">
                            <label for="categoria">Categoría:</label>
                            <select name="categoria" id="categoria" onchange="this.form.submit()">
                                <option value="" <?= ($filtro_categoria_id === null) ? 'selected' : '' ?>>Todas las Categorías</option>
                                <?php foreach ($todas_las_categorias as $categoria): ?>
                                    <option value="<?= htmlspecialchars($categoria['id_categoria']) ?>"
                                        <?= ($filtro_categoria_id !== null && (string)$filtro_categoria_id === (string)$categoria['id_categoria']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($categoria['nombre_categoria']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label for="buscar">Buscar:</label>
                            <input type="text" name="buscar" id="buscar" placeholder="Nombre o descripción" value="<?= htmlspecialchars($filtro_busqueda ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label>Precio:</label>
                            <input type="number" name="precio_min" placeholder="Mín." value="<?= htmlspecialchars($filtro_precio_min ?? '') ?>">
                            <input type="number" name="precio_max" placeholder="Máx." value="<?= htmlspecialchars($filtro_precio_max ?? '') ?>">
                        </div>

                        <div class="filter-group">
                            <label for="ordenar_por">Ordenar por:</label>
                            <select name="ordenar_por" id="ordenar_por" onchange="this.form.submit()"> <option value="fecha_reciente" <?= ($ordenar_por == 'fecha_reciente') ? 'selected' : '' ?>>Fecha (más reciente)</option>
                                <option value="precio_asc" <?= ($ordenar_por == 'precio_asc') ? 'selected' : '' ?>>Precio (menor a mayor)</option>
                                <option value="precio_desc" <?= ($ordenar_por == 'precio_desc') ? 'selected' : '' ?>>Precio (mayor a menor)</option>
                                <option value="nombre_asc" <?= ($ordenar_por == 'nombre_asc') ? 'selected' : '' ?>>Nombre (A-Z)</option>
                            </select>
                        </div>

                        <button type="submit" class="hm-btn btn-primary uppercase">Aplicar Filtros</button>
                    </form>
                </aside>

                <main class="main-content" data-aos="fade-left">
                    <?php if (!empty($productos_por_categoria_en_listado)): ?>
                        <?php foreach ($productos_por_categoria_en_listado as $nombre_categoria => $productos): ?>
                            <?php if (!empty($productos)): ?>
                                <div class="category-section">
                                    <h2><?= htmlspecialchars($nombre_categoria) ?></h2>
                                    <div class="products-grid">
                                        <?php renderProductItems($productos, true); ?>
                                    </div>
                                    <div class="text-center mt-30">
                                        <a href="productos_controller.php?categoria=<?= htmlspecialchars($productos[0]['id_categoria'] ?? '') ?>&buscar=<?= htmlspecialchars($filtro_busqueda ?? '') ?>&precio_min=<?= htmlspecialchars($filtro_precio_min ?? '') ?>&precio_max=<?= htmlspecialchars($filtro_precio_max ?? '') ?>&ordenar_por=<?= htmlspecialchars($ordenar_por ?? '') ?>" class="hm-btn btn-primary uppercase">Ver todos en <?= htmlspecialchars($nombre_categoria) ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-results">No se encontraron productos que coincidan con los filtros aplicados en ninguna categoría.</p>
                    <?php endif; ?>
                </main>
            </div>
        </div>
    </div>

    <div id="productDetailModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span> <div id="modal-body-content">
                <div class="product-detail-loading">Cargando detalles del producto...</div>
            </div>
        </div>
    </div>

    <script src="assets/js/product_modal.js"></script> </body>
    <?php include 'assets/layout/flooter.php'; ?>
    <script src="assets/js/tienda_online.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <script>
        AOS.init();

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');

            // Comprobar si el modo oscuro está activo en el body
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled'); // Guardar el estado
            } else {
                localStorage.setItem('darkMode', 'disabled'); // Guardar el estado
            }
        }

        // Función para aplicar el modo oscuro al cargar la página
        function applyDarkModeOnLoad() {
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
            }
        }

        // Ejecutar la función al cargar el DOM
        document.addEventListener('DOMContentLoaded', applyDarkModeOnLoad);
    </script>
</body>
</html>