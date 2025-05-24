// Espera que todo el DOM esté cargado
document.addEventListener('DOMContentLoaded', function () {
    // Delegación de evento para los botones ✏️ (código existente)
    document.querySelectorAll('.btn-editar-nombre').forEach(boton => {
        boton.addEventListener('click', function (e) {
            e.stopPropagation();
            const id = this.dataset.id;
            activarEdicionNombre(id);
        });
    });

    // ¡IMPORTANTE! NO hay código aquí para abrir el modal automáticamente.
    // El modal solo se abrirá mediante la función mostrarModal(this) llamada desde el onclick de las tarjetas.
});

// --- Funciones de Gestión de Animales (sin cambios significativos) ---

function activarEdicionNombre(id) {
    const div = document.getElementById("nombre-animal-" + id);
    const nombreActual = div.innerText.replace("✏️", "").trim();
    div.innerHTML = `<input type="text" id="input-nombre-${id}" value="${nombreActual}" onblur="guardarNombre(${id})" onkeydown="if(event.key === 'Enter') this.blur();">`;
    document.getElementById("input-nombre-" + id).focus();
}

function guardarNombre(id) {
    const nuevoNombre = document.getElementById("input-nombre-" + id).value;
    fetch("controller.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `action=editar&id_animal=${encodeURIComponent(id)}&nombre=${encodeURIComponent(nuevoNombre)}`
    })
    .then(response => response.text())
    .then(() => {
        const contenedor = document.getElementById("nombre-animal-" + id);
        contenedor.innerHTML = `
            <span style="cursor:pointer;">
                ${nuevoNombre}
                <button class="btn-editar-nombre" data-id="${id}" style="border:none;background:none;cursor:pointer;padding:0;margin-left:5px;">✏️</button>
            </span>
        `;
        const nuevoBoton = contenedor.querySelector('.btn-editar-nombre');
        nuevoBoton.addEventListener('click', function (e) {
            e.stopPropagation();
            activarEdicionNombre(id);
        });
    });
}

function eliminarAnimal(id_animal, tarjeta) {
    document.getElementById('modalNombre').textContent = tarjeta.dataset.nombre;
    document.getElementById('modalAlimentacion').textContent = tarjeta.dataset.alimentacion;
    const modal = document.getElementById('modal-confirmacion');
    modal.dataset.id = id_animal;
    modal.classList.add('is-visible');
}

document.getElementById('btn-confirmar').addEventListener('click', function () {
    const modal = document.getElementById('modal-confirmacion');
    const id_animal = modal.dataset.id;
    if (id_animal) {
        actionEliminarAnimal(id_animal);
        cerrarModalConfirmacion();
    }
});

document.getElementById('btn-cancelar').addEventListener('click', function () {
    cerrarModalConfirmacion();
});

function cerrarModalConfirmacion() {
    const modal = document.getElementById('modal-confirmacion');
    modal.classList.remove('is-visible');
    delete modal.dataset.id;
}

function actionEliminarAnimal(id_animal) {
    fetch('controller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `action=eliminar&id_animal=${id_animal}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const tarjeta = document.querySelector(`.tarjeta-animal[data-id='${id_animal}']`);
            if (tarjeta) tarjeta.remove();
        } else {
            alert('No se pudo eliminar el animal.');
        }
    })
    .catch(err => console.error(err));
}

function editarAnimal(id_animal) {
    window.location.href = `editar_animal.php?id=${id_animal}`;
}

function moverCarrusel(direccion) {
    const carrusel = document.getElementById('grid-animales');
    const tarjeta = carrusel.querySelector('.tarjeta-animal');
    const ancho = tarjeta ? tarjeta.offsetWidth + 20 : 300;
    carrusel.scrollBy({ left: direccion * ancho, behavior: 'smooth' });
}

// --- Control del Modal y Animaciones ---

/**
 * Abre el modal de detalle del animal y carga sus datos.
 * @param {HTMLElement} elemento La tarjeta del animal que se hizo clic.
 */
