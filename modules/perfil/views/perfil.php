<?php

require_once(__DIR__ . '../../../../config/config.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: " . BASE_URL . "/public/index_controller.php?login=error&reason=nologin");
    exit;
}

if (isset($_SESSION['error'])) {
    $mensaje = json_encode($_SESSION['error']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('❌ Error', $mensaje, 'error');
        });
    </script>";
    unset($_SESSION['error']);
} else if (isset($_SESSION['message'])) {
    $mensajeExitoso = json_encode($_SESSION['message']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('✅ Operación Exitosa', $mensajeExitoso, 'success');
        });
    </script>";
    unset($_SESSION['message']);
}

if (!isset($userData) || !$userData) {
    $_SESSION['error'] = "No se pudo cargar la información del usuario.";
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$profileImage = $userData['imagen_url_Usuario'] ?? BASE_URL . '/modules/auth/perfiles/profileDefault.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - <?php echo htmlspecialchars($userData['nombreCompleto'] ?? 'Usuario'); ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/perfil/views/css/estilos.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">
</head>

<body class="min-h-screen flex bg-gray-100">

    <div class="flex min-h-screen w-full">
        <?php include '../../public/assets/layout/sidebar.php'; ?>

        <main id="mainContent" class="p-6 flex-1 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">

            <form class="datos" action="<?= BASE_URL ?>/modules/perfil/controller.php?accion=actualizar" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($userData['id_usuario'] ?? '') ?>">
                <input type="hidden" name="imagen_url_actual" value="<?= htmlspecialchars($profileImage) ?>">

                <div class="mb-8 p-6 bg-white rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-2">Foto de perfil</h2>
                    <p class="text-gray-600 mb-4">Sube tu foto de perfil, un retrato en primer plano es ideal. No pongas un logo, queremos verte la cara.</p>
                    
                    <div class="flex items-center space-x-4">
                        <img id="preview" src="<?= htmlspecialchars($profileImage) ?>" alt="Perfil" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                        <div>
                            <button type="button" onclick="document.getElementById('fileFoto').click()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors">SUBIR FOTO DE PERFIL</button>
                            <input type="file" id="fileFoto" name="fileFoto" accept="image/*" style="display: none;" onchange="previewFoto(event)">
                            <small class="block text-gray-500 mt-1">*Mínimo 500 x 500px</small>
                        </div>
                    </div>
                </div>

                <div class="section p-6 bg-white rounded-lg shadow-md mb-8">
                    <h3 class="text-xl font-semibold mb-2">Datos de usuario</h3>
                    <p class="text-gray-600 mb-4">Añade tus datos personales y de contacto.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" name="nombreCompleto" placeholder="Nombre completo" value="<?= htmlspecialchars($userData['nombreCompleto'] ?? '') ?>" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="nombre_usuario" placeholder="Nombre de usuario" value="<?= htmlspecialchars($userData['nombre_usuario'] ?? '') ?>" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="email" name="correo_usuario" placeholder="Correo electrónico" value="<?= htmlspecialchars($userData['correo_usuario'] ?? '') ?>" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="tel" name="telefono_usuario" placeholder="Teléfono Móvil" value="<?= htmlspecialchars($userData['telefono_usuario'] ?? '') ?>" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" name="direccion_usuario" placeholder="Dirección" value="<?= htmlspecialchars($userData['direccion_usuario'] ?? '') ?>" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="password" name="contrasena" placeholder="Contraseña (dejar en blanco para no cambiar)" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                 <div class="section p-6 bg-white rounded-lg shadow-md mb-8">
                    <h3 class="text-xl font-semibold mb-2">Redes sociales</h3>
                    <p class="text-gray-600 mb-4">Añade tus redes sociales.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="url" name="facebook" placeholder="Facebook URL" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                        <input type="url" name="instagram" placeholder="Instagram URL" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                        <input type="url" name="whatsapp" placeholder="Whatsapp" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" >
                    </div>
                </div>

                <div class="section p-6 bg-white rounded-lg shadow-md mb-8">
                    <h3 class="text-xl font-semibold mb-2">Ubicación principal</h3>
                    <p class="text-gray-600 mb-4">Indica tu ubicación.</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <select name="pais" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="Colombia" selected>Colombia</option>
                        </select>
                        <select id="departamento" name="departamento" onchange="cargarMunicipios()" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecciona un departamento</option>
                        </select>
                        <select id="municipio" name="municipio" class="p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Selecciona un municipio</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="w-full py-3 mt-6 bg-green-600 text-white font-bold rounded-md hover:bg-green-700 transition-colors">Guardar Cambios</button>
            </form>
        </main>
    </div>
    
    <?php include '../../modules/auth/layout/mensajesModal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

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
            fetch('<?= BASE_URL ?>/modules/perfil/views/js/colombia.json')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    datosColombia = data;
                    cargarDepartamentos();
                })
                .catch(error => console.error('Error al cargar el JSON:', error));
        });

        function cargarDepartamentos() {
            const departamentoSelect = document.getElementById('departamento');
            departamentoSelect.innerHTML = '<option value="">Selecciona un departamento</option>';
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

        lucide.createIcons();
    </script>

</body>
</html>