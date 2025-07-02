<?php
// Database connection for learnnest_db
$host = 'localhost';
$user = 'root'; // your DB username
$pass = '';
$db   = 'learnnest_db';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

if (isset($conn) && $conn) {
    $conn->set_charset('utf8mb4');
}

$success = false;
$error = '';

if (!isset($conn) || !$conn) {
    $error = 'Database connection failed. Please try again later.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $tag = $_POST['tag'] ?? '';
    $is_anonymous = (isset($_POST['is_anonymous']) && $_POST['is_anonymous'] == '1') ? 1 : 0;
    $file_path = null;

    // Handle file upload
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['file']['name']);
        $target = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
            $file_path = $target;
        }
    }

    // Debug: Check if table exists
    $check = $conn->query("SHOW TABLES LIKE 'posts'");
    if (!$check || $check->num_rows == 0) {
        $error = 'Error: The posts table does not exist in your database.';
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (title, description, tag, is_anonymous, file_path) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssis", $title, $description, $tag, $is_anonymous, $file_path);
            if ($stmt->execute()) {
                header('Location: forum-home.php?msg=question_posted');
                exit;
            } else {
                $error = 'SQL Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = 'Error preparing statement: ' . $conn->error;
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LearnNest - Ask a Question</title>
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <link rel="stylesheet" href="styles.css"/>
</head>
<body>
  <header class="navbar">
    <div class="navbar-title">LearnNest</div>
    <nav>
      <a href="#">Home</a>
      <a href="#">Courses</a>
      <a href="#">Profile</a>
    </nav>
  </header>

  <main class="container">
    <button onclick="window.location.href='forum-home.php'" class="back-btn" style="margin-bottom:1em;">
      <i class="fas fa-arrow-left"></i> Back to Forum
    </button>
    <h2>Ask a New Question</h2>
    <?php if ($error): ?>
      <div style="color: red; margin-bottom: 1em;"> <?= htmlspecialchars($error) ?> </div>
    <?php endif; ?>
    <form class="new-question-form" method="POST" enctype="multipart/form-data">
      <label>
        Title
        <input type="text" name="title" placeholder="Enter your question title" required />
      </label>
      <label>
        Details
        <textarea name="description" placeholder="Describe your question in detail..." rows="5" required></textarea>
      </label>
      <label>
        Tag
        <select name="tag">
          <option>Assignment</option>
          <option>Quiz</option>
          <option>Lecture</option>
          <option>Technical Issue</option>
        </select>
      </label>
      <label class="file-upload">
        <i class="fas fa-paperclip"></i> Attach File
        <input type="file" name="file" />
      </label>
      <label class="anon-toggle">
        <input type="checkbox" name="is_anonymous" value="1" />
        Post Anonymously
      </label>
      <button type="submit" class="submit-btn">Submit Question</button>
    </form>
  </main>
</body>
</html>
<!-- Improved error handling and table existence check -->