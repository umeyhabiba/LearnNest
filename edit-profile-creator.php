<?php
require 'db.php';
session_start();

// ✅ Ensure user is logged in and is a creator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'creator') {
    header("Location: signin.php");
    exit;
}

$userId = $_SESSION['user_id'];

// ✅ Fetch data from instructors table
$stmt = $pdo->prepare("SELECT * FROM instructors WHERE id = ?");
$stmt->execute([$userId]);
$data = $stmt->fetch();

// ✅ Build full name from FirstName + LastName
$fullName = isset($data['FirstName'], $data['LastName']) 
    ? $data['FirstName'] . ' ' . $data['LastName'] 
    : '';

$email = $data['Email'] ?? '';
$avatar = !empty($data['Image']) ? 'imageuploade/' . $data['Image'] : 'imageuploade/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Creator Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .profile-pic {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 2px solid #ddd;
        box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h2 class="mb-4 text-center">Edit Creator Profile</h2>

    <form action="update-profile-creator.php" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">

      <!-- Profile Picture Preview -->
      <div class="mb-3 text-center">
        <img src="<?= htmlspecialchars($avatar) ?>" alt="Profile Picture" class="profile-pic mb-3">
      </div>

      <div class="mb-3">
        <label for="name" class="form-label">Full Name:</label>
        <input type="text" class="form-control" name="name" id="name" 
               value="<?= htmlspecialchars($fullName) ?>" required>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" name="email" id="email" 
               value="<?= htmlspecialchars($email) ?>" required>
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
