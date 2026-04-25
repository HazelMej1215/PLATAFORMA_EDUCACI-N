<?php
// Configuración de sesión para el dominio real
$dominio = 'red-hedgehog-742097.hostingersite.com';

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $dominio,
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);
// Permitir recibir session_id por header personalizado


// Permitir recibir session_id por GET o POST
if (isset($_GET['session_id'])) {
    session_id($_GET['session_id']);
} elseif (isset($_POST['session_id'])) {
    session_id($_POST['session_id']);
} elseif (isset($_SERVER['HTTP_X_SESSION_ID'])) {
    session_id($_SERVER['HTTP_X_SESSION_ID']);
}
session_start();
session_start();

// Headers CORS para tu dominio
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: https://' . $dominio);
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Conexión a la base de datos (actualiza con tus datos reales)
$host = 'localhost';  // o el host que te indique Hostinger (ej: 'mysql.hostinger.com')
$db   = 'u853214219_proyecto';
$user = 'u853214219_Gamboa';
$pass = 'Gamboa10*';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a BD']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ==================== OBTENER TEMA ====================
if ($action === 'obtener_tema') {
    $tema = 'claro';
    if (isset($_SESSION['usuario_id'])) {
        $stmt = $pdo->prepare("SELECT tema FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $tema = $stmt->fetchColumn();
        if (!$tema) $tema = 'claro';
    }
    echo json_encode(['theme' => $tema]);
    exit;
}

if ($action === 'obtener_session_id') {
    echo json_encode(['session_id' => session_id()]);
    exit;
}

// ==================== ACCIONES PÚBLICAS ====================
if ($action === 'login') {
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['contraseña'] ?? '';
    $stmt = $pdo->prepare("SELECT id, nombre, rol, contraseña FROM usuarios WHERE correo = ?");
    $stmt->execute([$correo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user && $password === $user['contraseña']) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        $redirect = ($user['rol'] === 'admin') ? '/Administrador/dashboard' : '/Cliente/dashboard';
        echo json_encode([
            'success' => true,
            'redirect' => $redirect,
            'session_id' => session_id(),   // ← añadir
            'nombre' => $user['nombre'],    // ← añadir
            'rol' => $user['rol']           // ← añadir
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
    }
    exit;
}


function convertirYoutubeEmbed($url) {
    // Si ya es embed, devolverla igual
    if (strpos($url, 'youtube.com/embed/') !== false) {
        return $url;
    }
    
    // Extraer el código del video
    $pattern = '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]+)/';
    if (preg_match($pattern, $url, $matches)) {
        $video_id = $matches[1];
        return "https://www.youtube.com/embed/$video_id";
    }
    
    // Si no se puede extraer, devolver la original (fallback)
    return $url;
}

if ($action === 'registro') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $password = $_POST['contraseña'] ?? '';
    $rol = 'cliente';
    if (empty($nombre) || empty($correo) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE correo = ?");
    $check->execute([$correo]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$nombre, $correo, $password, $rol])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al registrar']);
    }
    exit;
}

if ($action === 'test_session') {
    echo json_encode([
        'session_id' => session_id(),
        'usuario_id' => $_SESSION['usuario_id'] ?? null,
        'logged' => isset($_SESSION['usuario_id'])
    ]);
    exit;
}

// ==================== ACCIONES PROTEGIDAS ====================
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$rol_usuario = $_SESSION['rol'];

if ($action === 'verificar_sesion') {
    echo json_encode(['logged' => true, 'rol' => $rol_usuario, 'nombre' => $_SESSION['nombre']]);
    exit;
}

if ($action === 'logout') {
    session_destroy();
    header('Location: https://' . $dominio . '/');
    exit;
}

// ==================== ADMINISTRADOR ====================
if ($rol_usuario !== 'admin' && in_array($action, ['alta_profesor', 'listar_profesores', 'alta_curso', 'visualizaciones_listado', 'calificaciones_listado', 'reporte_cursos', 'estadisticas_admin'])) {
    echo json_encode(['success' => false, 'message' => 'Requiere rol administrador']);
    exit;
}

if ($action === 'alta_profesor') {
    $nombre = $_POST['nombre'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $especialidad = $_POST['especialidad'] ?? '';
    if (empty($nombre) || empty($correo)) {
        echo json_encode(['success' => false, 'message' => 'Nombre y correo son obligatorios']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO profesores (nombre, correo, especialidad) VALUES (?, ?, ?)");
    if ($stmt->execute([$nombre, $correo, $especialidad])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en la BD']);
    }
    exit;
}

if ($action === 'listar_profesores') {
    $stmt = $pdo->query("SELECT id, nombre, especialidad FROM profesores ORDER BY nombre");
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'profesores' => $profesores]);
    exit;
}

if ($action === 'alta_curso') {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
        echo json_encode(['success' => false, 'message' => 'No autorizado']);
        exit;
    }
    
    foreach ($videos as $video) {
    $titulo = trim($video['titulo'] ?? '');
    $desc = trim($video['descripcion'] ?? '');
    $url = trim($video['url'] ?? '');
    if ($titulo === '' || $url === '') continue;
    
    // Convertir URL a formato embed
    $url_embed = convertirYoutubeEmbed($url);
    
    $stmtVideo->execute([$curso_id, $titulo, $desc, $orden, $url_embed]);
    $orden++;
}
    $input = json_decode(file_get_contents('php://input'), true);
    $nombre_curso = trim($input['nombre_curso'] ?? '');
    $descripcion_curso = trim($input['descripcion_curso'] ?? '');
    $profesor_id = (int)($input['profesor_id'] ?? 0);
    $videos = $input['videos'] ?? [];
    $imagen_base64 = $input['imagen'] ?? null;

    if (empty($nombre_curso) || !$profesor_id || empty($videos)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios']);
        exit;
    }

    $nombre_imagen = null;
    if ($imagen_base64) {
        $uploadDir = 'uploads/cursos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $nombre_imagen = 'curso_' . time() . '_' . uniqid() . '.png';
        $rutaCompleta = $uploadDir . $nombre_imagen;
        $data = explode(',', $imagen_base64);
        if (isset($data[1])) {
            file_put_contents($rutaCompleta, base64_decode($data[1]));
        } else {
            $nombre_imagen = null;
        }
    }

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("INSERT INTO cursos (nombre, descripcion, profesor_id, imagen) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre_curso, $descripcion_curso, $profesor_id, $nombre_imagen]);
        $curso_id = $pdo->lastInsertId();

        $orden = 1;
        $stmtVideo = $pdo->prepare("INSERT INTO videos (curso_id, titulo, descripcion, orden, url) VALUES (?, ?, ?, ?, ?)");
        foreach ($videos as $video) {
            $titulo = trim($video['titulo'] ?? '');
            $desc = trim($video['descripcion'] ?? '');
            $url = trim($video['url'] ?? '');
            if ($titulo === '' || $url === '') continue;
            $stmtVideo->execute([$curso_id, $titulo, $desc, $orden, $url]);
            $orden++;
        }
        $pdo->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'visualizaciones_listado') {
    $stmt = $pdo->query("
        SELECT c.nombre as curso, v.titulo, v.orden, v.visualizaciones
        FROM cursos c
        JOIN videos v ON c.id = v.curso_id
        ORDER BY c.id, v.orden
    ");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'visualizaciones' => $data]);
    exit;
}

if ($action === 'calificaciones_listado') {
    $stmt = $pdo->query("
        SELECT c.nombre as curso, u.nombre as usuario, cal.puntuacion, cal.comentario, cal.created_at
        FROM calificaciones cal
        JOIN cursos c ON cal.curso_id = c.id
        JOIN usuarios u ON cal.usuario_id = u.id
        ORDER BY c.id, cal.created_at DESC
    ");
    $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'calificaciones' => $calificaciones]);
    exit;
}

if ($action === 'reporte_cursos') {
    $stmt = $pdo->query("
        SELECT c.id, c.nombre,
               COALESCE(AVG(cal.puntuacion), 0) as promedio,
               COUNT(cal.id) as total_calificaciones,
               (SELECT GROUP_CONCAT(CONCAT(cal2.comentario, '|', u.nombre) SEPARATOR ';;')
                FROM calificaciones cal2
                JOIN usuarios u ON cal2.usuario_id = u.id
                WHERE cal2.curso_id = c.id AND cal2.comentario IS NOT NULL AND cal2.comentario != ''
                ORDER BY cal2.created_at DESC LIMIT 3) as comentarios_raw
        FROM cursos c
        LEFT JOIN calificaciones cal ON c.id = cal.curso_id
        GROUP BY c.id
        ORDER BY promedio DESC, total_calificaciones DESC
    ");
    $cursos = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $comentarios = [];
        if ($row['comentarios_raw']) {
            $items = explode(';;', $row['comentarios_raw']);
            foreach ($items as $item) {
                $parts = explode('|', $item);
                if (count($parts) == 2) {
                    $comentarios[] = ['comentario' => $parts[0], 'usuario' => $parts[1]];
                }
            }
        }
        $cursos[] = [
            'nombre' => $row['nombre'],
            'promedio' => round($row['promedio'], 1),
            'total_calificaciones' => (int)$row['total_calificaciones'],
            'comentarios' => $comentarios
        ];
    }
    echo json_encode(['success' => true, 'cursos' => $cursos]);
    exit;
}

if ($action === 'estadisticas_admin') {
    $totalCursos = $pdo->query("SELECT COUNT(*) FROM cursos")->fetchColumn();
    $totalProfesores = $pdo->query("SELECT COUNT(*) FROM profesores")->fetchColumn();
    $totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'cliente'")->fetchColumn();
    echo json_encode(['success' => true, 'totalCursos' => $totalCursos, 'totalProfesores' => $totalProfesores, 'totalUsuarios' => $totalUsuarios]);
    exit;
}

// ==================== CLIENTE ====================
elseif ($action === 'obtener_curso') {
    $curso_id = $_GET['curso_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT nombre, descripcion, imagen FROM cursos WHERE id = ?");
    $stmt->execute([$curso_id]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($curso) {
        echo json_encode(['success' => true, 'curso' => $curso]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Curso no encontrado']);
    }
    exit;
}



if ($action === 'listar_cursos_cliente') {
    if ($rol_usuario !== 'cliente') {
        echo json_encode(['success' => false, 'message' => 'Solo clientes']);
        exit;
    }
$stmt = $pdo->query("SELECT id, nombre, imagen FROM cursos ORDER BY id");
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'cursos' => $cursos]);
    exit;
}

if ($action === 'obtener_progreso') {
    $curso_id = $_GET['curso_id'] ?? 0;
    $stmt = $pdo->prepare("
        SELECT v.id, v.orden,
               IF(pv.visto IS NOT NULL, 1, 0) as visto
        FROM videos v
        LEFT JOIN progreso_videos pv ON v.id = pv.video_id AND pv.usuario_id = ?
        WHERE v.curso_id = ?
        ORDER BY v.orden
    ");
    $stmt->execute([$usuario_id, $curso_id]);
    $progreso = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'progreso' => $progreso]);
    exit;
}

if ($action === 'videos_curso') {
    $curso_id = $_GET['curso_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT id, titulo, orden, url FROM videos WHERE curso_id = ? ORDER BY orden");
    $stmt->execute([$curso_id]);
    $videos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'videos' => $videos]);
    exit;
}

if ($action === 'marcar_visto') {
    $input = json_decode(file_get_contents('php://input'), true);
    $video_id = $input['video_id'] ?? 0;
    $stmt = $pdo->prepare("INSERT INTO progreso_videos (usuario_id, video_id, visto, fecha_visto) VALUES (?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE visto=1, fecha_visto=NOW()");
    $stmt->execute([$usuario_id, $video_id]);
    $pdo->prepare("UPDATE videos SET visualizaciones = visualizaciones + 1 WHERE id = ?")->execute([$video_id]);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'actualizar_perfil') {
    $nueva_contraseña = $_POST['nueva_contraseña'] ?? '';
    $tema = $_POST['tema'] ?? '';
    $imagen = $_POST['imagen'] ?? '';
    $updates = [];
    $params = [];
    if (!empty($nueva_contraseña)) {
        $updates[] = "contraseña = ?";
        $params[] = $nueva_contraseña;
    }
    if (!empty($tema)) {
        $updates[] = "tema = ?";
        $params[] = $tema;
    }
    if (!empty($imagen)) {
        $nombreImagen = 'avatar_' . $usuario_id . '_' . time() . '.png';
        $ruta = 'uploads/' . $nombreImagen;
        if (!is_dir('uploads')) mkdir('uploads', 0777, true);
        $data = explode(',', $imagen);
        if (isset($data[1])) {
            file_put_contents($ruta, base64_decode($data[1]));
            $updates[] = "imagen_perfil = ?";
            $params[] = $nombreImagen;
        }
    }
    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'No hay datos para actualizar']);
        exit;
    }
    $params[] = $usuario_id;
    $sql = "UPDATE usuarios SET " . implode(", ", $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute($params)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
    }
    exit;
}

