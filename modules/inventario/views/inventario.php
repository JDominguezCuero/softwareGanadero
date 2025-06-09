<?php
// inventario/views/inventario.php

// Esto se puede dejar, aunque el controlador ya lo incluye.
// Es una buena práctica para asegurar que el config.php siempre esté disponible.
require_once(__DIR__ . '../../../../config/config.php');

// Los mensajes de sesión y error ya son manejados por el controlador antes de incluir la vista.
// Por lo tanto, las variables $msg y $mensjError (ahora $error en la URL)
// ya deberían estar disponibles aquí si vienen del controlador.

// Manejo de errores/mensajes pasados desde el controlador
// Usamos $_GET['error'] y $_GET['msg'] que el controlador envía.
if (isset($_GET['inv']) && $_GET['inv'] == 1 && isset($_GET['error'])) {
    $mensaje = json_encode($_GET['error']);
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('❌ Error', $mensaje, 'error');
        });
    </script>";
} else if (isset($_GET['msg'])) { // El !empty($inventario) en tu ejemplo original era confuso aquí.
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
    <title>Inventario de Alimentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/inventario/css/estilosInventario.css">
    
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>    
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">
</head>

<body class="min-h-screen flex bg-gray-100">

    <div class="flex min-h-screen w-full">
        <?php include '../../public/assets/layout/sidebar.php'; ?>
    
        <main class="flex-1 p-6 overflow-y-auto transition-all duration-300 h-full" style="margin: auto;">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Inventario de Alimentos</h1>
                <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarAlimento">
                    Agregar Alimento
                </button>            
            </div>
            
            <div style="position: relative;">
                <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Buscar alimento..." title="Escribe un nombre" style="padding-left: 35px;">
                <i data-lucide="search" style="position: absolute; left: 8px; top: 40%; transform: translateY(-50%); width: 16px; height: 16px; color: gray;"></i>
            </div>

            <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
                <table id="myTable" class="min-w-full bg-white rounded-md shadow-sm overflow-hidden">
                    <thead class="bg-blue-700 text-white">
                        <tr>
                            <th class="py-3 px-4 text-center w-[200px]">Nombre</th>
                            <th class="py-3 px-4 text-center w-[120px]">Cantidad</th>
                            <th class="py-3 px-4 text-center w-[150px]">Unidad de Medida</th>
                            <th class="py-3 px-4 text-center w-[180px]">Fecha de Ingreso</th>
                            <th class="py-3 px-4 text-center w-[120px]">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700 divide-y divide-gray-10">
                        <?php if (!empty($inventario)): ?>
                            <?php foreach ($inventario as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['nombre']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['cantidad']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['unidad_medida']) ?></td>
                                    <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['fecha_ingreso']) ?></td>
                                    <td class="py-3 px-6 text-center align-middle">
                                        <div class="flex justify-center items-center gap-2">
                                            <i data-lucide="square-pen"
                                                class="text-blue-600 hover:text-blue-800 cursor-pointer w-4 h-4"
                                                data-toggle="modal" data-target="#modalEditarAlimento"
                                                data-id="<?= $item['id_alimento'] ?>"
                                                data-nombre="<?= htmlspecialchars($item['nombre']) ?>"
                                                data-cantidad="<?= htmlspecialchars($item['cantidad']) ?>"
                                                data-unidad_medida="<?= htmlspecialchars($item['unidad_medida']) ?>"
                                                data-fecha_ingreso="<?= htmlspecialchars($item['fecha_ingreso']) ?>">
                                            </i>
                                            <a href="<?= BASE_URL ?>/modules/inventario/controller.php?accion=eliminar&id=<?= $item['id_alimento'] ?>" 
                                                onclick="return confirm('¿Estás seguro que deseas eliminar este alimento del inventario?');">
                                                <i data-lucide="trash-2" class="text-red-600 hover:text-red-800 cursor-pointer w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center py-4 text-gray-500">No hay alimentos registrados en el inventario.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
            
            <div style="margin-top: 30px;">
                <!-- Botón que abre el modal -->
                <button class="btn btn-success" data-toggle="modalExcel" onclick="exportarTablaAExcel()" >
                    Exportar Excel
                </button>           
            </div>

        </main>
        
    </div>

    <?php include __DIR__ . '/layout/registrar_alimento.php'; ?>

    <?php include __DIR__ . '/layout/editar_alimento.php'; ?>
    
    <?php include '../../modules/auth/layout/mensajesModal.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        $(document).ready(function () {
            // Lógica para precargar datos en el modal de edición
            $('#modalEditarAlimento').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget); // Botón que disparó el modal
                var id = button.data('id');
                var nombre = button.data('nombre');
                var cantidad = button.data('cantidad');
                var unidad_medida = button.data('unidad_medida');
                var fecha_ingreso = button.data('fecha_ingreso');

                var modal = $(this);
                modal.find('#editar_id_alimento').val(id);
                modal.find('#editar_nombre').val(nombre);
                modal.find('#editar_cantidad').val(cantidad);
                modal.find('#editar_unidadMedida').val(unidad_medida);
                modal.find('#editar_fechaIngreso').val(fecha_ingreso);
            });

            // Limpia la URL después de mostrar el mensaje (similar a tu lógica actual)
            if(window.location.search.includes("msg=") || window.location.search.includes("error=")) {
                window.history.replaceState({}, document.title, window.location.pathname + "?accion=listar");
            }
        });

        // Función de búsqueda
        function myFunction() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("myTable");
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) { 
                let rowContainsFilter = false;
                const tds = tr[i].getElementsByTagName("td");
                for (j = 0; j < tds.length - 1; j++) { // Recorre todas las celdas menos la última (acciones)
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
  
  <script>
 function exportarTablaAExcel() {
    // Obtiene la tabla HTML
    var tabla = document.getElementById("myTable");

    // Crea la hoja de cálculo desde la tabla HTML
    var hoja = XLSX.utils.table_to_sheet(tabla, { raw: true });

    // Opcional: Remueve la columna de "Acciones" (última columna)
    const rango = XLSX.utils.decode_range(hoja['!ref']);
    for (let R = rango.s.r; R <= rango.e.r; ++R) {
        const celda = XLSX.utils.encode_cell({ r: R, c: rango.e.c });
        delete hoja[celda];
    }
    rango.e.c--; // Disminuye el número total de columnas por 1
    hoja['!ref'] = XLSX.utils.encode_range(rango);

    // Crea un libro de Excel
    var libro = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(libro, hoja, "Inventario");

    // Descarga el archivo Excel
    XLSX.writeFile(libro, "inventario_productos.xlsx", { bookType: "xlsx", type: "binary" });
 }
 </script>


 <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

</body>
</html>