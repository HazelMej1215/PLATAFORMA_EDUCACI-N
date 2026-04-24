<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'conexion.php';

// Total profesores
$profesores = $conexion->query("SELECT COUNT(*) as total FROM profesores")->fetch_assoc()['total'];

// Total cursos
$cursos = $conexion->query("SELECT COUNT(*) as total FROM cursos")->fetch_assoc()['total'];

// Total usuarios
$usuarios = $conexion->query("SELECT COUNT(*) as total FROM usuarios")->fetch_assoc()['total'];

// Promedio calificaciones
$promedio = $conexion->query("SELECT ROUND(AVG(estrellas), 1) as promedio FROM calificaciones")->fetch_assoc()['promedio'];

// Cursos más populares
$cursos_populares = [];
$result = $conexion->query("
    SELECT c.nombre, p.nombre as profesor,
    ROUND(AVG(cal.estrellas), 1) as calificacion,
    COUNT(DISTINCT cal.usuario_id) as estudiantes
    FROM cursos c
    JOIN profesores p ON c.profesor_id = p.id
    LEFT JOIN calificaciones cal ON c.id = cal.curso_id
    GROUP BY c.id
    ORDER BY calificacion DESC
    LIMIT 5
");
while($row = $result->fetch_assoc()) {
    $cursos_populares[] = $row;
}

// Últimos comentarios
$comentarios = [];
$result = $conexion->query("
    SELECT u.nombre_completo as usuario,
    cal.comentario, cal.estrellas
    FROM calificaciones cal
    JOIN usuarios u ON cal.usuario_id = u.id
    ORDER BY cal.created_at DESC
    LIMIT 5
");
while($row = $result->fetch_assoc()) {
    $comentarios[] = $row;
}

echo json_encode([
    "profesores" => $profesores,
    "cursos" => $cursos,
    "usuarios" => $usuarios,
    "promedio" => $promedio ?? "0.0",
    "cursos_populares" => $cursos_populares,
    "comentarios" => $comentarios
]);

$conexion->close();
?>