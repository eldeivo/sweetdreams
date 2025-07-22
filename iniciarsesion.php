<?php
session_start();
require 'conexion.php';  // conexión a la base de datos

// Si ya hay sesión iniciada, mostrar mensaje en lugar de redirigir
if (isset($_SESSION['id_cliente'])) {
    $nombre = htmlspecialchars($_SESSION['nombre']);
    $destino = ($_SESSION['id_cliente'] == 1) ? 'admin.php' : 'productos.php';
    $rol = ($_SESSION['id_cliente'] == 1) ? 'Administrador' : 'Cliente';

    echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Ya estás logueado</title>
    <link rel='stylesheet' href='styles.css'>
    <style>
        /* Anula los estilos globales problemáticos */
        body {
            display: block !important;  /* Anula el flex global */
            height: auto !important;    /* Anula height 100vh global */
            background: linear-gradient(135deg, #f06292, #a2e1ef, #bb82cb);
            padding: 40px;
            font-family: 'Poppins', sans-serif;
        }
        .mensaje {
            max-width: 450px;
            margin: 50px auto;
            background: white;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            text-align: center;
        }
        .mensaje h2 {
            color: #6b4c7b;
        }
        .mensaje a {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            background: #f06292;
            color: white;
            border-radius: 10px;
            text-decoration: none;
            transition: background 0.3s ease;
        }
        .mensaje a:hover {
            background: #d81b60;
        }
    </style>
</head>
<body>

    <div class='mensaje'>
        <h2>Hola, $nombre 👋</h2>
        <p>Ya has iniciado sesión como <strong>$rol</strong>.</p>
        <a href='$destino'>Ir al panel</a>
        <a href='cerrarsesion.php'>Cerrar sesión</a>
    </div>
</body>
</html>";
    exit;
}

// ---------------- Lógica de login ----------------
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo']);
    $password = trim($_POST['password']);

    if (empty($correo) || empty($password)) {
        $error = "Por favor, completa todos los campos.";
    } else {
        $stmt = $conn->prepare("SELECT id_cliente, nombre, correo, contraseña FROM clientes WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $password === $usuario['contraseña']) {

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
<title>Iniciar Sesión</title>
<link rel="stylesheet" href="styles.css">
</head>
<body class="centrado">

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
