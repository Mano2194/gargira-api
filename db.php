<?php
// db.php - Database connection script
$host = 'localhost';
$db = 'gargira';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$images_folder_dir = $_SERVER['DOCUMENT_ROOT'] . "/images/";
$images_folder_path = $host . "/images/";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}
?>