<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

$action = $_GET['action'] ?? '';

if ($action === 'listar') {
    $result = $conexion->query("SELECT id, nombre, correo, created_at FROM administradores ORDER BY created_at DESC");
    $admins = [];
    while ($row = $result->fetch_assoc()) $admins[] = $row;
    echo json_encode($admins);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$action = $body['action'] ?? '';

if ($action === 'crear') {
    $nombre = $conexion->real_escape_string($body['nombre']);
    $correo = $conexion->real_escape_string($body['correo']);
    $contrasena = password_hash($body['contrasena'], PASSWORD_DEFAULT);

    $check = $conexion->query("SELECT id FROM administradores WHERE correo = '$correo'");
    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "mensaje" => "El correo ya está registrado"]);
        exit;
    }

    if ($conexion->query("INSERT INTO administradores (nombre, correo, contrasena) VALUES ('$nombre', '$correo', '$contrasena')"))
        echo json_encode(["success" => true]);
    else
        echo json_encode(["success" => false, "mensaje" => "Error al crear"]);
}

if ($action === 'eliminar') {
    $id = intval($body['id']);
    if ($conexion->query("DELETE FROM administradores WHERE id=$id"))
        echo json_encode(["success" => true]);
    else
        echo json_encode(["success" => false]);
}

$conexion->close();
?>