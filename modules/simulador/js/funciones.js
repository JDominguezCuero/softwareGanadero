// Espera que todo el DOM esté cargado
document.addEventListener('DOMContentLoaded', function () {
    // Delegación de evento para los botones ✏️ (código existente)
    document.querySelectorAll('.btn-editar-nombre').forEach(boton => {
        boton.addEventListener('click', function (e) {
            e.stopPropagation(); // Evita que se propague el evento al padre (la tarjeta)
            const id = this.dataset.id;
            activarEdicionNombre(id);
        });
    });

    // Animar carga de barras iniciales (solo al cargar la página)
    document.querySelectorAll('.progreso').forEach(bar => {
        const valor = parseInt(bar.style.width); 
        animarBarra(bar, valor, true); 
    });

    const botonesAceleracion = document.querySelectorAll('.btn-acelerar-tiempo');
    botonesAceleracion.forEach(boton => {
        boton.addEventListener('click', function () {
            const factor = this.dataset.factor;
            setAceleracionTiempo(factor);
            // Llama a la función para actualizar la clase activa inmediatamente
            // Esto es más visual, aunque la página se recargará después del fetch.
            actualizarBotonActivo(factor); 
        });
    });

    const storedFactor = sessionStorage.getItem('current_time_factor');
    if (storedFactor) {
        actualizarBotonActivo(storedFactor);
    } else {
        actualizarBotonActivo('1'); 
    }

    setInterval(fetchAndRenderAnimals, 5000); 
    fetchAndRenderAnimals(); 

    const modelViewerGlobal = document.getElementById('modelo3DAnimal'); 

    if (modelViewerGlobal) {
        modelViewerGlobal.addEventListener('load', () => {
            console.log("Modelo 3D cargado. Intentando acceder a las animaciones...");
            // VERIFICACIÓN CLAVE AQUÍ
            if (modelViewerGlobal.model && modelViewerGlobal.model.animations && Array.isArray(modelViewerGlobal.model.animations)) {
                console.log("Animaciones disponibles:");
                modelViewerGlobal.model.animations.forEach(animation => {
                    console.log(`Animación: ${animation.name}, Duración: ${animation.duration} segundos`);
                });
            } else {
                console.log("El modelo 3D cargado no contiene animaciones o no se pudo acceder a ellas.");
                // Puedes añadir un console.log de modelViewerGlobal.model para ver qué es
                console.log("Contenido de modelViewerGlobal.model:", modelViewerGlobal.model);
            }
        });

        modelViewerGlobal.addEventListener('error', (event) => {
            console.error("Error al cargar el modelo 3D:", event);
        });

    } else {
        console.error("Error: El elemento #modelo3DAnimal no se encontró en el DOM al cargar la página.");
    }

});

// --- Funciones de Gestión de Animales (activar/guardar nombre) ---
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

// --- Funciones del Modal de Confirmación de Eliminar ---

function eliminarAnimal(id_animal, tarjeta) {
    // Puedes usar la información de 'tarjeta' aquí si tu modal de confirmación la necesita
    // Por ejemplo, para mostrar el nombre del animal que se va a eliminar.
    const modal = document.getElementById('modal-confirmacion');
    if (modal) {
        modal.dataset.id = id_animal; // Almacena el ID en el modal de confirmación
        modal.classList.add('is-visible'); // Asumiendo que 'is-visible' controla la visibilidad
    }
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
    if (modal) {
        modal.classList.remove('is-visible');
        delete modal.dataset.id; // Limpia el ID al cerrar el modal de confirmación
    }
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
            if (tarjeta) tarjeta.remove(); // Elimina la tarjeta del DOM
        } else {
            alert('No se pudo eliminar el animal: ' + (data.error || 'Error desconocido.'));
        }
    })
    .catch(err => console.error("Error al eliminar animal:", err));
}

function moverCarrusel(direccion) {
    const carrusel = document.getElementById('grid-animales');
    const tarjeta = carrusel.querySelector('.tarjeta-animal');
    const ancho = tarjeta ? tarjeta.offsetWidth + 20 : 300;
    carrusel.scrollBy({ left: direccion * ancho, behavior: 'smooth' });
}

// --- Función para abrir el modal de detalle del animal ---

