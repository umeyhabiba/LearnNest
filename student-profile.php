<?php
require 'db.php';
session_start();

// ✅ Ensure user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: signin.php");
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$userId]);
$student = $stmt->fetch();

// ✅ Adjusted for new database column names
$name = isset($student['FirstName'], $student['LastName']) 
        ? $student['FirstName'] . ' ' . $student['LastName'] 
        : 'Unknown';

$email = $student['Email'] ?? 'noemail@example.com';

// ✅ Correct image folder path (now points to imageuploade/)
$avatar = !empty($student['Image']) 
    ? 'imageuploade/' . $student['Image'] 
    : 'imageuploade/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="student-profile.css" />
</head>
<style>
  .profile-pic {
    width: 150px;         /* fixed width */
    height: 150px;        /* fixed height */
    object-fit: cover;    /* crop image without stretching */
    border-radius: 50%;   /* make it circular */
    border: 3px solid #fff; /* optional border */
    box-shadow: 0px 0px 8px rgba(0,0,0,0.2);
}
@media (max-width: 576px) {
    .profile-pic {
        width: 100px;
        height: 100px;
    }
}


</style>
<body>

  <div class="container py-5" id="studentProfile">

    <!-- Profile Card -->
    <div class="card profile-card text-center shadow-sm mb-5">
      <div class="card-body">
        <img src="<?= htmlspecialchars($avatar) ?>" alt="Profile Picture" class="rounded-circle profile-pic">
        <h3 id="studentName"><?= htmlspecialchars($name) ?></h3>
        <p id="studentEmail" class="text-muted"><?= htmlspecialchars($email) ?></p>
        <a href="edit-profile.php" class="btn btn-outline-primary mt-2">Edit Profile</a>
      </div>
    </div>

    <!-- Enrolled Courses -->
    <div class="card shadow-sm mb-5">
      <div class="card-body">
        <h4 class="mb-4 text-center">Enrolled Courses</h4>
        <div class="row justify-content-center">
          <div class="col-md-10">
            <ul class="list-group" id="enrolledCourses">
              <!-- JS will inject course blocks here -->
            </ul>
          </div>
        </div>
      </div>
    </div>

    <!-- Notes & Certificates -->
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title"><a href="#">MY Notes</a></h5>
            <p class="card-text text-muted">This section will display your saved lecture notes.</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">My Certificates</h5>
            <p class="card-text text-muted">This section will display your earned certificates.</p>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script src="student-profile.js"></script>
</body>
</html>
