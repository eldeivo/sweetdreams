<?php
session_start();
include 'conexion.php'; // tu archivo de conexión

// Simulamos un cliente con saldo (esto vendría de una base de datos normalmente)
$saldo_cliente = 1000; // Supongamos que el cliente tiene $1000 de saldo

$productos = $_SESSION['carrito'] ?? [];
$total = 0;

foreach ($productos as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

$mensaje = "";
if ($total > $saldo_cliente) {
    $mensaje = "<p class='error'>Saldo insuficiente 😥</p>";
} else {
    $saldo_cliente -= $total;
    $mensaje = "<p class='exito'>¡Gracias por tu compra! 🎉</p>";
    $_SESSION['carrito'] = []; // vaciar carrito
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra Finalizada</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to bottom right, #ffe6f0, #ffe);
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
        }
        .container {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #ffb6c1;
        }
        .exito {
            color: green;
            font-size: 1.5rem;
        }
        .error {
            color: red;
            font-size: 1.5rem;
        }
        .saldo {
            margin-top: 1rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Finalizar Compra 🛍️</h1>
        <?= $mensaje ?>

        <?php if ($total <= $saldo_cliente + $total): ?>
            <h2>Productos comprados:</h2>
            <table>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
                <?php foreach ($productos as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['nombre']) ?></td>
                        <td>$<?= number_format($item['precio'], 2) ?></td>
                        <td><?= $item['cantidad'] ?></td>
                        <td>$<?= number_format($item['precio'] * $item['cantidad'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p class="saldo">Tu nuevo saldo es: <strong>$<?= number_format($saldo_cliente, 2) ?></strong></p>
        <?php endif; ?>
    </div>
</body>
</html>