function mostrarModal(elemento) {
    const modalAnimal = document.getElementById('modalAnimal');
    if (!modalAnimal) {
        console.error("Error: El modal #modalAnimal no se encontró en el DOM.");
        return;
    }

    modalAnimal.style.display = 'flex'; 

    modalAnimal.dataset.id_animal = elemento.dataset.id; 
    document.getElementById('modalNombre').textContent = elemento.dataset.nombre;

    const tipoAnimal = elemento.dataset.tipoNombre

    cargarModelo3D(tipoAnimal);
    // Crea un objeto con los datos iniciales del animal de la tarjeta
    // Esto se usa para inicializar las barras del modal al abrirlo.
    const animalData = {
        id_animal: elemento.dataset.id,
        nombre: elemento.dataset.nombre,
        alimentacion: parseInt(elemento.dataset.alimentacion), 
        higiene: parseInt(elemento.dataset.higiene),
        salud: parseInt(elemento.dataset.salud),
        produccion: parseInt(elemento.dataset.produccion) 
    };
    
    // Actualiza y anima las barras de progreso dentro del modal
    actualizarModalDetalleAnimal(animalData);

    // Reinicia la animación del modelo 3D a 'Idle' al abrir el modal
    // const modelViewer = document.getElementById('modelo3DAnimal');
    // if (modelViewer) {
    //     modelViewer.animationName = 'Idle';
    //     modelViewer.play();
    // } else {
    //     console.warn("Elemento <model-viewer> no encontrado al abrir el modal.");
    // }
}

function cargarModelo3D(tipoAnimal) {
    console.log("--> cargarModelo3D llamada con tipoAnimal:", tipoAnimal);
    const modelViewer = document.getElementById('modelo3DAnimal');
    
    if (!modelViewer) {
        console.warn("Elemento <model-viewer> no encontrado.");
        return;
    }

     let modelFileName = '';
    const tipoAnimalNormalized = tipoAnimal.toLowerCase(); 
    const basePath = 'images/'; 

    switch (tipoAnimalNormalized) { 
        case 'vaca': 
            modelFileName = 'vacaAnim.glb';
            break;
        case 'cerdo': 
            modelFileName = 'pigAnim.glb'; 
            break;
        case 'gallina': 
            modelFileName = 'gallinaAnim.glb'; 
            break;
        case 'cabra': 
            modelFileName = 'sheepAnim.glb'; 
            break;
        case 'toro': 
            modelFileName = 'bullAnim.glb'; 
            break;
        case 'caballo': 
            modelFileName = 'horseAnim.glb'; 
            break;
        case 'burro': 
            modelFileName = 'donkeyAnim.glb'; 
            break;
        default:
            console.warn(`Modelo 3D no definido para el tipo de animal: ${tipoAnimal}. Usando modelo por defecto.`);
            modelFileName = 'default_animal.glb'; 
            break;
    }

    const finalModelPath = basePath + modelFileName; 

    modelViewer.src = finalModelPath; 
    modelViewer.animationName = 'Idle'; 
    modelViewer.play();
}

// --- Función para cerrar el modal de detalle del animal y actualizar la tarjeta principal ---
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


// --- Funciones de Backend y Actualización de UI ---

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
            // Reproducir sonidos según la acción
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

            // Actualiza la tarjeta principal inmediatamente
            actualizarTarjetaAnimal(data.animal);
            
            // Actualiza el modal de detalle (que debería estar abierto)
            actualizarModalDetalleAnimal(data.animal); 

        } else {
            alert('Error: ' + (data.error || 'No se pudo realizar la acción.'));
        }
    })
    .catch(error => console.error('Error en realizarAccion:', error));
}


// --- FUNCIÓN AUXILIAR PARA ANIMAR CUALQUIER BARRA DE PROGRESO ---

function animarBarra(barraElement, valor, isInitialLoad = false) { 
    if (barraElement) {
        const targetWidth = valor;
        
        if (isInitialLoad) {
            // Animación desde 0% solo en la carga inicial
            barraElement.style.transition = 'none'; // Desactiva la transición temporalmente
            barraElement.style.width = '0%'; // Resetea a 0%
            barraElement.textContent = valor + '%'; // Muestra el valor final mientras se prepara

            // Forzar un reflow para que el navegador "vea" el 0% antes de animar
            void barraElement.offsetWidth; 

            // Reactivar la transición y animar al ancho final
            barraElement.style.transition = 'width 1.5s ease-in-out'; // Duración de la animación
            barraElement.style.width = targetWidth + '%'; 

        } else {
            // Animación suave desde el valor actual para las actualizaciones posteriores
            barraElement.style.transition = 'width 1.5s ease-in-out'; // Mantiene la transición
            barraElement.style.width = targetWidth + '%'; 
            barraElement.textContent = valor + '%'; // Actualiza el texto inmediatamente
        }
    }
}


