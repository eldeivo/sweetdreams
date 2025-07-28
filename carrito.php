<?php
session_start();


if (isset($_POST['agregar'])) {
    $producto = [
        'id' => $_POST['id_producto'],
        'nombre' => $_POST['nombre'],
        'precio' => $_POST['precio'],
        'cantidad' => $_POST['cantidad']
    ];

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $repetido = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['id'] == $producto['id']) {
            $item['cantidad'] += $producto['cantidad'];
            $repetido = true;
            break;
        }
    }

    if (!$repetido) {
        $_SESSION['carrito'][] = $producto;
    }

    header("Location: ver_carrito.php");
    exit;
}

?>
