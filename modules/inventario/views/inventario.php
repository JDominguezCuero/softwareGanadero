<?php
require_once(__DIR__ . '../../../../config/config.php');

//Llamar al controlador

// Verifica si hay sesión iniciada
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../../public/index.php?login=error&reason=nologin");
    exit;
} else {    
    // echo "<script>alert('✅ Bienvenido.');</script>";
}

// Manejo de errores
if (isset($_GET['inv']) && $_GET['inv'] == 1 && isset($_GET['error'])) {
    $mensaje = json_encode($_GET['error']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('❌ Error', $mensaje, 'error');
        });
    </script>";

}else if(!empty($inventario) && isset($_GET['msg'])){
    $mensajeExitoso = json_encode($_GET['msg']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('✅ Operación Exitosa', $mensajeExitoso, 'success');
        });
    </script>";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
</head>

<body>

    <div class="flex min-h-screen w-full">
        <!-- Layout Sidebar -->
        <?php include '../../public/assets/layout/sidebar.php'; ?>
    
        <main class="flex-1 p-6 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Inventario de Productos Ganaderos</h1>
                <!-- Botón que abre el modal -->
                <button class="btn btn-success" data-toggle="modal" data-target="#miModal">
                    Agregar Producto
                </button>              
            </div>
            
            <div style="position: relative;">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Buscar..." title="Type in a name" style="padding-left: 35px;">
                <i data-lucide="search" style="position: absolute; left: 8px; top: 40%; transform: translateY(-50%); width: 16px; height: 16px; color: gray;"></i>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table id="myTable" class="min-w-full">
                    <thead class="bg-green-700 text-white">
                        <tr>
                            <th class="py-3 px-6 text-center">Producto</th>
                            <th class="py-3 px-6 text-center">Cantidad</th>
                            <th class="py-3 px-6 text-center">Descripción</th>
                            <th class="py-3 px-6 text-center">Precio Unitario</th>
                            <th class="py-3 px-6 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">                           
                        <?php if (!empty($inventario)): ?>
                        <?php foreach ($inventario as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nombre_producto']) ?></td>
                                <td><?= htmlspecialchars($item['cantidad']) ?></td>
                                <td><?= htmlspecialchars($item['descripcion_producto']) ?></td>
                                <td>$<?= number_format($item['precio_unitario'], 2) ?></td>
                                <td class="acciones">
                                    <i data-lucide="square-pen"
                                        style="cursor:pointer; width:16px; height:16px;"
                                        data-toggle="modal" data-target="#modalEditar"
                                        data-id="<?= $item['id_producto'] ?>"
                                        data-producto="<?= htmlspecialchars($item['nombre_producto']) ?>"
                                        data-cantidad="<?= htmlspecialchars($item['cantidad']) ?>"
                                        data-descripcion="<?= htmlspecialchars($item['descripcion_producto']) ?>"
                                        data-precio="<?= $item['precio_unitario'] ?>">
                                    </i>
                                |
                                    <a href="<?= BASE_URL ?>/modules/inventario/controller.php?accion=eliminar&id=<?= $item['id_producto'] ?>" 
                                        onclick="return confirm('¿Estás seguro que deseas eliminar este producto?');">
                                            <i data-lucide="trash-2" style="cursor:pointer; width:16px; height:16px;"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">No hay productos registrados.</td></tr>
                        <?php endif; ?>
                                            
                    </tbody>
                </table>
            </div>
            
            <div style="margin-top: 30px;">
                <!-- Botón que abre el modal -->
                <button class="btn btn-success" style="background-color: grey" data-toggle="modalExcel" data-target="#imprimirExcel">
                    Exportar Excel
                </button>              
            </div>

        </main>
        
    </div>

    <!-- Layout -->
    <!-- Registro -->        
    <?php include 'views/layout/registrar.php'; ?>

    <!-- Editar -->
    <?php include 'views/layout/editar.php'; ?>
    
    <!-- Manejo de errores -->
    <?php include '../../modules/auth/layout/mensajesModal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        lucide.createIcons()

        $(document).ready(function () {
        $('#modalEditar').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Botón que disparó el modal
            var id = button.data('id');
            var producto = button.data('producto');
            var cantidad = button.data('cantidad');
            var descripcion = button.data('descripcion');
            var precio = button.data('precio');

            var modal = $(this);
            modal.find('#editar_id_producto').val(id);
            modal.find('#editar_producto').val(producto);
            modal.find('#editar_cantidad').val(cantidad);
            modal.find('#editar_descripcion').val(descripcion);
            modal.find('#editar_precioUnitario').val(precio);
        });

        //Limpia la url despues de mostrar el mensaje
        if(window.location.search.includes("msg=")) {
            window.history.replaceState({}, document.title, window.location.pathname + "?accion=listar");
        }
    });

    function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        table = document.getElementById("myTable");
        tr = table.getElementsByTagName("tr");
       for (i = 1; i < tr.length; i++) {
            let rowContainsFilter = false;
            const tds = tr[i].getElementsByTagName("td");
            for (let j = 0; j < tds.length; j++) {
                const td = tds[j];
                if (td) {
                    const txtValue = td.textContent || td.innerText;
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