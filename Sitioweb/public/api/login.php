<?php
require_once 'config.php';

$correo = $_POST['correo'] ?? '';
$password = $_POST['contraseña'] ?? '';

$stmt = $pdo->prepare("SELECT id, nombre, rol, contraseña FROM usuarios WHERE correo = ?");
$stmt->execute([$correo]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['contraseña'])) {
    $_SESSION['usuario_id'] = $user['id'];
    $_SESSION['nombre'] = $user['nombre'];
    $_SESSION['rol'] = $user['rol']; // 'admin' o 'cliente'
    
    $redirect = ($user['rol'] === 'admin') ? '/Admin/dashboard' : '/Cliente/dashboard';
    echo json_encode(['success' => true, 'redirect' => $redirect]);
} else {
    echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
}
?>