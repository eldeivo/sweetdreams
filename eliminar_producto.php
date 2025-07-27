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

$stmt = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
$stmt->execute([$id_producto]);

header('Location: admin.php');
exit;
