<?php
header("Content-Type: application/json");
require_once("../includes/db.php");

$input = json_decode(file_get_contents("php://input"), true);

if (!isset($input['id'], $input['name'], $input['description'], $input['duration'], 
    $input['price'], $input['students'], $input['status'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE courses SET 
        name = :name, 
        description = :description, 
        duration = :duration, 
        price = :price, 
        students = :students, 
        status = :status, 
        updated_at = NOW()
    WHERE id = :id");

    $stmt->execute([
        ':name' => $input['name'],
        ':description' => $input['description'],
        ':duration' => $input['duration'],
        ':price' => (float)$input['price'],
        ':students' => (int)$input['students'],
        ':status' => $input['status'],
        ':id' => (int)$input['id']
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Course updated successfully",
        "updated" => $stmt->rowCount()
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error" => $e->getMessage()
    ]);
}
?>
