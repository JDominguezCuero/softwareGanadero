document.addEventListener('DOMContentLoaded', () => {
    const notificationListContent = document.getElementById('notificationListContent');
    const detailContent = document.getElementById('detailContent');
    const selectAllBtn = document.getElementById('selectAllBtn');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const deleteAllBtn = document.getElementById('deleteAllBtn');

    // Mensajes predeterminados para el vendedor
    const predefinedMessages = [
        "¡Hola! Gracias por tu interés en nuestro producto. Estoy disponible para responder a tus preguntas.",
        "Agradezco tu mensaje. Te confirmo que el producto está disponible. ¿Te gustaría programar una visita para verlo?",
        "Gracias por contactarnos. Para más información o si deseas comprar, por favor, llámanos al número registrado en pantalla.",
        "Estamos encantados de que estés interesado/a. Próximamente me pondré en contacto contigo para ofrecerte más detalles.",
        "Tu interés es valioso para nosotros. Por favor, indícame si tienes alguna pregunta específica."
    ];

    // Función para actualizar el estado del botón de eliminar seleccionadas
    const updateDeleteSelectedButton = () => {
        const checkedCheckboxes = document.querySelectorAll('.notification-checkbox:checked');
        deleteSelectedBtn.disabled = checkedCheckboxes.length === 0;
    };

    // Función para manejar las solicitudes AJAX
    async function sendAjaxRequest(action, data, method = 'POST') {
        // Asegúrate de que BASE_URL esté disponible globalmente. En tu vista PHP (notificacion.php)
        // debes tener algo como: <script>const BASE_URL = '<?php echo BASE_URL; ?>';</script>
        if (typeof BASE_URL === 'undefined') {
            console.error("BASE_URL no está definido. Asegúrate de definirlo en tu HTML/PHP antes de cargar este script.");
            return { success: false, message: "Error de configuración: BASE_URL no definido." };
        }
        
        const url = `${BASE_URL}/modules/notificaciones/controller.php?accion=${action}`;
        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) {
                const rawErrorText = await response.text();
                console.error("RAW PHP ERROR RESPONSE (si hay error HTTP):", rawErrorText);

                let errorData;
                try {
                    errorData = JSON.parse(rawErrorText); 
                } catch (e) {
                    errorData = { 
                        message: `Error HTTP! Estado: ${response.status}. Respuesta cruda: ${rawErrorText.substring(0, 200)}...` 
                    };
                }
                throw new Error(`Error en la solicitud AJAX: ${errorData.message || 'Error desconocido'}`);
            }
            
            const rawSuccessText = await response.text();
            console.log("RAW PHP SUCCESS RESPONSE (debería ser JSON):", rawSuccessText);
            
            try {
                return JSON.parse(rawSuccessText);
            } catch (e) {
                console.error("ERROR: La respuesta del servidor no es JSON válida, a pesar de ser 200 OK.", rawSuccessText);
                throw new Error(`El servidor respondió con contenido inválido (no JSON). Contenido: ${rawSuccessText.substring(0, 200)}...`);
            }
        } catch (error) {
            console.error('Error en la solicitud AJAX:', error);
            if (typeof showModal === 'function') {
                showModal('❌ Error', error.message, 'error');
            } else {
                alert('Hubo un error al procesar tu solicitud. Por favor, inténtalo de nuevo. Detalle: ' + error.message);
            }
            return { success: false, message: error.message };
        }
    }

    // Manejar clics en los elementos de notificación para mostrar detalles
    notificationListContent.addEventListener('click', async (event) => {
        const item = event.target.closest('.notification-item');
        if (item) {
            // Evitar que el clic en el checkbox o botón de eliminar abra los detalles
            if (event.target.classList.contains('notification-checkbox') || event.target.classList.contains('delete-single-btn')) {
                return;
            }

            // Remover la clase 'active' de cualquier item previamente seleccionado
            document.querySelectorAll('.notification-item.active').forEach(activeItem => {
                activeItem.classList.remove('active');
            });
            // Añadir la clase 'active' al item clickeado
            item.classList.add('active');

            // Obtener los datos del item de la notificación usando dataset
            const id = item.dataset.id;
            const mensaje = item.dataset.mensaje;
            const fecha = item.dataset.fecha;
            const productoNombre = item.dataset.productoNombre;
            const productoDescripcion = item.dataset.productoDescripcion;
            const productoImagen = item.dataset.productoImagen;
            const productoPrecio = item.dataset.productoPrecio;
            const productoId = item.dataset.productoId; // ID del producto
            const emisorId = item.dataset.idUsuarioEmisor; // ID del emisor original (comprador)

            const emisorNombre = item.dataset.emisorNombre;
            const emisorCorreo = item.dataset.emisorCorreo;
            const emisorTelefono = item.dataset.emisorTelefono;
            const tipoNotificacion = item.dataset.tipoNotificacion; // <-- ¡OBTENEMOS EL TIPO DE NOTIFICACIÓN!

            let messageLabel = '';
            let senderInfoLabel = '';
            let quickReplySectionHTML = ''; // Variable para almacenar el HTML de la sección de respuesta rápida

            // Lógica para determinar las etiquetas y la visibilidad de la respuesta rápida
            if (tipoNotificacion === 'respuesta') {
                // Si la notificación es una respuesta (vista por el comprador)
                messageLabel = 'Mensaje del Vendedor:';
                senderInfoLabel = 'Información del Vendedor:';
                quickReplySectionHTML = ''; // No mostrar la sección de respuesta rápida
            } else { // tipoNotificacion === 'interes' (o por defecto)
                // Si la notificación es una consulta de interés (vista por el vendedor)
                messageLabel = 'Mensaje del Comprador:';
                senderInfoLabel = 'Información del Interesado:';
                // Mostrar la sección de respuesta rápida
                quickReplySectionHTML = `
                    <hr class="separator">
                    <div class="quick-reply-section">
                        <h4>Responder Rápidamente:</h4>
                        <select id="predefinedMessageSelect" class="form-control mb-2">
                            <option value="">Selecciona un mensaje para responder...</option>
                            ${predefinedMessages.map((msg, index) => `<option value="${msg}">${msg.substring(0, 110)}...</option>`).join('')}
                        </select>
                        <button id="sendPredefinedMessageBtn" class="btn btn-primary" disabled>Enviar Mensaje</button>
                        <input type="hidden" id="emisorIdTarget" value="${emisorId}"> 
                        <input type="hidden" id="productoIdTarget" value="${productoId}"> 
                        <input type="hidden" id="emisorCorreoTarget" value="${emisorCorreo}">
                        <input type="hidden" id="compradorNombreTarget" value="${emisorNombre}">
                        <input type="hidden" id="emisorTelefonoTarget" value="${emisorTelefono}"> 
                    </div>
                `;
            }

            // Limpiar y mostrar el contenido detallado
            detailContent.classList.remove('no-selection');
            detailContent.innerHTML = `
                <h3>${productoNombre || 'Detalle de Notificación'}</h3>
                <span class="detail-date">${new Date(fecha).toLocaleDateString('es-ES', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })}</span>
                
                <div class="product-info-section">
                    ${productoImagen && productoImagen !== 'placeholder.jpg' ? `<img src="${productoImagen}" alt="${productoNombre}" class="product-image">` : ''}
                    <div class="product-details-text">
                        <p><strong>Precio del Producto:</strong> ${productoPrecio}</p>
                        <p><strong>Descripción del Producto:</strong> ${productoDescripcion}</p>
                        ${productoId ? `<p><a href="${BASE_URL}/modules/productos/views/detalle_producto.php?id=${productoId}" target="_blank">Ver Producto Completo</a></p>` : ''}
                    </div>
                </div>

                <div class="message-section">
                    <p><strong>${messageLabel}</strong> ${mensaje}</p> </div>
                
                <hr class="separator">

                <div class="interested-party-info">
                    <h4>${senderInfoLabel}</h4> <p><strong>Nombre:</strong> ${emisorNombre}</p>
                    <p><strong>Correo:</strong> <a href="mailto:${emisorCorreo}">${emisorCorreo}</a></p>
                    <p><strong>Teléfono:</strong> <a href="tel:${emisorTelefono}">${emisorTelefono}</a></p>
                </div>

                ${quickReplySectionHTML} `;

            // Obtener referencias a los nuevos elementos para la respuesta rápida
            // ESTAS REFERENCIAS Y SUS EVENT LISTENERS SOLO DEBEN APLICARSE SI LA SECCIÓN SE MOSTRÓ
            const predefinedMessageSelect = document.getElementById('predefinedMessageSelect');
            const sendPredefinedMessageBtn = document.getElementById('sendPredefinedMessageBtn');
            const emisorIdTarget = document.getElementById('emisorIdTarget');
            const productoIdTarget = document.getElementById('productoIdTarget');
            const emisorCorreoTarget = document.getElementById('emisorCorreoTarget');
            const compradorNombreTarget = document.getElementById('compradorNombreTarget');
            const emisorTelefonoTarget = document.getElementById('emisorTelefonoTarget');

            // Asegurarse de que todos los elementos existan antes de añadir event listeners
            // Esto es crucial para que no intente añadir listeners a elementos que no existen
            if (predefinedMessageSelect && sendPredefinedMessageBtn && emisorIdTarget && productoIdTarget && emisorCorreoTarget && compradorNombreTarget && emisorTelefonoTarget) {
                // Habilitar/deshabilitar el botón de enviar según la selección
                predefinedMessageSelect.addEventListener('change', () => {
                    sendPredefinedMessageBtn.disabled = !predefinedMessageSelect.value;
                });

                // Manejar el clic en el botón de enviar mensaje predefinido
                sendPredefinedMessageBtn.addEventListener('click', async () => {
                    const selectedMessage = predefinedMessageSelect.value;
                    const targetEmail = emisorCorreoTarget.value;
                    const productName = productoNombre; 
                    const buyerName = compradorNombreTarget.value;
                    const buyerId = emisorIdTarget.value; 
                    const productId = productoIdTarget.value; 
                    const buyerPhone = emisorTelefonoTarget.value; 

                    // Validar que los datos esenciales estén presentes
                    if (selectedMessage && targetEmail && buyerId && productId) {
                        // Enviar la solicitud AJAX al controlador para enviar el correo,
                        // crear la notificación interna y (opcionalmente) enviar SMS
                        const response = await sendAjaxRequest('enviarRespuestaRapida', {
                            destinatarioEmail: targetEmail,
                            destinatarioNombre: buyerName,
                            destinatarioId: buyerId, 
                            destinatarioTelefono: buyerPhone, 
                            mensaje: selectedMessage,
                            nombreProducto: productName,
                            idProducto: productId 
                        });

                        // Manejar la respuesta del servidor
                        if (response.success) {
                            if (typeof showModal === 'function') {
                                showModal('✅ Éxito', response.message, 'success'); 
                            } else {
                                alert(response.message);
                            }
                            // Resetear la selección y deshabilitar el botón
                            predefinedMessageSelect.value = "";
                            sendPredefinedMessageBtn.disabled = true;
                        } else {
                            if (typeof showModal === 'function') {
                                showModal('❌ Error', 'Fallo al enviar el mensaje: ' + response.message, 'error');
                            } else {
                                alert('Fallo al enviar el mensaje: ' + response.message);
                            }
                        }
                    } else {
                        // Mensaje de advertencia si faltan datos
                        if (typeof showModal === 'function') {
                            showModal('⚠️ Advertencia', 'Por favor, selecciona un mensaje y asegúrate que todos los datos están presentes.', 'warning');
                        } else {
                            alert('Por favor, selecciona un mensaje y asegúrate que todos los datos están presentes.');
                        }
                    }
                });
            }

            // Marcar la notificación como leída si aún no lo está
            if (item.classList.contains('unread')) {
                const response = await sendAjaxRequest('marcarComoLeido', { ids: [id] });
                if (response.success) {
                    item.classList.remove('unread');
                    console.log(`Notificación ${id} marcada como leída en la DB.`);
                } else {
                    console.error('Fallo al marcar como leído:', response.message);
                }
            }
        } // Fin if (item)
    }); // Fin addEventListener 'click'

    // Event listeners para los botones de acción global
    selectAllBtn.addEventListener('click', () => {
        const checkboxes = document.querySelectorAll('.notification-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        updateDeleteSelectedButton();
    });

    deleteSelectedBtn.addEventListener('click', async () => {
        const checkedIds = Array.from(document.querySelectorAll('.notification-checkbox:checked'))
                               .map(cb => cb.closest('.notification-item').dataset.id);
        if (checkedIds.length > 0) {
            if (confirm(`¿Estás seguro de que quieres eliminar ${checkedIds.length} notificaciones seleccionadas?`)) {
                const response = await sendAjaxRequest('eliminar', { ids: checkedIds });
                if (response.success) {
                    checkedIds.forEach(id => {
                        document.querySelector(`.notification-item[data-id="${id}"]`).remove();
                    });
                    if (typeof showModal === 'function') {
                        showModal('✅ Éxito', response.message, 'success');
                    } else {
                        alert(response.message);
                    }
                    updateDeleteSelectedButton();
                    // Si no hay más notificaciones, mostrar el mensaje "No tienes notificaciones"
                    if (notificationListContent.querySelectorAll('.notification-item').length === 0) {
                        notificationListContent.innerHTML = '<p class="no-notifications">No tienes notificaciones por el momento.</p>';
                        detailContent.innerHTML = '<p>Selecciona una notificación para ver los detalles.</p>';
                        detailContent.classList.add('no-selection');
                    }
                } else {
                    if (typeof showModal === 'function') {
                        showModal('❌ Error', response.message, 'error');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            }
        } else {
            if (typeof showModal === 'function') {
                showModal('⚠️ Advertencia', 'No hay notificaciones seleccionadas para eliminar.', 'warning');
            } else {
                alert('No hay notificaciones seleccionadas para eliminar.');
            }
        }
    });

    deleteAllBtn.addEventListener('click', async () => {
        if (confirm('¿Estás seguro de que quieres eliminar TODAS tus notificaciones? Esta acción es irreversible.')) {
            const response = await sendAjaxRequest('eliminarTodas', {});
            if (response.success) {
                notificationListContent.innerHTML = '<p class="no-notifications">No tienes notificaciones por el momento.</p>';
                detailContent.innerHTML = '<p>Selecciona una notificación para ver los detalles.</p>';
                detailContent.classList.add('no-selection');
                if (typeof showModal === 'function') {
                    showModal('✅ Éxito', response.message, 'success');
                } else {
                    alert(response.message);
                }
                updateDeleteSelectedButton();
            } else {
                if (typeof showModal === 'function') {
                    showModal('❌ Error', response.message, 'error');
                } else {
                    alert('Error: ' + response.message);
                }
            }
        }
    });

    // Delegación de eventos para checkboxes (para elementos que se añaden dinámicamente)
    notificationListContent.addEventListener('change', (event) => {
        if (event.target.classList.contains('notification-checkbox')) {
            updateDeleteSelectedButton();
        }
    });

    // Delegación de eventos para eliminar notificación individual
    notificationListContent.addEventListener('click', async (event) => {
        if (event.target.classList.contains('delete-single-btn')) {
            const button = event.target;
            const id = button.dataset.id;
            if (confirm('¿Estás seguro de que quieres eliminar esta notificación?')) {
                const response = await sendAjaxRequest('eliminar', { ids: [id] });
                if (response.success) {
                    // Remover el elemento del DOM
                    button.closest('.notification-item').remove();
                    if (typeof showModal === 'function') {
                        showModal('✅ Éxito', response.message, 'success');
                    } else {
                        alert(response.message);
                    }
                    updateDeleteSelectedButton();
                    // Si se elimina la última notificación, mostrar el mensaje de "no notificaciones"
                    if (notificationListContent.querySelectorAll('.notification-item').length === 0) {
                        notificationListContent.innerHTML = '<p class="no-notifications">No tienes notificaciones por el momento.</p>';
                        detailContent.innerHTML = '<p>Selecciona una notificación para ver los detalles.</p>';
                        detailContent.classList.add('no-selection');
                    } else {
                        // Si se eliminó la notificación actualmente activa, limpiar el panel de detalles
                        const activeItem = document.querySelector('.notification-item.active');
                        if (!activeItem || activeItem.dataset.id === id) { 
                            detailContent.innerHTML = '<p>Selecciona una notificación para ver los detalles.</p>';
                            detailContent.classList.add('no-selection');
                        }
                    }
                } else {
                    if (typeof showModal === 'function') {
                        showModal('❌ Error', response.message, 'error');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            }
        }
    });

    // Llamada inicial para asegurar que el botón de eliminar seleccionadas esté en el estado correcto al cargar la página
    updateDeleteSelectedButton();
});