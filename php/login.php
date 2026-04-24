<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

$body = json_decode(file_get_contents('php://input'), true);
$correo = $conexion->real_escape_string($body['correo'] ?? '');
$contrasena = $body['contrasena'] ?? '';

$result = $conexion->query("SELECT * FROM administradores WHERE correo = '$correo'");

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "mensaje" => "Correo o contraseña incorrectos"]);
    exit;
}

$admin = $result->fetch_assoc();

if (password_verify($contrasena, $admin['contrasena'])) {
    echo json_encode([
        "success" => true,
        "admin" => [
            "id" => $admin['id'],
            "nombre" => $admin['nombre'],
            "correo" => $admin['correo']
        ]
    ]);
} else {
    echo json_encode(["success" => false, "mensaje" => "Correo o contraseña incorrectos"]);
}

$conexion->close();
?>