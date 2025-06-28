document.addEventListener("DOMContentLoaded", function () {
    const botones = document.querySelectorAll(".contactar-vendedor");
    const notificationBadge = document.querySelector('.notification-btn span');

    // Asignación de la función showNotifications a window
    // para que pueda ser llamada desde el atributo onclick del HTML
    window.showNotifications = function() {
        const notificationsPanel = document.getElementById('notifications-panel');
        notificationsPanel.classList.toggle('hidden');

        // Al abrir el panel, actualiza el contador y asume que las notificaciones son vistas
        if (!notificationsPanel.classList.contains('hidden')) {
            if (notificationBadge) {
                notificationBadge.textContent = '0';
                notificationBadge.classList.add('hidden');
            }
            // Opcional: Aquí podrías llamar a una función para marcar todas
            // las notificaciones visibles como leídas en la base de datos.
            // Ejemplo: marcarTodasNotificacionesLeidas();
        }
    };

    // Función para cerrar el panel al hacer clic fuera
    document.addEventListener('click', function(event) {
        const notificationsPanel = document.getElementById('notifications-panel');
        const notificationButton = document.querySelector('.notification-btn');

        if (notificationsPanel && notificationButton) {
            if (!notificationsPanel.contains(event.target) && !notificationButton.contains(event.target)) {
                notificationsPanel.classList.add('hidden');
            }
        }
    });

    // Delegación de eventos para el contenedor de notificaciones
    document.getElementById("notifications-container").addEventListener("click", function(event) {
        if (event.target.closest(".delete-notification-btn")) {
            event.stopPropagation();
            
            const button = event.target.closest(".delete-notification-btn");
            const notificationId = button.dataset.id;
            
            if (confirm("¿Estás seguro de que quieres eliminar esta notificación?")) {
                eliminarNotificacion(notificationId);
            }
            return;
        }

        const notificationItem = event.target.closest(".notification-item");
        if (notificationItem) {
            const notificationId = notificationItem.dataset.id;
            window.location.href = `/LoginADSO/modules/notificaciones/controller.php?accion=listar&id=${notificationId}`;
        }
    });

    // Función para eliminar una notificación
    function eliminarNotificacion(id) {
        const idsToDelete = Array.isArray(id) ? id : [id]; 
        fetch(`/LoginADSO/modules/notificaciones/controller.php?accion=eliminar`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids: idsToDelete })
        })
        .then(res => {
            const contentType = res.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                throw new Error("Respuesta del servidor no es JSON para eliminar.");
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                mostrarToast("Notificación eliminada.");
                actualizarNotificaciones(); // Vuelve a cargar las notificaciones para actualizar la lista
            } else {
                alert("Error al eliminar notificación: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error al eliminar notificación:", error);
            alert("Hubo un problema al eliminar la notificación.");
        });
    }

    // Función principal para actualizar las notificaciones (contador y contenido)
    function actualizarNotificaciones() {
        fetch('/LoginADSO/modules/notificaciones/controller.php?accion=listarNotificaciones')
            .then(res => {
                const contentType = res.headers.get("content-type");
                if (!contentType || !contentType.includes("application/json")) {
                    console.error("Respuesta del servidor no es JSON para listar_json:", res);
                    throw new Error("Respuesta inesperada del servidor.");
                }
                return res.json();
            })
            .then(data => {
                const container = document.getElementById("notifications-container");
                const unreadCount = data.filter(noti => !noti.leido).length; 

                container.innerHTML = ""; // Limpia el contenido actual

                if (data.length === 0) {
                    container.innerHTML = "<p class='text-gray-500 text-center p-4'>No hay notificaciones.</p>";
                } else {
                    data.forEach(noti => {
                        const div = document.createElement("div");

                        div.classList.add("notification-item", "mb-2", "p-2", "rounded", "relative", "flex", "flex-col", "gap-1", "transition-colors", "duration-200", "cursor-pointer");
                        
                        // Añade colores diferentes si no ha sido leída
                        if (!noti.leido) {
                            div.classList.add("bg-blue-100", "border-blue-200", "hover:bg-blue-200");
                        } else {
                            div.classList.add("bg-gray-100", "border-gray-200", "hover:bg-gray-200");
                        }
                        
                        // Guarda el ID de la notificación para la redirección
                        div.dataset.id = noti.id_notificacion; 
                        
                        // Contenido de la notificación mejorado
                        div.innerHTML = `
                            <div class="flex justify-between items-start">
                            <p class="text-sm font-semibold text-gray-800">${noti.mensaje}</p>
                            ${noti.imagen_url ? `<img src="${noti.imagen_url}" alt="Producto" class="w-12 h-12 object-cover rounded-sm border border-gray-200">` : ''}
                                <button class="delete-notification-btn text-red-500 hover:text-red-700 ml-2 p-1 rounded-full text-xs" data-id="${noti.id_notificacion}" title="Eliminar">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            ${noti.emisor_nombre ? `<p class="text-xs text-gray-600">De: ${noti.emisor_nombre}</p>` : ''}
                            ${noti.nombre_producto ? `<p class="text-xs text-gray-600">Producto: ${noti.nombre_producto}</p>` : ''}
                            ${noti.fecha ? `<p class="text-xs text-gray-500 text-right">${new Date(noti.fecha).toLocaleString()}</p>` : ''}
                        `;
                        container.appendChild(div);
                    });
                }

                if (notificationBadge) {
                    if (unreadCount > 0) {
                        notificationBadge.textContent = unreadCount;
                        notificationBadge.classList.remove('hidden');
                    } else {
                        notificationBadge.textContent = '0';
                        notificationBadge.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error("Error al obtener notificaciones:", error);
                const container = document.getElementById("notifications-container");
                if (container) {
                    container.innerHTML = "<p class='text-red-500 text-center p-4'>Error al cargar notificaciones.</p>";
                }
                const notificationBadge = document.querySelector('.notification-btn span');
                if (notificationBadge) {
                    notificationBadge.textContent = '0';
                    notificationBadge.classList.add('hidden');
                }
            });
    }

    // Funcionalidad para contactar vendedor (tu código original)
    botones.forEach(boton => {
        boton.addEventListener("click", function () {
            const idProducto = this.dataset.idProducto;
            const idVendedor = this.dataset.idVendedor;

            const confirmacion = confirm("¿Estás seguro de que quieres contactar con el vendedor de este producto?");
            
            if (confirmacion) {
                fetch('/LoginADSO/modules/notificaciones/controller.php?accion=insertar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        id_producto: idProducto,
                        id_vendedor: idVendedor
                    })
                })
                .then(async res => {
                    const contentType = res.headers.get("content-type");
                    if (!contentType || !contentType.includes("application/json")) {
                        throw new Error("Respuesta del servidor no es JSON");
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        actualizarNotificaciones();
                        mostrarToast("¡Notificación enviada al vendedor!");
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch(error => {
                    console.error("Error en la solicitud:", error);
                    alert("Hubo un problema al contactar al vendedor.");
                });
            } else {
                alert("Contacto cancelado.");
            }
        });
    });

    // Función para mostrar el toast
    function mostrarToast(mensaje) {
        const toast = document.getElementById("toast-notificacion");
        if (toast) {
            toast.textContent = mensaje;
            toast.style.display = "block";
            setTimeout(() => {
                toast.style.display = "none";
            }, 3000);
        }
    }

    // Llama a actualizarNotificaciones al cargar la página para obtener el estado inicial del contador
    actualizarNotificaciones();
});