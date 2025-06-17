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
        <!-- <div class="message bot-message">
             ¡Hola! 👋, soy Claudia, tu asistente virtual.
        </div> -->
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


<script src="<?= BASE_URL ?>/modules/asistenteVirtual/views/js/chatbot.js"></script>