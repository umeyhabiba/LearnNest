<?php
require 'db.php';

// Handle AJAX request for posts
if (isset($_GET['ajax']) && $_GET['ajax'] === 'posts') {
    header('Content-Type: application/json');
    $sql = "SELECT id, title, tag, is_anonymous, created_at, upvotes, file_path FROM posts ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    echo json_encode(['success' => true, 'posts' => $posts]);
    $conn->close();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LearnNest - Course Forum</title>
  <!-- Google Fonts for modern look -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet"/>
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"/>
  <link rel="stylesheet" href="styles.css"/>
</head>
<body>
  <!-- Header / Navigation Bar -->
  <header class="navbar">
    <div class="navbar-title">LearnNest</div>
    <nav>
      <a href="#">Home</a>
      <a href="#">Courses</a>
      <a href="#">Profile</a>
    </nav>
  </header>

  <main class="container">
    <h1>Question and Query Forum</h1>
    <!-- Search Bar -->
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search questions..."/>
      <button><i class="fas fa-search"></i></button>
    </div>

    <!-- Tag Filter Section -->
    <div class="tag-filters">
  <button class="tag active" data-tag="All">All</button>
  <button class="tag" data-tag="Assignment">Assignment Help</button>
  <button class="tag" data-tag="Technical Issue">Technical Issues</button>
  <button class="tag" data-tag="general">General</button>
   <button class="tag" data-tag="lecture">Lecture</button>
</div>

    <!-- Forum Post Cards -->
    <section class="forum-list" id="posts">
      <?php
      // Direct PHP rendering of posts for fallback/debug
      $host = 'localhost';
      $user = 'root';
      $pass = '';
      $db   = 'learnnest_db';
      $conn = new mysqli($host, $user, $pass, $db);
      if ($conn->connect_error) {
          echo "<div style='color:red'>Database connection failed: " . $conn->connect_error . "</div>";
      } else {
          $sql = "SELECT id, title, tag, is_anonymous, created_at, file_path FROM posts ORDER BY created_at DESC";
          $result = $conn->query($sql);
          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  echo '<div class="forum-card">';
                  echo '<div class="forum-card-title">' . htmlspecialchars($row['title']) . '</div>';
                  echo '<div class="forum-card-tags"><span class="tag">' . htmlspecialchars($row['tag'] ?: 'General') . '</span></div>';
                  echo '<div class="forum-card-meta">';
                  echo '<span><i class="fas fa-clock"></i> ' . htmlspecialchars($row['created_at']) . '</span>';
                  echo '</div>';
                  if (!empty($row['file_path'])) {
                      echo '<div class="post-file"><a href="' . htmlspecialchars($row['file_path']) . '" target="_blank" download><i class="fas fa-paperclip"></i> Download Attachment</a></div>';
                  }
                  echo '<a href="post-thread.php?post_id=' . $row['id'] . '" class="view-btn" style="margin: 0.5em auto 0 auto; display: block;"><i class="fas fa-eye"></i> View</a>';
                  echo '</div>';
              }
          } else {
              echo "<div>No posts found.</div>";
          }
          $conn->close();
      }
      ?>
      <!-- Example Post Card -->
     <!-- <div class="forum-card">
        <div class="forum-card-title">How do I submit Assignment 2?</div>
        <div class="forum-card-tags">
          <span class="tag">Assignment Help</span>
        </div>
        <div class="forum-card-meta">
          <span><i class="fas fa-comments"></i> 5 replies</span>
          <span><i class="fas fa-clock"></i> 2 hours ago</span>
        </div>
      </div>
      <div class="forum-card">
        <div class="forum-card-title">Quiz 1: Question 3 clarification</div>
        <div class="forum-card-tags">
          <span class="tag">Quiz</span>
        </div>
        <div class="forum-card-meta">
          <span><i class="fas fa-comments"></i> 2 replies</span>
          <span><i class="fas fa-clock"></i> 1 day ago</span>
        </div>
      </div>
      Add more cards as needed -->
    </section>

    <!-- Floating Ask a Question Button -->
    <a href="new-question.PHP" class="fab" title="Ask a Question">
      <i class="fas fa-plus"></i>
    </a>
  </main>
</body>
</html>

<script>
let allPosts = [];

async function loadPosts() {
  const response = await fetch('forum-home.php?ajax=posts');
  const data = await response.json();
  allPosts = data.posts;
  renderPosts(allPosts);
}

function renderPosts(posts) {
  const postsDiv = document.getElementById('posts');
  postsDiv.innerHTML = '';
  posts.forEach(post => {
    postsDiv.innerHTML += `
      <div class="forum-card">
        <div class="forum-card-title">${post.title}</div>
        <div class="forum-card-tags">
          <span class="tag">${post.tag || 'General'}</span>
        </div>
        <div class="forum-card-meta">
          <span><i class="fas fa-clock"></i> ${post.created_at}</span>
        </div>
        <button onclick="viewPost(${post.id})" class="view-btn" style="margin: 0.5em auto 0 auto; display: block;">
          <i class="fas fa-eye"></i> View
        </button>
      </div>
    `;
  });
}

function viewPost(postId) {
  window.location.href = `post-thread.html?post_id=${postId}`;
}

window.onload = loadPosts;

// SEARCH FUNCTIONALITY
document.getElementById('searchInput').addEventListener('input', function() {
  const query = this.value.toLowerCase();
  const filtered = allPosts.filter(post =>
    post.title.toLowerCase().includes(query) ||
    (post.tag && post.tag.toLowerCase().includes(query))
  );
  renderPosts(filtered);
});


// TAG FILTER FUNCTIONALITY
document.querySelectorAll('.tag-filters .tag').forEach(btn => {
  btn.addEventListener('click', function() {
    // Remove 'active' from all buttons
    document.querySelectorAll('.tag-filters .tag').forEach(b => b.classList.remove('active'));
    // Add 'active' to clicked button
    this.classList.add('active');
    const selectedTag = this.getAttribute('data-tag');
    filterAndRender(selectedTag, document.getElementById('searchInput').value);
  });
});

// Helper to filter by tag and search query together
function filterAndRender(selectedTag = "All", searchQuery = "") {
  let filtered = allPosts;
  if (selectedTag && selectedTag !== "All") {
    filtered = filtered.filter(post => {
      // Treat empty or missing tags as "general"
      const tag = post.tag ? post.tag.toLowerCase() : "general";
      return tag === selectedTag.toLowerCase();
    });
  }
  if (searchQuery) {
    const query = searchQuery.toLowerCase();
    filtered = filtered.filter(post =>
      post.title.toLowerCase().includes(query) ||
      ((post.tag || "General").toLowerCase().includes(query))
    );
  }
  renderPosts(filtered);
}
// Update search to work with tag filter
document.getElementById('searchInput').addEventListener('input', function() {
  const activeTag = document.querySelector('.tag-filters .tag.active').getAttribute('data-tag');
  filterAndRender(activeTag, this.value);
});
</script>