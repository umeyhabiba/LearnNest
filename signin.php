<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign In - Learn_Nest</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <style>
    body {
      background: #f8f9fa;
    }
    .fade-in {
      animation: fadeIn 1s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

<!-- Navbar Start -->
<nav class="navbar navbar-expand-lg bg-white navbar-light shadow sticky-top p-0">
    <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
        <h2 class="m-0 text-primary"><i class="fa fa-book me-3"></i>LEARN_NEST</h2>
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
            <a href="index.html" class="nav-item nav-link">Home</a>
            <a href="about.html" class="nav-item nav-link">About</a>
            <a href="courses.html" class="nav-item nav-link">Courses</a>
            <a href="contact.html" class="nav-item nav-link">Contact</a>
        </div>
        
    </div>
</nav>
<!-- Navbar End -->

<?php
require 'db.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    // Check instructors first
    $stmt = $pdo->prepare("SELECT * FROM instructors WHERE Email = ? AND Password = ?");
    $stmt->execute([$email, $password]);
    $instructor = $stmt->fetch();
    if ($instructor) {
        $_SESSION['instructor_name'] = $instructor['FirstName'] . ' ' . $instructor['LastName'];
        header('Location: instructor-dashboard.php');
        exit;
    }
    $stmt = $pdo->prepare("SELECT * FROM students WHERE Email = ? AND Password = ?");
    $stmt->execute([$email, $password]);
    $student = $stmt->fetch();
    if ($student) {
        $_SESSION['student_name'] = $student['FirstName'] . ' ' . $student['LastName'];
        header('Location: student-dashboard.php');
        exit;
    }
    
    echo '<div class="alert alert-danger text-center">Invalid email or password.</div>';
}
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4 shadow rounded-4 fade-in" style="width: 100%; max-width: 400px;">
    <div class="alert alert-info d-flex align-items-center mb-4" role="alert" style="font-size: 1rem;">
      <i class="fa fa-users fa-lg me-2 text-primary"></i>
      <div>
         Login to connect as a <span class="fw-semibold">Student</span> or <span class="fw-semibold">Instructor</span>.<br>
        Access your dashboard, join our community, and unlock the best learning resources!
      </div>
    </div>
    <h3 class="text-center mb-4 text-primary">Sign In</h3>
    <form method="post" action="signin.php">
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
      </div>
      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary py-2">Sign In</button>
      </div>
      <div class="text-center">
        <small>Don't have an account? <a href="signup.php" class="text-decoration-none text-primary">Sign Up</a></small>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
