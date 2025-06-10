<?php

// Verifica si hay sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../../public/index_controller.php?login=error&reason=nologin");
    exit;
} else {    
    // echo "<script>alert('✅ Bienvenido.');</script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Simulador Ganadero - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>    
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">
</head>

<body class="min-h-screen flex bg-gray-100">

  <div class="flex min-h-screen w-full">

      <!-- Layout Sidebar -->
      <?php include '../../public/assets/layout/sidebar.php'; ?>

      <!-- Main Content -->
      <main class="flex-1 p-6 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">

          <div class="flex justify-between items-center mb-8">
    <h2 class="text-2xl font-bold">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?> 👋</h2>
    <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="showNotifications()">
        🔔 Notificaciones
    </button>
</div>

<!-- Modal para mostrar las notificaciones -->
<div id="notificationModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h2>Notificaciones</h2>
        <ul id="notificationList"></ul>
        <button class="close-btn" onclick="closeModal()">Cerrar</button>
    </div>
</div>

<style>
    /* Estilos para el modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 5px;
        width: 300px;
        text-align: center;
    }

    .close-btn {
        background: red;
        color: white;
        padding: 5px 10px;
        border: none;
        cursor: pointer;
        margin-top: 10px;
    }

    .bg-green-600 {
        background: #16a34a;
    }

    .hover\:bg-green-700:hover {
        background: #15803d;
    }

    .notification-btn {
        padding: 10px;
        border: none;
        cursor: pointer;
        margin-top: 20px;
    }
</style>

          <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
              <div class="bg-white p-6 rounded-lg shadow text-center">
                  <h5 class="text-gray-500">Producción Total</h5>
                  <h2 class="text-2xl font-bold">7,540 L</h2>
                  <p class="text-gray-400">Últimos 5 meses</p>
              </div>
              <div class="bg-white p-6 rounded-lg shadow text-center">
                  <h5 class="text-gray-500">Animales Registrados</h5>
                  <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($_animal['cantidadAnimales']); ?></h2>
                  <p class="text-gray-400">Total en inventario</p>
              </div>
              <div class="bg-white p-6 rounded-lg shadow text-center">
                  <h5 class="text-gray-500">Vacunas Pendientes</h5>
                  <h2 class="text-2xl font-bold">12</h2>
                  <p class="text-gray-400">Próximos 7 días</p>
              </div>
              <div class="bg-white p-6 rounded-lg shadow text-center">
                  <h5 class="text-gray-500">Inventario Crítico</h5>
                  <h2 class="text-2xl font-bold">5 Items</h2>
                  <p class="text-gray-400">Concentrados y medicinas</p>
              </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <div class="col-span-2 bg-white p-6 rounded-lg shadow">
                  <h5 class="text-xl font-semibold mb-4">Gráfico de Producción Mensual</h5>
                  <canvas id="graficoProduccion" height="150"></canvas>
              </div>

              <div class="bg-white p-6 rounded-lg shadow">
                  <h5 class="text-xl font-semibold mb-4">Alertas Rápidas</h5>
                  <ul class="list-disc pl-5 text-gray-600 space-y-2">
                      <li>✔ Revisión de ganado programada</li>
                      <li>✔ Inventario bajo en alimentos</li>
                      <li>✔ 2 animales enfermos en observación</li>
                  </ul>
              </div>
          </div>
          
          <!-- Layout Flooter -->
          <!-- <?php include '../../public/assets/layout/flooter.php'; ?> -->

      </main>

  </div>

  <!-- SIDEBAR -->
  <script>
      
      // Estilos Dashboard
      const ctx = document.getElementById('graficoProduccion').getContext('2d');
      const produccionChart = new Chart(ctx, {
          type: 'bar',
          data: {
              labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May'],
              datasets: [{
                  label: 'Litros de leche',
                  data: [1200, 1500, 1800, 1700, 1600],
                  backgroundColor: 'rgba(34,197,94,0.7)',
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: {
                      position: 'top',
                  },
                  title: {
                      display: true,
                      text: 'Producción mensual'
                  }
              }
          }
      });
  </script>

</body>

</html>
