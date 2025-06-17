<!-- Modal Usuario -->
<div class="modal fade" id="modalAgregarUsuario" tabindex="-1" role="dialog" aria-labelledby="modalAgregarUsuarioLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarUsuarioLabel">Agregar Nuevo Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="<?= BASE_URL ?>/modules/auth/controller.php?accion=agregar" method="POST" id="formRegistroUsuario" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombreCompleto">Nombre Completo:</label>
                                <input type="text" class="form-control" name="nombreCompleto" id="nombreCompleto" placeholder="Nombre completo" required>
                            </div>
                            <div class="form-group">
                                <label for="nombre_usuario">Nombre de Usuario:</label>
                                <input type="text" class="form-control" name="nombre_usuario" id="nombre_usuario" placeholder="Usuario" required>
                            </div>
                            <div class="form-group">
                                <label for="correo_usuario">Correo Electrónico:</label>
                                <input type="email" class="form-control" name="correo_usuario" id="correo_usuario" placeholder="correo@ejemplo.com" required>
                            </div>
                            <div class="form-group">
                                <label for="telefono_usuario">Teléfono:</label>
                                <input type="tel" class="form-control" name="telefono_usuario" id="telefono_usuario" placeholder="Número de teléfono" required>
                            </div>
                            <div class="form-group">
                                <label for="rol_id">Rol:</label>
                                <select class="form-control" name="rol_id" id="rol_id" required>
                                    <option value="">Selecciona un rol</option>
                                    <?php
                                    if (isset($roles) && is_array($roles)) {
                                        foreach ($roles as $rol) {
                                            echo '<option value="' . htmlspecialchars($rol['id_rol']) . '">' . htmlspecialchars($rol['nombre_rol']) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="direccion_usuario">Dirección:</label>
                                <input type="text" class="form-control" name="direccion_usuario" id="direccion_usuario" placeholder="Dirección" required>
                            </div>
                            <div class="form-group">
                                <label for="estado">Estado:</label>
                                <select class="form-control" name="estado" id="estado" required>
                                    <option value="">Selecciona estado</option>
                                    <option value="Activo">Activo</option>
                                    <option value="Inactivo">Inactivo</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contrasena">Contraseña:</label>
                                <input type="password" class="form-control" name="contrasena" id="contrasena" placeholder="Contraseña" required>
                            </div>
                            <div class="form-group">
                                <label for="imagen">Imagen de Perfil:</label>
                                <input type="file" class="form-control-file" name="imagen" id="imagen" accept="image/*">
                                <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF.</small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-success" form="formRegistroUsuario">Agregar Usuario</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('formRegistroUsuario');

        form.addEventListener('submit', function (e) {
            const nombre = document.getElementById('nombreCompleto').value.trim();
            const usuario = document.getElementById('nombre_usuario').value.trim();
            const correo = document.getElementById('correo_usuario').value.trim();
            const telefono = document.getElementById('telefono_usuario').value.trim();
            const direccion = document.getElementById('direccion_usuario').value.trim();
            const estado = document.getElementById('estado').value;
            const contrasena = document.getElementById('contrasena').value;
            const rol = document.getElementById('rol_id').value;

            let errores = [];

            if (nombre === '') errores.push('El nombre completo es obligatorio.');
            if (usuario === '') errores.push('El nombre de usuario es obligatorio.');

            if (correo === '') {
                errores.push('El correo electrónico es obligatorio.');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                errores.push('El correo electrónico no tiene un formato válido.');
            }

            if (telefono === '') {
                errores.push('El teléfono es obligatorio.');
            } else if (!/^\d{10,}$/.test(telefono)) {
                errores.push('El teléfono debe tener al menos 10 dígitos numéricos.');
            }

            if (direccion === '') errores.push('La dirección es obligatoria.');

            if (estado !== 'Activo' && estado !== 'Inactivo') {
                errores.push('Selecciona un estado válido.');
            }

            if (contrasena !== '' && !validarPassword(contrasena)) {
                errores.push('La contraseña debe tener al menos 5 caracteres, incluyendo una mayúscula, una minúscula, un número y un carácter especial.');
            }

            if (rol === '') errores.push('Selecciona un rol válido.');

            if (errores.length > 0) {
                e.preventDefault(); // Evita el envío del formulario
                alert('Corrige los siguientes errores:\n\n' + errores.join('\n'));
            }

            function validarPassword(password) {
                const regex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{5,}$/;
                return regex.test(password);
            }
        });
    });
</script>

