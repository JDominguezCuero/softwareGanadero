<div class="modal fade" id="modalAgregarAlimento" tabindex="-1" role="dialog" aria-labelledby="modalAgregarAlimentoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarAlimentoLabel">Agregar Nuevo Alimento al Inventario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/inventario/controller.php?accion=agregar" method="POST" id="formRegistroAlimento">
                    <div class="form-group">
                        <label for="nombre">Nombre del Alimento:</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre del Alimento" required>
                    </div>
                    <div class="form-group">
                        <label for="cantidad">Cantidad:</label>
                        <input type="number" class="form-control" name="cantidad" id="cantidad" placeholder="Cantidad" min="0" step="1" required>
                    </div>
                    <div class="form-group">
                        <label for="unidad_medida">Unidad de Medida:</label>
                        <input type="text" class="form-control" name="unidad_medida" id="unidad_medida" placeholder="ej. kg, litros, unidades" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_ingreso">Fecha de Ingreso:</label>
                        <input type="date" class="form-control" name="fecha_ingreso" id="fecha_ingreso" required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formRegistroAlimento">Agregar Alimento</button>
            </div>
        </div>
    </div>
</div>