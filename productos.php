<?php
session_start();
require 'conexion.php';

// Verificar que esté logueado y no sea admin
if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] == 1) {
    header('Location: index.php');
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
<meta charset="UTF-8" />
<title>Productos - Sweet Dreams</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f06292, #a2e1ef, #bb82cb);
        min-height: 100vh;
        margin: 0;
        padding: 40px 20px;
        color: #4b3b57;
    }
    h1 {
        text-align: center;
        margin-bottom: 30px;
        font-family: 'Baloo 2', cursive;
        font-size: 3rem;
        color: #cb6ce6;
    }
    .mensaje {
        background: #dcedc8;
        color: #33691e;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 10px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }
    .error {
        background: #ffcdd2;
        color: #b71c1c;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 10px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
        text-align: center;
    }
    table {
        border-collapse: collapse;
        width: 90%;
        max-width: 900px;
        margin: 0 auto 40px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(107, 76, 123, 0.15);
        overflow: hidden;
    }
    th, td {
        padding: 15px 20px;
        text-align: center;
    }
    th {
        background-color: #f48fb1;
        color: white;
        font-weight: 700;
        font-family: 'Baloo 2', cursive;
        font-size: 1.2rem;
    }
    tr:nth-child(even) {
        background-color: #f9e1f7;
    }
    input[type=number] {
        width: 70px;
        padding: 6px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
        text-align: center;
    }
    button {
        background: #f06292;
        border: none;
        padding: 10px 20px;
        color: white;
        font-weight: bold;
        border-radius: 15px;
        cursor: pointer;
        transition: background 0.3s;
        font-family: 'Baloo 2', cursive;
    }
    button:hover {
        background: #d81b60;
    }
    .saldo {
        max-width: 900px;
        margin: 0 auto 30px;
        font-size: 1.3rem;
        text-align: right;
        font-weight: bold;
        color: #6b4c7b;
    }
    .nav-links {
        max-width: 900px;
        margin: 0 auto 15px;
        text-align: right;
    }
    .nav-links a {
        text-decoration: none;
        color: #7c7296;
        margin-left: 15px;
        font-weight: 600;
    }
    .nav-links a:hover {
        color: #d81b60;
    }
</style>
</head>
<body>

<div class="nav-links">
    <a href="productos.php">Productos</a>
    <a href="recargar.php">Recargar saldo</a>
    <a href="compras.php">Historial de compras</a>
    <a href="logout.php">Cerrar sesión</a>
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
