<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start();
}
require_once(__DIR__ . '/../config/config.php');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Nosotros | Sistema Ganadero</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css">
    <link rel="stylesheet" href="assets/css/principal.css">
    <link rel="stylesheet" href="assets/css/detalleProducto.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>     
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">

    <style>
        /* Estilos generales para el contenido */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f4f0; /* Un beige muy claro, terroso */
        }

        /* Títulos de sección uniformes */
        .section-title {
            font-size: 2.5rem; /* Títulos grandes y llamativos */
            font-weight: 800;
            color: #4a6b0c; /* Verde oscuro, orgánico */
            margin-bottom: 2.5rem; /* Espacio uniforme bajo el título */
            text-align: center;
        }

        /* Sección de Características */
        .features {
            display: grid;
            /* Usamos 2 columnas para pantallas pequeñas y 4 para medianas/grandes para evitar el espacio */
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem; /* Menos margen arriba para centrar mejor el contenido */
        }

        .feature {
            background-color: #fcfcfc; /* Fondo blanco para las tarjetas */
            padding: 1.5rem;
            border-radius: 0.75rem; /* Bordes más suaves */
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08); /* Sombra sutil */
            text-align: center;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            border: 1px solid #e0d8cc; /* Borde suave */
        }

        .feature:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); /* Sombra más pronunciada al pasar el ratón */
        }

        .feature h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #3b5323; /* Verde más oscuro para subtítulos */
        }

        .feature p {
            color: #5d4037; /* Marrón para texto principal */
            line-height: 1.6;
        }

        /* Sección de Propuesta de Valor */
        .valor-section {
            display: flex;
            flex-direction: column;
            gap: 3rem; /* Más espacio entre imagen y texto */
            align-items: center;
            margin-top: 4rem;
            background-color: #ffffff;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .valor-section img {
            max-width: 100%;
            height: auto;
            border-radius: 0.75rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .valor-texto h2 {
            text-align: left; /* Título alineado a la izquierda */
            font-size: 3rem;
            font-weight: 800;
            color: #3b5323;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .valor-texto p {
            font-size: 1.125rem;
            line-height: 1.8;
            color: #4b382f;
            margin-bottom: 1rem;
        }

        .valor-texto em {
            font-weight: 600;
            color: #6a8b3d; /* Verde oliva */
            font-style: normal; /* Para quitar la cursiva por defecto del <em> */
        }

        @media (min-width: 768px) { /* Para pantallas medianas y grandes */
            .valor-section {
                flex-direction: row; /* La imagen y el texto en fila */
            }
            .valor-section > div {
                flex: 1; /* Distribuye el espacio equitativamente */
            }
        }

        /* Sección de Estadísticas */
        .stats {
            /* display: grid; */
            /* Usamos 3 columnas fijas para pantallas grandes para una alineación perfecta */
            grid-template-columns: repeat(3, minmax(200px, 1fr)); 
            gap: 2rem;
            margin-top: 1.5rem; /* Menos margen arriba para centrar mejor el contenido */
            justify-content: center; /* Centra los elementos en el contenedor grid */
            align-items: start; /* Alinea los elementos al inicio */
        }

        @media (max-width: 767px) { /* Para pantallas más pequeñas, ajusta a 1 columna */
            .stats {
                grid-template-columns: 1fr;
            }
        }
        @media (min-width: 768px) and (max-width: 1023px) { /* Para tablets, 2 columnas */
            .stats {
                grid-template-columns: repeat(2, minmax(200px, 1fr));
            }
        }


        .stat {
            background-color: #ebf3e0; /* Un verde claro para el fondo */
            padding: 2rem;
            border-radius: 0.75rem;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            border: 1px solid #d1e2c4;
            display: flex; /* Usar flexbox para centrar contenido */
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 150px; /* Asegura una altura mínima para la uniformidad */
        }

        .stat h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #3b5323;
            margin-bottom: 0.75rem;
        }

        .stat p {
            font-size: 1.1rem;
            color: #5d4037;
        }

        /* Nueva Sección: Nuestro Equipo */
        .team-section {
            background-color: #fcfcfc; /* Fondo blanco */
            padding: 4rem 1rem;
            margin-top: 4rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .team-section h2 {
            font-size: 2.8rem;
            font-weight: 800;
            color: #3b5323;
            text-align: center;
            margin-bottom: 3.5rem;
        }

        .team-members {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2.5rem;
            justify-items: center;
        }

        .team-member-card {
            background-color: #f8f4f0; /* Un beige suave para las tarjetas */
            border-radius: 1rem; /* Bordes más redondeados */
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12); /* Sombra más profunda */
            padding: 2.5rem;
            text-align: center;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            max-width: 320px;
            border: 2px solid #d4c0a5; /* Borde más robusto */
        }

        .team-member-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
        }

        .team-member-card img {
            width: 140px; /* Tamaño de imagen un poco más grande */
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1.8rem auto;
            border: 5px solid #6a8b3d; /* Borde verde más grueso para las fotos */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        .team-member-card h3 {
            font-size: 1.8rem;
            font-weight: 700;
            color: #3b5323;
            margin-bottom: 0.6rem;
        }

        .team-member-card .role {
            font-weight: 600;
            color: #4a6b0c; /* Verde para el cargo */
            font-size: 1.15rem;
            margin-top: 0.5rem;
            margin-bottom: 1rem;
        }
        .team-member-card p:last-child { /* Estilo para la descripción */
            font-size: 0.95rem;
            color: #5d4037;
            line-height: 1.6;
        }
    </style>
</head>

