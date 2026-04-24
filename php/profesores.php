<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

$action = $_GET['action'] ?? '';

if ($action === 'listar') {
    $result = $conexion->query("SELECT * FROM profesores ORDER BY created_at DESC");
    $profesores = [];
    while ($row = $result->fetch_assoc()) {
        $profesores[] = $row;
    }
    echo json_encode($profesores);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$action = $body['action'] ?? '';

if ($action === 'crear') {
    $nombre = $conexion->real_escape_string($body['nombre']);
    $correo = $conexion->real_escape_string($body['correo']);
    $especialidad = $conexion->real_escape_string($body['especialidad']);

    $check = $conexion->query("SELECT id FROM profesores WHERE correo = '$correo'");
    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "mensaje" => "El correo ya está registrado"]);
        exit;
    }

    $sql = "INSERT INTO profesores (nombre, correo, especialidad) VALUES ('$nombre', '$correo', '$especialidad')";
    if ($conexion->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "mensaje" => "Error al guardar"]);
    }
}

if ($action === 'editar') {
    $id = intval($body['id']);
    $nombre = $conexion->real_escape_string($body['nombre']);
    $correo = $conexion->real_escape_string($body['correo']);
    $especialidad = $conexion->real_escape_string($body['especialidad']);

    $sql = "UPDATE profesores SET nombre='$nombre', correo='$correo', especialidad='$especialidad' WHERE id=$id";
    if ($conexion->query($sql)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "mensaje" => "Error al editar"]);
    }
}

if ($action === 'eliminar') {
    $id = intval($body['id']);
    if ($conexion->query("DELETE FROM profesores WHERE id=$id")) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "mensaje" => "Error al eliminar"]);
    }
}

$conexion->close();
?>