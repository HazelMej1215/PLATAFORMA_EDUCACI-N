<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once 'conexion.php';

$input = file_get_contents('php://input');
$body = json_decode($input, true);

if (!$body) {
    echo json_encode(["success" => false, "mensaje" => "Sin datos recibidos"]);
    exit;
}

$correo = $conexion->real_escape_string($body['correo'] ?? '');
$contrasena = $body['contrasena'] ?? '';

// Primero buscar en administradores
$result = $conexion->query("SELECT * FROM administradores WHERE correo = '$correo'");

if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    if (password_verify($contrasena, $admin['contrasena'])) {
        echo json_encode([
            "success" => true,
            "rol" => "admin",
            "admin" => [
                "id" => $admin['id'],
                "nombre" => $admin['nombre'],
                "correo" => $admin['correo']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "mensaje" => "Contraseña incorrecta"]);
    }
    exit;
}

// Luego buscar en usuarios (clientes)
$result = $conexion->query("SELECT * FROM usuarios WHERE correo = '$correo'");

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    if (password_verify($contrasena, $usuario['contrasena'])) {
        echo json_encode([
            "success" => true,
            "rol" => "cliente",
            "usuario" => [
                "id" => $usuario['id'],
                "nombre" => $usuario['nombre_completo'],
                "correo" => $usuario['correo']
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "mensaje" => "Contraseña incorrecta"]);
    }
    exit;
}

echo json_encode(["success" => false, "mensaje" => "Correo no encontrado"]);
$conexion->close();
?>