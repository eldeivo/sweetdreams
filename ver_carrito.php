<?php
session_start();

// PROCESAR PRODUCTO AGREGADO AL CARRITO
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

<!-- AHORA EL HTML -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Carrito - Sweet Dreams</title>
    <link rel="stylesheet" href="estilos.css">
</head>
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #fff5fb;
    color: #333;
    text-align: center;
    padding: 30px;
}

h1 {
    font-size: 2.5em;
    color: #d14c8f;
    margin-bottom: 20px;
    text-shadow: 1px 1px 2px #fff;
}

table {
    margin: 0 auto;
    border-collapse: collapse;
    width: 80%;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(219, 112, 147, 0.2);
    border-radius: 12px;
    overflow: hidden;
}

th, td {
    padding: 15px;
    border-bottom: 1px solid #f0cadd;
}

th {
    background-color: #fce4ec;
    color: #b71c5e;
    font-weight: bold;
}

td {
    background-color: #fff;
}

.boton {
    display: inline-block;
    background: linear-gradient(90deg, #ff8fb1, #ffa4d3);
    color: white;
    padding: 12px 25px;
    margin: 20px 10px;
    border: none;
    border-radius: 25px;
    text-decoration: none;
    font-size: 1em;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.3s ease;
}

.boton:hover {
    transform: scale(1.05);
    background: linear-gradient(90deg, #ff6f91, #ff9ecb);
}

p {
    font-size: 1.2em;
    color: #777;
    margin-top: 40px;
}

    </style>
<body>
    <h1>Tu carrito 🛒</h1>

    <!-- Mostrar los productos del carrito -->
    <?php if (!empty($_SESSION['carrito'])): ?>
        <table>
            <tr>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Cantidad</th>
            </tr>
            <?php foreach ($_SESSION['carrito'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                    <td>$<?= number_format($item['precio'], 2) ?></td>
                    <td><?= $item['cantidad'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- BOTÓN FINALIZAR COMPRA -->
        <form method="POST" action="finalizar_compra.php">
            <button type="submit" class="boton">Finalizar compra</button>
        </form>
    <?php else: ?>
        <p>Tu carrito está vacío 😢</p>
    <?php endif; ?>

    <a href="productos.php" class="boton">Seguir comprando</a>
</body>
</html>
