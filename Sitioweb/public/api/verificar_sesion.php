<?php
require_once 'config.php';
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['logged' => false]);
    exit;
}
echo json_encode(['logged' => true, 'rol' => $_SESSION['rol']]);
?>