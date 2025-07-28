<?php
// 1. Authentication Check (Example - adapt to your system)
// session_start();
// if (!isset($_SESSION['user_id'])) {
//     http_response_code(401);
//     die(json_encode(["success" => false, "message" => "Unauthorized"]));
// }


// Ensure no output before headers
if (ob_get_level()) ob_clean();

// Set strict headers
header("Content-Type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

// Database connection
require_once("../includes/db.php");

// Error handler
set_exception_handler(function($e) {
    http_response_code(500);
    die(json_encode([
        "success" => false,
        "message" => "Server error",
        "error" => $e->getMessage() // Remove in production
    ]));
});

try {
    // Validate request
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        throw new Exception("Method Not Allowed");
    }

    // Get and validate ID
    if (!isset($_GET['id'])) {
        http_response_code(400);
        throw new Exception("Missing course ID");
    }

    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false) {
        http_response_code(400);
        throw new Exception("Invalid course ID format");
    }

    // Check if course exists
    $checkStmt = $pdo->prepare("SELECT id FROM courses WHERE id = ?");
    $checkStmt->execute([$id]);
    
    if ($checkStmt->rowCount() === 0) {
        http_response_code(404);
        throw new Exception("Course not found");
    }

    // Perform deletion
    $deleteStmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $deleteStmt->execute([$id]);

    if ($deleteStmt->rowCount() === 0) {
        throw new Exception("No rows deleted"); // Should never reach here due to prior check
    }

    // Success response
    echo json_encode([
        "success" => true,
        "message" => "Course deleted successfully",
        // "deleted_id" => $id
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Database error",
        "error_code" => $e->getCode()
    ]);
}
?>