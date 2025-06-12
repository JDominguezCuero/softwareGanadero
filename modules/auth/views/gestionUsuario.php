<?php
require_once(__DIR__ . '../../../../config/config.php');

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../../public/index_controller.php?login=error&reason=nologin");
    exit;
}

if (isset($_GET['inv']) && $_GET['inv'] == 1 && isset($_GET['error'])) {
    $mensaje = json_encode($_GET['error']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('❌ Error', $mensaje, 'error');
        });
    </script>";
} else if (isset($_GET['msg'])) {
    $mensajeExitoso = json_encode($_GET['msg']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('✅ Operación Exitosa', $mensajeExitoso, 'success');
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="<?= BASE_URL ?>/modules/productos/css/estilosProductos.css">

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
            <div class="hm-wrapper">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Gestión de Usuarios
                        
                    </h1>
                    <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarUsuario">
                        Agregar Nuevo Usuario
                    </button>
                </div>
                
                <div style="position: relative;">
                    <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Buscar producto..." title="Escribe un nombre" style="padding-left: 35px;">
                    <i data-lucide="search" style="position: absolute; left: 8px; top: 40%; transform: translateY(-50%); width: 16px; height: 16px; color: gray;"></i>
                </div>

                <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
                    <table id="myTable" class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-green-700 text-white">
                            <tr>
                                <th class="py-3 px-4 text-center w-[80px]">Imagen de Perfil</th>
                                <th class="py-3 px-4 text-center w-[80px]">Nombre Completo</th>
                                <th class="py-3 px-4 text-center w-[150px]">Nombre de Usuario</th>
                                <th class="py-3 px-4 text-center w-[100px]">Correo Electronico</th>
                                <th class="py-3 px-4 text-center w-[100px]">Dirección</th>
                                <th class="py-3 px-4 text-center w-[100px]">Telefono</th>
                                <th class="py-3 px-4 text-center w-[110px]">Estado</th>
                                <th class="py-3 px-4 text-center w-[100px]">Rol</th>
                                <th class="py-3 px-4 text-center w-[100px]">Descripción Rol</th>
                                <th class="py-3 px-4 text-center w-[120px]">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php if (!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $item): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-2 px-4 text-center">
                                        <?php if (!empty($item['imagen_url_Usuario'])): ?>
                                        <img src="<?= htmlspecialchars($item['imagen_url_Usuario']) ?>" alt="Producto" class="product-thumbnail">
                                        <?php else: ?>
                                        N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['nombreCompleto']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['nombre_usuario']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['correo_usuario']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['direccion_usuario']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['telefono_usuario']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['estado']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['nombre_rol']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['descripcion']) ?></td>

                                    <td class="py-3 px-6 text-center align-middle">
                                        <div class="flex justify-center items-center gap-2">
                                            <i data-lucide="square-pen"
                                                class="text-blue-600 hover:text-blue-800 cursor-pointer w-4 h-4"
                                                style="width:16px; height:16px;"
                                                data-toggle="modal" data-target="#modalEditarUsuario"
                                                data-id="<?= $item['id_usuario'] ?>"
                                                data-imagen_url="<?= htmlspecialchars($item['imagen_url_Usuario']) ?>"
                                                data-nombre="<?= htmlspecialchars($item['nombreCompleto']) ?>"
                                                data-nombreUsuario="<?= htmlspecialchars($item['nombre_usuario']) ?>"
                                                data-correo="<?= htmlspecialchars($item['correo_usuario']) ?>"
                                                data-direccion="<?= htmlspecialchars($item['direccion_usuario']) ?>"
                                                data-telefono="<?= htmlspecialchars($item['telefono_usuario']) ?>"
                                                data-estado="<?= htmlspecialchars($item['estado']) ?>"
                                                data-nombre="<?= htmlspecialchars($item['nombre_rol']) ?>">                                        
                                            </i>
                                            <span class="mx-1 text-gray-400">|</span>
                                            <a href="<?= BASE_URL ?>/modules/auth/controller.php?accion=eliminar&id=<?= $item['id_usuario'] ?>"
                                            onclick="return confirm('¿Estás seguro que deseas eliminar este usuario?');">
                                                <i data-lucide="trash-2"
                                                    class="inline-block text-red-500 hover:text-red-700 cursor-pointer"
                                                    style="width:16px; height:16px;">
                                                </i>
                                            </a>
                                        </div>
                                    </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-gray-500">No hay usuarios registrados.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                </div>
                
                <div style="margin-top: 30px;">
                    <button class="btn btn-secondary" style="background-color: grey">
                        Exportar Excel
                    </button>           
                </div>

            </div>
        </main>        
    </div>

    <?php include __DIR__ . '/../layout/registrar_usuario.php'; ?>
    <?php include __DIR__ . '/../layout/editar_usuario.php'; ?>
    
    <?php include '../../modules/auth/layout/mensajesModal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        $(document).ready(function () {

            // Limpia la URL después de mostrar el mensaje
            if(window.location.search.includes("msg=") || window.location.search.includes("error=")) {
                window.history.replaceState({}, document.title, window.location.pathname + "?accion=listar");
            }
        });

        // Función de búsqueda (sin cambios)
        function myFunction() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                let rowContainsFilter = false;
                const tds = tr[i].getElementsByTagName("td");
                for (j = 1; j < tds.length - 1; j++) { // Recorre todas las celdas menos la última (acciones)
                    const td = tds[j];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            rowContainsFilter = true;
                            break;
                        }
                    }
                }
                tr[i].style.display = rowContainsFilter ? "" : "none";
            }
        }
    </script>

</body>
</html>