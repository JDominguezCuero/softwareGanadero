<div class="modal fade" id="modalEditarAlimento" tabindex="-1" role="dialog" aria-labelledby="modalEditarAlimentoLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarAlimentoLabel">Editar Alimento del Inventario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/inventario/controller.php?accion=editar" method="POST" id="formEditarAlimento">
                    <input type="hidden" name="id_alimento" id="editar_id_alimento">

                    <div class="form-group">
                        <label for="editar_nombre">Nombre del Alimento:</label>
                        <input type="text" class="form-control" name="nombre" id="editar_nombre" placeholder="Nombre del Alimento" required>
                    </div>
                    <div class="form-group">
                        <label for="editar_cantidad">Cantidad:</label>
                        <input type="number" class="form-control" name="cantidad" id="editar_cantidad" placeholder="Cantidad" min="0" step="1" required>
                    </div>
                    <div class="form-group">
                        <label for="editar_unidadMedida">Unidad de Medida:</label>
                        <input type="text" class="form-control" name="unidad_medida" id="editar_unidadMedida" placeholder="ej. kg, litros, unidades" required>
                    </div>
                    <div class="form-group">
                        <label for="editar_fechaIngreso">Fecha de Ingreso:</label>
                        <input type="date" class="form-control" name="fecha_ingreso" id="editar_fechaIngreso" required>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formEditarAlimento">Guardar Cambios</button>
            </div>

        </div>
    </div>
</div>