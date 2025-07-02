<?php
require 'db.php';
session_start();

$userId = $_SESSION['user_id'] ?? null;

// Make sure user is logged in
if (!$userId) {
  die("Unauthorized access.");
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$avatarPath = null;

// Handle avatar upload
if (!empty($_FILES['avatar']['name'])) {
  $uploadDir = 'uploads/avatars/';
  
  // Create the folder if it doesn't exist
  if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']);
  $targetPath = $uploadDir . $fileName;

  // Move the uploaded file
  if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
    $avatarPath = $targetPath;
  }
}

// Update creator table
if ($avatarPath) {
  $stmt = $pdo->prepare("UPDATE creators SET name = ?, email = ?, avatar = ? WHERE id = ?");
  $stmt->execute([$name, $email, $avatarPath, $userId]);
} else {
  $stmt = $pdo->prepare("UPDATE creators SET name = ?, email = ? WHERE id = ?");
  $stmt->execute([$name, $email, $userId]);
}

// Redirect back to creator profile
header("Location: creator-profile.php");
exit;
?>
