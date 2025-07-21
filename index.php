<?php
session_start();
require 'conexion.php';  // conexión a la base de datos

// Si ya está logueado, redirigir
if (isset($_SESSION['id_cliente'])) {
    if ($_SESSION['id_cliente'] == 1) {
        header('Location: admin.php');
        exit;
    } else {
        header('Location: productos.php');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    if (empty($correo) || empty($password)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        // Consultar en la base de datos sin cifrado
        $stmt = $conn->prepare("SELECT id_cliente, nombre, correo, contraseña FROM clientes WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $password === $usuario['contraseña']) {  // comparación simple
            // Iniciar sesión
            $_SESSION['id_cliente'] = $usuario['id_cliente'];
            $_SESSION['nombre'] = $usuario['nombre'];

            if ($usuario['id_cliente'] == 1) {
                header('Location: admin.php');
            } else {
                header('Location: productos.php');
            }
            exit;
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8" />
<title>Iniciar Sesión - Sweet Dreams</title>
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
    .login-container {
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
    input[type="email"], input[type="password"] {
        width: 100%;
        padding: 12px 15px;
        margin: 12px 0 20px;
        border: 2px solid #d8b0d9;
        border-radius: 12px;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s;
    }
    input[type="email"]:focus, input[type="password"]:focus {
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
    .link-register {
        margin-top: 15px;
        font-size: 0.9rem;
    }
    .link-register a {
        color: #a64ca6;
        text-decoration: none;
    }
    .link-register a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="login-container">
    <h2>Iniciar Sesión</h2>
    <?php if($error): ?>
        <div class="error"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="email" name="correo" placeholder="Correo electrónico" required />
        <input type="password" name="password" placeholder="Contraseña" required />
        <button type="submit">Entrar</button>
    </form>
    <div class="link-register">
        ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
    </div>
</div>

</body>
</html>
