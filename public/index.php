<?php
$file = "assets/counter.txt";

if (!file_exists($file)) {
    file_put_contents($file, 0);
}
$count = (int)file_get_contents($file);

$count++;
file_put_contents($file, $count);

$countStr = str_pad($count, 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio | Sistema Ganadero</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css" />
    <link rel="stylesheet" href="assets/css/principal.css">
</head>

<body>

    <button class="dark-toggle" onclick="toggleDarkMode()">Modo Oscuro</button>

    <header>
        <div class="carousel">
            <img src="assets/images/fondo1.png" alt="Ganadería 1">
            <img src="assets/images/fondo2.png" alt="Ganadería 2">
            <img src="assets/images/fondo3.png" alt="Ganadería 3">
            <img src="assets/images/fondo4.png" alt="Ganadería 4">
        </div>
        <div class="header-content">
            <h1>Sistema Ganadero Inteligente</h1>
            <p>Administra tu finca, controla tus animales y simula tu producción de forma fácil y rápida</p>
            <button class="btn" onclick="location.href='../modules/auth/views/autenticacion.php'">Ingresar</button>
            <button class="btn" onclick="location.href='simulador.html'">Simular Finca</button>
        </div>
    </header>

    <section>
        <h2 data-aos="fade-up">¿Qué ofrece nuestro sistema?</h2>
        <div class="features">
            <div class="feature" data-aos="fade-right">
                <h3>Registro de Animales</h3>
                <p>Lleva un control detallado del inventario de animales en tu finca.</p>
            </div>
            <div class="feature" data-aos="fade-up">
                <h3>Simulación de Producción</h3>
                <p>Simula el crecimiento y productividad de tu ganado en diferentes escenarios.</p>
            </div>
            <div class="feature" data-aos="fade-left">
                <h3>Alertas y Recordatorios</h3>
                <p>Notificaciones para vacunaciones, alimentación y controles.</p>
            </div>
            <div class="feature" data-aos="zoom-in">
                <h3>Reportes en Tiempo Real</h3>
                <p>Visualiza indicadores clave sobre tu producción y bienestar animal.</p>
            </div>
        </div>
    </section>

    <section class="valor-section">
        <div>
            <img src="assets/images/simulatorProfile.png" alt="Mujeres trabajando felices con tablet e inventario">
        </div>
        <div class="valor-texto">
            <h2>PROPUESTA <br> VALOR</h2>
            <p><em>¡Aprende, gestiona y juega mientras transformas la ganadería!</em></p>
            <p>Hemos desarrollado una plataforma educativa y administrativa innovadora, pensada para pequeños y medianos
                ganaderos, aprendices y
                estudiantes. Nuestro sistema web combina un simulador interactivo de cuidado animal, tipo juego, con un
                robusto sistema de
                inventario ganadero.</p>
            <p>Podrás aprender sobre alimentación, salud y producción animal mientras administras insumos reales como
                alimentos,
                medicinas y herramientas. Automatiza procesos, mejora tus decisiones y conecta el aprendizaje con la
                práctica,
                todo desde una interfaz sencilla y funcional.</p>
            <p>¡Haz de la educación ganadera una experiencia divertida y eficiente!</p>
        </div>
    </section>

    <section data-aos="fade-up">
        <h2>Estadísticas del sistema</h2>
        <div class="stats">
            <div class="stat">
                <h3>+1,200</h3>
                <p>Usuarios Registrados</p>
            </div>
            <div class="stat">
                <h3>+8,000</h3>
                <p>Animales Registrados</p>
            </div>
            <div class="stat">
                <h3>+2,500</h3>
                <p>Simulaciones Realizadas</p>
            </div>
        </div>
    </section>

    <section class="contacto" data-aos="fade-up">
        <h2>Contáctanos</h2>
        <form>
            <input type="text" placeholder="Nombre completo" required />
            <input type="email" placeholder="Correo electrónico" required />
            <textarea rows="5" placeholder="Escribe tu mensaje..." required></textarea>
            <button class="btn" type="submit">Enviar</button>
        </form>
    </section>

    <section>
        <div class="counter-container">
            <?php foreach(str_split($countStr) as $digit): ?>
            <div class="digit"><?= $digit ?></div>
            <?php endforeach; ?>
            <div class="icon">👁️</div>
        </div>
        <p class="text">Visitas a esta página</p>
    </section>

    <footer>
        <p>&copy; 2025 Sistema Ganadero | <a href="#">Política de Privacidad</a></p>
    </footer>

    <a class="whatsapp" href="https://wa.me/573206339397" target="_blank" title="Escríbenos por WhatsApp">💬</a>

    <script src="https://www.powr.io/powr.js?platform=html"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
    AOS.init();

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
    }
    </script>
</body>

</html>