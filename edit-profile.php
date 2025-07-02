<?php
require 'db.php';
session_start();

// ✅ Just read the session
$userId = $_SESSION['user_id'] ?? null;
$userType = $_SESSION['user_type'] ?? 'student';

// Load data from correct table
$table = ($userType === 'creator') ? 'creators' : 'students';

$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$userId]);
$data = $stmt->fetch();

$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$avatar = !empty($data['avatar']) ? $data['avatar'] : 'uploads/avatars/default.png';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-5">
    <h2 class="mb-4">Edit Profile</h2>

    <form action="update-profile.php" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">

      <div class="mb-3">
        <label for="name" class="form-label">Full Name:</label>
        <input type="text" class="form-control" name="name" id="name" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" name="email" id="email" required>
      </div>

      <div class="mb-3">
        <label for="avatar" class="form-label">Profile Picture:</label>
        <input type="file" class="form-control" name="avatar" id="avatar" accept="image/*">
      </div>

      <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
  </div>
</body>
</html>
