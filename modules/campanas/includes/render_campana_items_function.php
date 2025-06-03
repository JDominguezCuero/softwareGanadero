<?php
require_once(__DIR__ . '../../../../config/config.php');

/**
 * Renderiza una lista de campañas en el formato HTML de 'campana-item'.
 *
 * @param array $campanas Array de campañas, cada una con sus datos.
 * @return void
 */
function renderCampanaItems(array $campanas): void {
    if (empty($campanas)) {
        echo '<p class="no-results">No hay campañas disponibles en este momento.</p>';
        return;
    }

    // Usaremos un 'campaigns-grid' para el estilo de cuadrícula
    echo '<div class="campaigns-grid">'; 
    foreach ($campanas as $campana) {
        $id_campana = htmlspecialchars($campana['id_campana'] ?? '');
        $titulo = htmlspecialchars($campana['titulo'] ?? 'Campaña sin título');
        $descripcion_corta = htmlspecialchars(mb_strimwidth($campana['descripcion'] ?? '', 0, 150, '...')); 
        $ubicacion = htmlspecialchars($campana['ubicacion'] ?? 'Ubicación Desconocida');
        $fecha_evento = new DateTime($campana['fecha_evento'] ?? 'now');
        $imagen_url = htmlspecialchars($campana['imagen_url'] ?? '../../../public/assets/images/placeholder_campana.png');
        $nombre_organizador = htmlspecialchars($campana['nombre_usuario'] ?? 'Organizador Desconocido');
        
        ?>
        <div class="campana-item" data-campana-id="<?= $id_campana ?>">
            <div class="c-portada">
                <img src="<?= $imagen_url ?>" alt="<?= $titulo ?>">
            </div>
            <div class="c-info">
                <h3><?= $titulo ?></h3>
                <p class="descripcion"><?= $descripcion_corta ?></p>
                <p class="ubicacion"><i class="las la-map-marker"></i> <?= $ubicacion ?></p>
                <p class="fecha"><i class="las la-calendar"></i> <?= $fecha_evento->format('d/m/Y H:i') ?></p>
                <p class="organizador">Organizado por:&nbsp;&nbsp;<strong><?= $nombre_organizador ?></strong></p>
                <a href="<?= BASE_URL ?>/modules/campanas/controller.php?id=<?= $id_campana ?>" class="hm-btn btn-primary uppercase">Ver Detalles</a>
            </div>
        </div>
        <?php
    }
    echo '</div>'; // Cierre del contenedor de la cuadrícula
}
?>