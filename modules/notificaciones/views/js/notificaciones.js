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

    async function openNotificationDetail(notificationId) {
        const targetNotification = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
        
        if (targetNotification) {
            // Remover la clase 'active' de cualquier item previamente seleccionado
            document.querySelectorAll('.notification-item.active').forEach(activeItem => {
                activeItem.classList.remove('active');
            });
            // Añadir la clase 'active' al item encontrado
            targetNotification.classList.add('active');

            // Obtener los datos del item de la notificación usando dataset
            const id = targetNotification.dataset.id;
            const mensaje = targetNotification.dataset.mensaje;
            const fecha = targetNotification.dataset.fecha;
            const productoNombre = targetNotification.dataset.productoNombre;
            const productoDescripcion = targetNotification.dataset.productoDescripcion;
            const productoImagen = targetNotification.dataset.productoImagen;
            const productoPrecio = targetNotification.dataset.productoPrecio;
            const productoId = targetNotification.dataset.productoId;
            const emisorId = targetNotification.dataset.idUsuarioEmisor;

            const emisorNombre = targetNotification.dataset.emisorNombre;
            const emisorCorreo = targetNotification.dataset.emisorCorreo;
            const emisorTelefono = targetNotification.dataset.emisorTelefono;
            const tipoNotificacion = targetNotification.dataset.tipoNotificacion;

            let messageLabel = '';
            let senderInfoLabel = '';
            let quickReplySectionHTML = '';

            if (tipoNotificacion === 'respuesta') {
                messageLabel = 'Mensaje del Vendedor:';
                senderInfoLabel = 'Información del Vendedor:';
                quickReplySectionHTML = '';
            } else {
                messageLabel = 'Mensaje del Comprador:';
                senderInfoLabel = 'Información del Interesado:';
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

            const predefinedMessageSelect = document.getElementById('predefinedMessageSelect');
            const sendPredefinedMessageBtn = document.getElementById('sendPredefinedMessageBtn');
            const emisorIdTarget = document.getElementById('emisorIdTarget');
            const productoIdTarget = document.getElementById('productoIdTarget');
            const emisorCorreoTarget = document.getElementById('emisorCorreoTarget');
            const compradorNombreTarget = document.getElementById('compradorNombreTarget');
            const emisorTelefonoTarget = document.getElementById('emisorTelefonoTarget');

            if (predefinedMessageSelect && sendPredefinedMessageBtn && emisorIdTarget && productoIdTarget && emisorCorreoTarget && compradorNombreTarget && emisorTelefonoTarget) {
                predefinedMessageSelect.addEventListener('change', () => {
                    sendPredefinedMessageBtn.disabled = !predefinedMessageSelect.value;
                });

                sendPredefinedMessageBtn.addEventListener('click', async () => {
                    const selectedMessage = predefinedMessageSelect.value;
                    const targetEmail = emisorCorreoTarget.value;
                    const productName = productoNombre; 
                    const buyerName = compradorNombreTarget.value;
                    const buyerId = emisorIdTarget.value; 
                    const productId = productoIdTarget.value; 
                    const buyerPhone = emisorTelefonoTarget.value; 

                    if (selectedMessage && targetEmail && buyerId && productId) {
                        const response = await sendAjaxRequest('enviarRespuestaRapida', {
                            destinatarioEmail: targetEmail,
                            destinatarioNombre: buyerName,
                            destinatarioId: buyerId, 
                            destinatarioTelefono: buyerPhone, 
                            mensaje: selectedMessage,
                            nombreProducto: productName,
                            idProducto: productId 
                        });

                        if (response.success) {
                            if (typeof showModal === 'function') {
                                showModal('✅ Éxito', response.message, 'success'); 
                            } else {
                                alert(response.message);
                            }
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
                        if (typeof showModal === 'function') {
                            showModal('⚠️ Advertencia', 'Por favor, selecciona un mensaje y asegúrate que todos los datos están presentes.', 'warning');
                        } else {
                            alert('Por favor, selecciona un mensaje y asegúrate que todos los datos están presentes.');
                        }
                    }
                });
            }

            // Marcar la notificación como leída si aún no lo está
            if (targetNotification.classList.contains('unread')) {
                const response = await sendAjaxRequest('marcarComoLeido', { ids: [id] });
                if (response.success) {
                    targetNotification.classList.remove('unread');
                    console.log(`Notificación ${id} marcada como leída en la DB.`);
                } else {
                    console.error('Fallo al marcar como leído:', response.message);
                }
            }
        } else {
            console.warn(`No se encontró la notificación con ID ${notificationId} para abrir.`);
        }
    }
    // --- FIN DE LA FUNCIÓN openNotificationDetail ---

    // ====================================================================
    // UNIFICACIÓN DE LISTENERS DE CLIC PARA notificationListContent
    // ====================================================================

    notificationListContent.addEventListener('click', async (event) => {
        // Lógica para eliminar una sola notificación
        if (event.target.classList.contains('delete-single-btn')) {
            event.stopPropagation(); // Detener la propagación para evitar que el item padre sea clickeado
            const button = event.target;
            const id = button.dataset.id;
            if (confirm('¿Estás seguro de que quieres eliminar esta notificación?')) {
                const response = await sendAjaxRequest('eliminar', { ids: [id] });
                if (response.success) {
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
            return; // Importante: Salir después de manejar el clic en el botón de eliminar
        }

        // Lógica para abrir el detalle de la notificación (si no fue el botón de eliminar)
        const item = event.target.closest('.notification-item');
        if (item) {
            // Si el clic fue en el checkbox, simplemente retornamos sin abrir el detalle.
            // La propagación no necesita ser detenida aquí ya que el 'change' event es el que importa.
            if (event.target.classList.contains('notification-checkbox')) {
                return; 
            }
            await openNotificationDetail(item.dataset.id);
        }
    });

    // --- LÓGICA PARA ABRIR NOTIFICACIÓN DESDE URL AL CARGAR LA PÁGINA ---
    const urlParams = new URLSearchParams(window.location.search);
    const notificationIdToOpen = urlParams.get('id');

    if (notificationIdToOpen) {
        setTimeout(() => {
            const targetNotification = document.querySelector(`.notification-item[data-id="${notificationIdToOpen}"]`);
            if (targetNotification) {
                openNotificationDetail(notificationIdToOpen);
            } else {
                console.error("ERROR: No se encontró el elemento de la notificación con data-id:", notificationIdToOpen);
            }
        }, 300);
    }
    // --- FIN DE LA LÓGICA DE APERTURA POR URL ---

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

    // Llamada inicial para asegurar que el botón de eliminar seleccionadas esté en el estado correcto al cargar la página
    updateDeleteSelectedButton();
});