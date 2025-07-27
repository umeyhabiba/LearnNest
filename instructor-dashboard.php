<?php
require 'db.php';
session_start();
// Fetch instructor info from session or DB
$instructor = null;
if (isset($_SESSION['instructor_email'])) {
    $stmt = $pdo->prepare('SELECT * FROM instructors WHERE Email = ?');
    $stmt->execute([$_SESSION['instructor_email']]);
    $instructor = $stmt->fetch();
}
if (!$instructor && isset($_SESSION['instructor_name'])) {
    $stmt = $pdo->prepare('SELECT * FROM instructors WHERE CONCAT(FirstName, " ", LastName) = ?');
    $stmt->execute([$_SESSION['instructor_name']]);
    $instructor = $stmt->fetch();
}
$name = $instructor ? $instructor['FirstName'] . ' ' . $instructor['LastName'] : 'Instructor';
$email = $instructor ? $instructor['Email'] : '';
$qualification = $instructor ? ($instructor['Qualifications'] ?? '') : '';
$image = $instructor && !empty($instructor['Image']) ? 'imageuploade/' . $instructor['Image'] : 'https://randomuser.me/api/portraits/men/45.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LearnNest Instructor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
        }
        body {
            min-height: 100vh;
            height: 100vh;
            background: linear-gradient(120deg, #f8fafc 0%, #e9ecef 100%) fixed;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }
        .container {
            max-width: 100vw;
            width: 100vw;
            min-height: calc(100vh - 70px - 32px); /* navbar + margin */
            height: 100%;
            margin: 0;
            padding: 0 1.5rem;
            background: rgba(255,255,255,0.92);
            border-radius: 1.2rem;
            box-shadow: 0 4px 32px rgba(0,0,0,0.07);
            display: flex;
            flex-direction: column;
        }
        .dashboard-row {
            min-height: 100%;
            display: flex;
            flex: 1 1 0%;
        }
        .sidebar-col {
            flex: 0 0 320px;
            max-width: 340px;
            min-width: 260px;
            margin-right: 2.5rem;
            display: flex;
            flex-direction: column;
        }
        .main-content-col {
            flex: 1 1 0%;
            min-width: 0;
            display: flex;
            flex-direction: column;
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
            color: #fff !important;
            border-top-left-radius: 1.2rem;
            border-top-right-radius: 1.2rem;
            box-shadow: 0 -2px 12px rgba(0,0,0,0.06);
            margin: 0;
            padding: 0;
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
        @media (max-width: 767px) {
            .navbar { min-height: 56px; font-size: 1rem; }
            .navbar-brand, .navbar-nav .nav-link { font-size: 1rem; }
            .container { padding: 1rem 0.3rem; }
        }
        /* --- Enhanced Course Selector --- */
        #instructorCourseSelect {
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
        #instructorCourseSelect:focus {
            border-color: #6610f2;
            box-shadow: 0 0 0 0.2rem rgba(102,16,242,0.10);
            background: #fff;
        }
        label[for="instructorCourseSelect"] {
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
            #instructorCourseSelect { font-size: 1rem; height: 2.5rem; padding-left: 0.7rem; }
            .course-select-wrapper { padding: 0.7rem 0.5rem 1rem 0.5rem; }
        }
        /* --- Enhanced Search Bar --- */
        .search-wrapper {
            background: #fff;
            border-radius: 1rem;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            padding: 1.2rem 1.5rem 1.5rem 1.5rem;
        }
        label[for="studentSearch"] {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }
        .search-input-group {
            border: 2px solid #0d6efd;
            border-radius: 0.7rem;
            overflow: hidden;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .search-input-group:focus-within {
            border-color: #6610f2;
            box-shadow: 0 0 0 0.2rem rgba(102,16,242,0.10);
        }
        #studentSearch {
            font-size: 1.1rem;
            height: 3rem;
            border: none;
            box-shadow: none !important;
            background: #f8fafc;
            font-weight: 500;
            padding-left: 1.2rem;
        }
        #studentSearch:focus {
            background: #fff;
            z-index: 3;
        }
        #searchBtn {
            background: #0d6efd;
            border: none;
            font-size: 1.1rem;
            color: #fff;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        <a class="nav-link" href="#"><i class="fa-solid fa-chalkboard-teacher me-1"></i>Lectures</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-chart-bar me-1"></i>Analytics</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <span class="text-white me-3"><i class="fa-solid fa-user-circle me-1"></i> <?php echo htmlspecialchars($name); ?></span>
                    <button class="btn btn-outline-light btn-sm" id="logoutBtn" onclick="window.location.href='signin.php';"><i class="fa-solid fa-sign-out-alt me-1"></i>Logout</button>
                </div>
            </div>
        </div>
    </nav>
    <div class="container">
        <div class="dashboard-row mb-4">
            <div class="sidebar-col mb-3">
                <div class="card text-center shadow-sm mb-3">
                    <div class="card-body">
                        <img src="<?php echo htmlspecialchars($image); ?>" alt="Avatar" class="rounded-circle mb-2" width="70" height="70">
                        <h5 class="fw-bold mb-0"><?php echo htmlspecialchars($name); ?></h5>
                        <div class="text-muted mb-1" style="font-size:0.98rem;"><?php echo htmlspecialchars($email); ?></div>
                        <div class="text-muted mb-2" style="font-size:0.95rem;"><?php echo htmlspecialchars($qualification); ?></div>
                        <div class="d-flex justify-content-center gap-2 mb-2">
                            <span class="badge bg-primary" data-bs-toggle="tooltip" title="Total Courses">5 Courses</span>
                            <span class="badge bg-success" data-bs-toggle="tooltip" title="Avg Rating">4.8★</span>
                        </div>
                    </div>
                </div>
                <!-- Sidebar Navigation Links -->
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-white fw-bold text-center">Instructor Menu</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><a href="instructor-dashboard.html" class="text-decoration-none text-primary"><i class="fa-solid fa-gauge me-2"></i>Dashboard Home</a></li>
                        <li class="list-group-item"><a href="Courseupl.html" class="text-decoration-none text-primary"><i class="fa-solid fa-upload me-2"></i>Course Upload</a></li>
                        <!-- <li class="list-group-item"><a href="create-course.php" class="text-decoration-none text-primary"><i class="fa-solid fa-plus me-2"></i>Create Course</a></li> -->
                        <li class="list-group-item"><a href="creator-profile.php" class="text-decoration-none text-primary"><i class="fa-solid fa-user me-2"></i>Creator Profile</a></li>
                        <!-- <li class="list-group-item"><a href="edit-profile.php" class="text-decoration-none text-primary"><i class="fa-solid fa-user-pen me-2"></i>Edit Profile</a></li> -->
                        <li class="list-group-item"><a href="forum-home.PHP" class="text-decoration-none text-primary"><i class="fa-solid fa-comments me-2"></i>Forum</a></li>
                    </ul>
                </div>
            </div>
            <div class="main-content-col">
        
        <div class="row mb-4 justify-content-center">
            <div class="col-md-8">
                <div class="course-select-wrapper">
                    <label for="instructorCourseSelect" class="form-label fw-bold">Select Course</label>
                    <select class="form-select" id="instructorCourseSelect">
                        <option value="1">Web Development Bootcamp</option>
                        <option value="2">Data Science 101</option>
                    </select>
                </div>
            </div>
        </div>
       
        <div class="row mb-4">
            <div class="col-md-4 mb-2">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fa-solid fa-users fa-2x text-primary mb-2"></i>
                        <h6 class="card-title">Total Students</h6>
                        <span class="fs-4 fw-bold" id="totalStudents">32</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fa-solid fa-chart-line fa-2x text-success mb-2"></i>
                        <h6 class="card-title">Average Grade</h6>
                        <span class="fs-4 fw-bold" id="avgGrade">87%</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-2">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fa-solid fa-triangle-exclamation fa-2x text-danger mb-2"></i>
                        <h6 class="card-title">At-Risk Students</h6>
                        <span class="fs-4 fw-bold" id="atRisk">3</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white fw-bold">
                        Class Grade Distribution
                    </div>
                    <div class="card-body">
                        <canvas id="gradeDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-white fw-bold">Grades Overview</div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 col-md-3 mb-3">
                        <div class="fw-bold text-primary" style="font-size:1.2rem;">B+</div>
                        <div style="font-size:0.9rem;">Class Avg Grade</div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="fw-bold text-success" style="font-size:1.2rem;">87%</div>
                        <div style="font-size:0.9rem;">Avg %</div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="fw-bold text-warning" style="font-size:1.2rem;">98%</div>
                        <div style="font-size:0.9rem;">Highest</div>
                    </div>
                    <div class="col-6 col-md-3 mb-3">
                        <div class="fw-bold text-danger" style="font-size:1.2rem;">55%</div>
                        <div style="font-size:0.9rem;">Lowest</div>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="row mb-4 justify-content-center">
            <div class="col-md-8">
                <div class="search-wrapper">
                    <label for="studentSearch" class="form-label fw-bold">Find a Student</label>
                    <div class="input-group search-input-group">
                        <input type="text" class="form-control" id="studentSearch" placeholder="Search by name or email...">
                        <button class="btn btn-primary" type="button" id="searchBtn"><i class="fa-solid fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header bg-white fw-bold">Student Progress & Grades Overview</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0" id="studentsTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Progress</th>
                                <th>Grade</th>
                                <th>Grade Letter</th>
                                <th>Last Active</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Jane Doe</td>
                                <td>jane@example.com</td>
                                <td>80%</td>
                                <td>88%</td>
                                <td><span class="badge bg-success">A</span></td>
                                <td>2025-06-25</td>
                                <td><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#studentDetailModal"><i class="fa-solid fa-eye"></i></button></td>
                            </tr>
                            <tr>
                                <td>John Smith</td>
                                <td>john@example.com</td>
                                <td>60%</td>
                                <td>72%</td>
                                <td><span class="badge bg-primary">B+</span></td>
                                <td>2025-06-24</td>
                                <td><button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#studentDetailModal"><i class="fa-solid fa-eye"></i></button></td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="studentDetailModal" tabindex="-1" aria-labelledby="studentDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="studentDetailModalLabel">Student Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Name:</strong> <span id="modalStudentName">Jane Doe</span><br>
                                <strong>Email:</strong> <span id="modalStudentEmail">jane@example.com</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Progress:</strong> <span id="modalStudentProgress">80%</span><br>
                                <strong>Grade %:</strong> <span id="modalStudentGrade">88%</span><br>
                                <strong>Grade:</strong> <span id="modalStudentGradeLetter" class="badge bg-success">A</span>
                            </div>
                        </div>
                        <h6>Module Progress</h6>
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Module 1: HTML & CSS
                                <span class="badge bg-success">Completed</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Module 2: JavaScript
                                <span class="badge bg-warning text-dark">In Progress</span>
                            </li>
                        </ul>
                        <h6>Recent Activity</h6>
                        <ul class="list-group">
                            <li class="list-group-item">Completed Quiz 1 (2025-06-20)</li>
                            <li class="list-group-item">Watched Lecture 2 (2025-06-22)</li>
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
       
        <div class="card mb-4">
            <div class="card-header bg-white fw-bold">Course Summary</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <div class="d-flex flex-column gap-2">
                            <div><strong>Course Name:</strong> <span id="summaryCourseName">Web Development Bootcamp</span></div>
                            <div><strong>Start Date:</strong> <span id="summaryStartDate">2025-05-01</span></div>
                            <div><strong>Duration:</strong> <span id="summaryDuration">12 weeks</span></div>
                            <div><strong>Status:</strong> <span class="badge bg-success">Active</span></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Modules Covered:</strong>
                        <span id="summaryModules">HTML & CSS, JavaScript, Bootstrap</span>
                        <div class="mt-3" style="width: 180px; min-width:180px;">
                            <label class="form-label" style="font-size:0.9rem;">Overall Progress</label>
                            <div class="progress" style="height: 18px;">
                                <div class="progress-bar bg-primary" id="courseProgress" role="progressbar" style="width: 65%; font-size: 0.95rem;" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100">65%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-white fw-bold">Student Grades</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
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
                                <td>Jane Doe</td>
                                <td>Module 1</td>
                                <td>80%</td>
                                <td>85%</td>
                                <td>83%</td>
                                <td><span class="badge bg-primary">B+</span></td>
                                <td><span class="badge bg-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>John Smith</td>
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
    </div> <!-- Close .container -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        const ctx = document.getElementById('gradeDistributionChart').getContext('2d');
        const gradeDistributionChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['A', 'B', 'C', 'D', 'F'],
                datasets: [{
                    label: '# of Students',
                    data: [8, 12, 7, 3, 2], 
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(253, 126, 20, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(25, 135, 84, 1)',
                        'rgba(13, 110, 253, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(253, 126, 20, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        const courseData = {
            1: [ 
                { name: 'Jane Doe', email: 'jane@example.com', progress: 80, grade: 88, lastActive: '2025-06-25', gradeLetter: 'A' },
                { name: 'John Smith', email: 'john@example.com', progress: 60, grade: 72, lastActive: '2025-06-24', gradeLetter: 'B+' }
            ],
            2: [ 
                { name: 'Alice Brown', email: 'alice@example.com', progress: 90, grade: 95, lastActive: '2025-06-26', gradeLetter: 'A+' },
                { name: 'Bob Lee', email: 'bob@example.com', progress: 55, grade: 60, lastActive: '2025-06-23', gradeLetter: 'B' }
            ]
        };
        let currentCourse = '1';
        function renderStudentsTable(students) {
            const tbody = document.querySelector('#studentsTable tbody');
            tbody.innerHTML = '';
            students.forEach(s => {
                tbody.innerHTML += `<tr>
                    <td>${s.name}</td>
                    <td>${s.email}</td>
                    <td>${s.progress}%</td>
                    <td>${s.grade}%</td>
                    <td><span class='badge bg-${s.gradeLetter === 'A+' || s.gradeLetter === 'A' ? 'success' : s.gradeLetter === 'B+' ? 'primary' : 'warning'}'>${s.gradeLetter}</span></td>
                    <td>${s.lastActive}</td>
                    <td><button class='btn btn-sm btn-outline-primary' data-bs-toggle='modal' data-bs-target='#studentDetailModal'><i class='fa-solid fa-eye'></i></button></td>
                </tr>`;
            });
        }
        
        renderStudentsTable(courseData[currentCourse]);
        document.getElementById('instructorCourseSelect').addEventListener('change', function() {
            currentCourse = this.value;
            renderStudentsTable(courseData[currentCourse]);
        });
        document.getElementById('studentSearch').addEventListener('input', function() {
            const value = this.value.toLowerCase();
            renderStudentsTable(courseData[currentCourse].filter(s => s.name.toLowerCase().includes(value) || s.email.toLowerCase().includes(value)));
        });
        
        document.querySelectorAll('#studentsTable tbody tr td button').forEach(btn => {
            btn.addEventListener('click', function() {
              
            });
        });
        
        function getGradeLetter(percent) {
            if (percent >= 90) return 'A+';
            if (percent >= 80) return 'A';
            if (percent >= 70) return 'B+';
            if (percent >= 60) return 'B';
            if (percent >= 50) return 'C';
            return 'D';
        }
        
        var gradePercent = 88;
        document.getElementById('modalStudentGradeLetter').innerText = getGradeLetter(gradePercent);
        
        document.getElementById('logoutBtn').addEventListener('click', function() {
            window.location.href = 'signin.php';
        });
    </script>
</body>
</html>
