<div class="modal fade" id="modalRestablecer" tabindex="-1" role="dialog" aria-labelledby="modalRestablecerLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <!-- Encabezado del modal -->
            <div class="modal-header">
                <h5 class="modal-title" id="modalRestablecerLabel">Restablecer Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Cuerpo del modal -->
            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/auth/controller.php?accion=enviarEnlaceRestablecimiento" method="POST" id="formRestablecer">
                    <div class="form-group">
                        <label for="email">Correo electrónico:</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Ingresa tu correo" required>
                    </div>
                </form>
            </div>

            <!-- Pie del modal -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success" form="formRestablecer">Enviar enlace</button>
            </div>

        </div>
    </div>
</div>