function mostrarModal(elemento) {
    const modalAnimal = document.getElementById('modalAnimal');
    if (!modalAnimal) {
        console.error("Error: El modal #modalAnimal no se encontró en el DOM.");
        return;
    }

    // Asegura que el modal esté visible
    modalAnimal.style.display = 'flex';

    // ¡CRUCIAL! Establece el ID del animal en el modal, tomado de la tarjeta clicada.
    modalAnimal.dataset.id_animal = elemento.dataset.id;

    const modalProduccionSpan = document.getElementById('modalProduccion');
    if (modalProduccionSpan) {
        modalProduccionSpan.innerText = elemento.dataset.produccion;
    }


    // Crea un objeto animal con los datos actuales para pasárselo a la función de actualización
    const animalData = {
        id_animal: elemento.dataset.id,
        nombre: elemento.dataset.nombre,
        alimentacion: parseInt(elemento.dataset.alimentacion), // Asegúrate de que sean números
        higiene: parseInt(elemento.dataset.higiene),
        salud: parseInt(elemento.dataset.salud),
        produccion: parseInt(elemento.dataset.produccion) // Asegúrate de que sean números
    };

    // Llamamos a actualizarModalDetalleAnimal para cargar y animar las barras/porcentajes del modal
    // con los datos actuales de la tarjeta.
    actualizarModalDetalleAnimal(animalData);

    // Inicializa o resetea la animación del modelo 3D a 'Idle'
    const modelViewer = document.getElementById('modelo3DAnimal');
    if (modelViewer) {
        modelViewer.animationName = 'Idle';
        modelViewer.play();
    } else {
        console.warn("Elemento <model-viewer> no encontrado al abrir el modal.");
    }
}

/**
 * Cierra el modal de detalle del animal.
 */
function cerrarModal() {
    const modalAnimal = document.getElementById('modalAnimal');
    if (modalAnimal) {
        modalAnimal.style.display = 'none';
        // Opcional: Resetea la animación a 'Idle' cuando se cierra el modal
        const modelViewer = document.getElementById('modelo3DAnimal');
        if (modelViewer) {
            modelViewer.animationName = 'Idle';
            modelViewer.play();
        }
    }
}

/**
 * Reproduce una animación específica en el modelo 3D.
 * @param {string} animationName El nombre de la animación a reproducir (ej. 'Eating', 'Walk').
 */
function playAnimation(animationName) {
    const modelViewer = document.getElementById('modelo3DAnimal');
    if (modelViewer) {
        modelViewer.animationName = animationName;
        modelViewer.play();

        // Si la animación no es 'Idle', escucha cuando termine para volver al estado 'Idle'.
        if (animationName !== 'Idle') {
            modelViewer.addEventListener('animation-finished', () => {
                modelViewer.animationName = 'Idle';
                modelViewer.play();
            }, { once: true });
        }
    } else {
        console.warn("Elemento <model-viewer> no encontrado. No se pudo reproducir la animación.");
    }
}

/**
 * Realiza una acción específica sobre el animal y activa la animación correspondiente.
 * @param {string} accion El tipo de acción a realizar (ej. 'alimentar', 'bañar').
 */
function accionModal(accion) {
    const modalAnimal = document.getElementById('modalAnimal');
    // Acceder a dataset.id de forma segura
    const id_animal = modalAnimal ? modalAnimal.dataset.id : null;

    if (!id_animal) {
        console.warn("ID del animal no encontrado en el modal. No se puede realizar la acción '" + accion + "'.");
        return;
    }

    let animationToPlay = 'Idle'; // Animación por defecto si la acción no tiene una específica

    switch (accion) {
        case 'alimentar':
            animationToPlay = 'Eating';
            break;
        case 'bañar':
            animationToPlay = 'Walk'; // Puede ser 'Walk' o 'Idle' si no hay una animación de "bañar"
            break;
        case 'medicar':
            animationToPlay = 'Idle'; // O una animación de estado neutro
            break;
        case 'dormir':
            animationToPlay = 'Death'; // Si "Death" representa el animal tumbado/relajado
            break;
        case 'jugar':
            animationToPlay = 'Gallop'; // 'Gallop', 'Gallop_Jump', 'Attack_Kick' pueden simular juego
            break;
        default:
            console.log("Acción no reconocida:", accion);
            animationToPlay = 'Idle';
            break;
    }

    playAnimation(animationToPlay);
    realizarAccion(id_animal, accion);
}

