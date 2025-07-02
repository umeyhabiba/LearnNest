<?php
require 'db.php';
session_start();

// ✅ Hardcode userType for this page — for creators only
$userId = $_SESSION['user_id'] ?? null;
$userType = 'creator'; // Fixed for this page

// Load from 'creators' table only
$table = 'creators';

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
  <title>Edit Creator Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-5">
    <h2 class="mb-4">Edit Creator Profile</h2>

    <form action="update-profile-creator.php" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">

      <div class="mb-3 text-center">
        <img src="<?= htmlspecialchars($avatar) ?>" alt="Profile Picture" width="100" height="100" class="rounded-circle mb-2">
      </div>

      <div class="mb-3">
        <label for="name" class="form-label">Full Name:</label>
        <input type="text" class="form-control" name="name" id="name" required value="<?= htmlspecialchars($name) ?>">
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" name="email" id="email" required value="<?= htmlspecialchars($email) ?>">
      </div>

      <div class="mb-3">
        <label for="avatar" class="form-label">Profile Picture:</label>
        <input type="file" class="form-control" name="avatar" id="avatar" accept="image/*">
      </div>

      <button type="submit" class="btn btn-primary w-100">Save Profile</button>
    </form>
  </div>
</body>
</html>
