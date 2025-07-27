<?php
require 'db.php';
session_start();

// Make sure user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'creator') {
    header("Location: signin.php");
    exit;
}

$userId = $_SESSION['user_id'];

// --- Split Full Name into FirstName and LastName ---
$fullName = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';

$nameParts = explode(' ', trim($fullName), 2);
$firstName = $nameParts[0] ?? '';
$lastName = $nameParts[1] ?? '';

$avatarPath = null;

// --- Handle avatar upload ---
if (!empty($_FILES['avatar']['name'])) {
    $uploadDir = 'imageuploade/';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $avatarPath = $fileName; // ✅ Only save file name (not full path)
    }
}

// --- Update instructors table ---
if ($avatarPath) {
    $stmt = $pdo->prepare("
        UPDATE instructors 
        SET FirstName = ?, LastName = ?, Email = ?, Image = ? 
        WHERE id = ?
    ");
    $stmt->execute([$firstName, $lastName, $email, $avatarPath, $userId]);
} else {
    $stmt = $pdo->prepare("
        UPDATE instructors 
        SET FirstName = ?, LastName = ?, Email = ? 
        WHERE id = ?
    ");
    $stmt->execute([$firstName, $lastName, $email, $userId]);
}

// --- Redirect back to creator profile ---
header("Location: creator-profile.php");
exit;
?>
