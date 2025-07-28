<?php
session_start();


if (isset($_POST['agregar'])) {
    $producto = [
        'id' => $_POST['id_producto'],
        'nombre' => $_POST['nombre'],
        'precio' => $_POST['precio'],
        'cantidad' => $_POST['cantidad']
    ];

    // Si el carrito no existe, lo creamos
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Revisar si ya está en el carrito
    $repetido = false;
    foreach ($_SESSION['carrito'] as &$item) {
        if ($item['id'] == $producto['id']) {
            $item['cantidad'] += $producto['cantidad'];
            $repetido = true;
            break;
        }
    }

    // Si no estaba, lo agregamos
    if (!$repetido) {
        $_SESSION['carrito'][] = $producto;
    }

    header("Location: ver_carrito.php");
    exit;
}

?>