if ($action === 'obtener_perfil') {
    $stmt = $pdo->prepare("SELECT nombre, correo, imagen_perfil, tema FROM usuarios WHERE id = ?");
    $stmt->execute([$usuario_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'perfil' => $user]);
    exit;
}

if ($action === 'calificar_curso') {
    $curso_id = $_POST['curso_id'] ?? 0;
    $puntuacion = $_POST['puntuacion'] ?? 0;
    $comentario = $_POST['comentario'] ?? '';
    if (!$curso_id || !$puntuacion) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO calificaciones (usuario_id, curso_id, puntuacion, comentario) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE puntuacion = ?, comentario = ?");
    $stmt->execute([$usuario_id, $curso_id, $puntuacion, $comentario, $puntuacion, $comentario]);
    echo json_encode(['success' => true]);
    exit;
}

if ($action === 'generar_certificado') {
    $curso_id = $_GET['curso_id'] ?? 0;
    $nombre_usuario = $_SESSION['nombre'];
    $stmt = $pdo->prepare("SELECT nombre FROM cursos WHERE id = ?");
    $stmt->execute([$curso_id]);
    $curso = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$curso) {
        echo json_encode(['success' => false, 'message' => 'Curso no encontrado']);
        exit;
    }
    $cert_data = [
        'usuario' => $nombre_usuario,
        'curso' => $curso['nombre'],
        'fecha' => date('d/m/Y'),
        'codigo' => uniqid('CERT-')
    ];
    echo json_encode(['success' => true, 'certificado' => $cert_data]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Acción no válida']);
?>