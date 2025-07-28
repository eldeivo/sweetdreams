<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Verifica si viene del carrito
    if (isset($_POST['id_producto']) && isset($_POST['cantidad'])) {
        $id_producto = (int)$_POST['id_producto'];
        $cantidad = (int)$_POST['cantidad'];
    }
    // Si no, verifica si viene del formulario simple
    else if (isset($_POST['producto_id']) && isset($_POST['producto_cantidad'])) {
        $id_producto = (int)$_POST['producto_id'];
        $cantidad = (int)$_POST['producto_cantidad'];
    } else {
        echo "Datos de producto inválidos.";
        exit;
    }

    // Validaciones
    if ($id_producto <= 0 || $cantidad <= 0) {
        echo "Datos inválidos.";
        exit;
    }

    if (!isset($_SESSION['id_cliente'])) {
        echo "Usuario no autenticado.";
        exit;
    }

    $id_cliente = $_SESSION['id_cliente'];

    try {
        $pdo->beginTransaction();

        // Verificar saldo actual
        $stmtSaldo = $pdo->prepare("SELECT saldo FROM clientes WHERE id_cliente = ?");
        $stmtSaldo->execute([$id_cliente]);
        $saldo = $stmtSaldo->fetchColumn();

        if ($saldo === false) {
            throw new Exception("Cliente no encontrado.");
        }

        // Obtener precio y stock del producto
        $stmtProducto = $pdo->prepare("SELECT precio, stock FROM productos WHERE id_producto = ?");
        $stmtProducto->execute([$id_producto]);
        $producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            throw new Exception("Producto no encontrado.");
        }

        $total = $producto['precio'] * $cantidad;

        if ($producto['stock'] < $cantidad) {
            throw new Exception("Stock insuficiente.");
        }

        if ($saldo < $total) {
            throw new Exception("Saldo insuficiente.");
        }

        // Llamar procedimiento almacenado
        $stmt = $pdo->prepare("CALL registrar_venta(?, ?, ?)");
        $stmt->execute([$id_producto, $cantidad, $id_cliente]);

        // Actualizar saldo
        $nuevoSaldo = $saldo - $total;
        $stmtSaldo = $pdo->prepare("UPDATE clientes SET saldo = ? WHERE id_cliente = ?");
        $stmtSaldo->execute([$nuevoSaldo, $id_cliente]);

        $pdo->commit();

        // Opcional: eliminar producto del carrito
        if (isset($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $i => $item) {
                if ($item['id'] == $id_producto) {
                    unset($_SESSION['carrito'][$i]);
                    break;
                }
            }
        }

        echo "Compra realizada con éxito.";

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error en la compra: " . $e->getMessage();
    }
} else {
    echo "Método no permitido.";
}