// --- Funciones de Backend y Actualización de UI ---
// CÓDIGO COMPLETO DE LA FUNCIÓN realizarAccion
function realizarAccion(id_animal, accion) {
    fetch('controller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `action=${accion}&id_animal=${id_animal}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Reproducir sonidos si es el caso
            if (accion === 'alimentar') {
                const audio = document.getElementById('audioAlimentar');
                if (audio) audio.play();
            }
            if (accion === 'bañar') {
                const audio = document.getElementById('audioBanar');
                if (audio) audio.play();
            }
            if (accion === 'medicar') {
                const audio = document.getElementById('audioCurar');
                if (audio) audio.play();
            }
            actualizarTarjetaAnimal(data.animal);
            actualizarModalDetalleAnimal(data.animal); // Llama a esta función también
        } else {
            alert('Error: ' + (data.error || 'No se pudo realizar la acción.'));
        }
    })
    .catch(error => console.error('Error en realizarAccion:', error));
}


// CÓDIGO COMPLETO DE LA FUNCIÓN actualizarTarjetaAnimal
function actualizarTarjetaAnimal(animal) {
    const tarjeta = document.querySelector(`.tarjeta-animal[data-id='${animal.id_animal}']`);
    if (!tarjeta) return;

    tarjeta.dataset.alimentacion = animal.alimentacion;
    tarjeta.dataset.higiene = animal.higiene;
    tarjeta.dataset.salud = animal.salud;
    tarjeta.dataset.produccion = animal.produccion; // Asumo que "produccion" se sigue actualizando aquí

    const alimentacionBarra = tarjeta.querySelector('.barra-progreso.alimentacion .progreso');
    const higieneBarra = tarjeta.querySelector('.barra-progreso.higiene .progreso');
    const saludBarra = tarjeta.querySelector('.barra-progreso.salud .progreso');
    const produccionTexto = tarjeta.querySelector('.produccion'); // Si tienes un elemento para la producción fuera de una barra

    // Funció auxiliar para animar una barra
    function animarBarra(barraElement, valor) {
        if (barraElement) {
            // Guarda el valor final para que la transición lo use
            const targetWidth = valor;
            
            // Poner a 0% y actualizar texto SIN TRANSICIÓN al principio
            barraElement.style.transition = 'none'; // Desactiva la transición temporalmente
            barraElement.style.width = '0%';
            barraElement.textContent = valor + '%'; // Actualiza el texto
            
            // Forzar el redibujado (reflow) para que el navegador "vea" el 0%
            void barraElement.offsetWidth; 

            // Aplicar transición y ancho final
            barraElement.style.transition = 'width 1.5s ease-in-out'; // Reactiva la transición
            barraElement.style.width = targetWidth + '%'; // Anima al valor final
        }
    }

    // Aplica la animación a cada barra de la tarjeta
    animarBarra(alimentacionBarra, animal.alimentacion);
    animarBarra(higieneBarra, animal.higiene);
    animarBarra(saludBarra, animal.salud);
    
    if (produccionTexto) {
        produccionTexto.textContent = `🥛 Producción: ${animal.produccion}%`;
    }
}


