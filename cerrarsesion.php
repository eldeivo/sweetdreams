<?php
session_start();

// Destruir todas las variables de sesión
$_SESSION = [];

// Si quieres destruir la sesión completamente, elimina la cookie de sesión también
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente destruir la sesión
session_destroy();

// Redirigir al login (o a la página que quieras)
header("Location: iniciarsesion.php");
exit;
