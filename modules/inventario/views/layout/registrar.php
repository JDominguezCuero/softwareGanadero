<div class="modal fade" id="miModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Registro de Inventario</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/inventario/controller.php?accion=agregar" method="POST" id="formRegistro">
                    <div class="form-group">
                        <input type="text" class="form-control" name="producto" placeholder="Producto" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" name="cantidad" placeholder="Cantidad" min="0" step="1" required>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" name="descripcion" placeholder="Descripción" required>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-control" name="precioUnitario" placeholder="Precio Unitario" step="0.01" required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formRegistro">Agregar Producto</button>
            </div>
        </div>
    </div>
</div>