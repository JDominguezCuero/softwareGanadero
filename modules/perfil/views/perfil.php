<?php
// inventario/views/inventario.php

// Esto se puede dejar, aunque el controlador ya lo incluye.
// Es una buena práctica para asegurar que el config.php siempre esté disponible.
require_once(__DIR__ . '../../../../config/config.php');


?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil de Usuario</title>
  <link rel="stylesheet" href="css/estilos.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/inventario/css/estilosInventario.css">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>    
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">
  
</head>

<body class="min-h-screen flex bg-gray-100">

    <div  class="min-h-screen flex bg-gray-100">
        <?php include '../../../public/assets/layout/sidebar.php'; ?>

        <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">
            
                    <div>
                        <h2>Foto de perfil</h2>
                        <p>Sube tu foto de perfil, un retrato en primer plano es ideal. No pongas un logo, queremos verte la cara.</p>
                    </div>

                    <div class="foto-perfil">
                        <img id="imagen.jpg" src="imagen.jpg" alt="Perfil">
                        <br>
                        <button onclick="document.getElementById('fileFoto').click()">SUBIR FOTO DE PERFIL</button>
                        <input type="file" id="fileFoto" accept="image/*" style="display: none;" onchange="previewFoto(event)">
                        <small>*Mínimo 500 x 500px</small>
                    </div>
                    

                    <form class="datos">
                        <div class="section">
                            <h3>Datos de usuario</h3>
                            <p>Añade tus datos personales y de contacto.</p>
                            <div class="form-grid">
                            <input type="text" name="nombre_completo" placeholder="Nombre completo">
                            <input type="text" name="nombre_usuario" placeholder="Nombre de usuario">
                            <input type="password" name="contrasena" placeholder="Contraseña">
                            <input type="text" name="direccion" placeholder="Dirección">
                            <input type="email" name="correo" placeholder="Correo electrónico">
                            <input type="tel" name="telefono_movil" placeholder="Teléfono Móvil">
                            </div>
                        </div>

                        <div class="section">   
                            <h3 style="margin-top:40px;">Redes sociales</h3>
                            <p>Añade tus redes sociales.</p>
                            <div class="form-grid">
                            <input type="url" name="facebook" placeholder="Facebook URL">
                            <input type="url" name="instagram" placeholder="Instagram URL">
                            <input type="url" name="whatsapp" placeholder="Whatsapp">
                            </div>
                        </div>

                        <div class="section">
                            
                            <h3 style="margin-top:40px;">Dirección principal</h3>
                            <p>Indica la dirección de tu estudio o donde vives. Usa el botón para marcar la ubicación.</p>
                            <div class="form-grid">

                            <select>
                            <option selected>Colombia</option>
                            </select>

                            <select id="departamento" onchange="cargarMunicipios()">
                                <option value="">Selecciona un departamento</option>
                            </select>

                            <select id="municipio">
                                <option value="">Selecciona un municipio</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="submit-btn">Guardar Cambios</button>
                    </form>
              
        </main>
        
    </div>

  <script>
    function previewFoto(event) {
      const reader = new FileReader();
      reader.onload = function () {
        document.getElementById('preview').src = reader.result;
      };
      reader.readAsDataURL(event.target.files[0]);
    }

    
    let datosColombia = [];

    document.addEventListener("DOMContentLoaded", function () {
      fetch('http://localhost/editarperfil/colombia.json') // Asegúrate que este archivo existe y está en la misma carpeta
        .then(response => response.json())
        .then(data => {
          datosColombia = data;
          cargarDepartamentos();
        })
        .catch(error => console.error('Error al cargar el JSON:', error));
    });

    function cargarDepartamentos() {
      const departamentoSelect = document.getElementById('departamento');
      departamentoSelect.innerHTML = '<option value="">Selecciona un departamento</option>'; // Limpia anteriores
      datosColombia.forEach(depto => {
        const option = document.createElement('option');
        option.value = depto.departamento;
        option.textContent = depto.departamento;
        departamentoSelect.appendChild(option);
      });
    }

    function cargarMunicipios() {
      const departamento = document.getElementById('departamento').value;
      const municipioSelect = document.getElementById('municipio');
      municipioSelect.innerHTML = '<option value="">Selecciona un municipio</option>';

      const depto = datosColombia.find(d => d.departamento === departamento);
      if (depto) {
        depto.ciudades.forEach(muni => {
          const option = document.createElement('option');
          option.value = muni;
          option.textContent = muni;
          municipioSelect.appendChild(option);
        });
      }
    }
  </script>

</body>
</html>
