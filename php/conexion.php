<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "plataforma_educacion";

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die(json_encode([
        "error" => "Error de conexión: " . $conexion->connect_error
    ]));
}

$conexion->set_charset("utf8");
?>