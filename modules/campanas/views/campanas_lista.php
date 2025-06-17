<?php

require_once(__DIR__ . '../../../../config/config.php');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campañas y Eventos - AgroMarket</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="../../public/assets/css/principal.css">
      <link rel="stylesheet" href="../../public/assets/css/detalleProducto.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>    
    <link rel="stylesheet" href="../../public/assets/css/estilos.css">
    <style>
        /* Estilos básicos para la cuadrícula de campañas */
        .campaigns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); /* 300px como tamaño mínimo */
            gap: 30px; /* Espacio entre las tarjetas */
            margin-top: 30px;
        }

        .campana-item {
            background-color: var(--texto-claro); /* Fondo claro para las tarjetas */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%; /* Asegura que todas las tarjetas tengan la misma altura si están en un grid */
            color: var(--texto-oscuro);
        }

        .dark-mode .campana-item {
            background-color: var(--oscuro); /* Fondo oscuro en modo oscuro */
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            border: 1px solid #555;
        }

        .campana-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
        }

        .campana-item .c-portada {
            width: 100%;
            height: 200px; /* Altura fija para las imágenes de portada */
            overflow: hidden;
        }

        .campana-item .c-portada img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Recorta la imagen para que cubra el espacio */
            display: block;
        }

        .campana-item .c-info {
            padding: 20px;
            flex-grow: 1; /* Permite que el contenido se expanda */
            display: flex;
            flex-direction: column;
        }

        .campana-item .c-info h3 {
            font-size: 1.5em;
            margin-top: 0;
            margin-bottom: 10px;
            color: var(--verde); /* Título en verde */
        }

        .campana-item .c-info .descripcion {
            font-size: 0.95em;
            color: #555;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .dark-mode .campana-item .c-info .descripcion {
            color: #bbb;
        }

        .campana-item .c-info p {
            margin-bottom: 8px;
            font-size: 0.9em;
            display: flex;
            align-items: center;
        }
        .campana-item .c-info p i {
            margin-right: 8px;
            font-size: 1.1em;
            color: var(--marron);
        }

        .campana-item .c-info .organizador {
            margin-top: auto; /* Empuja el organizador al final */
            font-size: 0.85em;
            color: #777;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .dark-mode .campana-item .c-info .organizador {
             color: #aaa;
             border-top: 1px solid #444;
        }

        .campana-item .hm-btn {
            display: block;
            margin-top: 20px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .campaigns-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }
        @media (max-width: 480px) {
            .campaigns-grid {
                grid-template-columns: 1fr; /* Una columna en pantallas muy pequeñas */
            }
            .campana-item .c-portada {
                height: 180px;
            }
        }
    </style>
</head>
<body class="min-h-screen flex bg-gray-100">
    <div class="flex min-h-screen w-full">
        <?php
            if (isset($_SESSION['usuario'])) {
                include '../../public/assets/layout/sidebar.php';
            }
        ?>
        <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">   
            
            <div class="hm-wrapper">

                <?php include '../../public/assets/layout/header.php'; ?>        
                
                <div class="hm-page-block">
                    <div class="container">
                        <div class="header-title" data-aos="fade-up">
                            <h1>Campañas y Eventos Agropecuarios</h1>
                            <p>Encuentra mercados campesinos, ferias y reuniones en tu comunidad.</p>
                            <?php if (isset($_SESSION['user_id'])): // Suponiendo que usas 'user_id' para saber si el usuario está logueado ?>
                                <a href="crear_campana.php" class="hm-btn btn-primary uppercase mt-30">Crear Nueva Campaña</a>
                            <?php endif; ?>
                        </div>

                        <div class="main-content">
                            <?php
                            // Asegúrate de que $campanas_listado viene del controlador
                            if (isset($campanas_listado) && !empty($campanas_listado)) {
                                renderCampanaItems($campanas_listado);
                            } else {
                                echo '<p class="no-results">Actualmente no hay campañas activas. ¡Sé el primero en publicar una!</p>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php include '../../public/assets/layout/flooter.php'; ?>

            </div>
        </main>
    </div>

    <script src="https://www.powr.io/powr.js?platform=html"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="../../public/assets/js/tienda_online.js"></script>

    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
        AOS.init();
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