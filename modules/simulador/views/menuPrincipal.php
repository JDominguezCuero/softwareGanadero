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
    <meta charset="UTF-8">
    <title>Simulador Ganadero</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/simulador/css/estilos.css">
</head>
<body>
    <div class="flex min-h-screen w-full">
      <?php include '../../../public/assets/layout/sidebar.php'; ?>
      <main class="flex-1 p-6 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">
        
        <div class="fondo">
            <!-- Música de fondo -->
            <audio autoplay loop id="bg-music">
                <source src="<?= BASE_URL ?>/modules/simulador/sounds/granja.mp3" type="audio/mpeg">
            </audio>

            <div class="menu-container">
                <div class="menu-card animate__animated animate__fadeInDown">
                    <h1>🐮 Bienvenido al Simulador Ganadero 🐷</h1>
                    <p class="subtitulo">¡Cuida tus animales como si fueran tus mascotas virtuales!</p>

                    <div class="botones">
                        <a href="<?= BASE_URL ?>/modules/simulador/controller.php?action=mostrar" class="btn">🎮 Iniciar Simulación</a>
                        <a href="<?= BASE_URL ?>/modules/simulador/views/configuracion.php" class="btn">⚙️ Configurar Animales</a>
                        <a href="<?= BASE_URL ?>/modules/inventario/controller.php?accion=listar" class="btn">📦 Ver Inventario</a>
                        <a href="<?= BASE_URL ?>/modules/usuarios/views/perfil.php" class="btn">👤 Mi Perfil</a>
                        <a href="<?= BASE_URL ?>/public/logout.php" class="btn salir">🚪 Salir</a>
                    </div>
                </div>
            </div>

            <!-- Icono para pausar la música -->
            <button id="toggle-music" title="Pausar música 🎵">🔇</button>
        </div>
      </main>
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

