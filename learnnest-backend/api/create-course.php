<?php
header("Content-Type: application/json");

require_once("../includes/db.php");

// Read POST data - add price field
$name        = $_POST['name'] ?? '';
$description = $_POST['description'] ?? '';
$duration    = $_POST['duration'] ?? '';
$price       = $_POST['price'] ?? 0.00;  // New field
$students    = $_POST['students'] ?? 0;
$status      = $_POST['status'] ?? 'Draft';

// Add price to validation
if (empty($name) || empty($description) || empty($duration) || empty($status)) {
    echo json_encode([
        "success" => false,
        "message" => "Missing required fields!"
    ]);
    exit;
}

try {
    // Update the query to include price
    $stmt = $pdo->prepare("INSERT INTO courses (name, description, duration, price, students, status)
                           VALUES (:name, :description, :duration, :price, :students, :status)");

    $stmt->execute([
        ":name"        => $name,
        ":description" => $description,
        ":duration"    => $duration,
        ":price"       => (float)$price,  // New field
        ":students"    => (int)$students,
        ":status"      => $status
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Course created successfully!"
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database Error: " . $e->getMessage()
    ]);
}