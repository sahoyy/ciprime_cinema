 <?php
$host = 'sql107.hstn.me';
$db   = 'mseet_38830455_ciprime';
$user = 'mseet_38830455';
$pass = 'Project20DB';
$charset = 'latin1'; // << UBAH DARI utf8mb4 ke latin1

// DSN format
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
