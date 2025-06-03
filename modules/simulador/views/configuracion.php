<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../public/index.php");
    exit;
}
require_once(__DIR__ . '/../../../config/config.php');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Configurar Simulación</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/simulador/css/estilos.css" />    
</head>
<body>
    <div class="flex min-h-screen w-full">
        <div class="fondo">
            <!-- Música de fondo -->
            <audio autoplay loop id="bg-music">
                <source src="<?= BASE_URL ?>/modules/simulador/sounds/granja.mp3" type="audio/mpeg">
            </audio>

            <div class="config-form animate__animated animate__fadeInDown">
                <h2>Configura tu Simulación</h2>
                <form method="POST" action="../controller.php">
                    <input type="hidden" name="action" value="configurar" />

                    <label for="nombre">Nombre:</label>
                    <input type="text" name="nombre" id="nombre" required />

                    <label for="tipo_animal">Tipo de Animal:</label>
                    <select name="tipo_animal" id="tipo_animal" required style="padding: 10px 15px; margin-bottom: 20px;">
                        <option value="" disabled selected>-- Selecciona un animal --</option>
                        <option value="vaca">Vaca 🐄</option>
                        <option value="toro">Toro 🐂</option>
                        <option value="burro">Burro 🫏</option>
                        <option value="caballo">Caballo 🐴</option>
                        <option value="cerdo">Cerdo 🐖</option>
                        <option value="cabra">Cabra 🐐</option>
                        <option value="gallina">Gallina 🐔</option>
                    </select>

                    <label for="cantidad">Cantidad de Animales:</label>
                    <input type="number" name="cantidad" id="cantidad" min="1" max="5" required />

                    <label for="edad">Edad:</label>
                    <input type="number" name="edad" id="edad" min="1" max="25" required />

                    <button type="submit" class="btn">Comenzar</button>
                </form>
            </div>

        <div class="botones-simulador">
        <button id="toggle-music" title="Pausar música 🎵">    🔇</button>
        <a href="menuPrincipal.php" class="btvolver1">← Volver al Menu Principal</a>
        </div>
        </div>    

    </div>

    <script>
        const music = document.getElementById("bg-music");
        const toggleBtn = document.getElementById("toggle-music");
        toggleBtn.addEventListener("click", () => {
            if (music.paused) {
                music.play();
                toggleBtn.textContent = "🔇";
            } else {
                music.pause();
                toggleBtn.textContent = "🔊";
            }
        });
    </script>
</body>
</html>
