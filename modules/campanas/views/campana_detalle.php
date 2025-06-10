<?php

require_once(__DIR__ . '../../../../config/config.php');

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($campana_detalle['titulo'] ?? 'Detalle de Campaña') ?> - AgroMarket</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/principal.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>    
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">
    <style>
        /* Contenedor principal del detalle */
        .campaign-detail-container {
            display: flex;
            flex-wrap: wrap; /* Permite que los bloques se envuelvan en pantallas pequeñas */
            gap: 30px;
            /* margin-top: 30px; */
            background-color: var(--texto-claro); /* Fondo claro para el detalle */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .dark-mode .campaign-detail-container {
            background-color: var(--oscuro);
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            border: 1px solid #555;
        }

        /* Sección de imagen */
        .campaign-image {
            flex: 1 1 400px; /* Crece y encoge, con un tamaño base de 400px */
            max-width: 50%; /* Ocupa máximo la mitad del ancho */
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .campaign-image img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }

        /* Sección de información */
        .campaign-info {
            flex: 1 1 400px; /* Crece y encoge, con un tamaño base de 400px */
            max-width: 50%; /* Ocupa máximo la mitad del ancho */
            padding: 0 20px;
            box-sizing: border-box;
        }

        .campaign-info h1 {
            color: var(--verde);
            font-size: 2.5em;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .campaign-info .meta-info p {
            font-size: 1.1em;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            color: var(--texto-oscuro);
        }
        .dark-mode .campaign-info .meta-info p {
            color: var(--texto-claro);
        }

        .campaign-info .meta-info p i {
            margin-right: 10px;
            font-size: 1.3em;
            color: var(--marron);
        }

        .campaign-info .description-full {
            margin-top: 25px;
            line-height: 1.6;
            font-size: 1em;
            color: #444;
        }
        .dark-mode .campaign-info .description-full {
            color: #ddd;
        }

        /* Sección del organizador */
        .organizer-info {
            flex: 1 1 100%; /* Ocupa todo el ancho en una nueva línea */
            margin-top: 30px;
            padding: 25px;
            background-color: var(--claro);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border-left: 5px solid var(--verde);
            color: var(--texto-oscuro);
        }
        .dark-mode .organizer-info {
            background-color: #2b2b2b;
            border-left-color: var(--marron);
            color: var(--texto-claro);
        }

        .organizer-info h2 {
            font-size: 1.8em;
            color: var(--verde);
            margin-top: 0;
            margin-bottom: 15px;
        }

        .organizer-info p {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }
        .organizer-info p i {
            margin-right: 10px;
            font-size: 1.2em;
            color: var(--marron);
        }

        .organizer-contact-buttons {
            margin-top: 20px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .organizer-contact-buttons a {
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 1em;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .organizer-contact-buttons .btn-whatsapp {
            background-color: #25D366;
            color: white;
        }
        .organizer-contact-buttons .btn-whatsapp:hover {
            background-color: #1DA851;
            transform: translateY(-2px);
        }

        .organizer-contact-buttons .btn-email {
            background-color: var(--marron);
            color: white;
        }
        .organizer-contact-buttons .btn-email:hover {
            background-color: #7a5c52;
            transform: translateY(-2px);
        }

        /* Botón de volver */
        .back-button-container {
            margin-top: 40px;
            text-align: center;
        }

        /* Media queries para responsividad */
        @media (max-width: 992px) {
            .campaign-image, .campaign-info {
                max-width: 100%; /* Ocupan todo el ancho en tablets */
                padding: 0; /* Eliminar padding horizontal si ocupan todo el ancho */
            }
            .campaign-info {
                margin-top: 20px; /* Espacio entre imagen y texto */
            }
        }
        @media (max-width: 768px) {
            .campaign-detail-container {
                flex-direction: column; /* Apila los elementos en móviles */
                padding: 20px;
            }
            .campaign-info h1 {
                font-size: 2em;
            }
            .organizer-contact-buttons {
                justify-content: center;
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
        <main id="mainContent" class="flex-1 p-6 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">   
            
            <div class="hm-wrapper">

                <?php include '../../public/assets/layout/header.php'; ?>

                <div class="hm-page-block">
                     <div class="container">
                         <?php if ($campana_detalle): ?>
                              <div class="campaign-detail-container" data-aos="fade-up">
                                  <div class="campaign-image">
                                      <img src="<?= htmlspecialchars($campana_detalle['imagen_url'] ?? 'assets/img/placeholder_campana.jpg') ?>" alt="<?= htmlspecialchars($campana_detalle['titulo']) ?>">
                                 </div>
                                <div class="campaign-info">
                                    <h1><?= htmlspecialchars($campana_detalle['titulo']) ?></h1>
                                     <div class="meta-info">
                                        <p><i class="las la-calendar"></i> Fecha del Evento: <?= (new DateTime($campana_detalle['fecha_evento']))->format('d/m/Y H:i') ?></p>
                                        <p><i class="las la-map-marker"></i> Ubicación: <?= htmlspecialchars($campana_detalle['ubicacion']) ?></p>
                                        <p><i class="las la-clock"></i> Publicado el: <?= (new DateTime($campana_detalle['fecha_publicacion']))->format('d/m/Y') ?></p>
                                         <p><i class="las la-info-circle"></i> Estado:&nbsp;&nbsp;<span style="font-weight: bold; color: <?= $campana_detalle['estado'] === 'activa' ? 'var(--verde)' : 'gray' ?>;"><?= htmlspecialchars(ucfirst($campana_detalle['estado'])) ?></span></p>
                                     </div>
                                     <div class="description-full">
                                        <h2>Descripción Detallada</h2>
                                         <p><?= nl2br(htmlspecialchars($campana_detalle['descripcion'])) ?></p>
                                    </div>
                                </div>

                                 <div class="organizer-info" data-aos="fade-up">
                                    <h2>Contacto del Organizador</h2>
                                    <p><i class="las la-user"></i> Nombre:&nbsp;&nbsp;<?= htmlspecialchars($campana_detalle['nombre_usuario'] ?? 'N/A') ?></p>
                                    <?php if (!empty($campana_detalle['telefono_usuario'])): ?>
                                        <p><i class="las la-phone"></i> Teléfono:&nbsp;&nbsp;<a style="color: black" href="tel:<?= htmlspecialchars($campana_detalle['telefono_usuario']) ?>"><?= htmlspecialchars($campana_detalle['telefono_usuario']) ?></a></p>
                                     <?php endif; ?>
                                     <?php if (!empty($campana_detalle['correo_usuario'])): ?>
                                         <p><i class="las la-envelope"></i> Email:&nbsp;&nbsp;<a style="color: blue" href="mailto:<?= htmlspecialchars($campana_detalle['correo_usuario']) ?>"><?= htmlspecialchars($campana_detalle['correo_usuario']) ?></a></p>
                                     <?php endif; ?>
                                    <?php if (!empty($campana_detalle['direccion_usuario'])): ?>
                                         <p><i class="las la-home"></i> Dirección:&nbsp;&nbsp;<?= htmlspecialchars($campana_detalle['direccion_usuario']) ?></p>
                                     <?php endif; ?>

                                     <div class="organizer-contact-buttons">
                                         <?php if (!empty($campana_detalle['telefono_usuario'])): ?>
                                              <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $campana_detalle['telefono_usuario']) ?>" target="_blank" class="btn-whatsapp">
                                                   <i class="lab la-whatsapp"></i> WhatsApp
                                            </a>
                                         <?php endif; ?>
                                         <?php if (!empty($campana_detalle['correo_usuario'])): ?>
                                               <a href="mailto:<?= htmlspecialchars($campana_detalle['correo_usuario']) ?>" class="btn-email">
                                                  <i class="las la-envelope"></i> Enviar Email
                                            </a>
                                        <?php endif; ?>
                                     </div>
                                </div>
                            </div>

                              <div class="back-button-container" data-aos="fade-up">
                                 <a href="<?= BASE_URL ?>/modules/campanas/controller.php" class="hm-btn btn-primary uppercase">
                                     <i class="las la-arrow-left"></i> Volver a Campañas
                                  </a>
                             </div>

                         <?php else: ?>
                            <div class="text-center no-results" data-aos="fade-in">
                                <h1>Campaña no encontrada</h1>
                                <p>Lo sentimos, la campaña que buscas no existe o ha sido eliminada.</p>
                                <a href="<?= BASE_URL ?>/modules/campanas/controller.php" class="hm-btn btn-primary uppercase mt-30">
                                    <i class="las la-arrow-left"></i> Ver todas las Campañas
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php include '../../public/assets/layout/flooter.php'; ?>

            </div>
        </main>
    </div>

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