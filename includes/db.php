<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cargar configuración desde .env de forma segura
$env_path = __DIR__ . '/../.env';
$env = [];
if (file_exists($env_path)) {
    $env = parse_ini_file($env_path);
}

$host = $env['DB_HOST'] ?? 'localhost';
$dbname = $env['DB_NAME'] ?? 'academy_db';
$username = $env['DB_USER'] ?? 'root';
$password = $env['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<h3>Error de conexión a la base de datos:</h3>";
    echo "Por favor, revisa tu archivo <strong>.env</strong> y asegúrate de que la base de datos <strong>$dbname</strong> exista.";
    echo "<br>Error: " . $e->getMessage();
    exit;
}
?>
