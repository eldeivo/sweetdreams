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

$msg = null;
$tipo = null;

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    $msg = "Error al conectar con la base de datos: " . htmlspecialchars($e->getMessage());
    $tipo = "error";
}
?>

<?php if ($msg !== null): ?>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const msg = <?php echo json_encode($msg); ?>;
    const tipo = <?php echo json_encode($tipo); ?>;

    const toast = document.createElement('div');
    toast.textContent = msg;

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

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 1500);
});
</script>
<?php endif; ?>
