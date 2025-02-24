<?php
// config.php
$host = 'localhost';
$db   = 'monitoring';
$user = 'your_db_user_name';    // <-- change this
$pass = 'your_db_password';    // <-- change this

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
