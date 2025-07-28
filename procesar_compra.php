<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] == 1) {
    header('Location: iniciarsesion.php');
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$carrito = $_SESSION['carrito'] ?? [];

if (empty($carrito)) {
    header('Location: productos.php');
    exit;
}

try {
    $conn->beginTransaction();

    // Obtener saldo actual
    $stmt = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id_cliente]);
    $cliente = $stmt->fetch();
    $saldo = floatval($cliente['saldo']);

    $total_compra = 0.0;

    // Validar stock y calcular total
    foreach ($carrito as $id_producto => $item) {
        $cantidad = intval($item['cantidad']);

        $stmt = $conn->prepare("SELECT precio, stock FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch();

        if (!$producto) {
            throw new Exception("Producto no encontrado (ID: $id_producto)");
        }

        if ($cantidad > $producto['stock']) {
            throw new Exception("Stock insuficiente para: " . $item['nombre']);
        }

        $total_compra += $producto['precio'] * $cantidad;
    }

    // Verificar si hay saldo suficiente
    if ($total_compra > $saldo) {
        throw new Exception("Saldo insuficiente. Total: $$total_compra | Saldo disponible: $$saldo");
    }

    // Procesar la venta
    foreach ($carrito as $id_producto => $item) {
        $cantidad = intval($item['cantidad']);

        // Obtener precio actual del producto
        $stmt = $conn->prepare("SELECT precio FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch();
        $precio_unitario = floatval($producto['precio']);
        $total = $precio_unitario * $cantidad;

        // Insertar en ventas
        $stmt = $conn->prepare("INSERT INTO ventas (id_cliente, id_producto, cantidad, precio_unitario, total) 
                                VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$id_cliente, $id_producto, $cantidad, $precio_unitario, $total]);

        // Actualizar stock
        $stmt = $conn->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
        $stmt->execute([$cantidad, $id_producto]);
    }

    // Descontar saldo del cliente
    $stmt = $conn->prepare("UPDATE clientes SET saldo = saldo - ? WHERE id_cliente = ?");
    $stmt->execute([$total_compra, $id_cliente]);

    // Limpiar carrito
    unset($_SESSION['carrito']);

    $conn->commit();
    $mensaje = "¡Compra realizada con éxito! Total: $" . number_format($total_compra, 2);
} catch (Exception $e) {
    $conn->rollBack();
    $mensaje = "❌ Error al procesar la compra: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra procesada</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="nav-links">
        <a href="productos.php">Volver a productos</a>
        <a href="ver_carrito.php">Ver carrito</a>
        <a href="compras.php">Historial</a>
    </div>

    <h1>Resultado de la compra</h1>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
</body>
</html>