// CÓDIGO COMPLETO DE LA FUNCIÓN actualizarModalDetalleAnimal
function actualizarModalDetalleAnimal(animal) {
    const modalAnimal = document.getElementById('modalAnimal');
    // Asegúrate de que el modal esté abierto y sea para el animal correcto
    if (!modalAnimal || modalAnimal.dataset.id_animal !== animal.id_animal.toString()) {
        return;
    }

    // Actualiza el nombre del animal en el modal, si es necesario (asumo que 'modalNombre' es para el h2)
    document.getElementById('modalNombre').textContent = animal.nombre; // Asegúrate de que el nombre se actualice si es relevante

    // Si tienes elementos separados para mostrar solo el número (sin barra), actualízalos aquí
    // Por ejemplo, si tuvieras un <span id="numeroAlimentacionModal"></span>
    // document.getElementById('numeroAlimentacionModal').innerText = animal.alimentacion;
    // document.getElementById('numeroHigieneModal').innerText = animal.higiene;
    // document.getElementById('numeroSaludModal').innerText = animal.salud;
    // document.getElementById('modalProduccion').innerText = animal.produccion; // Si Producción tiene un span de texto fuera de la barra

    // --- Lógica para animar las barras de progreso dentro del MODAL ---
    const alimentacionBarraModal = modalAnimal.querySelector('#barra-modal-alimentacion');
    const higieneBarraModal = modalAnimal.querySelector('#barra-modal-higiene');
    const saludBarraModal = modalAnimal.querySelector('#barra-modal-salud');

    // Funció auxiliar para animar una barra en el modal (reutilizamos la lógica)
    function animarBarraModal(barraElement, valor) {
        if (barraElement) {
            const targetWidth = valor;

            // Desactiva la transición, resetea a 0% y actualiza texto
            barraElement.style.transition = 'none';
            barraElement.style.width = '0%';
            barraElement.textContent = valor + '%'; // Actualiza el texto dentro de la barra
            
            // Forzar el redibujado (reflow)
            void barraElement.offsetWidth; 

            // Reactiva la transición y anima al valor final
            barraElement.style.transition = 'width 1.5s ease-in-out';
            barraElement.style.width = targetWidth + '%';
        }
    }

    // Aplica la animación a cada barra del modal
    animarBarraModal(alimentacionBarraModal, animal.alimentacion);
    animarBarraModal(higieneBarraModal, animal.higiene);
    animarBarraModal(saludBarraModal, animal.salud);
}

// CÓDIGO INICIAL DE ANIMACIÓN DE BARRAS (MANTENER ESTE)
// Este código solo se ejecuta una vez al cargar la página para animar las barras iniciales.
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.progreso').forEach(bar => {
        const width = parseInt(bar.style.width);
        bar.style.width = '0%'; // Reset for animation
        setTimeout(() => {
            bar.style.transition = 'width 1.5s ease-in-out';
            bar.style.width = width + '%';
        }, 100);
    });
});

// Asegúrate de que el modal de detalle del animal tenga el id_animal en su dataset
// Esto generalmente se hace en la función que abre el modal (`mostrarModal`, etc.)
// Ejemplo de cómo se podría hacer en la función que abre el modal:
/*
function mostrarModal(tarjeta) {
    const id_animal = tarjeta.dataset.id;
    const modal = document.getElementById('modalAnimal');
    modal.dataset.id_animal = id_animal; // <-- Asegura que este dataset exista
    // ... resto de la lógica para cargar datos y mostrar el modal ...
}
*/
function verHistorial() {
    alert("Mostrando historial de actividades del animal.");
}

function verPerfil() {
    alert("Perfil detallado del animal con estadísticas y fechas importantes.");
}

function cambiarFondo(escena) {
    const escenario = document.getElementById("escenarioModal");
    if (!escenario) {
        console.warn("No se encontró el contenedor escenarioModal en el DOM");
        return;
    }

    let ruta = '';

    switch (escena) {
        case 'granja':
            ruta = '../../modules/simulador/images/escenario-granja.png';
            break;
        case 'establo':
            ruta = '../../modules/simulador/images/escenario-establo.png';
            break;
        case 'noche':
            ruta = '../../modules/simulador/images/escenario-noche.png';
            break;
        default:
            console.warn("Escena no reconocida: " + escena);
            return;
    }

    escenario.style.backgroundImage = `url('${ruta}')`;
}