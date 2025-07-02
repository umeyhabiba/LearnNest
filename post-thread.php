<?php
$host = 'localhost';
$user = 'root'; // your DB username
$pass = '';     // your DB password
$db   = 'learnnest_db';
$conn = new mysqli($host, $user, $pass, $db); // use default port 3306
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}
$conn->set_charset('utf8mb4');

// AJAX: Get post and comments
if (isset($_GET['ajax']) && $_GET['ajax'] === 'post') {
    header('Content-Type: application/json');
    $post_id = intval($_GET['post_id'] ?? 0);

    $post_sql = "SELECT id, title, description, tag, is_anonymous, created_at, file_path FROM posts WHERE id = ?";
    $post_stmt = $conn->prepare($post_sql);
    $post_stmt->bind_param("i", $post_id);
    $post_stmt->execute();
    $post_result = $post_stmt->get_result();
    $post = $post_result->fetch_assoc();

    $comments_sql = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at ASC";
    $comments_stmt = $conn->prepare($comments_sql);
    $comments_stmt->bind_param("i", $post_id);
    $comments_stmt->execute();
    $comments_result = $comments_stmt->get_result();

    $comments = [];
    while ($row = $comments_result->fetch_assoc()) {
        $comments[] = $row;
    }

    echo json_encode(['success' => true, 'post' => $post, 'comments' => $comments]);
    exit;
}

// AJAX: Post a comment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    header('Content-Type: application/json');
    $post_id = intval($_POST['post_id']);
    $content = $_POST['content'] ?? '';
    $is_anonymous = isset($_POST['is_anonymous']) && $_POST['is_anonymous'] == '1' ? 1 : 0;

    $stmt = $conn->prepare("INSERT INTO comments (post_id, content, is_anonymous) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $post_id, $content, $is_anonymous);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Comment posted!']);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $stmt->close();
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LearnNest - Post Thread</title>
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

  <main class="container" style="display: flex; flex-direction: column; align-items: center;">
    <div id="post" style="width: 100%; max-width: 600px;"></div>
    <div id="comments" style="width: 100%; max-width: 600px;"></div>
    <!-- Reply Input Box -->
    <!-- Remove the extra reply-form, only keep the comment-form for comments -->
    <form class="comment-form" style="width: 100%; max-width: 600px;">
      <textarea name="content" required></textarea>
      <label>
        <input type="checkbox" name="is_anonymous" /> Post Anonymously
      </label>
      <input type="hidden" name="post_id" value="" />
      <button type="submit">Submit Comment</button>
    </form>
  </main>
</body>
</html>


<script>
async function loadPost(postId) {
  const response = await fetch('get_post_with_comments.php?post_id=' + postId);
  const data = await response.json();
  const postDiv = document.getElementById('post');
  const commentsDiv = document.getElementById('comments');

  // Render the post as a styled card
  postDiv.innerHTML = `
    <div class="forum-card">
      <div class="forum-card-title">${data.post.is_anonymous == 1 ? 'Anonymous' : data.post.title}</div>
      <div class="post-description">${data.post.description}</div>
      <div class="forum-card-tags">
        <span class="tag">${data.post.tag || 'General'}</span>
      </div>
      <div class="forum-card-meta">
        <span><i class="fas fa-clock"></i> ${data.post.created_at}</span>
      </div>
       ${data.post.file_path ? `
      <div class="post-file">
        <a href="${data.post.file_path}" target="_blank" download>
          <i class="fas fa-paperclip"></i> Download Attachment
        </a>
      </div>
    ` : ''}
    </div>
  `;

  // Render comments
  commentsDiv.innerHTML = '<h3>Comments</h3>';
  data.comments.forEach(comment => {
    commentsDiv.innerHTML += `
      <div class="comment">
        <p><strong>${comment.is_anonymous == 1 ? 'Anonymous' : 'User'}:</strong> ${comment.content}</p>
        <small>${comment.created_at}</small>
      </div>
    `;
  });
}
</script>


<script>
// Helper to get post_id from URL
function getPostIdFromUrl() {
  const params = new URLSearchParams(window.location.search);
  return params.get('post_id');
}

// Set the post_id hidden input and load the post when DOM is ready
window.addEventListener('DOMContentLoaded', function() {
  const postId = getPostIdFromUrl();
  if (postId) {
    document.querySelector('input[name="post_id"]').value = postId;
    loadPost(postId);
  }
});

document.querySelector('.comment-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);
  formData.set('is_anonymous', form.querySelector('input[type="checkbox"]').checked ? 1 : 0);

  const response = await fetch('post_comment.php', {
    method: 'POST',
    body: formData
  });
  const result = await response.json();
  alert(result.message || result.error);
  if (result.success) {
    form.reset();
    // Reload comments after posting
    loadPost(formData.get('post_id'));
  }
});
</script>