<body class="min-h-screen flex bg-gray-100">
    <div class="flex min-h-screen w-full">
        <?php
            if (isset($_SESSION['usuario'])) {
                include 'assets/layout/sidebar.php';
            }
        ?>    
        <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;"> 
        
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

                <section class="max-w-6xl mx-auto py-12 px-4 bg-white rounded-xl shadow-lg mt-8" data-aos="fade-up">
                    <h2 class="section-title">¿Qué ofrece nuestro sistema?</h2>
                    <div class="features">
                        <div class="feature" data-aos="fade-right">
                            <h3>Registro de Animales</h3>
                            <p>Lleva un control detallado del inventario de animales en tu finca, facilitando la trazabilidad y la gestión individual de cada animal.</p>
                        </div>
                        <div class="feature" data-aos="fade-up">
                            <h3>Simulación de Producción</h3>
                            <p>Simula el crecimiento y productividad de tu ganado en diferentes escenarios, optimizando decisiones para maximizar rendimientos.</p>
                        </div>
                        <div class="feature" data-aos="fade-left">
                            <h3>Alertas y Recordatorios</h3>
                            <p>Recibe notificaciones automáticas para vacunaciones, alimentación, controles sanitarios y más, asegurando el bienestar de tu rebaño.</p>
                        </div>
                    </div>
                </section>

                <section class="valor-section max-w-6xl mx-auto py-12 px-6 mt-8">
                    <div data-aos="fade-right">
                        <img src="assets/images/simulatorProfile.png" alt="Mujeres trabajando felices con tablet e inventario">
                    </div>
                    <div class="valor-texto" data-aos="fade-left">
                        <h2>PROPUESTA <br> VALOR</h2>
                        <p><em>¡Aprende, gestiona y juega mientras transformas la ganadería!</em></p>
                        <p>Hemos desarrollado una plataforma educativa y administrativa innovadora, pensada para pequeños y medianos ganaderos, aprendices y estudiantes. Nuestro sistema web combina un simulador interactivo de cuidado animal, tipo juego, con un robusto sistema de inventario ganadero.</p>
                        <p>Podrás aprender sobre alimentación, salud y producción animal mientras administras insumos reales como alimentos, medicinas y herramientas. Automatiza procesos, mejora tus decisiones y conecta el aprendizaje con la práctica, todo desde una interfaz sencilla y funcional.</p>
                        <p>¡Haz de la educación ganadera una experiencia divertida y eficiente!</p>
                    </div>
                </section>

                <section class="max-w-6xl mx-auto py-12 px-4 bg-white rounded-xl shadow-lg mt-8" data-aos="fade-up">
                    <h2 class="section-title">Estadísticas del sistema</h2>
                    <div class="stats">
                        <div class="stat" data-aos="zoom-in" data-aos-delay="100">
                            <h3>+1,200</h3>
                            <p>Usuarios Registrados</p>
                        </div>
                        <div class="stat" data-aos="zoom-in" data-aos-delay="200">
                            <h3>+8,000</h3>
                            <p>Animales Registrados</p>
                        </div>
                        <div class="stat" data-aos="zoom-in" data-aos-delay="300">
                            <h3>+2,500</h3>
                            <p>Reportes Generados</p>
                        </div>
                    </div>
                </section> 
                
                <section class="team-section max-w-6xl mx-auto py-16 px-6 mt-8" data-aos="fade-up">
                    <h2>Nuestro Equipo de Desarrollo</h2>
                    <div class="team-members">
                        <div class="team-member-card" data-aos="fade-up" data-aos-delay="200">
                            <img src="assets/images/equipo-miembro-2.jpg" alt="Foto de Miembro del Equipo 2">
                            <h3>Juan Santos</h3>
                            <p class="role">Desarrolladora Frontend / Diseñador UX</p>
                            <p>Responsable de la interfaz de usuario, garantizando una experiencia intuitiva y visualmente atractiva para todos nuestros usuarios.</p>
                        </div>
                        <div class="team-member-card" data-aos="fade-up" data-aos-delay="100">
                            <img src="assets/images/equipo-miembro-1.jpg" alt="Foto de Miembro del Equipo 1">
                            <h3>José Domínguez</h3>
                            <p class="role">Líder de Proyecto / Desarrollador Backend</p>
                            <p>Encargado de la arquitectura de la base de datos y la lógica del servidor, asegurando un rendimiento óptimo y seguro de la plataforma.</p>
                        </div>
                        <div class="team-member-card" data-aos="fade-up" data-aos-delay="300">
                            <img src="assets/images/equipo-miembro-3.jpg" alt="Foto de Miembro del Equipo 3">
                            <h3>Jasbleidy Morales</h3>
                            <p class="role">Especialista en Simulación y Datos</p>
                            <p>Experto en el modelado de datos para la simulación ganadera y la integración de funcionalidades analíticas avanzadas del sistema.</p>
                        </div>
                    </div>
                </section>
                <?php include 'assets/layout/flooter.php'; ?>
            </div>
        </main>
    </div>

    <script src="https://www.powr.io/powr.js?platform=html"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/tienda_online.js"></script>

    <script>
    AOS.init({
        duration: 1000, 
        once: true,    
    });

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');

        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled'); 
        } else {
            localStorage.setItem('darkMode', 'disabled'); 
        }
    }

    function applyDarkModeOnLoad() {
        if (localStorage.getItem('darkMode') === 'enabled') {
            document.body.classList.add('dark-mode');
        }
    }

    document.addEventListener('DOMContentLoaded', applyDarkModeOnLoad);
    </script>
</body>
</html>