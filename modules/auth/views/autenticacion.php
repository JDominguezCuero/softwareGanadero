<?php
require_once(__DIR__ . '../../../../config/config.php');

// registro
if (isset($_GET['success']) && $_GET['success'] == 2) {
    $mensaje = "Error desconocido, contactese con el administrador";
    if (isset($_GET['error']) && $_GET['error']) {
        $mensaje = $_GET['error'];
    }    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('❌ Error al registrar', '$mensaje', 'error');
        });
    </script>";
    
}else if(isset($_GET['success']) && $_GET['success'] == 1){
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('✅ Registro Exitoso', 'Usuario registrado correctamente', 'success');
        });
    </script>";
}

// inicio
if (isset($_GET['login']) && $_GET['login'] == 1) {
    $usuario = isset($_GET['usuario']) ? addslashes($_GET['usuario']) : 'Desconocido';

    if (isset($_GET['error']) && $_GET['error']) {
        $mensaje = $_GET['error'];
    }    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('❌ Acceso Denegado', '$mensaje', 'error');
        });
    </script>";
}

// restablecimiento
if (isset($_GET['enviado']) && $_GET['enviado'] == 2) {
    $mensaje = "Error desconocido, contactese con el administrador";

    if (isset($_GET['error']) && $_GET['error']) {
        $mensaje = $_GET['error'];
    }    
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('❌ Acceso Denegado', '$mensaje', 'error');
        });
    </script>";
}else if(isset($_GET['enviado']) && $_GET['enviado'] == 1){
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showModal('✅Envio Exitoso', 'Verifica tu correo electronico.', 'success');
        });
    </script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login y Register - Jose Domínguez Cuero</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="stylesheet" href="../../../public/assets/css/estilos.css">
</head>
<body class="contenedorBody">
    <!-- Layout Sidebar -->
    
        <main>

            <div class="contenedor__todo">
                <div class="caja__trasera">
                    <div class="caja__trasera-login">
                        <h3>¿Ya tienes una cuenta?</h3>
                        <p>Inicia sesión para entrar en la página</p>
                        <button id="btn__iniciar-sesion">Iniciar Sesión</button>
                    </div>
                    <div class="caja__trasera-register">
                        <h3>¿Aún no tienes una cuenta?</h3>
                        <p>Regístrate para que puedas iniciar sesión</p>
                        <button id="btn__registrarse">Regístrarse</button>
                    </div>
                </div>

                <!--Formulario de Login y registro-->
                <div class="contenedor__login-register">
                    <!--Login-->
                    <form action="<?= BASE_URL ?>/modules/auth/controller.php?accion=login" method="POST" class="formulario__login">
                        <h2>Iniciar Sesión</h2>
                        <input type="text" name="correoElectronicoLogin" placeholder="Correo Electronico" required>
                        <input type="password" name="contrasenaLogin" placeholder="Contraseña" required>
                        <button type="submit">Entrar</button>

                        <div class="login-options">
                            <br>
                            <a href="../../../public/index_controller.php" style="color: green;" class="btn-small">Volver</a>
                            <br>
                            <a  href="#" data-toggle="modal" data-target="#modalRestablecer" style="color: green;" class="forgot-password">¿Olvidaste tu contraseña?</a>
                        </div>
                    </form>

                    <!--Register-->
                    <form action="<?= BASE_URL ?>/modules/auth/controller.php?accion=registro" method="POST" class="formulario__register">
                        <h2>Regístrarse</h2>
                        <input type="text" name="nombreCompleto" placeholder="Nombre completo" required>
                        <input type="text" name="correoElectronico" placeholder="Correo Electronico" required>
                        <input type="text" name="usuario" placeholder="Usuario" required>
                        <input type="password" name="contrasena" placeholder="Contraseña" required>
                        <button type="submit">Regístrarse</button>

                        <div class="login-options">
                            <br>
                            <a href="../../../public/index_controller.php" style="color: green;" class="btn-small">Volver</a>
                        </div>
                    </form>
                </div>
            </div>

        </main>     
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

        <?php include '../layout/mensajesModal.php'; ?>
        <?php include '../layout/restablecer.php'; ?>
        <script src="../../../public/assets/js/script.js"></script>
</body>
</html>