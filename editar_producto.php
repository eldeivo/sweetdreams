<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] != 1) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id_producto = intval($_GET['id']);
$error = '';
$mensaje = '';

// Obtener producto
$stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
$stmt->execute([$id_producto]);
$producto = $stmt->fetch();

if (!$producto) {
    header('Location: admin.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    if ($nombre == '' || $precio <= 0 || $stock < 0) {
        $error = "Por favor, completa correctamente todos los campos.";
    } else {
        $stmt = $conn->prepare("UPDATE productos SET nombre = ?, precio = ?, stock = ? WHERE id_producto = ?");
        $stmt->execute([$nombre, $precio, $stock, $id_producto]);
        $mensaje = "Producto actualizado con éxito.";

        // Refrescar datos
        $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        $producto = $stmt->fetch();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Editar Producto</title>
<style>
  /* Igual estilo que agregar_producto.php */
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
      max-width: 500px;
      width: 100%;
      box-shadow: 0 10px 40px rgba(107, 76, 123, 0.2);
      text-align: center;
  }
  h1 {
      font-family: 'Baloo 2', cursive;
      font-size: 2.8rem;
      color: #cb6ce6;
      margin-bottom: 30px;
  }
  form {
      display: flex;
      flex-direction: column;
      gap: 20px;
  }
  input[type="text"],
  input[type="number"] {
      padding: 12px;
      border-radius: 15px;
      border: 2px solid #d8b0d9;
      font-size: 1rem;
      outline: none;
      transition: border-color 0.3s ease;
  }
  input[type="text"]:focus,
  input[type="number"]:focus {
      border-color: #f06292;
  }
  button {
      background: #f06292;
      color: white;
      border: none;
      padding: 15px;
      border-radius: 25px;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background 0.3s ease;
      font-weight: 700;
  }
  button:hover {
      background: #d81b60;
  }
  .mensaje {
      margin-top: 15px;
      color: green;
      font-weight: 600;
  }
  .error {
      margin-top: 15px;
      color: #d81b60;
      font-weight: 600;
  }
  a {
      display: inline-block;
      margin-top: 25px;
      color: #6b4c7b;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s ease;
  }
  a:hover {
      color: #f06292;
  }
</style>
</head>
<body>

<div class="contenedor-principal">
  <h1>Editar Producto</h1>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" placeholder="Nombre del producto" required>
    <input type="number" name="precio" value="<?= htmlspecialchars($producto['precio']) ?>" placeholder="Precio" min="0" step="0.01" required>
    <input type="number" name="stock" value="<?= htmlspecialchars($producto['stock']) ?>" placeholder="Stock" min="0" step="1" required>
    <button type="submit">Actualizar</button>
  </form>

  <a href="admin.php">&laquo; Volver al Panel</a>
</div>

</body>
</html>
