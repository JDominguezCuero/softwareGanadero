<div class="modal fade" id="modalAgregarProducto" tabindex="-1" role="dialog" aria-labelledby="modalAgregarProductoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarProductoLabel">Agregar Nuevo Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/productos/controller.php?accion=agregar" method="POST" id="formRegistroProducto" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre">Nombre del Producto:</label>
                                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre del Producto" required>
                            </div>
                            <div class="form-group">
                                <label for="precio">Precio:</label>
                                <input type="number" class="form-control" name="precio" id="precio" placeholder="Precio (ej. 99.99)" min="0" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="stock">Cantidad:</label>
                                <input type="number" class="form-control" name="stock" id="stock" placeholder="Cantidad en stock" min="0" step="1" required>
                            </div>
                            <div class="form-group">
                                <label for="categoria_id">Categoría:</label>
                                <select class="form-control" name="categoria_id" id="categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    <?php
                                    // Asegúrate de que $categorias esté definida (debe venir del controller.php)
                                    if (isset($categorias) && is_array($categorias)) {
                                        foreach ($categorias as $categoria) {
                                            echo '<option value="' . htmlspecialchars($categoria['id_categoria']) . '">' . htmlspecialchars($categoria['nombre_categoria']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <textarea class="form-control" name="descripcion" id="descripcion" rows="5" placeholder="Descripción detallada del producto" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="imagen">Cargar Imagen:</label>
                                <input type="file" class="form-control-file" name="imagen" id="imagen" accept="image/*">
                                <small class="form-text text-muted">Solo archivos de imagen (JPG, PNG, GIF, etc.).</small>
                            </div>
                            <div class="form-group form-check mt-4">
                                <input type="checkbox" class="form-check-input" name="estado_oferta" id="estado_oferta" value="1">
                                <label class="form-check-label" for="estado_oferta">Producto en Oferta</label>
                            </div>
                            <div class="form-group" id="precio_anterior_group" style="display: none;">
                                <label for="precio_anterior">Precio Anterior (solo si hay oferta):</label>
                                <input type="number" class="form-control" name="precio_anterior" id="precio_anterior" placeholder="Precio original" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formRegistroProducto">Agregar Producto</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const estadoOfertaCheckbox = document.getElementById('estado_oferta');
        const precioAnteriorGroup = document.getElementById('precio_anterior_group');
        const precioAnteriorInput = document.getElementById('precio_anterior');

        const togglePrecioAnterior = () => {
            if (estadoOfertaCheckbox.checked) {
                precioAnteriorGroup.style.display = 'block';
                precioAnteriorInput.setAttribute('required', 'required');
            } else {
                precioAnteriorGroup.style.display = 'none';
                precioAnteriorInput.removeAttribute('required');
                precioAnteriorInput.value = '';
            }
        };

        estadoOfertaCheckbox.addEventListener('change', togglePrecioAnterior);
        togglePrecioAnterior(); 
    });
</script>