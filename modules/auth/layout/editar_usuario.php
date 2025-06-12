<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarUsuarioLabel">Editar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/usuarios/controller.php?accion=editar" method="POST" id="formEditarUsuario" enctype="multipart/form-data">
                    <input type="hidden" name="id_usuario" id="editar_id_usuario">
                    <input type="hidden" name="imagen_url_actual" id="editar_imagen_url_actual">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editar_nombreCompleto">Nombre Completo:</label>
                                <input type="text" class="form-control" name="nombreCompleto" id="editar_nombreCompleto" placeholder="Nombre completo del usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="editar_nombre_usuario">Nombre de Usuario:</label>
                                <input type="text" class="form-control" name="nombre_usuario" id="editar_nombre_usuario" placeholder="Nombre de usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="editar_correo_usuario">Correo Electrónico:</label>
                                <input type="email" class="form-control" name="correo_usuario" id="editar_correo_usuario" placeholder="Correo electrónico" required>
                            </div>
                            <div class="form-group">
                                <label for="editar_contrasena">Cambiar Contraseña (opcional):</label>
                                <input type="password" class="form-control" name="contrasena" id="editar_contrasena" placeholder="Nueva contraseña (dejar en blanco para mantener la actual)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editar_direccion_usuario">Dirección:</label>
                                <input type="text" class="form-control" name="direccion_usuario" id="editar_direccion_usuario" placeholder="Dirección del usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="editar_telefono_usuario">Teléfono:</label>
                                <input type="text" class="form-control" name="telefono_usuario" id="editar_telefono_usuario" placeholder="Teléfono" required>
                            </div>
                            <div class="form-group">
                                <label>Imagen Actual:</label><br>
                                <img id="imagen_preview_usuario" src="" alt="Imagen actual" style="max-width: 150px; height: auto; margin-bottom: 10px; display: block;">
                                <label for="editar_imagen_usuario">Cargar Nueva Imagen:</label>
                                <input type="file" class="form-control-file" name="imagen" id="editar_imagen_usuario" accept="image/*">
                                <small class="form-text text-muted">Deja en blanco para mantener la imagen actual.</small>
                            </div>
                            <div class="form-group">
                                <label for="editar_estado_usuario">Estado:</label>
                                <select class="form-control" name="estado" id="editar_estado_usuario" required>
                                    <option value="0">Selecciona un estado</option>
                                    <option value="1">Activo</option>
                                    <option value="2">Inactivo</option>
                                </select>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formEditarUsuario">Guardar Cambios</button>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#modalEditarUsuario').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var nombre = button.data('nombre');
            var nombreUsuario = button.data('nombreusuario');
            var correo = button.data('correo');
            var direccion = button.data('direccion');
            var telefono = button.data('telefono');
            var estado = button.data('estado');
            var imagen_url = button.data('imagen_url');

            var modal = $(this);
            modal.find('#editar_id_usuario').val(id);
            modal.find('#editar_nombreCompleto').val(nombre);
            modal.find('#editar_nombre_usuario').val(nombreUsuario);
            modal.find('#editar_correo_usuario').val(correo);
            modal.find('#editar_direccion_usuario').val(direccion);
            modal.find('#editar_telefono_usuario').val(telefono);
            modal.find('#editar_estado_usuario').val(estado);
            modal.find('#editar_imagen_url_actual').val(imagen_url);

            var imgPreview = modal.find('#imagen_preview_usuario');
            if (imagen_url) {
                imgPreview.attr('src', imagen_url).show();
            } else {
                imgPreview.hide();
            }
        });
    });
</script>
