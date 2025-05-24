<?php
$token = $_GET['token'] ?? '';
$email = $_GET['email'] ?? '';

require_once(__DIR__ . '../../../../config/config.php');

if (isset($_GET['success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($_GET['success'] == 2): ?>
                showModal('❌ Error al establecer', "<?= htmlspecialchars($_GET['error'] ?? 'Error desconocido, contactese con el administrador') ?>", 'error');
            <?php elseif ($_GET['success'] == 1): ?>
                showModal('✅ Restablecimiento Exitoso', 'Vuelve al login y accede con tus credenciales.', 'success');
            <?php endif; ?>
        });
    </script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/modules/auth/css/styleAuth.css">

    <title>Restablecer contraseña | PROGAN</title>
</head>
<body>
    <div class="container">
        <h2>Restablecer contraseña</h2>
        <form action="<?= BASE_URL ?>/modules/auth/controller.php?accion=restablecer" method="POST" autocomplete="off">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">

            <label for="contrasenaNueva">Nueva contraseña:</label>
            <input type="password" id="contrasenaNueva" name="contrasenaNueva" required minlength="8" placeholder="Mínimo 8 caracteres" autocomplete="new-password">

            <label for="confirmarContrasena">Confirmar contraseña:</label>
            <input type="password" id="confirmarContrasena" name="confirmarContrasena" required minlength="8" placeholder="Confirma tu nueva contraseña" autocomplete="new-password">

            <button type="submit">Restablecer contraseña</button>

            <div class="login-options">
                <br>
                <a href="<?= BASE_URL ?>/modules/auth/views/autenticacion.php" style="color: green;" class="btn-small">Volver</a>
            </div>
        </form>
        <p class="note">Recuerda usar una contraseña segura y que puedas recordar.</p>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <?php include __DIR__ . '/../layout/mensajesModal.php'; ?>

</body>
</html>
