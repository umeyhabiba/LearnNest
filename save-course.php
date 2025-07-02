<?php
require 'db.php';
session_start();

$creatorId = $_SESSION['user_id'] ?? null;
$userType = $_SESSION['user_type'] ?? '';

if (!$creatorId || $userType !== 'creator') {
  echo "Unauthorized access.";
  exit;
}

$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$thumbnailPath = null;

// Handle thumbnail upload if file was selected
if (!empty($_FILES['thumbnail']['name'])) {
  $uploadDir = 'uploads/thumbnails/';
  if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
  }

  $fileName = uniqid() . '_' . basename($_FILES['thumbnail']['name']);
  $targetPath = $uploadDir . $fileName;

  if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
    $thumbnailPath = $targetPath;
  }
}

// Insert course into database
$stmt = $pdo->prepare("INSERT INTO courses (creator_id, title, description, thumbnail) VALUES (?, ?, ?, ?)");
$stmt->execute([$creatorId, $title, $description, $thumbnailPath]);

// Redirect to Courseupl.html with success flag
header("Location: Courseupl.html?uploaded=1");
exit;
?>
