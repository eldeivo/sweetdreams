<?php
session_start();
require 'conexion.php';

// Verificar que esté logueado y no sea admin
if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] == 1) {
    header('Location: iniciarsesion.php');
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

// Obtener productos disponibles
$stmt = $conn->query("SELECT * FROM productos WHERE stock > 0");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$mensaje = '';

// Procesar compra
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_producto = intval($_POST['id_producto']);
    $cantidad = intval($_POST['cantidad']);
     echo "Cantidad recibida: " . $cantidad;

       // Validar cantidad
    if ($cantidad <= 0) {
        $error = "Cantidad inválida.";
    } else {
        // Obtener producto seleccionado
        $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch();

        if (!$producto) {
            $error = "Producto no encontrado.";
        } elseif ($cantidad > $producto['stock']) {
            $error = "No hay suficiente stock.";
        } else {
            // Obtener saldo cliente
            $stmt = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
            $stmt->execute([$id_cliente]);
            $cliente = $stmt->fetch();

            $total = $producto['precio'] * $cantidad;

            if ($cliente['saldo'] < $total) {
                $error = "No tienes saldo suficiente.";
            } else {
                // Restar saldo al cliente
                $nuevo_saldo = $cliente['saldo'] - $total;
                $stmt = $conn->prepare("UPDATE clientes SET saldo = ? WHERE id_cliente = ?");
                $stmt->execute([$nuevo_saldo, $id_cliente]);

                // Actualizar stock producto
                $nuevo_stock = $producto['stock'] - $cantidad;
                $stmt = $conn->prepare("UPDATE productos SET stock = ? WHERE id_producto = ?");
                $stmt->execute([$nuevo_stock, $id_producto]);

                // Insertar venta
                $stmt = $conn->prepare("INSERT INTO ventas (id_cliente, id_producto, cantidad, precio_unitario, total, fecha) VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([$id_cliente, $id_producto, $cantidad, $producto['precio'], $total]);

                $mensaje = "Compra realizada con éxito.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="stylesheet" href="styles.css">
<meta charset="UTF-8" />
<title>Productos - Sweet Dreams</title>
</head>
<body class="productos-page">

<div class="nav-links">
    <a href="productos.php">Productos</a>
    <a href="recargar.php">Recargar saldo</a>
    <a href="compras.php">Historial de compras</a>
    <a href="cerrarsesion.php">Cerrar sesión</a>
</div>

<h1>Catálogo de Productos</h1>

<?php if($mensaje): ?>
    <div class="mensaje"><?=htmlspecialchars($mensaje)?></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="error"><?=htmlspecialchars($error)?></div>
<?php endif; ?>

<?php
// Mostrar saldo actual
$stmt = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch();
?>

<div class="saldo">Saldo disponible: $<?=number_format($cliente['saldo'], 2)?></div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Precio ($)</th>
            <th>Stock</th>
            <th>Cantidad</th>
            <th>Comprar</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($productos as $p): ?>
            <tr>
                <td><?=htmlspecialchars($p['nombre'])?></td>
                <td><?=number_format($p['precio'], 2)?></td>
                <td><?=intval($p['stock'])?></td>
                <td>
                    <form method="POST" style="margin:0;">
                        <input type="hidden" name="id_producto" value="<?=$p['id_producto']?>">
                        <input type="number" name="cantidad" min="1" max="<?=$p['stock']?>" value="1" required>
                </td>
                <td>
                        <button type="submit">Comprar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
