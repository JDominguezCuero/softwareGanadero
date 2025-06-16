<div class="bot-icon-container">
    <a class="bot-activator" href="#">
        🤖
    </a>
    <span class="bot-tooltip-text">Chatea con Claudia! </span>
</div>

<div class="chatbot-floating-container" id="chatbot-floating-container">
    <div class="chatbot-header">
        <span>Asistente Virtual</span>
        <button class="close-chatbot" id="close-chatbot">✖</button>
    </div>
    <div class="chatbot-body" id="chatbot-body">
        <div class="message bot-message">
             ¡Hola! 👋, soy Claudia, tu asistente virtual.
        </div>
    </div>
    <div class="chatbot-options" id="chatbot-options">
    </div>
</div>

<footer class="bg-gray-800 text-white py-4 mt-6">
    <div class="container">
        <div class="foo-row">
            <div class="foo-col">
                <h2>Regístrate <br>a nuestra página</h2>
                <form action="" method="GET">
                    <div class="f-input">
                        <input type="text" placeholder="Ingrese su correo">
                        <button type="submit" class="hm-btn-round btn-primary"><i class="far fa-paper-plane"></i></button>
                    </div>
                </form>
            </div>
            <div class="foo-col">
                <ul>
                    <li><a href="index_controller.php">Productos</a></li>
                    <li><a href="productos_controller.php">Catálogo de Productos</a></li>
                    <li><a href="nosotros.php">Nosotros</a></li>
                    <li><a href="contacto.php">Contacto</a></li>
                    <li><a href="../modules/auth/views/autenticacion.php">Ingresar</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

