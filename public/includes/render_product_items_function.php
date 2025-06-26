<?php
// public/render_product_items_function.php

/**
 * Renderiza una lista de productos en el formato HTML de 'product-item'.
 *
 * @param array $products Array de productos, cada uno con sus datos.
 * @param bool $show_old_price Indica si se debe mostrar el precio anterior.
 * @return void
 */
function renderProductItems(array $products, bool $show_old_price = false): void {
    if (empty($products)) {
        // Puedes imprimir un mensaje si no hay productos, o simplemente no renderizar nada.
        // echo '<p class="no-products-message">No hay productos disponibles en esta categoría.</p>';
        return;
    }

    // echo '<div class="products-grid">';

    foreach ($products as $product) {
        // Asegúrate de que todas las claves existan y sean seguras para HTML
        $id_producto = htmlspecialchars($product['id_producto'] ?? '');
        $nombre_producto = htmlspecialchars($product['nombre_producto'] ?? 'Producto Desconocido');
        $descripcion_corta = htmlspecialchars(mb_strimwidth($product['descripcion_producto'] ?? '', 0, 100, '...'));
        $precio_unitario = number_format($product['precio_unitario'] ?? 0, 2);
        $precio_anterior = number_format($product['precio_anterior'] ?? 0, 2); // Puede ser 0 si no existe
        $imagen_url = htmlspecialchars($product['imagen_url'] ?? 'placeholder.jpg');
        $estado_oferta = (bool)($product['estado_oferta'] ?? false);
        $id_usuario_vendedor = htmlspecialchars($product['id_usuario'] ?? ''); 
        
        // Lógica para determinar si es "nuevo" basada en la fecha de publicación
        $es_nuevo = false;
        if (isset($product['fecha_publicacion'])) {
            $fecha_publicacion = new DateTime($product['fecha_publicacion']);
            $fecha_actual = new DateTime();
            $intervalo = $fecha_publicacion->diff($fecha_actual);
            // Considerar 'nuevo' si fue publicado en los últimos 30 días
            if ($intervalo->days <= 30) {
                $es_nuevo = true;
            }
        }

        $nombre_usuario = htmlspecialchars($product['nombre_usuario'] ?? '');
        $telefono_usuario = htmlspecialchars($product['telefono_usuario'] ?? '');
        // Asegúrate de que tu modelo devuelva 'correo_usuario' para esta clave
        $email_usuario = htmlspecialchars($product['correo_usuario'] ?? '');
        // Agrega el campo de dirección si lo estás usando y lo recuperas del modelo
        $direccion_usuario = htmlspecialchars($product['direccion_usuario'] ?? '');

        ?>
        <div class="product-item" data-product-id="<?= $id_producto ?>">
            <div class="p-portada">
                <img src="<?= $imagen_url ?>" alt="<?= $nombre_producto ?>">
                <?php if ($estado_oferta): ?>
                    <span class="stin stin-oferta">Oferta</span>
                <?php endif; ?>
                <!-- <?php if ($es_nuevo): ?>
                    <span class="stin stin-new">Nuevo</span>
                <?php endif; ?> -->
            </div>
            <div class="p-info">
                <h3><?= $nombre_producto ?></h3>
                <p class="descripcion"><?= $descripcion_corta ?></p>
                <div class="precio">
                    <span>S/ <?= $precio_unitario ?></span>
                    <?php if ($show_old_price && ($product['precio_anterior'] ?? 0) > ($product['precio_unitario'] ?? 0)): ?>
                        <span class="thash">S/ <?= $precio_anterior ?></span>
                    <?php endif; ?>
                </div>
                <?php if (isset($product['nombre_usuario']) && !empty($product['nombre_usuario'])): ?>
                    <p class="seller-info">Vendido por: <strong><?= $nombre_usuario ?></strong></p>
                <?php else: ?>
                    <p class="seller-info">Vendedor no disponible</p>
                <?php endif; ?>
                <a  
                   class="hm-btn btn-primary uppercase contactar-vendedor" 
                   data-id-vendedor="<?= $id_usuario_vendedor ?>" 
                   data-id-producto="<?= $id_producto ?>">
                   Contactar Con El Vendedor
                </a>

            </div>
        </div>
        <?php
    }
    // echo '</div>'; 
}
?>