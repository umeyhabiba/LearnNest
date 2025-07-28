<?php
$host = 'localhost';
$dbname = 'learnnest';
$username = 'root';
$password = '';

// Create both connections
try {
    // PDO connection (for files using PDO)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // mysqli connection (for files using mysqli)
    $conn = mysqli_connect($host, $username, $password, $dbname);
    mysqli_set_charset($conn, 'utf8mb4');
    
    if (!$conn) {
        throw new Exception("MySQLi connection failed: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    // Return JSON error if request expects JSON
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        die(json_encode(['success' => false, 'message' => 'Database connection failed']));
    }
    // Otherwise return plain text error
    die("Database connection failed: " . $e->getMessage());
}
?>