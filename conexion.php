<?php
$host = 'localhost';
$db   = 'sweetdreams';
$user = 'root';
$pass = '12345678';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [                            
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
    $msg = "Conexión a la base de datos exitosa.";
    $tipo = "exito";
} catch (PDOException $e) {
    $msg = "Error al conectar con la base de datos: " . htmlspecialchars($e->getMessage());
    $tipo = "error";
    // Si quieres detener la ejecución si hay error, descomenta la siguiente línea:
    // exit;
}
?>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const msg = <?php echo json_encode($msg); ?>;
    const tipo = <?php echo json_encode($tipo); ?>;

    const toast = document.createElement('div');
    toast.textContent = msg;

    // Estilos inline para evitar interferencias con CSS global
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.left = '50%';
    toast.style.transform = 'translateX(-50%)';
    toast.style.padding = '15px 25px';
    toast.style.borderRadius = '12px';
    toast.style.fontSize = '1rem';
    toast.style.fontWeight = '600';
    toast.style.zIndex = '9999';
    toast.style.boxShadow = '0 8px 20px rgba(0,0,0,0.15)';
    toast.style.cursor = 'default';
    toast.style.transition = 'opacity 0.5s ease';
    toast.style.opacity = '1';

    if (tipo === 'exito') {
        toast.style.backgroundColor = '#d4edda';
        toast.style.color = '#155724';
        toast.style.border = '1px solid #c3e6cb';
    } else {
        toast.style.backgroundColor = '#f8d7da';
        toast.style.color = '#721c24';
        toast.style.border = '1px solid #f5c6cb';
    }

    document.body.appendChild(toast);

    // Desaparece luego de 3 segundos
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 3000);
});
</script>
