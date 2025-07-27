<?php
require 'db.php';
session_start();

// ✅ Ensure user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: signin.php");
    exit;
}

$userId = $_SESSION['user_id'];

// ✅ Fetch student data
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$userId]);
$data = $stmt->fetch();

// ✅ Get existing values
$firstName = $data['FirstName'] ?? '';
$lastName  = $data['LastName'] ?? '';
$email     = $data['Email'] ?? '';
$avatar    = !empty($data['Image']) ? 'imageuploade/' . $data['Image'] : 'imageuploade/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .profile-pic-preview {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0px 0px 8px rgba(0,0,0,0.2);
        margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <h2 class="mb-4">Edit Profile</h2>

    <form action="update-profile.php" method="POST" enctype="multipart/form-data" class="shadow p-4 rounded bg-light">

      <!-- First Name -->
      <div class="mb-3">
        <label for="firstName" class="form-label">First Name:</label>
        <input type="text" class="form-control" name="firstName" id="firstName"
               value="<?= htmlspecialchars($firstName) ?>" required>
      </div>

      <!-- Last Name -->
      <div class="mb-3">
        <label for="lastName" class="form-label">Last Name:</label>
        <input type="text" class="form-control" name="lastName" id="lastName"
               value="<?= htmlspecialchars($lastName) ?>" required>
      </div>

      <!-- Email -->
      <div class="mb-3">
        <label for="email" class="form-label">Email:</label>
        <input type="email" class="form-control" name="email" id="email"
               value="<?= htmlspecialchars($email) ?>" required>
      </div>

      <!-- Current Profile Picture -->
      <div class="mb-3 text-center">
        <img src="<?= htmlspecialchars($avatar) ?>" alt="Current Avatar" class="profile-pic-preview">
      </div>

      <!-- Upload New Picture -->
      <div class="mb-3">
        <label for="avatar" class="form-label">Upload New Picture:</label>
        <input type="file" class="form-control" name="avatar" id="avatar" accept="image/*">
      </div>

      <button type="submit" class="btn btn-primary">Save Profile</button>
    </form>
  </div>
</body>
</html>
