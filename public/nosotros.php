<?php
// Puedes mantener la lógica del contador si quieres, o eliminarla si solo es para la tienda principal
// $file = "assets/counter.txt";
// if (!file_exists($file)) {
//     file_put_contents($file, 0);
// }
// $count = (int)file_get_contents($file);
// $count++;
// file_put_contents($file, $count);
// $countStr = str_pad($count, 3, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nosotros | Sistema Ganadero</title>
    <!-- FUENTE GOOGLE FONTS : Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- ICONS: Font Awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <!-- ICONS: Line Awesome -->
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <!-- Animaciones AOS -->
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="assets/css/principal.css">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>    
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">

</head>

<body class="min-h-screen flex bg-gray-100">
    <div class="flex min-h-screen w-full">
        <?php
            if (isset($_SESSION['usuario'])) {
                include 'assets/layout/sidebar.php';
            }
        ?>    
        <main class="flex-1 p-6 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;"> 
    
            <div class="hm-wrapper">

                <?php include 'assets/layout/header.php'; ?>


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
            </div>
        </main>
    </div>
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