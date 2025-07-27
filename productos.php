<?php
session_start();
require 'conexion.php';


if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] == 1) {
    header('Location: iniciarsesion.php');
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

// Inicializar carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Procesar agregado al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $id_producto = intval($_POST['id_producto']);
    $nombre = $_POST['nombre'];
    $precio = floatval($_POST['precio']);
    $cantidad = intval($_POST['cantidad']);

    // Si ya está en el carrito, sumar cantidades
    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto]['cantidad'] += $cantidad;
    } else {
        $_SESSION['carrito'][$id_producto] = [
            'nombre' => $nombre,
            'precio' => $precio,
            'cantidad' => $cantidad
        ];
    }

    $mensaje = "Producto agregado al carrito 🛒";
}

// Obtener productos disponibles
$stmt = $conn->query("SELECT * FROM productos WHERE stock > 0");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener saldo cliente
$stmt = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Productos - Sweet Dreams</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="productos-page">

<div class="nav-links">
    <a href="productos.php">Productos</a>
    <a href="ver_carrito.php">Ver carrito 🛒</a>
    <a href="recargar.php">Recargar saldo</a>
    <a href="compras.php">Historial de compras</a>
    <a href="cerrarsesion.php">Cerrar sesión</a>
</div>

<h1>Catálogo de Productos</h1>

<?php if (!empty($mensaje)) : ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<div class="saldo">Saldo disponible: $<?= number_format($cliente['saldo'], 2) ?></div>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Precio ($)</th>
            <th>Stock</th>
            <th>Cantidad</th>
            <th>Agregar al carrito</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nombre']) ?></td>
                <td><?= number_format($p['precio'], 2) ?></td>
                <td><?= intval($p['stock']) ?></td>
                <td>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                        <input type="hidden" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>">
                        <input type="hidden" name="precio" value="<?= $p['precio'] ?>">
                        <input type="number" name="cantidad" min="0" max="<?= $p['stock'] ?>" value="0" required>
                </td>
                <td>
                        <button type="submit" name="agregar">Agregar 🛒</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>
