<?php
require 'db.php';
session_start();

$_SESSION['user_id'] = 1;
$_SESSION['user_type'] = 'creator';

$userId = $_SESSION['user_id'] ?? 1;

$stmt = $pdo->prepare("SELECT * FROM creators WHERE id = ?");
$stmt->execute([$userId]);
$creator = $stmt->fetch();

$name = $creator['name'] ?? 'Unknown';
$email = $creator['email'] ?? 'noemail@example.com';
$avatar = !empty($creator['avatar']) ? $creator['avatar'] : 'uploads/avatars/default.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Creator Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="creator-profile.css" />
</head>
<body>

  <div class="container py-5" id="creatorProfile">

    <!-- Profile Card -->
    <div class="card profile-card text-center shadow-sm mb-5">
      <div class="card-body">
        <img src="<?= htmlspecialchars($avatar) ?>" alt="Creator" class="rounded-circle profile-pic mb-3" width="120" height="120">
        <h3 id="creatorName"><?= htmlspecialchars($name) ?></h3>
        <p id="creatorEmail" class="text-muted"><?= htmlspecialchars($email) ?></p>

        <!-- ✅ Two separate buttons -->
        <a href="edit-profile-creator.php" class="btn btn-outline-secondary me-2">Edit Profile</a>
        <a href="create-course.php" class="btn btn-primary">Create New Course</a>
      </div>
    </div>

    <!-- Uploaded Courses Section -->
   <!-- Uploaded Courses Section -->
<!-- Uploaded Courses Section -->
<div class="card shadow-sm mb-5">
  <div class="card-body">
    <h4 class="mb-4">Uploaded Courses</h4>
    <ul class="list-group">
      <?php
        $stmt = $pdo->prepare("SELECT * FROM courses WHERE creator_id = ?");
        $stmt->execute([$userId]);
        $courses = $stmt->fetchAll();

        if ($courses && count($courses) > 0):
          foreach ($courses as $course):

            // Get real number of enrolled students for this course
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
            <p class="display-6" id="enrollmentCount">4</p>
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
    const items = document.querySelectorAll(".uploaded-course");
    items.forEach((item, index) => {
      setTimeout(() => {
        item.classList.add("visible");
      }, index * 200); // stagger appearance
    });
  });
</script>

</body>
</html>

