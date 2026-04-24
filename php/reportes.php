<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once 'conexion.php';

$action = $_GET['action'] ?? '';

if ($action === 'resumen') {

    $vis = 0;
    $r = $conexion->query("SELECT COUNT(*) as total FROM visualizaciones WHERE visto = 1");
    if ($r) $vis = $r->fetch_assoc()['total'];

    $prom = "0.0";
    $r = $conexion->query("SELECT ROUND(AVG(estrellas), 1) as promedio FROM calificaciones");
    if ($r) $prom = $r->fetch_assoc()['promedio'] ?? "0.0";

    $com = 0;
    $r = $conexion->query("SELECT COUNT(*) as total FROM calificaciones");
    if ($r) $com = $r->fetch_assoc()['total'];

    $curso_top = '-';
    $r = $conexion->query("SELECT c.nombre FROM cursos c LEFT JOIN calificaciones cal ON c.id = cal.curso_id GROUP BY c.id ORDER BY AVG(cal.estrellas) DESC LIMIT 1");
    if ($r && $r->num_rows > 0) $curso_top = $r->fetch_assoc()['nombre'];

    $cursos_pop = [];
    $r = $conexion->query("SELECT c.nombre, ROUND(AVG(cal.estrellas), 1) as calificacion, COUNT(DISTINCT cal.id) as total_comentarios, COUNT(DISTINCT vis.id) as total_visualizaciones FROM cursos c LEFT JOIN calificaciones cal ON c.id = cal.curso_id LEFT JOIN videos v ON c.id = v.curso_id LEFT JOIN visualizaciones vis ON v.id = vis.video_id AND vis.visto = 1 GROUP BY c.id ORDER BY calificacion DESC LIMIT 10");
    if ($r) while ($row = $r->fetch_assoc()) $cursos_pop[] = $row;

    $coms = [];
    $r = $conexion->query("SELECT u.nombre_completo as usuario, c.nombre as curso, cal.comentario, cal.estrellas FROM calificaciones cal JOIN usuarios u ON cal.usuario_id = u.id JOIN cursos c ON cal.curso_id = c.id ORDER BY cal.created_at DESC LIMIT 8");
    if ($r) while ($row = $r->fetch_assoc()) $coms[] = $row;

    $vis_curso = [];
    $r = $conexion->query("SELECT c.nombre, p.nombre as profesor, COUNT(DISTINCT v.id) as total_videos, COUNT(DISTINCT CASE WHEN vis.visto = 1 THEN vis.id END) as total_visualizaciones FROM cursos c JOIN profesores p ON c.profesor_id = p.id LEFT JOIN videos v ON c.id = v.curso_id LEFT JOIN visualizaciones vis ON v.id = vis.video_id GROUP BY c.id ORDER BY total_visualizaciones DESC");
    if ($r) while ($row = $r->fetch_assoc()) $vis_curso[] = $row;

    echo json_encode([
        "visualizaciones" => $vis,
        "promedio" => $prom,
        "comentarios" => $com,
        "curso_top" => $curso_top,
        "cursos_populares" => $cursos_pop,
        "comentarios_recientes" => $coms,
        "visualizaciones_curso" => $vis_curso
    ]);

} else {
    echo json_encode(["error" => "Accion no valida", "action" => $action]);
}

$conexion->close();
?>