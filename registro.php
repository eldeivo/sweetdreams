<?php
session_start();
require 'conexion.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);

    // Validar campos obligatorios
    if (!$nombre || !$correo || !$password || !$password_confirm) {
        $error = "Por favor completa todos los campos.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo no válido.";
    } elseif ($password !== $password_confirm) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si correo ya existe
        $stmt = $conn->prepare("SELECT id_cliente FROM clientes WHERE correo = ?");
        $stmt->execute([$correo]);
        if ($stmt->fetch()) {
            $error = "El correo ya está registrado.";
        } else {
            // Insertar nuevo cliente con contraseña hasheada
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO clientes (nombre, correo, contraseña, saldo) VALUES (?, ?, ?, 0.00)");
            $resultado = $stmt->execute([$nombre, $correo, $hash]);

            if ($resultado) {
                $success = "Registro exitoso. Ahora puedes iniciar sesión.";
            } else {
                $error = "Error al registrar usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Registro - Sweet Dreams</title>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f06292, #a2e1ef, #bb82cb);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0;
    }

    .registro-container {
        background: white;
        padding: 40px 50px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(107, 76, 123, 0.2);
        width: 320px;
        text-align: center;
    }

    h2 {
        margin-bottom: 25px;
        color: #6b4c7b;
    }

    input[type="text"], input[type="email"], input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin: 12px 0 20px;
        border: 2px solid #d8b0d9;
        border-radius: 12px;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s;
    }

    input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
        border-color: #f06292;
    }

    button {
        background: #f06292;
        color: white;
        padding: 15px 30px;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background 0.3s;
    }

    button:hover {
        background: #d81b60;
    }

    .error {
        color: #d81b60;
        margin-bottom: 15px;
    }

    .success {
        color: #4caf50;
        margin-bottom: 15px;
    }

    .link-login {
        margin-top: 15px;
        font-size: 0.9rem;
    }

    .link-login a {
        color: #a64ca6;
        text-decoration: none;
    }

    .link-login a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="registro-container">
    <h2>Registro de Usuario</h2>

    <?php if ($error): ?>
        <div class="error"><?=htmlspecialchars($error)?></div>
    <?php elseif ($success): ?>
        <div class="success"><?=htmlspecialchars($success)?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="text" name="nombre" placeholder="Nombre completo" required />
        <input type="email" name="correo" placeholder="Correo electrónico" required />
        <input type="password" name="password" placeholder="Contraseña" required />
        <input type="password" name="password_confirm" placeholder="Confirmar contraseña" required />
        <button type="submit">Registrar</button>
    </form>

    <div class="link-login">
        ¿Ya tienes cuenta? <a href="index.php">Iniciar sesión</a>
    </div>
</div>

</body>
</html>
