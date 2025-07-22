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
            
            $stmt = $conn->prepare("INSERT INTO clientes (nombre, correo, contraseña, saldo) VALUES (?, ?, ?, 0.00)");
            $resultado = $stmt->execute([$nombre, $correo, $password]);


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
<link rel="stylesheet" href="styles.css">
</head>
<body class="centrado">

<div class="login-container">

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
        ¿Ya tienes cuenta? <a href="iniciarsesion.php">Iniciar sesión</a>
    </div>
</div>

</body>
</html>
