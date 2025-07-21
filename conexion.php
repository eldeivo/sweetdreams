<?php
$host = 'localhost';        // usualmente localhost
$db   = 'sweetdreams';      // nombre de tu base de datos
$user = 'root';             // tu usuario de MySQL
$pass = '12345678';                 // tu contraseña de MySQL (vacío si no tienes)
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Para lanzar excepciones en errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Para obtener arreglos asociativos por defecto
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Para usar sentencias preparadas reales
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Si falla la conexión, muestra error amigable
    echo "Error de conexión a la base de datos.";
    // En desarrollo puedes mostrar más info así:
    // echo "Error: " . $e->getMessage();
    exit;
}
?>
