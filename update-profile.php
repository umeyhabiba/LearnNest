<?php
require 'db.php';
session_start();

$userType = $_SESSION['user_type'] ?? 'student';
$userId   = $_SESSION['user_id'] ?? 1;

// ✅ Get updated data
$firstName = $_POST['firstName'];
$lastName  = $_POST['lastName'];
$email     = $_POST['email'];
$avatarPath = null;

// ✅ Handle avatar upload (to imageuploade folder)
if (!empty($_FILES['avatar']['name'])) {
    $uploadDir = 'imageuploade/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
        $avatarPath = $fileName; // store only filename in DB
    }
}

// ✅ Select correct table
$table = ($userType === 'creator') ? 'creators' : 'students';

// ✅ Check if user already exists
$stmt = $pdo->prepare("SELECT COUNT(*) FROM $table WHERE id = ?");
$stmt->execute([$userId]);
$exists = $stmt->fetchColumn();

if ($exists) {
    // ✅ UPDATE
    if ($avatarPath) {
        $sql = "UPDATE $table SET FirstName = ?, LastName = ?, Email = ?, Image = ? WHERE id = ?";
        $params = [$firstName, $lastName, $email, $avatarPath, $userId];
    } else {
        $sql = "UPDATE $table SET FirstName = ?, LastName = ?, Email = ? WHERE id = ?";
        $params = [$firstName, $lastName, $email, $userId];
    }
} else {
    // ✅ INSERT (first time profile creation)
    if ($avatarPath) {
        $sql = "INSERT INTO $table (id, FirstName, LastName, Email, Image) VALUES (?, ?, ?, ?, ?)";
        $params = [$userId, $firstName, $lastName, $email, $avatarPath];
    } else {
        $sql = "INSERT INTO $table (id, FirstName, LastName, Email) VALUES (?, ?, ?, ?)";
        $params = [$userId, $firstName, $lastName, $email];
    }
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

// ✅ Redirect to correct profile page
if ($userType === 'creator') {
    header("Location: creator-profile.php");
} else {
    header("Location: student-profile.php");
}
exit;
?>
