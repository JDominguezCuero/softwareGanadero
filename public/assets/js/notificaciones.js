document.addEventListener("DOMContentLoaded", () => {
    const botonesContactar = document.querySelectorAll('.hm-btn');

    botonesContactar.forEach(btn => {
        btn.addEventListener('click', () => {
            const idProducto = btn.getAttribute('id');
            const receptorId = btn.getAttribute('data-id-vendedor');

            fetch('/LoginADSO/public/includes/notificaciones/enviar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    receptor_id: receptorId,
                    id_producto: idProducto, // <- agregado
                    mensaje: `Un usuario quiere contactarte por el producto ID ${idProducto}`
                })
            })
            .then(response => {
                if (!response.ok) throw new Error("No se pudo contactar con el servidor.");
                return response.json();
            })
            .then(data => {
                if (data.status === 'ok') {
                    mostrarToast("¡Notificación enviada al vendedor!");
                } else {
                    mostrarToast("Error al enviar la notificación: " + data.message, true);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                mostrarToast("Error al enviar la notificación.", true);
            });
        });
    });
});

function mostrarToast(mensaje, error = false) {
    const toast = document.getElementById("toast-notificacion");
    toast.textContent = mensaje;
    toast.style.backgroundColor = error ? "#dc2626" : "#16a34a";
    toast.style.display = "block";

    setTimeout(() => {
        toast.style.display = "none";
    }, 3000);
}

// Mostrar notificaciones al hacer clic en la campana
function showNotifications() {
    const panel = document.getElementById("notifications-panel");
    panel.classList.toggle("hidden");

    fetch('/LoginADSO/public/includes/notificaciones/listar.php')
        .then(res => res.json())
        .then(notificaciones => {
            const container = document.getElementById("notifications-container");
            container.innerHTML = '';

            if (notificaciones.length === 0) {
                container.innerHTML = '<p>No tienes notificaciones.</p>';
                return;
            }

            notificaciones.forEach(noti => {
                const div = document.createElement("div");
                div.className = "bg-white shadow rounded p-3 mb-2";
                div.innerHTML = `<p>${noti.mensaje}</p><small class="text-gray-500">${noti.fecha}</small>`;
                container.appendChild(div);
            });
        });
}