<div class="foo-copy">
    <div class="container mx-auto text-center">
        <p>&copy; 2025 Simulador Ganadero. Todos los derechos reservados.</p>
        <p style="font-size: 11px;">José Domínguez Cuero - Jasbleidy Morales - Juan Santos.</p>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', () => {
    const botActivator = document.querySelector('.bot-activator');
    const chatbotFloatingContainer = document.getElementById('chatbot-floating-container');
    const closeChatbotBtn = document.getElementById('close-chatbot');
    const chatbotBody = document.getElementById('chatbot-body');
    const chatbotOptions = document.getElementById('chatbot-options'); // Renombrado de userOptionsDiv

    let currentUserData = {}; // Para almacenar temporalmente los datos del usuario/producto

    // --- Definición de flujos de conversación ---
    const conversationFlows = {
        mainMenu: {
            message: "¿En qué puedo ayudarte hoy? ",
            options: [
                { text: "Registrarme como usuario ✍️", next: "registerUser" }, 
                { text: "Publicar un producto 📦", next: "publishProduct" }, 
                { text: "Preguntas frecuentes ❓", next: "faq" }, 
                { text: "Contactar soporte 📞", next: "contact" } 
            ]
        },
        registerUser: {
            message: "¡Excelente! 👍 Para registrarte, necesito algunos datos. ¿Cuál es tu <strong>nombre completo</strong>?",
            input: true,
            field: "nombreCompleto",
            next: "registerUserEmail"
        },
        registerUserEmail: {
            message: "¿Cuál es tu <strong>correo electrónico</strong>? 📧", 
            input: true,
            field: "correo",
            next: "registerUserPassword"
        },
        registerUserPassword: {
            message: "Por favor, crea una <strong>contraseña</strong> para tu cuenta. 🔑", 
            input: true,
            field: "contrasena",
            next: "registerUserConfirm"
        },
        registerUserConfirm: {
            message: "¡Listo! ✨ Con esto podemos crear tu cuenta. ¿Estás seguro/a de que quieres registrarte con estos datos?",
            options: [
                { text: "Sí, registrarme ✅", action: "confirmRegister" }, 
                { text: "No, quiero corregir algo ↩️", next: "mainMenu" } 
            ]
        },
        publishProduct: {
            message: "Comprendo. 📄 ¿Cuál es el <strong>nombre del producto</strong> que quieres publicar?",
            input: true,
            field: "nombreProducto",
            next: "publishProductPrice"
        },
        publishProductPrice: {
            message: "¿Cuál es el <strong>precio</strong> de tu producto? (Ej. 150000 COP) 💰", 
            input: true,
            field: "precioProducto",
            next: "publishProductImage"
        },
        publishProductImage: {
            message: "¿Tienes una <strong>imagen</strong> del producto? 📸 (Por ahora, puedes poner 'sí' o 'no', luego te guiaré para subirla)",
            input: true,
            field: "imagenProducto",
            next: "publishProductConfirm"
        },
        publishProductConfirm: {
            message: "Perfecto. Con esto tenemos la base para tu producto. ¿Confirmas la publicación? 👍", 
            options: [
                { text: "Sí, publicar 🎉", action: "confirmPublish" }, 
                { text: "No, quiero corregir algo ↩️", next: "mainMenu" } 
            ]
        },
        faq: {
            message: "¿Sobre qué te gustaría saber? 🤔 Aquí tienes algunas preguntas frecuentes:", 
            options: [
                { text: "¿Cómo hago un pedido? 🛒", response: "Para hacer un pedido, visita nuestra sección de productos, selecciona lo que desees y sigue los pasos del carrito.", next: "afterFAQ" }, 
                { text: "¿Cuál es el horario de atención? ⏰", response: "Nuestro horario de atención es de lunes a viernes, de 9 AM a 6 PM.", next: "afterFAQ" }, 
                { text: "¿Cómo puedo contactar a soporte? 👨‍💻", response: "Puedes contactar a soporte enviando un correo a soporte@ejemplo.com o llamando al 123-456-7890.", next: "afterFAQ" }, 
                { text: "Volver al menú principal 🏠", next: "mainMenu" } 
            ]
        },
        contact: {
            message: "Claro, puedes contactarnos al 320 6339397 📞 o enviarnos un correo a info@tudominio.com 📧. ¿Necesitas algo más?",
            options: [
                { text: "Volver al menú principal 🏠", next: "mainMenu" } 
            ]
        },
        afterFAQ: {
            message: "¿Hay algo más en lo que pueda ayudarte? 😊", 
            options: [
                { text: "Sí, ver más preguntas ➕", next: "faq" }, 
                { text: "No, gracias. Volver al menú principal 👋", next: "mainMenu" } 
            ]
        }
    };

    let currentFlowState = "mainMenu";
    let awaitingUserInput = false;
    let currentInputField = "";

    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message', `${sender}-message`);
        messageDiv.innerHTML = text;
        chatbotBody.appendChild(messageDiv);
        chatbotBody.scrollTop = chatbotBody.scrollHeight;
    }

    function displayOptionsOrInput(state) {
        chatbotOptions.innerHTML = '';

        const flow = conversationFlows[state];

        if (flow.input) {
            awaitingUserInput = true;
            currentInputField = flow.field;

            const inputContainer = document.createElement('div');
            inputContainer.classList.add('chat-input-container');

            const textInput = document.createElement('input');
            textInput.type = "text";
            textInput.placeholder = "Escribe tu respuesta aquí...";
            textInput.classList.add('chat-text-input');
            textInput.id = "chat-text-input";

            const sendButton = document.createElement('button');
            sendButton.textContent = "Enviar";
            sendButton.classList.add('chat-send-button');
            sendButton.onclick = () => handleUserInput(textInput.value);

            inputContainer.appendChild(textInput);
            inputContainer.appendChild(sendButton);
            chatbotOptions.appendChild(inputContainer);

            textInput.focus();
            textInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendButton.click();
                }
            });

        } else if (flow.options) {
            awaitingUserInput = false;
            currentInputField = "";

            flow.options.forEach(option => {
                const button = document.createElement('button');
                button.classList.add('user-option-button');
                button.textContent = option.text;
                button.addEventListener('click', () => handleOptionSelection(option));
                chatbotOptions.appendChild(button);
            });
        }
    }

    function handleOptionSelection(option) {
        addMessage(option.text, 'user');
        chatbotOptions.innerHTML = '';

        if (option.response) {
            setTimeout(() => {
                addMessage(option.response, 'bot');
                setTimeout(() => {
                    currentFlowState = option.next;
                    startBotConversation();
                }, 800);
            }, 500);
        } else if (option.action) {
            if (option.action === "confirmRegister") {
                addMessage("¡Genial! 🎉 Estoy procesando tu registro con los siguientes datos: <br>" + 
                           `<strong>Nombre:</strong> ${currentUserData.nombreCompleto}<br>` +
                           `<strong>Correo:</strong> ${currentUserData.correo}<br>` +
                           `<strong>Contraseña:</strong> (oculta)`, 'bot');
                addMessage("Tu cuenta ha sido creada exitosamente. ¡Bienvenido/a! 🚀", 'bot'); 
                currentUserData = {};
                setTimeout(() => {
                    currentFlowState = "mainMenu";
                    startBotConversation();
                }, 1500);
            } else if (option.action === "confirmPublish") {
                addMessage("¡Perfecto! ✅ Estamos publicando tu producto con los siguientes datos: <br>" + 
                           `<strong>Nombre:</strong> ${currentUserData.nombreProducto}<br>` +
                           `<strong>Precio:</strong> ${currentUserData.precioProducto}<br>` +
                           `<strong>Imagen:</strong> ${currentUserData.imagenProducto}`, 'bot');
                addMessage("Tu producto ha sido publicado. ¡Pronto estará visible! 👁️‍🗨️", 'bot'); 
                currentUserData = {};
                setTimeout(() => {
                    currentFlowState = "mainMenu";
                    startBotConversation();
                }, 1500);
            }
        } else if (option.next) {
            currentFlowState = option.next;
            startBotConversation();
        }
    }

    function handleUserInput(inputText) {
        if (!inputText.trim()) return;

        addMessage(inputText, 'user');
        currentUserData[currentInputField] = inputText.trim();

        awaitingUserInput = false;

        const currentFlow = conversationFlows[currentFlowState];
        if (currentFlow && currentFlow.next) {
            currentFlowState = currentFlow.next;
            startBotConversation();
        } else {
            addMessage("Gracias por tu información. ¿Necesitas algo más? 😊", 'bot'); 
            currentFlowState = "mainMenu";
            displayOptionsOrInput(currentFlowState);
        }
    }

    function startBotConversation() {
        const flow = conversationFlows[currentFlowState];
        if (flow) {
            setTimeout(() => {
                addMessage(flow.message, 'bot');
                displayOptionsOrInput(currentFlowState);
            }, 500);
        }
    }

    botActivator.addEventListener('click', (e) => {
        e.preventDefault();
        chatbotFloatingContainer.classList.add('open');
        botActivator.classList.add('pulsing');
        // if (chatbotBody.children.length <= 1) { 
        //      addMessage("Hola, soy Carlos, tu asistente virtual. ¿En qué puedo ayudarte hoy?", 'bot'); // Asegúrate de que este mensaje tenga el emoji también si quieres
        // }
        currentFlowState = "mainMenu";
        startBotConversation();
    });

    closeChatbotBtn.addEventListener('click', () => {
        chatbotFloatingContainer.classList.remove('open');
        botActivator.classList.remove('pulsing');
    });
});
</script>