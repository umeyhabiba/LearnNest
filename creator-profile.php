<?php
require 'db.php';
session_start();

// ✅ Ensure user is logged in as creator
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'creator') {
    header("Location: signin.php");
    exit;
}

$userId = $_SESSION['user_id'];

// ✅ Fetch creator (instructor) data
$stmt = $pdo->prepare("SELECT * FROM instructors WHERE id = ?");
$stmt->execute([$userId]);
$creator = $stmt->fetch();

$fullName = isset($creator['FirstName'], $creator['LastName']) 
    ? $creator['FirstName'] . ' ' . $creator['LastName'] 
    : 'Unknown';

$email = $creator['Email'] ?? 'noemail@example.com';

// ✅ Correct path for profile picture
$avatar = (!empty($creator['Image'])) 
    ? 'imageuploade/' . htmlspecialchars($creator['Image']) 
    : 'imageuploade/default.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Creator Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="creator-profile.css" />
  <style>
    .profile-pic {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0px 0px 8px rgba(0,0,0,0.2);
    }
  </style>
</head>
<body>

  <div class="container py-5" id="creatorProfile">
    <!-- Profile Card -->
    <div class="card profile-card text-center shadow-sm mb-5">
      <div class="card-body">
        <img src="<?= $avatar ?>" alt="Creator" class="profile-pic mb-3">
        <h3><?= htmlspecialchars($fullName) ?></h3>
        <p class="text-muted"><?= htmlspecialchars($email) ?></p>

        <!-- Buttons -->
        <a href="edit-profile-creator.php" class="btn btn-outline-secondary me-2">Edit Profile</a>
        <a href="create-course.php" class="btn btn-primary">Create New Course</a>
      </div>
    </div>

    <!-- Uploaded Courses Section -->
    <div class="card shadow-sm mb-5">
      <div class="card-body">
        <h4 class="mb-4">Uploaded Courses</h4>
        <ul class="list-group">
          <?php
          $stmt = $pdo->prepare("SELECT * FROM courses WHERE creator_id = ?");
          $stmt->execute([$userId]);
          $courses = $stmt->fetchAll();

          if ($courses):
              foreach ($courses as $course):
                  $enrollStmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
                  $enrollStmt->execute([$course['id']]);
                  $enrollCount = $enrollStmt->fetchColumn();
                  ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center fade-in uploaded-course">
                    <div>
                      <strong><?= htmlspecialchars($course['title']) ?></strong>
                      <div class="text-muted small"><?= htmlspecialchars($course['description']) ?></div>
                    </div>
                    <span class="badge bg-success rounded-pill"><?= $enrollCount ?> Enrolled</span>
                  </li>
          <?php
              endforeach;
          else:
              echo "<li class='list-group-item text-muted'>No courses uploaded yet.</li>";
          endif;
          ?>
        </ul>
      </div>
    </div>

    <!-- Stats Section -->
    <div class="row g-4">
      <div class="col-md-6">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Total Enrollments</h5>
            <p class="display-6" id="enrollmentCount">0</p>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card text-center shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Average Feedback</h5>
            <p class="display-6" id="averageFeedback">4.5 ★</p>
          </div>
        </div>
      </div>
    </div>
  </div>

<script src="creator-profile.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".uploaded-course").forEach((item, index) => {
        setTimeout(() => item.classList.add("visible"), index * 200);
    });
});
</script>
</body>
</html>
