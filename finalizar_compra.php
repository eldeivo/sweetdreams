<?php
session_start();
include 'conexion.php';

$id_cliente = $_SESSION['id_cliente'] ?? 1;

$msg = "";
$tipo = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Quitar var_dump y exit para que se procese el POST
    // var_dump($_POST);
    // exit;

    $id_producto = filter_input(INPUT_POST, 'id_producto', FILTER_VALIDATE_INT);
$cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

if (!$id_producto || !$cantidad || $id_producto <= 0 || $cantidad <= 0) {
    $msg = "Datos de producto inválidos. (Debug: id_producto=$id_producto, cantidad=$cantidad)";
    $tipo = "error";
}


    if ($id_producto <= 0 || $cantidad <= 0) {
        $msg = "Datos de producto inválidos.";
        $tipo = "error";
    } else {
        try {
            // Obtener saldo actual del cliente
            $sql_saldo = $conn->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
            $sql_saldo->execute([$id_cliente]);
            $saldo_cliente = $sql_saldo->fetchColumn();

            // Obtener precio y stock del producto
            $sql_producto = $conn->prepare("SELECT precio, stock, nombre FROM productos WHERE id_producto = ?");
            $sql_producto->execute([$id_producto]);
            $producto = $sql_producto->fetch();

            if (!$producto) {
                $msg = "El producto no existe.";
                $tipo = "error";
            } else {
                $precio = (float)$producto['precio'];
                $stock = (int)$producto['stock'];
                $nombre = $producto['nombre'];

                if ($stock < $cantidad) {
                    $msg = "No hay suficiente stock para " . htmlspecialchars($nombre);
                    $tipo = "error";
                } else {
                    $total = $precio * $cantidad;

                    if ($saldo_cliente < $total) {
                        $msg = "Saldo insuficiente 😥";
                        $tipo = "error";
                    } else {
                        // Llamar al procedimiento almacenado para registrar venta
                        $stmt = $conn->prepare("CALL registrar_venta(?, ?, ?)");
                        $stmt->execute([$id_producto, $cantidad, $id_cliente]);

                        // Actualizar saldo del cliente
                        $nuevo_saldo = $saldo_cliente - $total;
                        $sql_update = $conn->prepare("UPDATE clientes SET saldo = ? WHERE id_cliente = ?");
                        $sql_update->execute([$nuevo_saldo, $id_cliente]);

                        $msg = "¡Gracias por tu compra de " . htmlspecialchars($nombre) . "! 🎉";
                        $tipo = "exito";
                    }
                }
            }
        } catch (PDOException $e) {
            $msg = "Error en la compra: " . htmlspecialchars($e->getMessage());
            $tipo = "error";
        }
    }
} else {
    $msg = "No se recibieron datos para procesar la compra.";
    $tipo = "error";
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
            text-align: center;
        }
        .exito {
            color: green;
            font-size: 1.5rem;
        }
        .error {
            color: red;
            font-size: 1.5rem;
        }
        .boton {
            background: linear-gradient(90deg, #ff8fb1, #ffa4d3);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            box-shadow: 0 6px 20px rgba(107, 76, 123, 0.2);
            margin-top: 30px;
            cursor: pointer;
        }
        .boton:hover {
            background-color: #ab52c4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Finalizar Compra 🛍️</h1>
        <p class="<?= $tipo === 'exito' ? 'exito' : 'error' ?>">
            <?= htmlspecialchars($msg) ?>
        </p>

        <a href="productos.php" class="boton">Seguir comprando</a>
    </div>

    <?php if ($tipo === "exito" || $tipo === "error"): ?>
    <script>
    window.addEventListener('DOMContentLoaded', () => {
        const toast = document.createElement('div');
        toast.textContent = <?= json_encode($msg) ?>;
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%)';
        toast.style.padding = '15px 25px';
        toast.style.borderRadius = '12px';
        toast.style.fontSize = '1rem';
        toast.style.fontWeight = '600';
        toast.style.zIndex = '9999';
        toast.style.boxShadow = '0 8px 20px rgba(0,0,0,0.15)';
        toast.style.cursor = 'default';
        toast.style.transition = 'opacity 0.5s ease';
        toast.style.opacity = '1';
        if ("<?= $tipo ?>" === "exito") {
            toast.style.backgroundColor = '#d4edda';
            toast.style.color = '#155724';
            toast.style.border = '1px solid #c3e6cb';
        } else {
            toast.style.backgroundColor = '#f8d7da';
            toast.style.color = '#721c24';
            toast.style.border = '1px solid #f5c6cb';
        }
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 1500);
    });
    </script>
    <?php endif; ?>
</body>
</html>
