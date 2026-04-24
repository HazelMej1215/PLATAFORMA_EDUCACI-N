<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

$body = json_decode(file_get_contents('php://input'), true);
$nombre = $conexion->real_escape_string($body['nombre'] ?? '');
$correo = $conexion->real_escape_string($body['correo'] ?? '');
$contrasena = $body['contrasena'] ?? '';

if (!$nombre || !$correo || !$contrasena) {
    echo json_encode(["success" => false, "mensaje" => "Todos los campos son obligatorios"]);
    exit;
}

$check = $conexion->query("SELECT id FROM usuarios WHERE correo = '$correo'");
if ($check->num_rows > 0) {
    echo json_encode(["success" => false, "mensaje" => "El correo ya está registrado"]);
    exit;
}

$hash = password_hash($contrasena, PASSWORD_DEFAULT);

if ($conexion->query("INSERT INTO usuarios (nombre_completo, correo, contrasena) VALUES ('$nombre', '$correo', '$hash')")) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "mensaje" => "Error al crear la cuenta"]);
}

$conexion->close();
?>