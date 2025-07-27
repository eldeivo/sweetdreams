<?php
session_start();
?>

<h2>🛍️ Tu carrito de compras</h2>

<?php
if (!empty($_SESSION['carrito'])) {
    echo "<table class='productos-page'>";
    echo "<tr><th>Producto</th><th>Precio</th><th>Cantidad</th><th>Total</th></tr>";
    $total_final = 0;

    foreach ($_SESSION['carrito'] as $item) {
        $total = $item['precio'] * $item['cantidad'];
        $total_final += $total;

        echo "<tr>";
        echo "<td>" . $item['nombre'] . "</td>";
        echo "<td>$" . number_format($item['precio'], 2) . "</td>";
        echo "<td>" . $item['cantidad'] . "</td>";
        echo "<td>$" . number_format($total, 2) . "</td>";
        echo "</tr>";
    }

    echo "<tr><td colspan='3'><strong>Total a pagar:</strong></td><td><strong>$" . number_format($total_final, 2) . "</strong></td></tr>";
    echo "</table>";
    echo "<br><a href='finalizar_compra.php'>🧾 Finalizar compra</a>";
} else {
    echo "<p>Tu carrito está vacío, agrega productos para concluir tu compra</p>";
}
?>
