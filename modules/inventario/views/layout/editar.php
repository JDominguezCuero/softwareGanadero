<div class="modal fade" id="modalEditar" tabindex="-1" role="dialog" aria-labelledby="modalEditarLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarLabel">Editar Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/inventario/controller.php?accion=editar" method="POST" id="formEditar">
                    <!-- ID oculto -->
                   <input type="hidden" name="id_producto" id="editar_id_producto">

                    <div class="form-group">
                        <input type="text" class="form-control" name="producto" id="editar_producto" placeholder="Producto" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" name="cantidad" id="editar_cantidad" placeholder="Cantidad" min="0" step="1" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="descripcion" id="editar_descripcion" placeholder="Descripción" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" name="precioUnitario" id="editar_precioUnitario" placeholder="Precio Unitario" step="0.01" required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formEditar">Guardar Cambios</button>
            </div>

        </div>
    </div>
</div>