// CÓDIGO COMPLETO DE LA FUNCIÓN actualizarTarjetaAnimal
// Actualiza los datos y barras de progreso en la tarjeta de la vista principal.
function actualizarTarjetaAnimal(animal) {
    const tarjeta = document.querySelector(`.tarjeta-animal[data-id='${animal.id_animal}']`);
    if (!tarjeta) {
        console.warn(`Tarjeta con ID ${animal.id_animal} no encontrada para actualizar.`);
        return;
    }

    // Actualiza los datasets de la tarjeta (para que los datos en el HTML estén frescos)
    tarjeta.dataset.alimentacion = animal.alimentacion;
    tarjeta.dataset.higiene = animal.higiene;
    tarjeta.dataset.salud = animal.salud;
    tarjeta.dataset.produccion = animal.produccion; 

    // Obtiene las referencias a los elementos de las barras de progreso de la tarjeta
    const alimentacionBarra = tarjeta.querySelector('.barra-progreso.alimentacion .progreso');
    const higieneBarra = tarjeta.querySelector('.barra-progreso.higiene .progreso');
    const saludBarra = tarjeta.querySelector('.barra-progreso.salud .progreso');
    
    // Si tienes un elemento de texto específico para la producción en la tarjeta
    const produccionTexto = tarjeta.querySelector('.produccion'); 

    // Aplica la animación a cada barra de la tarjeta con los nuevos valores
    animarBarra(alimentacionBarra, animal.alimentacion);
    animarBarra(higieneBarra, animal.higiene);
    animarBarra(saludBarra, animal.salud);
    
    // Actualiza el texto de producción si el elemento existe
    if (produccionTexto) {
        produccionTexto.textContent = `🥛 Producción: ${animal.produccion}%`;
    }
}


// CÓDIGO COMPLETO DE LA FUNCIÓN actualizarModalDetalleAnimal
// Actualiza los datos y barras de progreso dentro del modal de detalle del animal.
function actualizarModalDetalleAnimal(animal) {
    const modalAnimal = document.getElementById('modalAnimal');
    // Verifica que el modal exista y que el ID del animal en el modal coincida con el animal que se está actualizando
    if (!modalAnimal || modalAnimal.dataset.id_animal !== animal.id_animal.toString()) {
        // console.warn("Modal no encontrado o ID no coincide. No se actualiza el modal de detalle.");
        return;
    }

    // Actualiza el nombre del animal en el encabezado del modal
    document.getElementById('modalNombre').textContent = animal.nombre;

    // Obtiene las referencias a los elementos de las barras de progreso del modal
    const alimentacionBarraModal = modalAnimal.querySelector('#barra-modal-alimentacion');
    const higieneBarraModal = modalAnimal.querySelector('#barra-modal-higiene');
    const saludBarraModal = modalAnimal.querySelector('#barra-modal-salud');

    // Aplica la animación a cada barra del modal con los nuevos valores
    animarBarra(alimentacionBarraModal, animal.alimentacion);
    animarBarra(higieneBarraModal, animal.higiene);
    animarBarra(saludBarraModal, animal.salud);

    // Si 'Produccion' es un elemento de texto simple en el modal (no una barra), actualízalo
    const modalProduccionText = document.getElementById('modalProduccion'); 
    if (modalProduccionText) {
        modalProduccionText.textContent = animal.produccion;
    }
}

// --- Funciones para Animación del Modelo 3D ---

/**
 * Reproduce una animación específica en el modelo 3D.
 * @param {string} animationName El nombre de la animación a reproducir (ej. 'Eating', 'Walk').
 */
