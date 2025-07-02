<?php
require 'db.php';
session_start();
// Fetch student info from session or DB
$student = null;
if (isset($_SESSION['student_email'])) {
    $stmt = $pdo->prepare('SELECT * FROM students WHERE Email = ?');
    $stmt->execute([$_SESSION['student_email']]);
    $student = $stmt->fetch();
}
if (!$student && isset($_SESSION['student_name'])) {
    // fallback: try to get by name (not recommended, but for legacy session)
    $stmt = $pdo->prepare('SELECT * FROM students WHERE CONCAT(FirstName, " ", LastName) = ?');
    $stmt->execute([$_SESSION['student_name']]);
    $student = $stmt->fetch();
}
$name = $student ? $student['FirstName'] . ' ' . $student['LastName'] : 'Student';
$email = $student ? $student['Email'] : '';
$qualification = $student ? ($student['Education'] ?? '') : '';
$image = $student && !empty($student['Image']) ? 'imageuploade/' . $student['Image'] : 'https://randomuser.me/api/portraits/men/32.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnNest Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        
        body, html {
            width: 100vw;
            min-height: 100vh;       
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        body {
            background: linear-gradient(120deg, #f8fafc 0%, #e9ecef 100%) fixed;
        }
        .main-dashboard-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 0 1.5rem;
            width: 100%;
        }
        .row.mb-4, .row.mb-4 > [class^='col-'] {
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .col-lg-3 {
            padding-left: 0 !important;
            padding-right: 1.5rem !important;
        }
        .col-lg-9 {
            padding-left: 1.5rem !important;
            padding-right: 0 !important;
        }
        @media (max-width: 991px) {
            .main-dashboard-container {
                padding: 0 0.5rem;
            }
            .col-lg-3, .col-lg-9 {
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
        @media (max-width: 767px) {
            .main-dashboard-container { padding: 0.5rem 0 !important; }
            .col-lg-3, .col-lg-9 { padding: 0 !important; }
        }
       
        .card, .accordion-item {
            margin-bottom: 1.5rem;
        }
        .card:last-child, .accordion-item:last-child {
            margin-bottom: 0;
        }
        .card-body, .card-header {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }
        @media (max-width: 767px) {
            .card-body, .card-header { padding-left: 0.7rem; padding-right: 0.7rem; }
        }
        
        .container, .row.mb-4, .row.mb-4 > [class^='col-'] {
            margin: 0;
            padding: 0;
            max-width: unset;
            width: unset;
            min-width: unset;
        }
        .col-lg-3, .col-lg-9 { padding: 0 !important; }
       
        .navbar {
            min-height: 70px;
            font-size: 1.15rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            background: linear-gradient(90deg, #0d6efd 80%, #6610f2 100%) !important;
        }
        .navbar-brand, .navbar-nav .nav-link, .navbar-text {
            color: #fff !important;
            font-size: 1.25rem;
            padding-top: 0.6rem;
            padding-bottom: 0.6rem;
        }
        .navbar-nav .nav-link {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .navbar-nav .nav-link.active, .navbar-nav .nav-link:focus {
            color: #ffd700 !important;
        }
        .card-header {
            font-size: 1.1rem;
            font-weight: 600;
            background: linear-gradient(90deg, #f8fafc 60%, #e9ecef 100%);
            border-bottom: 1px solid #e3e6ea;
        }
        .card {
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            border: none;
            background: #fff;
            margin-bottom: 2rem;
        }
        .card:last-child {
            margin-bottom: 0;
        }
        .form-label, .table th {
            font-weight: 600;
            color: #343a40;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .progress {
            background: #e9ecef;
            border-radius: 8px;
        }
        .progress-bar {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .badge {
            border-radius: 0.5rem;
            font-size: 0.95rem;
            font-weight: 600;
        }
        .modal-content {
            border-radius: 1rem;
        }
        .list-group-item {
            border: none;
            border-bottom: 1px solid #f1f3f6;
            font-size: 1rem;
            background: transparent;
        }
        .list-group-item:last-child {
            border-bottom: none;
        }
        .footer, footer {
            font-size: 1rem;
            font-weight: 500;
            letter-spacing: 0.5px;
            background: linear-gradient(90deg, #0d6efd 80%, #6610f2 100%) !important;
            color: #222 !important;
        }
        .accordion-item {
            border-radius: 0.7rem !important;
            overflow: hidden;
            margin-bottom: 0.7rem;
            border: none;
            box-shadow: 0 1px 6px rgba(0,0,0,0.04);
        }
        .accordion-button {
            font-weight: 600;
            background: #f8fafc;
        }
        .accordion-button:not(.collapsed) {
            color: #0d6efd;
            background: #e9ecef;
        }
        .btn-outline-light {
            border-radius: 0.5rem;
            font-weight: 600;
        }
        
        #courseSelect {
            font-size: 1.2rem;
            height: 3.2rem;
            border-radius: 0.7rem;
            box-shadow: 0 2px 8px rgba(13,110,253,0.08);
            border: 2px solid #0d6efd;
            background: #f8fafc;
            font-weight: 600;
            padding-left: 1.2rem;
            padding-right: 2.5rem;
            transition: border-color 0.2s;
        }
        #courseSelect:focus {
            border-color: #6610f2;
            box-shadow: 0 0 0 0.2rem rgba(102,16,242,0.10);
            background: #fff;
        }
        label[for="courseSelect"] {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }
        .course-select-wrapper {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            padding: 1.2rem 1.5rem 1.5rem 1.5rem;
            margin-bottom: 2rem;
        }
        @media (max-width: 767px) {
            #courseSelect { font-size: 1rem; height: 2.5rem; padding-left: 0.7rem; }
            .course-select-wrapper { padding: 0.7rem 0.5rem 1rem 0.5rem; }
        }
        
        .dashboard-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0;
        }
        .sidebar-col {
            flex: 0 0 320px;
            max-width: 340px;
            min-width: 260px;
            margin-right: 2.5rem;
        }
        .main-content-col {
            flex: 1 1 0%;
            min-width: 0;
        }
        @media (max-width: 991px) {
            .dashboard-row {
                flex-direction: column;
            }
            .sidebar-col {
                max-width: 100%;
                margin-right: 0;
                margin-bottom: 2rem;
            }
        }
        
        .sidebar-col .card {
            margin-bottom: 1.2rem;
        }
        .sidebar-col .card:last-child {
            margin-bottom: 0;
        }
   
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            height: 100vh;
        }
        .main-dashboard-container {
            min-height: calc(100vh - 70px - 32px); /* navbar + margin */
            height: 100%;
            max-width: 100vw;
            width: 100vw;
            margin: 0;
            padding: 0 1.5rem;
        }
        .dashboard-row {
            min-height: 100%;
        }
        
        .progress-overview-bar {
            height: 8px;
            border-radius: 6px;
            background: #e9ecef;
            margin-top: 0.3rem;
            margin-bottom: 0.7rem;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .progress-overview-bar .progress-bar {
            height: 100%;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        @media (max-width: 767px) {
            .main-dashboard-container { padding: 0.5rem 0 !important; }
            .progress-overview-bar { width: 100%; }
        }
        
        .progress-oval {
            position: relative;
            width: 100px;
            height: 100px;
            margin: 0 auto 0.5rem auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .progress-oval svg {
            transform: rotate(-90deg);
        }
        .progress-bar-oval-bg {
            stroke: #e9ecef;
            stroke-width: 12;
        }
        .progress-bar-oval {
            stroke-width: 12;
            filter: drop-shadow(0 2px 6px rgba(0,0,0,0.10));
            stroke-linecap: round;
            transition: stroke-dashoffset 1.5s cubic-bezier(.4,2,.6,1);
        }
       
        .progress-oval .progress-value {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.2rem;
            font-weight: 700;
            color: #222;
            text-shadow: 0 1px 4px #fff, 0 1px 8px #e9ecef;
            transition: color 0.5s;
        }
        .progress-oval .progress-value.low {
            color: #dc3545; 
        }
        .progress-oval .progress-value.mid {
            color: #fd7e14;
        }
        .progress-oval .progress-value.high {
            color: #198754; 
        }
        .bullet-chart {
            position: relative;
            width: 100%;
            height: 32px;
            margin: 0.7rem 0 1.2rem 0;
            background: #e9ecef;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: visible;
        }
        .bullet-bar {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            border-radius: 12px;
            transition: width 1.5s cubic-bezier(.4,2,.6,1);
        }
        .bullet-marker {
            position: absolute;
            top: 0;
            width: 4px;
            height: 100%;
            background: #222;
            border-radius: 2px;
            box-shadow: 0 0 6px #2222;
            z-index: 2;
            transition: left 1.5s cubic-bezier(.4,2,.6,1);
        }
        .bullet-label {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 600;
            font-size: 1.05rem;
            color: #222;
            z-index: 3;
            pointer-events: none;
        }
        .bullet-value {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 700;
            font-size: 1.05rem;
            color: #222;
            z-index: 3;
            pointer-events: none;
        }
        .bullet-attendance .bullet-bar {
            background: linear-gradient(90deg, #0d6efd 60%, #6610f2 100%);
        }
        .bullet-quizzes .bullet-bar {
            background: linear-gradient(90deg, #ffc107 60%, #ff9800 100%);
        }
        .bullet-assignments .bullet-bar {
            background: linear-gradient(90deg, #198754 60%, #43e97b 100%);
        }
        .bullet-overall .bullet-bar {
            background: linear-gradient(90deg, #6610f2 60%, #0dcaf0 100%);
        }
        
        .bullet-chart, .bullet-bar, .bullet-marker, .bullet-label, .bullet-value,
        .bullet-attendance .bullet-bar, .bullet-quizzes .bullet-bar, .bullet-assignments .bullet-bar, .bullet-overall .bullet-bar {
            display: none !important;
        }
       
        .custom-progress-group {
            margin-bottom: 1.5rem;
        }
        .custom-progress-label {
            font-weight: 600;
            font-size: 1.05rem;
            margin-bottom: 0.3rem;
            color: #222;
        }
        .custom-progress-bar-bg {
            width: 100%;
            height: 22px;
            background: #e9ecef;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
            position: relative;
        }
        .custom-progress-bar {
            height: 100%;
            width: 0%;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.05rem;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            padding-right: 16px;
            transition: width 1.5s cubic-bezier(.4,2,.6,1), background 0.5s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .custom-progress-bar.attendance {
            background: linear-gradient(90deg, #0d6efd 60%, #6610f2 100%);
        }
        .custom-progress-bar.quizzes {
            background: linear-gradient(90deg, #ffc107 60%, #ff9800 100%);
            color: #222;
        }
        .custom-progress-bar.assignments {
            background: linear-gradient(90deg, #198754 60%, #43e97b 100%);
        }
        .custom-progress-bar.overall {
            background: linear-gradient(90deg, #6610f2 60%, #0dcaf0 100%);
        }
        .custom-progress-bar.low {
            background: linear-gradient(90deg, #dc3545 60%, #ff7675 100%);
        }
        .custom-progress-bar.mid {
            background: linear-gradient(90deg, #fd7e14 60%, #ffe082 100%);
            color: #222;
        }
        .custom-progress-bar.high {
            background: linear-gradient(90deg, #198754 60%, #43e97b 100%);
        }
        
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">LearnNest</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu" aria-controls="navbarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#"><i class="fa-solid fa-house me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-book me-1"></i>Courses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-list-check me-1"></i>Tasks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-chart-bar me-1"></i>Progress</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3"><i class="fa-solid fa-user-circle me-1"></i> <?php echo htmlspecialchars($name); ?></span>
                    <button class="btn btn-outline-light btn-sm" id="logoutBtn" onclick="window.location.href='signin.php';"><i class="fa-solid fa-sign-out-alt me-1"></i>Logout</button>
                </div>
            </div>
        </div>
    </nav>
    <div class="main-dashboard-container">
        <div class="dashboard-row mb-4">
            <div class="sidebar-col mb-3">
                <div class="card text-center shadow-sm mb-3">
                    <div class="card-body">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Avatar" class="rounded-circle mb-2" width="70" height="70">
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($name); ?></h5>
                        <div class="text-muted mb-1" style="font-size:0.98rem;"><?php echo htmlspecialchars($email); ?></div>
                        <div class="text-muted mb-2" style="font-size:0.95rem;"><?php echo htmlspecialchars($qualification); ?></div>
                        <div class="d-flex justify-content-center gap-2 mb-2">
                            <span class="badge bg-primary" data-bs-toggle="tooltip" title="Total Courses">3 Courses</span>
                            <span class="badge bg-success" data-bs-toggle="tooltip" title="Average Grade">89% Avg</span>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-warning text-dark" data-bs-toggle="tooltip" title="Badges Earned"><i class="fa-solid fa-medal me-1"></i>2 Badges</span>
                        </div>
                    </div>
                </div>
                <!-- Sidebar Navigation Links -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold text-center">Student Menu</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="student-dashboard.html" class="text-decoration-none text-primary"><i class="fa-solid fa-gauge me-2"></i>Dashboard Home</a></li>
                        <li class="list-group-item"><a href="forum-home.PHP" class="text-decoration-none text-primary"><i class="fa-solid fa-comments me-2"></i>Forum</a></li>
                        <li class="list-group-item"><a href="student-profile.php" class="text-decoration-none text-primary"><i class="fa-solid fa-user me-2"></i>Profile</a></li>
                        <li class="list-group-item"><a href="progress tracker.html" class="text-decoration-none text-primary"><i class="fa-solid fa-chart-line me-2"></i>Progress Tracker</a></li>
                    </ul>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">Recent Activity</div>
                    <div class="card-body p-2">
                        <ul class="list-group list-group-flush" id="activityFeed">
                            <li class="list-group-item">Completed Quiz 2 <span class="text-muted small">(2025-06-25)</span></li>
                            <li class="list-group-item">Watched Lecture 3 <span class="text-muted small">(2025-06-24)</span></li>
                            <li class="list-group-item">Submitted Assignment 1 <span class="text-muted small">(2025-06-22)</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="main-content-col">
                
                <div class="row mb-4 justify-content-center">
                    <div class="col-md-8">
                        <div class="course-select-wrapper">
                            <label for="courseSelect" class="form-label fw-bold">Select Course</label>
                            <select class="form-select" id="courseSelect">
                               
                                <option value="1">Web Development Bootcamp</option>
                                <option value="2">Data Science 101</option>
                                <option value="3">UI/UX Fundamentals</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white fw-bold">Course Summary</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="d-flex flex-column gap-2">
                                    <div><strong>Course Name:</strong> <span id="courseName">Web Development Bootcamp</span></div>
                                    <div><strong>Start Date:</strong> <span id="courseStart">2025-05-01</span></div>
                                    <div><strong>Duration:</strong> <span id="courseDuration">12 weeks</span></div>
                                    <div><strong>Status:</strong> <span class="badge bg-success" id="courseStatus">Active</span></div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Modules Covered:</strong>
                                <ul class="mb-2 ps-3">
                                    <li>HTML & CSS Basics</li>
                                    <li>JavaScript Essentials</li>
                                    <li>Bootstrap & Responsive Design</li>
                                </ul>
                                <label class="form-label" style="font-size:0.9rem;">Overall Progress</label>
                                <div class="progress" style="height: 10px; max-width:180px;">
                                    <div class="progress-bar bg-primary" id="courseProgress" role="progressbar" style="width: 65%; font-size: 0.8rem;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">65%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="accordion mb-4" id="modulesAccordion">
                   
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="module1Heading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#module1" aria-expanded="true" aria-controls="module1">
                                Module 1: HTML & CSS Basics
                            </button>
                        </h2>
                        <div id="module1" class="accordion-collapse collapse show" aria-labelledby="module1Heading" data-bs-parent="#modulesAccordion">
                            <div class="accordion-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Lecture Watched
                                        <span class="text-success"><i class="fa-solid fa-check-circle"></i></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Quiz Completed
                                        <span class="text-success"><i class="fa-solid fa-check-circle"></i></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Assignment Submitted
                                        <span class="text-danger"><i class="fa-solid fa-times-circle"></i></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white fw-bold">Grades Summary</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Module</th>
                                        <th>Quiz</th>
                                        <th>Assignment</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Module 1</td>
                                        <td>85%</td>
                                        <td>--</td>
                                        <td>85%</td>
                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                    </tr>
                                    <tr>
                                        <td>Module 2</td>
                                        <td>90%</td>
                                        <td>88%</td>
                                        <td>89%</td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
               
                <div class="card mb-4">
                    <div class="card-header bg-white fw-bold">Upcoming Tasks</div>
                    <div class="card-body">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Assignment 1: HTML Layout
                                <span class="badge bg-danger">Due: 2025-07-01</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Quiz 2: CSS Selectors
                                <span class="badge bg-warning text-dark">Due: 2025-07-05</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white fw-bold">Progress Overview</div>
                    <div class="card-body">
                        <div class="custom-progress-group">
                            <div class="custom-progress-label">Attendance</div>
                            <div class="custom-progress-bar-bg">
                                <div class="custom-progress-bar attendance" data-value="90">90%</div>
                            </div>
                        </div>
                        <div class="custom-progress-group">
                            <div class="custom-progress-label">Quizzes</div>
                            <div class="custom-progress-bar-bg">
                                <div class="custom-progress-bar quizzes" data-value="75">75%</div>
                            </div>
                        </div>
                        <div class="custom-progress-group">
                            <div class="custom-progress-label">Assignments</div>
                            <div class="custom-progress-bar-bg">
                                <div class="custom-progress-bar assignments" data-value="60">60%</div>
                            </div>
                        </div>
                        <div class="custom-progress-group">
                            <div class="custom-progress-label">Overall</div>
                            <div class="custom-progress-bar-bg">
                                <div class="custom-progress-bar overall" data-value="65">65%</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white fw-bold">Grades Overview</div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 col-md-3 mb-3">
                                <div class="fw-bold text-primary" style="font-size:1.2rem;">A</div>
                                <div style="font-size:0.9rem;">Current Grade</div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="fw-bold text-success" style="font-size:1.2rem;">89%</div>
                                <div style="font-size:0.9rem;">Average</div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="fw-bold text-warning" style="font-size:1.2rem;">95%</div>
                                <div style="font-size:0.9rem;">Highest</div>
                            </div>
                            <div class="col-6 col-md-3 mb-3">
                                <div class="fw-bold text-danger" style="font-size:1.2rem;">60%</div>
                                <div style="font-size:0.9rem;">Lowest</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white fw-bold">Detailed Grades</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Module</th>
                                        <th>Assignment</th>
                                        <th>Quiz</th>
                                        <th>Total</th>
                                        <th>Grade</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Module 1</td>
                                        <td>80%</td>
                                        <td>85%</td>
                                        <td>83%</td>
                                        <td><span class="badge bg-primary">B+</span></td>
                                        <td><span class="badge bg-success">Completed</span></td>
                                    </tr>
                                    <tr>
                                        <td>Module 2</td>
                                        <td>88%</td>
                                        <td>90%</td>
                                        <td>89%</td>
                                        <td><span class="badge bg-success">A</span></td>
                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                    </tr>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div> 
    <script>
        // Enable Bootstrap tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
        // Animate progress bars on load
        document.querySelectorAll('.progress-bar').forEach(function(bar) {
            var final = bar.getAttribute('aria-valuenow');
            bar.style.width = '0%';
            setTimeout(function() {
                bar.style.transition = 'width 1.2s cubic-bezier(.4,2,.6,1)';
                bar.style.width = final + '%';
            }, 300);
        });
        // Animate improved circular progress bars in Progress Overview
        document.querySelectorAll('.progress-oval').forEach(function(oval) {
            var value = parseInt(oval.getAttribute('data-value'), 10);
            var circle = oval.querySelector('.progress-bar-oval');
            var percentText = oval.querySelector('.progress-value');
            var radius = 40;
            var circumference = 2 * Math.PI * radius;
            var offset = circumference - (value / 100) * circumference;
            circle.style.strokeDasharray = circumference;
            circle.style.strokeDashoffset = circumference;
            // Set color class for percent text
            percentText.classList.remove('low', 'mid', 'high');
            if (value <= 50) {
                percentText.classList.add('low');
            } else if (value <= 69) {
                percentText.classList.add('mid');
            } else {
                percentText.classList.add('high');
            }
            setTimeout(function() {
                circle.style.strokeDashoffset = offset;
            }, 300);
        });
        // Animate bullet charts for Progress Overview
        document.querySelectorAll('.bullet-chart').forEach(function(chart) {
            var value = parseInt(chart.getAttribute('data-value'), 10);
            var target = parseInt(chart.getAttribute('data-target'), 10);
            var bar = chart.querySelector('.bullet-bar');
            var marker = chart.querySelector('.bullet-marker');
            // Animate bar width
            bar.style.width = '0%';
            setTimeout(function() {
                bar.style.width = value + '%';
            }, 300);
            // Animate marker position
            marker.style.left = '0%';
            setTimeout(function() {
                marker.style.left = target + '%';
            }, 300);
        });
        // Module/task filter
        document.getElementById('modulesAccordion').insertAdjacentHTML('beforebegin', `
            <div class="mb-3">
                <input type="text" class="form-control" id="moduleSearch" placeholder="Search modules/tasks...">
            </div>
        `);
        document.getElementById('moduleSearch').addEventListener('input', function() {
            var val = this.value.toLowerCase();
            document.querySelectorAll('#modulesAccordion .accordion-item').forEach(function(item) {
                var text = item.textContent.toLowerCase();
                item.style.display = text.includes(val) ? '' : 'none';
            });
        });
        // Course selector event (simulate data update)
        document.getElementById('courseSelect').addEventListener('change', function() {
            // Here you would update all dashboard data based on selected course
            // For now, just simulate with static data
        });
        // Logout button event (simulate logout)
        var logoutBtn = document.getElementById('logoutBtn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function() {
                window.location.href = 'signin.html';
            });
        }
        // Animate custom modern progress bars in Progress Overview
        document.querySelectorAll('.custom-progress-bar').forEach(function(bar) {
            var value = parseInt(bar.getAttribute('data-value'), 10);
            bar.style.width = '0%';
            // Color coding based on value
            bar.classList.remove('low', 'mid', 'high');
            if (value <= 50) {
                bar.classList.add('low');
            } else if (value <= 69) {
                bar.classList.add('mid');
            } else {
                bar.classList.add('high');
            }
            setTimeout(function() {
                bar.style.width = value + '%';
            }, 300);
        });
    </script>
</body>
</html>
