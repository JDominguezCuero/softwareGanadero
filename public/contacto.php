<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | Sistema Ganadero</title>
    <!-- FUENTE GOOGLE FONTS : Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- ICONS: Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

    <!-- ICONS: Line Awesome -->
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">

    <!-- Animaciones AOS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">

    <link rel="stylesheet" href="assets/css/principal.css">
</head>
<body>

    <?php include 'assets/layout/header.php'; ?>
    <button class="dark-toggle" onclick="toggleDarkMode()">Modo Oscuro</button>

    <header>
        <div>
            <img src="assets/images/contacto1.png" alt="Ganadería 1" class="imagen-contacto">
        </div>
        
        <div class="header-content">
            <h1>¿Necesitas ayuda? ¡Contáctanos!</h1>
            <p>Estamos aquí para resolver tus dudas, brindarte soporte o escucharte. Tu éxito es nuestra prioridad.</p>
        </div>
    </header>

    <section class="contact-modern" id="contact" data-aos="fade-up">
        <div class="container">
            <h2>Contáctanos</h2>
            <p>Estamos aquí para ayudarte. Envíanos un mensaje o encuéntranos en el mapa.</p>

            <div class="contact-grid">
                <div class="contact-form">
                    <form action="#" method="POST">
                        <div class="form-group">
                            <label for="name">Nombre completo</label>
                            <input type="text" id="name" name="name" placeholder="Tu nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" id="email" name="email" placeholder="tu.email@ejemplo.com" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Tu mensaje</label>
                            <textarea id="message" name="message" rows="6" placeholder="Escribe tu mensaje aquí..." required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Enviar mensaje</button>
                    </form>
                </div>

                <div class="contact-info-map">
                    <div class="contact-details">
                        <h3>Nuestra Ubicación</h3>
                        <p><i class="fas fa-map-marker-alt"></i> Carrera 11 # 28-51, Miradores de San Lorenzo, Puerto Boyacá, Boyacá, Colombia</p>
                        <p><i class="fas fa-phone"></i> +57 320 633 9397</p>
                        <p><i class="fas fa-envelope"></i> info@sofwganadero.com</p>
                    </div>

                    <div class="map-container">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15878.71830601446!2d-74.6540307!3d6.0371306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e40639903930b57%3A0xb30e38a4c803360!2sPuerto%20Boyac%C3%A1%2C%20Boyac%C3%A1%2C%20Colombia!5e0!3m2!1sen!2sco!4v1717286708764!5m2!1sen!2sco"
                            width="100%"
                            height="300"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'assets/layout/flooter.php'; ?>

    <script src="https://www.powr.io/powr.js?platform=html"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/tienda_online.js"></script>

    <script>
        AOS.init();

        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');

            // Comprobar si el modo oscuro está activo en el body
            if (document.body.classList.contains('dark-mode')) {
                localStorage.setItem('darkMode', 'enabled'); // Guardar el estado
            } else {
                localStorage.setItem('darkMode', 'disabled'); // Guardar el estado
            }
        }

        // Función para aplicar el modo oscuro al cargar la página
        function applyDarkModeOnLoad() {
            if (localStorage.getItem('darkMode') === 'enabled') {
                document.body.classList.add('dark-mode');
            }
        }

        // Ejecutar la función al cargar el DOM
        document.addEventListener('DOMContentLoaded', applyDarkModeOnLoad);
    </script>

</body>
</html>