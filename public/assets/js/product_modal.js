// assets/js/product_modal.js

document.addEventListener('DOMContentLoaded', () => {
    // 1. Seleccionar elementos del DOM
    const productItems = document.querySelectorAll('.product-item'); // Todas las tarjetas de producto
    const modal = document.getElementById('productDetailModal'); // El modal en sí
    const closeButton = document.querySelector('.close-button'); // El botón para cerrar el modal (la 'x')
    const modalBodyContent = document.getElementById('modal-body-content'); // El div donde se cargará el contenido del producto

    // 2. Añadir un Event Listener a cada tarjeta de producto
    productItems.forEach(item => {
        item.addEventListener('click', (event) => {
            // Evitar que el clic en el botón de "Añadir al Carrito" también abra el modal
            // Si el elemento clickeado o uno de sus padres cercanos tiene la clase 'hm-btn', se ignora el clic en la tarjeta.
            if (event.target.closest('.hm-btn')) {
                return; 
            }

            // Obtener el ID del producto de la tarjeta clicada.
            // Necesitamos que cada tarjeta de producto tenga un atributo 'data-product-id'.
            // Ejemplo: <div class="product-item" data-product-id="123">...</div>
            const productId = item.dataset.productId; 
            
            // Mostrar un mensaje de carga dentro del modal mientras esperamos los datos
            modalBodyContent.innerHTML = '<div class="product-detail-loading">Cargando detalles del producto...</div>';
            modal.style.display = 'flex'; // Hace que el modal sea visible (usando flex para el centrado CSS)

            fetch(`productos_controller.php?product_id=${productId}`) 
                .then(response => {
                    // Verificar si la respuesta de la red fue exitosa (código 200 OK)
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json(); // Parsea la respuesta JSON
                })
                .then(productData => {
                    // 4. Procesar los datos recibidos y mostrar en el modal
                    if (productData && !productData.error) {
                        // Si los datos son válidos y no hay errores, construir el HTML para el modal
                        modalBodyContent.innerHTML = `
                            <div class="product-image-container">
                                <img src="${productData.imagen_url}" alt="${productData.nombre_producto}">
                            </div>
                            <div class="product-info">
                                <h2>${productData.nombre_producto}</h2>
                                <p><strong>Descripción:</strong> ${productData.descripcion_completa}</p>
                                <p><strong>Categoría:</strong> ${productData.nombre_categoria}</p>
                                <p><strong>Stock Disponible:</strong> ${productData.stock}</p>
                                <p class="price">S/ ${parseFloat(productData.precio).toFixed(2)}</p>
                                
                                <div class="seller-details">
                                    <h3>Información del Vendedor</h3>
                                    <p><strong>Nombre:</strong> ${productData.nombre_vendedor}</p>
                                    <p><strong>Email:</strong> ${productData.email_vendedor}</p>
                                    <p><strong>Teléfono:</strong> ${productData.telefono_vendedor}</p>
                                    <p><strong>Dirección:</strong> ${productData.direccion_vendedor}</p>
                                </div>
                            </div>
                        `;
                    } else {
                        // Si hay un error en los datos (por ejemplo, producto no encontrado)
                        modalBodyContent.innerHTML = `<div class="product-detail-loading">Error: ${productData.error || 'Producto no encontrado o datos incompletos.'}</div>`;
                    }
                })
                .catch(error => {
                    // Capturar y mostrar errores en la consola y en el modal (por ejemplo, si falla la red)
                    console.error('Error al cargar los detalles del producto:', error);
                    modalBodyContent.innerHTML = '<div class="product-detail-loading">Error al cargar los detalles. Intente de nuevo más tarde.</div>';
                });
        });
    });

    // 5. Lógica para cerrar el modal
    // Al hacer clic en el botón de cerrar (la 'x')
    closeButton.addEventListener('click', () => {
        modal.style.display = 'none'; // Oculta el modal
    });

    // Al hacer clic fuera del contenido del modal (en el fondo semitransparente)
    window.addEventListener('click', (event) => {
        if (event.target == modal) {
            modal.style.display = 'none'; // Oculta el modal
        }
    });

    // Al presionar la tecla Escape
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.style.display === 'flex') {
            modal.style.display = 'none'; // Oculta el modal
        }
    });
});