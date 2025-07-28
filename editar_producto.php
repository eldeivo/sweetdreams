<?php
session_start();
require 'conexion.php';

if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] != 1) {
    header('Location: menuprincipal.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin.php');
    exit;
}

$id_producto = $_GET["id"] ?? null;
if (!$id_producto) {
    echo "ID de producto no válido.";
    exit;
}

// Para mostrar mensajes
$mensaje = "";
$error = "";

// Obtiene valores
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $precio = floatval($_POST["precio"]);
    $stock = intval($_POST["stock"]);

    try {
        $stmt = $conn->prepare("CALL admin_producto(?, ?, ?, ?, ?)");
        $stmt->execute([1, $id_producto, $nombre, $precio, $stock]);

        do {
            if ($stmt->columnCount()) {
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($resultado) {
                    if (isset($resultado['mensaje'])) {
                        $mensaje = $resultado['mensaje'];
                        $stmt2 = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
                        $stmt2->execute([$id_producto]);
                        $producto = $stmt2->fetch();
                    } elseif (isset($resultado['error'])) {
                        $error = $resultado['error'];
                    }
                }
            }
        } while ($stmt->nextRowset());
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }

} else {
    // Carga los datos actuales en el form
    $stmt = $conn->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->execute([$id_producto]);
    $producto = $stmt->fetch();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Editar Producto</title>
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
    <h1>Editar producto</h1>

    <?php if (!empty($mensaje)): ?>
        <p class="mensaje"><?php echo htmlspecialchars($mensaje); ?></p>
    <?php elseif (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (!empty($producto)): ?>
        <form method="post">
            <label>Nombre:
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" >
            </label>
            <label>Precio:
                <input type="number" step=".5" name="precio" value="<?php echo $producto['precio']; ?>" >
            </label>
            <label>Añadir stock:
                <input type="number" step="1" name="stock" value="0" min="0">
            </label>
            <button type="submit">Guardar cambios</button>
        </form>
    <?php elseif (empty($mensaje) && empty($error)): ?>
        <p class="error">Producto no encontrado.</p>
    <?php endif; ?>
    <p><a href="admin.php">← Volver</a></p>
    </div>
</body>
</html>
