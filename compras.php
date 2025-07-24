<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] == 1) {
    header('Location: index.php');
    exit;
}

$id_cliente = $_SESSION['id_cliente'];

$stmt = $conn->prepare("
    SELECT v.*, p.nombre AS nombre_producto 
    FROM ventas v
    JOIN productos p ON v.id_producto = p.id_producto
    WHERE v.id_cliente = ?
    ORDER BY v.fecha DESC
");
$stmt->execute([$id_cliente]);
$compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Historial de Compras</title>
<style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #f06292, #a2e1ef, #bb82cb);
      color: #4c306e;
      padding: 40px;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: flex-start;
    }
    .contenedor-principal {
      background: #ffffffdd;
      border-radius: 25px;
      padding: 40px;
      max-width: 900px;
      width: 100%;
      box-shadow: 0 10px 40px rgba(107, 76, 123, 0.2);
    }
    h1 {
      font-family: 'Baloo 2', cursive;
      font-size: 2.8rem;
      color: #cb6ce6;
      margin-bottom: 30px;
      text-align: center;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      padding: 12px 15px;
      border-bottom: 1px solid #ddd;
      text-align: center;
      color: #6b4c7b;
    }
    th {
      background-color: #f8d7da;
      color: #a71d5d;
      font-weight: 700;
    }
    tbody tr:hover {
      background-color: #fce4ec;
    }
    .sin-compras {
      text-align: center;
      margin-top: 50px;
      font-size: 1.2rem;
      color: #d81b60;
      font-weight: 600;
    }
</style>
</head>
<body>

<div class="contenedor-principal">
  <h1>Historial de Compras</h1>

  <?php if (count($compras) === 0): ?>
    <div class="sin-compras">No tienes compras registradas aún.</div>
  <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio Unitario</th>
          <th>Total Pagado</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($compras as $compra): ?>
          <tr>
            <td><?= htmlspecialchars($compra['nombre_producto']) ?></td>
            <td><?= htmlspecialchars($compra['cantidad']) ?></td>
            <td>$<?= number_format($compra['precio_unitario'], 2) ?></td>
            <td>$<?= number_format($compra['total'], 2) ?></td>
            <td><?= date('d/m/Y H:i', strtotime($compra['fecha'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

</body>
</html>
