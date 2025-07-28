<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] != 1) {
    header('Location: menuprincipal.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id_producto = intval($_GET['id']);

try {
    $stmt = $conn->prepare("CALL admin_producto(2, ?, '', 0, 0)");
    $stmt->execute([$id_producto]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado && isset($resultado['error'])) {
        $_SESSION['mensaje'] = $resultado['error'];
        $_SESSION['mensaje_tipo'] = 'error';
    } elseif ($resultado && isset($resultado['mensaje'])) {
        $_SESSION['mensaje'] = $resultado['mensaje'];
        $_SESSION['mensaje_tipo'] = 'exito';
    } else {
        $_SESSION['mensaje'] = 'Respuesta inesperada del servidor.';
        $_SESSION['mensaje_tipo'] = 'error';
    }
} catch (PDOException $e) {
    $_SESSION['mensaje'] = 'Error al eliminar producto: ' . $e->getMessage();
    $_SESSION['mensaje_tipo'] = 'error';
}

header('Location: admin.php');
exit;
