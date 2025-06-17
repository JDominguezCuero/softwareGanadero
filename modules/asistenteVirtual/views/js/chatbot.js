document.addEventListener('DOMContentLoaded', () => {
    const botActivator = document.querySelector('.bot-activator');
    const chatbotFloatingContainer = document.getElementById('chatbot-floating-container');
    const closeChatbotBtn = document.getElementById('close-chatbot');
    const chatbotBody = document.getElementById('chatbot-body');
    const chatbotOptions = document.getElementById('chatbot-options');

    let currentUserData = {}; // Para almacenar temporalmente los datos del usuario/producto
    let currentFlowState = "mainMenu";
    let awaitingUserInput = false;
    let currentInputField = "";

    // Variables para controlar el estado de login y el nombre del usuario
    let isUserLoggedIn = false;
    let userName = null; // Para almacenar el nombre del usuario logueado

    // --- Definición de flujos de conversación ---
    const conversationFlows = {
        mainMenu: {
            // El mensaje se construirá dinámicamente en startBotConversation
            options: [
                { text: "Publicar un producto 📦", next: "checkUserLoginForProduct" },
                { text: "Preguntas frecuentes ❓", next: "faq" },
                { text: "Contactar soporte 📞", next: "contact" }
            ]
        },
        // --- Flujo de Registro de Usuario ---
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
            message: "Por favor, crea una <strong>contraseña</strong> para tu cuenta. 🔑 Debe tener al menos 5 caracteres, incluyendo mayúscula, minúscula, un número y un carácter especial (ej. `!@#$%^&*`).",
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
        // --- Flujo de verificación/solicitud de login antes de publicar ---
        checkUserLoginForProduct: {
            message: "Para publicar un producto, necesitas tener una cuenta y estar conectado. ¿Ya tienes una cuenta o necesitas registrarte rápido?",
            options: [
                { text: "Ya tengo cuenta, iniciar sesión 🔑", next: "loginUserEmail" },
                { text: "Necesito una cuenta (Registro rápido) ✍️", next: "registerUser" },
                { text: "Volver al menú principal 🏠", next: "mainMenu" }
            ]
        },
        // --- Flujo de Inicio de Sesión ---
        loginUserEmail: {
            message: "Por favor, ingresa tu <strong>correo electrónico</strong> para iniciar sesión. 📧",
            input: true,
            field: "loginCorreo",
            next: "loginUserPassword"
        },
        loginUserPassword: {
            message: "Ahora, ingresa tu <strong>contraseña</strong>. 🔑",
            input: true,
            field: "loginContrasena",
            next: "loginUserConfirm"
        },
        loginUserConfirm: {
            message: "Listo para intentar iniciar sesión. ¿Confirmas tus credenciales?",
            options: [
                { text: "Sí, iniciar sesión ✅", action: "confirmLogin" },
                { text: "No, volver a intentar ↩️", next: "loginUserEmail" },
                { text: "Volver al menú principal 🏠", next: "mainMenu" }
            ]
        },
        // --- Flujo de Publicación de Producto ---
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
        // CAMBIO AQUÍ: publishProductImage ahora pide subir un archivo
        publishProductImage: {
            message: "Por favor, sube una <strong>imagen</strong> de tu producto. 📸",
            inputType: "file", // <-- Nuevo tipo de input
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
        // --- Otros flujos ---
        faq: {
            message: "Claro, aquí tienes algunas preguntas frecuentes:\n\n1. ¿Cómo puedo registrarme?\n2. ¿Cómo publico un producto?\n3. ¿Cómo contacto al soporte?",
            options: [
                { text: "1. Registrarme", response: "Para registrarte, puedes ir a la sección de 'Registrarme' o simplemente decírmelo aquí mismo para iniciar el proceso.", next: "mainMenu" },
                { text: "2. Publicar producto", response: "Para publicar un producto, primero debes iniciar sesión. Luego, ve a la opción 'Publicar un producto' en el menú principal.", next: "mainMenu" },
                { text: "3. Contactar soporte", response: "Puedes contactar a nuestro equipo de soporte enviando un correo a soporte@tudominio.com o llamando al +57 300 123 4567.", next: "mainMenu" },
                { text: "Volver al menú principal 🏠", next: "mainMenu" }
            ]
        },
        contact: {
            message: "Para contactar a nuestro equipo de soporte, puedes enviarnos un correo electrónico a 📧 **soporte@tudominio.com** o llamarnos al 📞 **+57 300 123 4567** (Horario de atención: L-V 9am-6pm).",
            options: [
                { text: "Volver al menú principal 🏠", next: "mainMenu" }
            ]
        }
    };

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

        // Actualizar mensajes de confirmación con datos actuales
        if (state === "registerUserConfirm") {
            flow.message = `¡Listo! ✨ Con esto podemos crear tu cuenta. ¿Estás seguro/a de que quieres registrarte con estos datos?<br>` +
                `<strong>Nombre:</strong> ${currentUserData.nombreCompleto || 'No ingresado'}<br>` +
                `<strong>Correo:</strong> ${currentUserData.correo || 'No ingresado'}<br>` +
                `<strong>Contraseña:</strong> (oculta)`;
        } else if (state === "publishProductConfirm") {
            // Mostrar el nombre del archivo de imagen si se subió
            const imageUrl = currentUserData.imagenProducto ? `<img src="${URL.createObjectURL(currentUserData.imagenProducto)}" alt="Previsualización" style="max-width: 100px; max-height: 100px; display: block; margin-top: 10px;">` : 'No especificado';
            flow.message = `Perfecto. Con esto tenemos la base para tu producto. ¿Confirmas la publicación? 👍<br>` +
                `<strong>Nombre:</strong> ${currentUserData.nombreProducto || 'No ingresado'}<br>` +
                `<strong>Precio:</strong> ${currentUserData.precioProducto || 'No ingresado'}<br>` +
                `<strong>Imagen:</strong> ${currentUserData.imagenProducto ? currentUserData.imagenProducto.name : 'No subida'}${imageUrl}`; // CAMBIO AQUÍ
        } else if (state === "loginUserConfirm") {
            flow.message = `Vas a intentar iniciar sesión con el correo: <strong>${currentUserData.loginCorreo || 'No ingresado'}</strong>.<br>¿Confirmas tus credenciales?`;
        }

        if (flow.input || flow.inputType === "file") { // <-- CAMBIO AQUÍ: Condición para input de texto o archivo
            awaitingUserInput = true;
            currentInputField = flow.field;

            const inputContainer = document.createElement('div');
            inputContainer.classList.add('chat-input-container');

            let inputElement;

            if (flow.inputType === "file") { // <-- NUEVO: Manejo de input de archivo
                inputElement = document.createElement('input');
                inputElement.type = 'file';
                inputElement.accept = 'image/*'; // Solo aceptar imágenes
                inputElement.classList.add('chat-file-input');
                inputElement.id = "chat-file-input";

                const sendButton = document.createElement('button');
                sendButton.textContent = "Subir Imagen";
                sendButton.classList.add('chat-send-button');
                sendButton.onclick = () => handleFileInput(inputElement.files[0]); // Pasa el archivo directamente

                inputContainer.appendChild(inputElement);
                inputContainer.appendChild(sendButton);

            } else { // Input de texto normal
                inputElement = document.createElement('input');
                inputElement.type = (currentInputField === 'contrasena' || currentInputField === 'loginContrasena') ? 'password' : 'text';
                inputElement.placeholder = "Escribe tu respuesta aquí...";
                inputElement.classList.add('chat-text-input');
                inputElement.id = "chat-text-input";

                const sendButton = document.createElement('button');
                sendButton.textContent = "Enviar";
                sendButton.classList.add('chat-send-button');
                sendButton.onclick = () => handleUserInput(inputElement.value);

                inputContainer.appendChild(inputElement);
                inputContainer.appendChild(sendButton);

                inputElement.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        sendButton.click();
                    }
                });
            }
            
            chatbotOptions.appendChild(inputContainer);
            inputElement.focus();

        } else if (flow.options) {
            awaitingUserInput = false;
            currentInputField = "";

            let optionsToDisplay = [...flow.options];

            if (state === "mainMenu") {
                optionsToDisplay = optionsToDisplay.filter(option =>
                    option.text !== "Registrarme como usuario ✍️" &&
                    option.text !== "Iniciar Sesión 🔑"
                );

                if (!isUserLoggedIn) {
                    optionsToDisplay.unshift({ text: "Iniciar Sesión 🔑", next: "loginUserEmail" });
                    optionsToDisplay.unshift({ text: "Registrarme como usuario ✍️", next: "registerUser" });
                }
            }


            optionsToDisplay.forEach(option => {
                const button = document.createElement('button');
                button.classList.add('user-option-button');
                button.innerHTML = option.text;
                button.addEventListener('click', () => handleOptionSelection(option));
                chatbotOptions.appendChild(button);
            });
        }
    }

    // --- sendDataToBackend: Función para manejar el envío al backend (AJUSTADO para FormData) ---
    async function sendDataToBackend(actionType, payload) {
        addMessage("Un momento, estoy procesando tu solicitud... ⏳", 'bot');
        const chatbotControllerPath = '../modules/asistenteVirtual/controller.php';

        let body;
        let headers = {};

        if (actionType === "confirmPublish" && payload.imagenProducto instanceof File) {
            // Si es confirmPublish y hay una imagen, usa FormData
            body = new FormData();
            body.append('action', actionType);
            for (const key in payload) {
                if (payload.hasOwnProperty(key)) {
                    body.append(key, payload[key]);
                }
            }
            // No establezcas 'Content-Type' para FormData, el navegador lo hará correctamente.
        } else {
            // Para otros casos, usa JSON
            body = JSON.stringify({
                action: actionType,
                payload: payload
            });
            headers['Content-Type'] = 'application/json';
        }

        try {
            const response = await fetch(chatbotControllerPath, {
                method: 'POST',
                headers: headers, // Usa el objeto headers
                body: body,
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Error de red o servidor (${response.status}): ${errorText}`);
            }

            const data = await response.json();

            if (data.success) {
                if (actionType === "confirmLogin" || actionType === "confirmRegister") {
                    localStorage.setItem('openChatOnLoad', 'true');
                    const nameToStore = data.userName || payload.nombreCompleto?.split(' ')[0] || payload.loginCorreo?.split('@')[0];
                    localStorage.setItem('loggedInUserName', nameToStore || 'usuario');

                    addMessage("¡Operación exitosa! Recargando la página para una mejor experiencia... 🎉", 'bot');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                    return;
                }

                addMessage(data.message + ' 🎉', 'bot');
                currentUserData = {}; // Limpia los datos temporales después de un éxito

                if (actionType === "confirmRegister") {
                    isUserLoggedIn = true;
                    if (data.userName) {
                        userName = data.userName;
                    } else {
                        userName = payload.nombreCompleto.split(' ')[0];
                    }
                    if (data.continueToPublish) {
                        setTimeout(() => {
                            currentFlowState = "publishProduct";
                            startBotConversation();
                        }, 1500);
                        return;
                    }
                }
            } else {
                addMessage("Hubo un problema: " + data.message + ' 😔', 'bot');
                if (actionType === "confirmLogin") {
                    setTimeout(() => {
                        currentFlowState = "loginUserEmail";
                        startBotConversation();
                    }, 1500);
                    return;
                }
                if (actionType === "confirmRegister") {
                    setTimeout(() => {
                        currentFlowState = "registerUser";
                        startBotConversation();
                    }, 1500);
                    return;
                }
            }
        } catch (error) {
            console.error('Error al enviar datos al servidor:', error);
            addMessage("Lo siento, no pude completar tu solicitud en este momento. Por favor, intenta más tarde. 😞", 'bot');
        } finally {
            if (!((actionType === "confirmLogin" || actionType === "confirmRegister") && data && data.success)) {
                 if (!["publishProduct", "loginUserEmail", "registerUser"].includes(currentFlowState)) {
                    setTimeout(() => {
                        currentFlowState = "mainMenu";
                        startBotConversation();
                    }, 2000);
                }
            }
        }
    }

    // --- handleOptionSelection (AJUSTADO) ---
    async function handleOptionSelection(option) {
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
                await sendDataToBackend("confirmRegister", currentUserData);
            } else if (option.action === "confirmLogin") {
                const loginPayload = {
                    correo: currentUserData.loginCorreo,
                    contrasena: currentUserData.loginContrasena
                };
                loginPayload.intendedAction = "publishProduct";
                await sendDataToBackend("confirmLogin", loginPayload);
            } else if (option.action === "confirmPublish") {
                if (!isUserLoggedIn) {
                    addMessage("Para publicar, primero necesitas iniciar sesión o registrarte. Por favor, selecciona una opción.", 'bot');
                    currentFlowState = "checkUserLoginForProduct";
                    startBotConversation();
                } else {
                    // Prepara el payload para enviar, incluyendo el objeto File
                    await sendDataToBackend("confirmPublish", currentUserData);
                }
            }
        } else if (option.next) {
            if (option.next === "checkUserLoginForProduct" && isUserLoggedIn) {
                currentFlowState = "publishProduct";
                startBotConversation();
            } else {
                currentFlowState = option.next;
                startBotConversation();
            }
        }
    }

    // --- NUEVA FUNCIÓN: handleFileInput para gestionar la subida de archivos ---
    function handleFileInput(file) {
        if (!file) {
            addMessage("Por favor, selecciona un archivo de imagen. 🤔", 'bot');
            setTimeout(() => displayOptionsOrInput(currentFlowState), 500);
            return;
        }

        // Mostrar un mensaje de que la imagen fue seleccionada (no subida aún)
        addMessage(`Imagen seleccionada: ${file.name}`, 'user');

        // Almacenar el objeto File directamente en currentUserData
        currentUserData[currentInputField] = file; 
        awaitingUserInput = false;

        const currentFlow = conversationFlows[currentFlowState];
        if (currentFlow && currentFlow.next) {
            currentFlowState = currentFlow.next;
            startBotConversation();
        } else {
            addMessage("Gracias por tu información. ¿Necesitas algo más? 😊", 'bot');
            currentFlowState = "mainMenu";
            startBotConversation();
        }
    }

    // --- handleUserInput (AJUSTADO para los nuevos campos de login y ocultar contraseña) ---
    function handleUserInput(inputText) {
        if (!inputText.trim()) {
            addMessage("Por favor, ingresa una respuesta válida. 🤔", 'bot');
            setTimeout(() => displayOptionsOrInput(currentFlowState), 500);
            return;
        }

        if (currentInputField === "contrasena" || currentInputField === "loginContrasena") {
            addMessage("********", 'user');
        } else {
            addMessage(inputText, 'user');
        }

        let isValid = true;
        let errorMessage = "";

        if (currentInputField === "correo" || currentInputField === "loginCorreo") {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(inputText.trim())) {
                isValid = false;
                errorMessage = "Por favor, ingresa un formato de correo electrónico válido. (ej. ejemplo@dominio.com)";
            }
        } else if (currentInputField === "contrasena" || currentInputField === "loginContrasena") {
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{5,}$/;
            if (!passwordRegex.test(inputText.trim())) {
                isValid = false;
                errorMessage = "La contraseña debe tener al menos 5 caracteres, incluir mayúscula, minúscula, un número y un carácter especial.";
            }
        } else if (currentInputField === "precioProducto") {
            const price = parseFloat(inputText.trim());
            if (isNaN(price) || price <= 0) {
                isValid = false;
                errorMessage = "Por favor, ingresa un precio válido y mayor a cero.";
            }
        }
        // Eliminamos la validación de 'imagenProducto' aquí, ya que será un input de archivo

        if (!isValid) {
            addMessage(errorMessage, 'bot');
            setTimeout(() => {
                addMessage(conversationFlows[currentFlowState].message, 'bot');
                displayOptionsOrInput(currentFlowState);
            }, 1000);
            return;
        }

        currentUserData[currentInputField] = inputText.trim();
        awaitingUserInput = false;

        const currentFlow = conversationFlows[currentFlowState];
        if (currentFlow && currentFlow.next) {
            currentFlowState = currentFlow.next;
            startBotConversation();
        } else {
            addMessage("Gracias por tu información. ¿Necesitas algo más? 😊", 'bot');
            currentFlowState = "mainMenu";
            startBotConversation(); 
        }
    }

    function startBotConversation() {
        let welcomeMessage = "Hola, soy Santos 🤖, tu asistente virtual. ¿En qué puedo ayudarte hoy?";

        if (isUserLoggedIn && userName) {
            welcomeMessage = `¡Hola 👋, <strong> ${userName}! </strong> Soy Santos 🤖, tu asistente virtual. ¿En qué puedo ayudarte hoy?`;
        }

        const flow = conversationFlows[currentFlowState];
        if (flow) {
            setTimeout(() => {
                const shouldAddMessage = !localStorage.getItem('openChatOnLoad') || currentFlowState !== "mainMenu";

                if (currentFlowState === "mainMenu" && shouldAddMessage) {
                    addMessage(welcomeMessage, 'bot');
                } else if (currentFlowState !== "mainMenu") {
                    addMessage(flow.message, 'bot');
                }
                displayOptionsOrInput(currentFlowState);
            }, 500);
        }
    }

    botActivator.addEventListener('click', (e) => {
        e.preventDefault();
        chatbotFloatingContainer.classList.add('open');
        botActivator.classList.add('pulsing');
        
        currentFlowState = "mainMenu";
        
        chatbotBody.innerHTML = ''; 
        startBotConversation();
    });

    closeChatbotBtn.addEventListener('click', () => {
        chatbotFloatingContainer.classList.remove('open');
        botActivator.classList.remove('pulsing');
    });

    async function checkInitialLoginStatus() {
        try {
            const response = await fetch('/LoginADSO/modules/asistenteVirtual/views/check_login_status.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            if (data.loggedIn) {
                isUserLoggedIn = true;
                userName = data.userName || null;
                console.log(`Usuario logueado: ${userName}`);

                if (localStorage.getItem('openChatOnLoad') === 'true') {
                    localStorage.removeItem('openChatOnLoad');
                    const storedUserName = localStorage.getItem('loggedInUserName');
                    if (storedUserName) {
                        userName = storedUserName;
                        localStorage.removeItem('loggedInUserName');
                    }
                    
                    chatbotFloatingContainer.classList.add('open');
                    botActivator.classList.add('pulsing');
                    
                    chatbotBody.innerHTML = '';
                    addMessage(`¡Bienvenido/a de nuevo, <strong> ${userName || 'usuario'}! </strong> Has iniciado sesión correctamente. 🎉`, 'bot');
                    
                    setTimeout(() => {
                        currentFlowState = "mainMenu";
                        startBotConversation(); 
                    }, 1000); 
                }
            } else {
                isUserLoggedIn = false;
                userName = null;
                console.log("Usuario no logueado.");
                localStorage.removeItem('openChatOnLoad');
                localStorage.removeItem('loggedInUserName');
            }
        } catch (error) {
            console.error("Error al verificar estado de login inicial:", error);
            isUserLoggedIn = false;
            userName = null;
            localStorage.removeItem('openChatOnLoad');
            localStorage.removeItem('loggedInUserName');
        }
    }

    checkInitialLoginStatus();
});