function playAnimation(animationName) {


    const modelViewer = document.getElementById('modelo3DAnimal');
    if (modelViewer) {
        modelViewer.animationName = animationName;
        modelViewer.play();

        // Si la animación que se va a reproducir NO es 'Idle',
        // se añade un listener para volver a 'Idle' cuando termine.
        if (animationName !== 'Idle') {
            modelViewer.addEventListener('animation-finished', () => {
                console.log("¡Animación terminada! Volviendo a Idle.");
                modelViewer.animationName = 'Idle';
                modelViewer.play();
            }, { once: true }); // El listener se elimina después de ejecutarse una vez
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
    const id_animal = modalAnimal ? modalAnimal.dataset.id_animal : null;

    if (!id_animal) {
        console.warn("ID del animal no encontrado en el modal. No se puede realizar la acción '" + accion + "'.");
        return;
    }

    let animationToPlay = 'Idle'; // Animación por defecto si la acción no tiene una específica

    // Asigna la animación según la acción
    switch (accion) {
        case 'alimentar':
            animationToPlay = 'Eating';
            break;
        case 'bañar':
            animationToPlay = 'Walk'; // O 'Idle' o alguna animación de movimiento suave si no hay una de "bañar"
            break;
        case 'medicar':
            animationToPlay = 'Idle'; // O una animación de estado neutro o interacción
            break;
        case 'dormir':
            animationToPlay = 'Death'; // 'Death' a menudo se usa para animales tumbados/relajados en modelos 3D
            break;
        case 'jugar':
            animationToPlay = 'Gallop'; // 'Gallop', 'Gallop_Jump', 'Attack_Kick' pueden simular juego/actividad
            break;
        default:
            console.log("Acción no reconocida:", accion);
            animationToPlay = 'Idle';
            break;
    }

    // Reproduce la animación antes de realizar la acción de backend
    playAnimation(animationToPlay);
    // Llama a la función de backend para procesar la acción y actualizar los datos
    realizarAccion(id_animal, accion);
}


// --- Otras funciones del modal (cambiar fondo, etc.) ---

function verHistorial() {
    alert("Mostrando historial de actividades del animal.");
}

// Función para cambiar el fondo del escenario del modal
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
            ruta = '../../modules/simulador/images/escenario-establo.png'; // Fondo por defecto
    }

    escenario.style.backgroundImage = `url('${ruta}')`;
}

// --- NUEVA FUNCIÓN PARA ACELERAR EL TIEMPO ---
function setAceleracionTiempo(factor) {
    sessionStorage.setItem('current_time_factor', factor); 

    fetch('controller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `action=set_time_factor&factor=${encodeURIComponent(factor)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            console.log("Factor de tiempo establecido a:", data.factor);
            actualizarBotonActivo(data.factor);
            fetchAndRenderAnimals();
        } else {
            alert('Error al establecer el factor de tiempo: ' + (data.error || 'Error desconocido.'));
        }
    })
    .catch(error => console.error('Error en setAceleracionTiempo:', error));
}

function actualizarBotonActivo(factorSeleccionado) {
    document.querySelectorAll('.btn-acelerar-tiempo').forEach(boton => {
        boton.classList.remove('active'); // Remover 'active' de todos
        if (boton.dataset.factor === String(factorSeleccionado)) {
            boton.classList.add('active'); // Añadir 'active' al botón seleccionado
        }
    });
}

function getEstadoEmojiLocal(salud) {
    if (salud < 30) return '😷';
    if (salud < 50) return '😟';
    if (salud < 80) return '😐';
    return '😊';
}

function fetchAndRenderAnimals() {
    fetch('controller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=get_animal_data' // Esta acción ya existe en tu controller.php
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && Array.isArray(data.animales)) {
            data.animales.forEach(animalData => {
                actualizarTarjetaAnimal(animalData);
            });

            
            const modal = document.getElementById('modalAnimal');
            if (modal && modal.style.display === 'flex') {
                const modalId = modal.dataset.id_animal; // Usamos dataset.id_animal, como lo estableces en mostrarModal
                if (modalId) {
                    const updatedAnimal = data.animales.find(a => String(a.id_animal) === String(modalId));
                    if (updatedAnimal) {
                        actualizarModalDetalleAnimal(updatedAnimal);
                    }
                }
            }
        } else {
            console.warn('Advertencia al obtener datos de animales:', data.error || 'Datos no válidos o array no recibido.');
        }
    })
    .catch(error => {
        console.error('Error en fetchAndRenderAnimals:', error);
    });
}
