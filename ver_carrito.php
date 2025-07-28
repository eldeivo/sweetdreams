<?php
session_start();

// Eliminar producto del carrito si se solicita
if (isset($_POST['eliminar'])) {
    $id_eliminar = $_POST['id_producto'] ?? null;
    if ($id_eliminar !== null && isset($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $key => $item) {
            if ($item['id'] == $id_eliminar) {
                unset($_SESSION['carrito'][$key]);
                // Reindexar array para evitar huecos
                $_SESSION['carrito'] = array_values($_SESSION['carrito']);
                break;
            }
        }
    }
    header("Location: ver_carrito.php");
    exit;
}

// Agregar producto al carrito (no es obligatorio si solo agregas desde catálogo)
if (isset($_POST['agregar'])) {
    $id_producto = $_POST['id_producto'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $cantidad = (int)($_POST['cantidad'] ?? 0);

    if (!$id_producto || $cantidad < 1) {
        // Aquí podrías agregar un mensaje de error si quieres
    } else {
        $producto = [
            'id' => $id_producto,
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad
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
    }
    header("Location: ver_carrito.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tu Carrito - Sweet Dreams</title>
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
        form.inline {
            display: inline;
        }
        .boton-eliminar {
            background: #e55353;
        }
    </style>
</head>
<body>
    <h1>Tu carrito 🛒</h1>

    <?php if (!empty($_SESSION['carrito'])): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio unitario</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Comprar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($_SESSION['carrito'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['id']) ?></td>
                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                    <td>$<?= number_format($item['precio'], 2) ?></td>
                    <td><?= (int)$item['cantidad'] ?></td>
                    <td>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>

                    <td>
                        <form method="POST" action="procesar_compra.php">
    <input type="hidden" name="id_producto" value="<?= $item['id'] ?>">
    <input type="hidden" name="cantidad" value="<?= $item['cantidad'] ?>">
    <button type="submit" name="comprar">Comprar este producto</button>
</form>

                    </td>

                    <td>
                        <form method="POST" action="ver_carrito.php" class="inline">
                            <input type="hidden" name="id_producto" value="<?= htmlspecialchars($item['id']) ?>">
                            <button type="submit" name="eliminar" class="boton boton-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tu carrito está vacío 😢</p>
    <?php endif; ?>

    <a href="productos.php" class="boton">Seguir comprando</a>
</body>
</html>
