<?php
// products/views/productos.php
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
                    <h1 class="text-2xl font-bold text-gray-800">Gestión de Productos</h1>
                    <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarProducto">
                        Agregar Nuevo Producto
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
                            <th class="py-3 px-4 text-center w-[80px]">Imagen</th>
                            <th class="py-3 px-4 text-center w-[150px]">Nombre</th>
                            <th class="py-3 px-4 text-center w-[100px]">Precio</th>
                            <th class="py-3 px-4 text-center w-[100px]">Cantidad</th>
                            <th class="py-3 px-4 text-center w-[110px]">Categoría</th>
                            <th class="py-3 px-4 text-center w-[100px]">En Oferta</th>
                            <th class="py-3 px-4 text-center w-[120px]">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-2 px-4 text-center">
                                    <?php if (!empty($item['imagen_url'])): ?>
                                    <img src="<?= htmlspecialchars($item['imagen_url']) ?>" alt="Producto" class="product-thumbnail">
                                    <?php else: ?>
                                    N/A
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['nombre_producto']) ?></td>
                                <td class="py-2 px-4 text-center">$<?= number_format($item['precio_unitario'], 2) ?></td>
                                <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['cantidad']) ?></td>
                                <td class="py-2 px-4 text-center"><?= htmlspecialchars($item['nombre_categoria'] ?? 'Sin Categoría') ?></td>
                                <td class="py-2 px-4 text-center"><?= ($item['estado_oferta'] == 1) ? 'Sí' : 'No' ?></td>
                                <td class="py-3 px-6 text-center align-middle">
                                    <div class="flex justify-center items-center gap-2">
                                        <i data-lucide="square-pen"
                                        class="text-blue-600 hover:text-blue-800 cursor-pointer w-4 h-4"
                                        style="width:16px; height:16px;"
                                        data-toggle="modal" data-target="#modalEditarProducto"
                                        data-id="<?= $item['id_producto'] ?>"
                                        data-nombre="<?= htmlspecialchars($item['nombre_producto']) ?>"
                                        data-descripcion="<?= htmlspecialchars($item['descripcion_producto']) ?>"
                                        data-precio="<?= htmlspecialchars($item['precio_unitario']) ?>"
                                        data-stock="<?= htmlspecialchars($item['cantidad']) ?>"
                                        data-imagen_url="<?= htmlspecialchars($item['imagen_url']) ?>"
                                        data-categoria_id="<?= htmlspecialchars($item['categoria_id']) ?>"
                                        data-estado_oferta="<?= htmlspecialchars($item['estado_oferta']) ?>"
                                        data-precio_anterior="<?= htmlspecialchars($item['precio_anterior']) ?>">
                                        </i>
                                        <span class="mx-1 text-gray-400">|</span>
                                        <a href="<?= BASE_URL ?>/modules/productos/controller.php?accion=eliminar&id=<?= $item['id_producto'] ?>"
                                        onclick="return confirm('¿Estás seguro que deseas eliminar este producto?');">
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
                                <td colspan="7" class="text-center py-4 text-gray-500">No hay productos registrados.</td>
                            </tr>
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

            </div>
        </main>        
    </div>

    <?php include __DIR__ . '/layout/registrar_producto.php'; ?>
    <?php include __DIR__ . '/layout/editar_producto.php'; ?>
    
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