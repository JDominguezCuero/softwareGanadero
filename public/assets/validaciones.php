<?php
// validaciones.php

function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validarPassword($password) {
    $regex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{5,}$/";
    return preg_match($regex, $password);
}

function camposNoVacios(array $campos) {
    foreach ($campos as $campo) {
        if (empty(trim($campo))) {
            return false;
        }
    }
    return true;
}

function validarSoloLetras($texto) {
    return preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u", $texto);
}

function validarSoloNumeros($numero) {
    return preg_match("/^\d+$/", $numero);
}

function validarLongitud($texto, $min = 0, $max = PHP_INT_MAX) {
    $len = mb_strlen($texto);
    return $len >= $min && $len <= $max;
}

function validarTelefono($telefono) {
    // Ejemplo simple: solo números y entre 7 y 15 dígitos
    return preg_match("/^\d{7,15}$/", $telefono);
}

function validarURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function validarFecha($fecha, $formato = 'Y-m-d') {
    $d = DateTime::createFromFormat($formato, $fecha);
    return $d && $d->format($formato) === $fecha;
}

function validarCoincidencia($valor1, $valor2) {
    return $valor1 === $valor2;
}

?>