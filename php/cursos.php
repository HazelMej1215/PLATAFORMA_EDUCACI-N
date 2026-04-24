<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'conexion.php';

$action = $_GET['action'] ?? '';

if ($action === 'listar') {
    $result = $conexion->query("
        SELECT c.*, p.nombre as profesor,
        COUNT(DISTINCT v.id) as total_videos,
        ROUND(AVG(cal.estrellas), 1) as calificacion
        FROM cursos c
        JOIN profesores p ON c.profesor_id = p.id
        LEFT JOIN videos v ON c.id = v.curso_id
        LEFT JOIN calificaciones cal ON c.id = cal.curso_id
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $cursos = [];
    while ($row = $result->fetch_assoc()) $cursos[] = $row;
    echo json_encode($cursos);
    exit;
}

if ($action === 'videos') {
    $curso_id = intval($_GET['curso_id']);
    $result = $conexion->query("SELECT * FROM videos WHERE curso_id = $curso_id ORDER BY secuencia ASC");
    $videos = [];
    while ($row = $result->fetch_assoc()) $videos[] = $row;
    echo json_encode($videos);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$action = $body['action'] ?? '';

if ($action === 'crear') {
    $nombre = $conexion->real_escape_string($body['nombre']);
    $descripcion = $conexion->real_escape_string($body['descripcion'] ?? '');
    $profesor_id = intval($body['profesor_id']);
    if ($conexion->query("INSERT INTO cursos (nombre, descripcion, profesor_id) VALUES ('$nombre', '$descripcion', $profesor_id)"))
        echo json_encode(["success" => true]);
    else echo json_encode(["success" => false, "mensaje" => "Error al guardar"]);
}

if ($action === 'editar') {
    $id = intval($body['id']);
    $nombre = $conexion->real_escape_string($body['nombre']);
    $descripcion = $conexion->real_escape_string($body['descripcion'] ?? '');
    $profesor_id = intval($body['profesor_id']);
    if ($conexion->query("UPDATE cursos SET nombre='$nombre', descripcion='$descripcion', profesor_id=$profesor_id WHERE id=$id"))
        echo json_encode(["success" => true]);
    else echo json_encode(["success" => false, "mensaje" => "Error al editar"]);
}

if ($action === 'eliminar') {
    $id = intval($body['id']);
    if ($conexion->query("DELETE FROM cursos WHERE id=$id"))
        echo json_encode(["success" => true]);
    else echo json_encode(["success" => false]);
}

if ($action === 'agregar_video') {
    $curso_id = intval($body['curso_id']);
    $titulo = $conexion->real_escape_string($body['titulo']);
    $url = $conexion->real_escape_string($body['url']);
    $seq = $conexion->query("SELECT COUNT(*) as total FROM videos WHERE curso_id = $curso_id")->fetch_assoc()['total'] + 1;
    if ($conexion->query("INSERT INTO videos (curso_id, titulo, url, secuencia) VALUES ($curso_id, '$titulo', '$url', $seq)"))
        echo json_encode(["success" => true]);
    else echo json_encode(["success" => false]);
}

if ($action === 'eliminar_video') {
    $id = intval($body['id']);
    if ($conexion->query("DELETE FROM videos WHERE id=$id"))
        echo json_encode(["success" => true]);
    else echo json_encode(["success" => false]);
}

$conexion->close();
?>