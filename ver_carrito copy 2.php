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
$mensaje = "";

echo "<pre>";
echo "ID CLIENTE: " . $id_cliente . "\n";
echo "ID PRODUCTO: " . $id_producto . "\n";
echo "CANTIDAD: " . $cantidad . "\n";
print_r($carrito);
echo "</pre>";
exit;


// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = intval($_POST['id_producto']);

    if (isset($_POST['eliminar'])) {
        unset($_SESSION['carrito'][$id_producto]);
        $mensaje = "Producto eliminado del carrito 🗑️";
    }

    if (isset($_POST['comprar']) && isset($carrito[$id_producto])) {
        $item = $carrito[$id_producto];
        $cantidad = $item['cantidad'];
        $precio_unitario = $item['precio'];
        $total = $cantidad * $precio_unitario;

        // Validar stock disponible
        $stmt = $conn->prepare("SELECT stock FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $stock_disponible = $stmt->fetchColumn();

        // Validar saldo del cliente
        $stmt = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
        $stmt->execute([$id_cliente]);
        $saldo_cliente = $stmt->fetchColumn();

        if ($stock_disponible < $cantidad) {
            $mensaje = "No hay suficiente stock para el producto.";
        } elseif ($saldo_cliente < $total) {
            $mensaje = "No tienes suficiente saldo para esta compra.";
        } else {
            try {
                $stmt = $conn->prepare("CALL registrar_venta(?, ?, ?)");
                $stmt->execute([$id_cliente, $id_producto, $cantidad]);

                unset($_SESSION['carrito'][$id_producto]);
                $mensaje = "Producto comprado con éxito 🎉";
            } catch (PDOException $e) {
                $mensaje = "Error al registrar la venta: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito - Sweet Dreams</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="carrito-page">

<div class="nav-links">
    <a href="productos.php">Seguir comprando</a>
    <a href="recargar.php">Recargar saldo</a>
    <a href="compras.php">Historial de compras</a>
    <a href="cerrarsesion.php">Cerrar sesión</a>
</div>

<h1>Tu Carrito 🛒</h1>

<?php if (!empty($mensaje)) : ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<?php if (empty($carrito)): ?>
    <p>Tu carrito está vacío.</p>
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
                    <td><?= number_format($item['cantidad'] * $item['precio'], 2) ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id_producto" value="<?= $id ?>">
                            <button type="submit" name="comprar">Comprar ahora</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id_producto" value="<?= $id ?>">
                            <button type="submit" name="eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
