<?php
session_start();
require 'conexion.php';

// Verificar que esté logueado y no sea admin
if (!isset($_SESSION['id_cliente']) || $_SESSION['id_cliente'] == 1) {
    header('Location: menuprincipal.php');
    exit;
}

$id_cliente = $_SESSION['id_cliente'];
$error = '';
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $monto = floatval($_POST['monto']);

    if (empty($correo) || empty($password) || $monto <= 0) {
        $error = "Por favor completa todos los campos correctamente.";
    } else {
        // Validar usuario y contraseña
        $stmt = $conn->prepare("SELECT id_cliente, contraseña, saldo FROM clientes WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            $error = "Correo no encontrado.";
        } elseif ($usuario['id_cliente'] != $id_cliente) {
            $error = "El correo no coincide con tu cuenta.";
        } elseif ($password !== $usuario['contraseña']) {  // aquí comparación simple, si usas hash cambia lógica
            $error = "Contraseña incorrecta.";
        } else {
            // Actualizar saldo
            $nuevo_saldo = $usuario['saldo'] + $monto;
            $stmt = $conn->prepare("UPDATE clientes SET saldo = ? WHERE id_cliente = ?");
            $stmt->execute([$nuevo_saldo, $id_cliente]);

            $mensaje = "Saldo recargado exitosamente. Nuevo saldo: $" . number_format($nuevo_saldo, 2);
            
            
          
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Recargar Saldo</title>
<style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(90deg, #ffffff, #f4c4e1);
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
      max-width: 600px;
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

    input[type="email"],
    input[type="password"],
    input[type="number"] {
      padding: 12px 15px;
      font-size: 1rem;
      border: 2px solid #d8b0d9;
      border-radius: 12px;
      outline: none;
      transition: border-color 0.3s;
      width: 100%;
    }

    input[type="email"]:focus,
    input[type="password"]:focus,
    input[type="number"]:focus {
      border-color: #f06292;
    }

    button {
      background: #f06292;
      color: white;
      border: none;
      padding: 15px 30px;
      border-radius: 25px;
      font-size: 1.1rem;
      cursor: pointer;
      transition: background 0.3s ease;
      width: 100%;
    }

    button:hover {
      background: #d81b60;
    }

    .mensaje {
      margin-top: 20px;
      font-weight: bold;
      color: #4caf50;
    }

    .error {
      margin-top: 20px;
      font-weight: bold;
      color: #d81b60;
    }
    .boton-contenedor {
  text-align: center;
  margin-top: 30px;
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
}

.boton:hover {
  background-color: #ab52c4;
}

</style>
</head>
<body>

<div class="contenedor-principal">
  <h1>Recargar Saldo</h1>

  <?php if ($mensaje): ?>
    <div class="mensaje"><?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="">
    <input type="email" name="correo" placeholder="Tu correo electrónico" required />
    <input type="password" name="password" placeholder="Tu contraseña" required />
    <input type="number" name="monto" placeholder="Monto a recargar" step="0.01" min="1" required />
    <button type="submit">Recargar</button>
     <a href="productos.php" class="boton">Seguir comprando</a>
     
  </form>
</div>

</body>
</html>
