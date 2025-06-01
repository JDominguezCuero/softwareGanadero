<div class="modal fade" id="modalEditarProducto" tabindex="-1" role="dialog" aria-labelledby="modalEditarProductoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarProductoLabel">Editar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/productos/controller.php?accion=editar" method="POST" id="formEditarProducto" enctype="multipart/form-data">
                    <input type="hidden" name="id_producto" id="editar_id_producto">
                    <input type="hidden" name="imagen_url_actual" id="editar_imagen_url_actual">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editar_nombre">Nombre del Producto:</label>
                                <input type="text" class="form-control" name="nombre" id="editar_nombre" placeholder="Nombre del Producto" required>
                            </div>
                            <div class="form-group">
                                <label for="editar_precio">Precio:</label>
                                <input type="number" class="form-control" name="precio" id="editar_precio" placeholder="Precio (ej. 99.99)" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="editar_stock">Cantidad:</label>
                                <input type="number" class="form-control" name="stock" id="editar_stock" placeholder="Cantidad en stock" min="0" step="1" required>
                            </div>
                            <div class="form-group">
                                <label for="editar_categoria_id">Categoría:</label>
                                <select class="form-control" name="categoria_id" id="editar_categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php
                                    if (isset($categorias) && is_array($categorias)) {
                                        foreach ($categorias as $categoria) {
                                            $selected = ''; 
                                            if (isset($item['categoria_id']) && $item['categoria_id'] == $categoria['id_categoria']) {
                                                $selected = 'selected';
                                            }
                                            echo '<option value="' . htmlspecialchars($categoria['id_categoria']) . '" ' . $selected . '>' . htmlspecialchars($categoria['nombre_categoria']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editar_descripcion">Descripción:</label>
                                <textarea class="form-control" name="descripcion" id="editar_descripcion" rows="5" placeholder="Descripción detallada del producto" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Imagen Actual:</label><br>
                                <img id="imagen_preview_editar" src="" alt="Imagen actual" style="max-width: 150px; height: auto; margin-bottom: 10px; display: block;">
                                <label for="editar_imagen">Cargar Nueva Imagen:</label>
                                <input type="file" class="form-control-file" name="imagen" id="editar_imagen" accept="image/*">
                                <small class="form-text text-muted">Deja en blanco para mantener la imagen actual. Solo archivos de imagen.</small>
                            </div>
                            <div class="form-group form-check mt-4">
                                <input type="checkbox" class="form-check-input" name="estado_oferta" id="editar_estado_oferta" value="1">
                                <label class="form-check-label" for="editar_estado_oferta">Producto en Oferta</label>
                            </div>
                            <div class="form-group" id="editar_precio_anterior_group" style="display: none;">
                                <label for="editar_precio_anterior">Precio Anterior (solo si hay oferta):</label>
                                <input type="number" class="form-control" name="precio_anterior" id="editar_precio_anterior" placeholder="Precio original" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formEditarProducto">Guardar Cambios</button>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editarEstadoOfertaCheckbox = document.getElementById('editar_estado_oferta');
        const editarPrecioAnteriorGroup = document.getElementById('editar_precio_anterior_group');
        const editarPrecioAnteriorInput = document.getElementById('editar_precio_anterior');

        const togglePrecioAnterior = () => {
            if (editarEstadoOfertaCheckbox.checked) {
                editarPrecioAnteriorGroup.style.display = 'block';
                editarPrecioAnteriorInput.setAttribute('required', 'required');
            } else {
                editarPrecioAnteriorGroup.style.display = 'none';
                editarPrecioAnteriorInput.removeAttribute('required');
                editarPrecioAnteriorInput.value = '';
            }
        };

        editarEstadoOfertaCheckbox.addEventListener('change', togglePrecioAnterior);

        $('#modalEditarProducto').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nombre = button.data('nombre');
            var descripcion = button.data('descripcion');
            var precio = button.data('precio');
            var stock = button.data('stock');
            var imagen_url = button.data('imagen_url'); // La URL actual de la imagen
            var categoria_id = button.data('categoria_id');
            var estado_oferta = button.data('estado_oferta');
            var precio_anterior = button.data('precio_anterior');

            var modal = $(this);
            modal.find('#editar_id_producto').val(id);
            modal.find('#editar_nombre').val(nombre);
            modal.find('#editar_descripcion').val(descripcion);
            modal.find('#editar_precio').val(precio);
            modal.find('#editar_stock').val(stock);
            // Guarda la URL actual en un campo oculto
            modal.find('#editar_imagen_url_actual').val(imagen_url);

            // Muestra la imagen actual en el preview
            var imgPreview = modal.find('#imagen_preview_editar');
            if (imagen_url) {
                imgPreview.attr('src', imagen_url).show();
            } else {
                imgPreview.hide();
            }

            modal.find('#editar_categoria_id').val(categoria_id);

            if (estado_oferta == 1) {
                modal.find('#editar_estado_oferta').prop('checked', true);
                modal.find('#editar_precio_anterior').val(precio_anterior);
            } else {
                modal.find('#editar_estado_oferta').prop('checked', false);
            }

            togglePrecioAnterior();
        });
    });
</script>