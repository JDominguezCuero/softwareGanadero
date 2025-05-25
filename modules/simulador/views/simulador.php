<?php
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit;
}

require_once(__DIR__ . '/../../../config/config.php');

// Función para obtener emoji según salud del animal
function getEstadoEmoji($salud) {
    if ($salud < 30) return '😷';
    if ($salud < 50) return '😟';
    if ($salud < 80) return '😐';
    return '😊';
}

if (isset($_SESSION['respuesta'])) {
    $respuesta = $_SESSION['respuesta'];
    echo '<div style="padding:10px; margin:10px 0; border-radius:5px; color:white; background-color:' . 
        ($respuesta['success'] ? 'green' : 'red') . ';">' .
        htmlspecialchars($respuesta['mensaje']) . 
    '</div>';
    unset($_SESSION['respuesta']);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Simulador Ganadero</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/simulador/css/estilos.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="flex min-h-screen w-full">
    <?php include '../../public/assets/layout/sidebar.php'; ?>
    <main class="flex-1 p-6 overflow-y-auto transition-all duration-300 h-full contenido" style="margin: auto;">



        <div class="container">
            <h1 class="titulo">🐾 Simulación en curso </h1>

            <?php if (empty($animales)): ?>
                <p class="mensaje-vacio">No hay animales configurados.</p>
            <?php else: ?>

                <div class="contenedor-carrusel">
                    <button class="btn-carrusel izquierda" onclick="moverCarrusel(-1)">❮</button>

                    <div class="grid-animales-responsivo" id="grid-animales">
                        <?php foreach ($animales as $animal): 

                            $emoji_estado = getEstadoEmoji($animal['salud']);
                        ?>
                            <div class="tarjeta-animal" 
                                data-id="<?= $animal['id_animal'] ?>" 
                                data-nombre="<?= htmlspecialchars($animal['nombre']) ?>" 
                                data-alimentacion="<?= $animal['alimentacion'] ?>" 
                                data-higiene="<?= $animal['higiene'] ?>" 
                                data-salud="<?= $animal['salud'] ?>" 
                                data-produccion="<?= $animal['produccion'] ?>" 
                                onclick="mostrarModal(this)">
                                <div class="animal-info" style="position: relative;">
                                    <img class="imagen-animal" src="../../modules/simulador/images/<?= htmlspecialchars($animal['tipo_nombre'] ?? 'default') ?>.png" alt="<?= $animal['tipo_nombre'] ?? 'default' ?>">
                                    <div class="estado-emoji"><?= $emoji_estado ?></div>
                                </div>

                                <div id="nombre-animal-<?php echo $animal['id_animal']; ?>">
                                    <span style="cursor: pointer;">
                                        <?php echo htmlspecialchars($animal['nombre']); ?>
                                        <button class="btn-editar-nombre" data-id="<?= $animal['id_animal']; ?>" style="border:none;background:none;cursor:pointer;padding:0;margin-left:5px;">
                                            ✏️
                                        </button>
                                    </span>
                                </div>

                                <div class="estado">
                                    <div class="barra-progreso alimentacion"> <label>🍽️</label>
                                        <div class="barra">
                                            <div class="progreso verde" style="width: <?= $animal['alimentacion'] ?>%"><?= $animal['alimentacion'] ?>%</div>
                                        </div>
                                    </div>
                                    <div class="barra-progreso higiene"> <label>🚿</label>
                                        <div class="barra">
                                            <div class="progreso azul" style="width: <?= $animal['higiene'] ?>%"><?= $animal['higiene'] ?>%</div>
                                        </div>
                                    </div>
                                    <div class="barra-progreso salud"> <label>💊</label>
                                        <div class="barra">
                                            <div class="progreso rojo" style="width: <?= $animal['salud'] ?>%"><?= $animal['salud'] ?>%</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="produccion">🥛 <strong>Producción:</strong> <?= $animal['produccion'] ?>%</p>
                                <!-- <div class="acciones">
                                    <button class="btn-juego" title="Alimentar">🍽️</button>
                                    <button class="btn-juego" title="Bañar">🚿</button>
                                    <button class="btn-juego" title="Medicar">💊</button>
                                </div> -->
                                <button 
                                    class="btn-juego" 
                                    title="Eliminar" 
                                    onclick="eliminarAnimal(<?= $animal['id_animal'] ?>, this.closest('.tarjeta-animal')); event.stopPropagation();">
                                    🗑
                                </button>

                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button class="btn-carrusel derecha" onclick="moverCarrusel(1)">❯</button>
                </div>               

                <?php include 'layout/detalleAnimal.php'; ?>
                <?php include 'layout/eliminarAnimal.php'; ?>    
                                
            <?php endif; ?>
        </div>

        <div class="botones-container">
            <button id="toggle-music" title="Pausar música 🎵">    🔇</button>

            <div class="aceleracion-tiempo-container">
                <button class="btn-acelerar-tiempo" data-factor="1" title="Tiempo Normal">1x</button>
                <button class="btn-acelerar-tiempo" data-factor="2" title="Doble Velocidad">2x</button>
                <button class="btn-acelerar-tiempo" data-factor="5" title="Quíntuple Velocidad">5x</button>
                <button class="btn-acelerar-tiempo" data-factor="10" title="Diez Veces Más Rápido">10x</button>
            </div>

            <a href="views/configuracion.php" class="btn volver">← Volver a Configuraciones .......</a>
        </div>                        


    </main>
</div>

<!-- Audios -->
<audio id="audioAlimentar" src="<?= BASE_URL ?>/modules/simulador/sounds/alimentar.mp3"></audio>
<audio id="audioBanar" src="<?= BASE_URL ?>/modules/simulador/sounds/banar.mp3"></audio>
<audio id="audioCurar" src="<?= BASE_URL ?>/modules/simulador/sounds/curar.mp3"></audio>

<script src="js/funciones.js"></script>
<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>


</body>
</html>
