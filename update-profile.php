<?php
require 'db.php';
session_start();

$userType = $_SESSION['user_type'] ?? 'student'; // fallback to student
$userId = $_SESSION['user_id'] ?? 1;

$name = $_POST['name'];
$email = $_POST['email'];
$avatarPath = null;

// Handle avatar upload
if (!empty($_FILES['avatar']['name'])) {
    $uploadDir = 'uploads/avatars/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $avatarPath = $targetPath;
    }
}

// Use correct table based on user type
$table = ($userType === 'creator') ? 'creators' : 'students';

// Check if user already exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE id = ?");
$stmt->execute([$userId]);
$exists = $stmt->fetchColumn();

if ($exists) {
    // UPDATE
    if ($avatarPath) {
        $sql = "UPDATE $table SET name = ?, email = ?, avatar = ? WHERE id = ?";
        $params = [$name, $email, $avatarPath, $userId];
    } else {
        $sql = "UPDATE $table SET name = ?, email = ? WHERE id = ?";
        $params = [$name, $email, $userId];
    }
} else {
    // INSERT (if first time)
    if ($avatarPath) {
        $sql = "INSERT INTO $table (id, name, email, avatar) VALUES (?, ?, ?, ?)";
        $params = [$userId, $name, $email, $avatarPath];
    } else {
        $sql = "INSERT INTO $table (id, name, email) VALUES (?, ?, ?)";
        $params = [$userId, $name, $email];
    }
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// Redirect to correct profile page
if ($userType === 'creator') {
    header("Location: creator-profile.php");
} else {
    header("Location: student-profile.php");
}
exit;
?>
