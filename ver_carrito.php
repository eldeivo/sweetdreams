<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] == 1) {
    header('Location: iniciarsesion.php');
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$carrito = $_SESSION['carrito'] ?? [];

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = intval($_POST['id_producto']);

    if (isset($_POST['eliminar'])) {
        unset($_SESSION['carrito'][$id_producto]);
        $_SESSION['mensaje'] = "Producto eliminado del carrito 🗑️";
        header("Location: ver_carrito.php");
        exit;
    }

    if (isset($_POST['comprar']) && isset($carrito[$id_producto])) {
        $item = $carrito[$id_producto];
        $cantidad = intval($item['cantidad']);

        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("SELECT precio, stock FROM productos WHERE id_producto = ?");
            $stmt->execute([$id_producto]);
            $producto = $stmt->fetch();

            if (!$producto) throw new Exception("Producto no encontrado.");

            $stock = intval($producto['stock']);
            $precio_unitario = floatval($producto['precio']);
            $total = $cantidad * $precio_unitario;

            if ($stock < $cantidad) throw new Exception("No hay suficiente stock.");
            
            $stmt = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
            $stmt->execute([$id_cliente]);
            $saldo = floatval($stmt->fetchColumn());

            if ($saldo < $total) throw new Exception("Saldo insuficiente.");

            $stmt = $conn->prepare("INSERT INTO ventas (id_cliente, id_producto, cantidad, precio_unitario, total)
                                    VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_cliente, $id_producto, $cantidad, $precio_unitario, $total]);

            $stmt = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
            $stmt->execute([$cantidad, $id_producto]);

            $stmt = $conn->prepare("UPDATE clientes SET saldo = saldo - ? WHERE id_cliente = ?");
            $stmt->execute([$total, $id_cliente]);

            $conn->commit();

            unset($_SESSION['carrito'][$id_producto]);
            $_SESSION['mensaje'] = "✅ Producto comprado con éxito 🎉";
            header("Location: ver_carrito.php");
            exit;
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['mensaje'] = "❌ Error: " . $e->getMessage();
            header("Location: ver_carrito.php");
            exit;
        }
    }
}

// Obtener saldo actual del cliente
$stmt = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
$stmt->execute([$id_cliente]);
$saldo_actual = $stmt->fetchColumn();
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
            width: 90%;
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
            margin: 10px 5px;
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
        .boton-eliminar {
            background: #e55353;
            padding: 10px 20px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            border: none;
        }
        .mensaje {
            margin: 20px auto;
            padding: 15px 30px;
            background-color: #e0f7e9;
            border: 1px solid #a5d6a7;
            border-radius: 10px;
            color: #2e7d32;
            font-weight: bold;
            width: fit-content;
            text-align: center;
        }
        .nav-links {
            margin-bottom: 25px;
        }
        .nav-links a {
            margin: 0 10px;
            text-decoration: none;
            font-weight: bold;
            color: #d14c8f;
        }
        .nav-links a:hover {
            color: #a3195c;
        }
        .saldo-info {
            text-align: right;
            margin: 10px auto 20px;
            width: 90%;
            color: #22396bff;
            font-size: 1.1em;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="nav-links">
    <a href="productos.php" >Seguir comprando</a>
    <a href="recargar.php" >Recargar saldo</a>
    <a href="compras.php" >Historial</a>
    <a href="cerrarsesion.php" >Cerrar sesión</a>
</div>

<h1>Tu Carrito 🛒</h1>

<?php if (!empty($_SESSION['mensaje'])): ?>
    <div class="mensaje"><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
    <?php unset($_SESSION['mensaje']); ?>
<?php endif; ?>

<div class="saldo-info">Saldo disponible: <strong>$<?= number_format($saldo_actual, 2) ?></strong></div>

<?php if (empty($carrito)): ?>
    <p>Tu carrito está vacío 😢</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Subtotal ($)</th>
                <th>Comprar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($carrito as $id => $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nombre']) ?></td>
                    <td><?= intval($item['cantidad']) ?></td>
                    <td>$<?= number_format($item['cantidad'] * $item['precio'], 2) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_producto" value="<?= $id ?>">
                            <button type="submit" name="comprar" class="boton">Comprar</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_producto" value="<?= $id ?>">
                            <button type="submit" name="eliminar" class="boton-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
