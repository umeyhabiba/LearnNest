<?php
session_start();
// $_SESSION['user_id'] = 1;
// $_SESSION['user_type'] = 'creator'; // for testing

if ($_SESSION['user_type'] !== 'creator') {
  echo "Access denied.";
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create New Course</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
  <style>
    body {
      background-color: #f9f9f9;
    }
    .form-container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
  </style>
</head>
<body>

  <div class="form-container">
    <h2 class="mb-4 text-center">Create New Course</h2>
    <form action="save-course.php" method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label for="title" class="form-label">Course Title</label>
        <input type="text" name="title" id="title" class="form-control" required />
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Course Description</label>
        <textarea name="description" id="description" rows="5" class="form-control" required></textarea>
      </div>
      <div class="mb-3">
        <label for="thumbnail" class="form-label">Course Thumbnail (optional)</label>
        <input type="file" name="thumbnail" id="thumbnail" class="form-control" accept="image/*" />
      </div>
      <button type="submit" class="btn btn-success w-100">Save Course</button>
    </form>
  </div>

</body>
</html>
