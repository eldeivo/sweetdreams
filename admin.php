<?php
session_start();
require 'conexion.php';
require 'funciones.php';

$poco_stock = verPocoStock($pdo);
// Verificar que sea admin
if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] != 1) {
    header('Location: menuprincipal.php');
    exit;
}

// Obtener productos
$stmt = $conn->query("SELECT * FROM productos");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Administracion de inventario</title>
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
        max-width: 1100px;
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
    a.boton {
        background: #f06292;
        color: white;
        padding: 8px 15px;
        border-radius: 25px;
        font-weight: 600;
        text-decoration: none;
        margin: 0 5px;
        display: inline-block;
        transition: background 0.3s ease;
    }
    a.boton:hover {
        background: #d81b60;
    }
    .agregar {
        text-align: right;
        margin-bottom: 20px;
    }
    .logout {
        margin-top: 20px;
        text-align: center;
    }
</style>
</head>
<body>

<div class="contenedor-principal">
    <h1>Administración de Inventario</h1>

    <div class="agregar">
        <a href="agregar_producto.php" class="boton">+ Agregar Producto</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?= htmlspecialchars($producto['id_producto']) ?></td>
                <td><?= htmlspecialchars($producto['nombre']) ?></td>
                <td>$<?= number_format($producto['precio'], 2) ?></td>
                <td><?= htmlspecialchars($producto['stock']) ?></td>
                <td>
                    <a href="editar_producto.php?id=<?= $producto['id_producto'] ?>" class="boton">Editar</a>
                    <a href="eliminar_producto.php?id=<?= $producto['id_producto'] ?>" class="boton" onclick="return confirm('¿Estás seguro de eliminar este producto?');">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- 🔻 Sección de VISTA DE POCO STOCK -->
    <h2 style="margin-top: 60px;"> Productos con Poco Stock</h2>

    <?php
    require_once("funciones.php");
    $poco_stock = verPocoStock($pdo); // Asegúrate de tener esta función en funciones.php
    ?>

    <?php if (count($poco_stock) > 0): ?>
        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Stock</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($poco_stock as $producto): ?>
                    <tr style="background-color: #fff0f0;">
                        <td><?= htmlspecialchars($producto['id_producto']) ?></td>
                        <td><?= htmlspecialchars($producto['nombre']) ?></td>
                        <td><?= htmlspecialchars($producto['stock']) ?></td>
                        <td>$<?= htmlspecialchars($producto['precio'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="margin-top: 20px;">✨ Todos los productos tienen suficiente stock.</p>
    <?php endif; ?>

    <div class="logout">
        <a href="logout.php" class="boton">Cerrar Sesión</a>
    </div>
</div>

</body>

</html>
