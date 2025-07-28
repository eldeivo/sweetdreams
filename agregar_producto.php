<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] != 1) {
    header('Location: menuprincipal.php');
    exit;
}

$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    $id_producto = 0;

    if ($nombre == '' || $precio <= 0 || $stock < 0) {
        $error = "Por favor, completa correctamente todos los campos.";
    } else {
        try {
            $stmt = $conn->prepare("CALL admin_producto(3, ?, ?, ?, ?)");
            $stmt->execute([$id_producto, $nombre, $precio, $stock]);

            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado && isset($resultado['error'])) {
                $error = $resultado['error'];
            } elseif ($resultado && isset($resultado['mensaje'])) {
                $_SESSION['mensaje'] = $resultado['mensaje'];
                $_SESSION['mensaje_tipo'] = 'exito';
                header('Location: admin.php'); // <-- sin #final
                exit;
            } else {
                $error = "Respuesta inesperada del servidor.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Agregar Producto</title>
<style>
  /* Igual estilo que admin.php para consistencia */
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
      color: #8b669eff;
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
  <h1>Agregar Producto</h1>
  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" action="">
    <input type="text" name="nombre" placeholder="Nombre del producto" required value="<?= isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : '' ?>">
    <input type="number" name="precio" placeholder="Precio" min="0" step="0.01" required value="<?= isset($_POST['precio']) ? htmlspecialchars($_POST['precio']) : '' ?>">
    <input type="number" name="stock" placeholder="Stock inicial" min="0" step="1" required value="<?= isset($_POST['stock']) ? htmlspecialchars($_POST['stock']) : '' ?>">
    <button type="submit">Agregar</button>
  </form>
  <a href="admin.php">&laquo; Volver al Panel</a>
</div>
</body>
</html>
