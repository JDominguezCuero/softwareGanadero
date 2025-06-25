document.addEventListener("DOMContentLoaded", function () {
    const botones = document.querySelectorAll(".contactar-vendedor");

    botones.forEach(boton => {
        boton.addEventListener("click", function () {
            const idProducto = this.dataset.idProducto;
            const idVendedor = this.dataset.idVendedor;

            fetch('/LoginADSO/modules/notificaciones/controller.php?accion=insertar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_producto: idProducto,
                    id_vendedor: idVendedor
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    actualizarNotificaciones();
                } else {
                    alert("Error: " + data.message);
                }
            });
        });
    });

    function actualizarNotificaciones() {
        fetch('/LoginADSO/public/includes/notificaciones/obtener_notificaciones.php')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById("notifications-container");
                const panel = document.getElementById("notifications-panel");
                container.innerHTML = "";

                if (data.length === 0) {
                    container.innerHTML = "<p class='text-gray-500'>No hay notificaciones nuevas.</p>";
                } else {
                    data.forEach(noti => {
                        const div = document.createElement("div");
                        div.classList.add("mb-2", "p-2", "rounded", "bg-gray-100");
                        div.textContent = noti.mensaje;
                        container.appendChild(div);
                    });
                }

                panel.classList.remove("hidden");
            });
    }
});