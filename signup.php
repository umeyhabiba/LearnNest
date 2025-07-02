<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Learn_Nest Registration Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style> 
    .fade-in {
      animation: fadeIn 1.5s ease-in;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .custom-info-box {
      background: linear-gradient(90deg, #e3f0ff 0%, #f8f9fa 100%);
      border-left: 6px solid #0d6efd;
      box-shadow: 0 2px 8px rgba(13,110,253,0.07);
      border-radius: 1rem;
      padding: 1.2rem 1.5rem;
      font-size: 1.08rem;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      gap: 1rem;
      max-width: 600px;
      width: 100%;
      margin-left: auto;
      margin-right: auto;
    }
    .custom-info-box .fa-user-plus {
      font-size: 2rem;
      color: #0d6efd;
      flex-shrink: 0;
      filter: drop-shadow(0 2px 4px #b6d4fe);
    }
    .custom-info-box strong {
      color: #0d6efd;
      font-size: 1.1rem;
    }
    .custom-info-box span.fw-semibold {
      color: #0a58ca;
      display: inline-block;
      padding: 0 8px;
      min-width: 80px;
      text-align: center;
    }
    .custom-radio-label {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.5rem 1.2rem;
      border: 2px solid #e3f0ff;
      border-radius: 2rem;
      background: #f8f9fa;
      cursor: pointer;
      font-weight: 500;
      font-size: 1.08rem;
      transition: border-color 0.2s, background 0.2s, color 0.2s;
    }
    .custom-radio-label .fa {
      font-size: 1.2rem;
      color: #0d6efd;
      transition: color 0.2s;
    }
    .form-check-input[type="radio"] {
      display: none;
    }
    .role-card {
      position: relative;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-width: 180px;
      min-height: 120px;
      padding: 1.2rem 1rem 1rem 1rem;
      border-radius: 1.5rem;
      border: 2px solid #e3f0ff;
      background: #f8f9fa;
      box-shadow: 0 2px 8px rgba(13,110,253,0.07);
      cursor: pointer;
      transition: box-shadow 0.2s, border-color 0.2s, background 0.2s, transform 0.18s;
      overflow: hidden;
    }
    .role-card .fa {
      font-size: 2.1rem;
      margin-bottom: 0.5rem;
      transition: color 0.2s;
    }
    .role-card .role-title {
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 0.2rem;
    }
    .role-card .role-desc {
      font-size: 0.97rem;
      color: #6c757d;
      margin-bottom: 0.2rem;
    }
    .role-card .checkmark {
      position: absolute;
      top: 12px;
      right: 16px;
      font-size: 1.3rem;
      color: #198754;
      opacity: 0;
      transition: opacity 0.2s;
      z-index: 2;
    }
    .role-card.student {
      border-color: #e3f0ff;
    }
    .role-card.instructor {
      border-color: #eafbe7;
    }
    .form-check-input[type="radio"]:checked + .role-card {
      box-shadow: 0 4px 16px rgba(13,110,253,0.13);
      border-color: #0d6efd;
      background: linear-gradient(90deg, #e3f0ff 60%, #f8f9fa 100%);
      transform: scale(1.04);
    }
    .form-check-input[type="radio"]:checked + .role-card.student {
      background: linear-gradient(90deg, #e3f0ff 60%, #f8f9fa 100%);
    }
    .form-check-input[type="radio"]:checked + .role-card.instructor {
      background: linear-gradient(90deg, #eafbe7 60%, #f8f9fa 100%);
      border-color: #198754;
    }
    .form-check-input[type="radio"]:checked + .role-card .checkmark {
      opacity: 1;
    }
    .role-card:hover {
      border-color: #0a58ca;
      box-shadow: 0 6px 18px rgba(13,110,253,0.18);
      background: #e3f0ff;
      transform: scale(1.03);
    }
    .role-card.instructor:hover {
      border-color: #198754;
      background: #eafbe7;
    }
    .role-card:active {
      transform: scale(0.98);
    }
    .role-avatar {
      width: 48px;
      height: 48px;
      border-radius: 50%;
      margin-bottom: 0.5rem;
      box-shadow: 0 2px 8px rgba(13,110,253,0.10);
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
  </style>
</head>
<body>
<?php
require 'db.php';
if(isset($_POST['Register'])){
    $role = $_POST['role'];
    if($role === 'student') {
        $firstName = $_POST['firstName'];
        $lastName  = $_POST['lastName'];
        $address   = $_POST['address'];
        $education = $_POST['education'];
        $email     = $_POST['email'];
        $contact   = $_POST['contact'];
        $password  = $_POST['password']; 
        $image     = $_FILES['image']['name']; 
        $image_name = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        if(move_uploaded_file($tmp_name, "imageuploade/" . $image_name)){
            $query = "INSERT INTO students (FirstName, LastName, Address, Education, Email, Contect, Password, Image)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $res = $stmt->execute([$firstName, $lastName, $address, $education, $email, $contact, $password, $image_name]);
            if($res){
                echo '<div class="alert alert-success text-center">Student registration successful! Redirecting to sign in...</div>';
                echo '<script>setTimeout(function(){ window.location.href = "signin.html"; }, 1500);</script>';
            } else {
                echo '<div class="alert alert-danger text-center">Error: ' . implode(' | ', $stmt->errorInfo()) . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger text-center">Image upload failed.</div>';
        }
    } else if($role === 'instructor') {
        $firstName = $_POST['instFirstName'];
        $lastName  = $_POST['instLastName'];
        $address   = $_POST['instAddress'];
        $qualifications = $_POST['instQualifications'];
        $expertise = $_POST['instExpertise'];
        $email     = $_POST['instEmail'];
        $contact   = $_POST['instContact'];
        $password  = $_POST['instPassword']; 
        $image     = $_FILES['instImage']['name']; 
        $image_name = $_FILES['instImage']['name'];
        $tmp_name = $_FILES['instImage']['tmp_name'];
        if(move_uploaded_file($tmp_name, "imageuploade/" . $image_name)){
            $query = "INSERT INTO instructors (FirstName, LastName, Address, Qualifications, Expertise, Email, Contect, Password, Image)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $res = $stmt->execute([$firstName, $lastName, $address, $qualifications, $expertise, $email, $contact, $password, $image_name]);
            if($res){
                echo '<div class="alert alert-success text-center">Instructor registration successful! Redirecting to sign in...</div>';
                echo '<script>setTimeout(function(){ window.location.href = "signin.html"; }, 1500);</script>';
            } else {
                echo '<div class="alert alert-danger text-center">Error: ' . implode(' | ', $stmt->errorInfo()) . '</div>';
            }
        } else {
            echo '<div class="alert alert-danger text-center">Image upload failed.</div>';
        }
    }
}
?>
  <div class="container my-5">
    <div class="text-center mb-4 fade-in">
      <h1 class="display-5 fw-bold text-primary">Learn_Nest Registration Form</h1>
      <div class="custom-info-box d-flex flex-column align-items-center mt-3 w-100" style="max-width:none; border-radius:0; margin-left:0; margin-right:0;">
        <span><i class="fa fa-user-plus mb-2" style="font-size:2rem;"></i></span>
        <div>
          <strong>Join Learn_Nest!</strong> <br>
          <span>Register as a <span class="fw-semibold">Student</span> or <span class="fw-semibold">Instructor</span> to access personalized dashboards, connect with our learning community, and unlock top courses and resources.</span>
        </div>
      </div>
    </div>
    <div class="card shadow rounded-4 p-4 fade-in">
      <form action="signup.php" method="post" enctype="multipart/form-data" id="registrationForm">
        <div class="mb-3">
          <label class="form-label w-100 text-center" style="display:block;"><b>Register as</b></label><br>
          <div class="d-flex justify-content-center gap-4 flex-wrap">
            <div class="form-check form-check-inline p-0 m-0">
              <input class="form-check-input" type="radio" name="role" id="studentRadio" value="student">
              <label class="role-card student" for="studentRadio">
                <span class="role-avatar">
                  <!-- Student SVG Avatar -->
                  <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="20" cy="20" r="20" fill="#e3f0ff"/>
                    <ellipse cx="20" cy="25" rx="10" ry="7" fill="#fff"/>
                    <ellipse cx="20" cy="17" rx="7" ry="7" fill="#ffe0b2"/>
                    <ellipse cx="17" cy="16" rx="1.2" ry="1.5" fill="#333"/>
                    <ellipse cx="23" cy="16" rx="1.2" ry="1.5" fill="#333"/>
                    <path d="M17 20c1.5 1.5 4.5 1.5 6 0" stroke="#333" stroke-width="1" stroke-linecap="round"/>
                    <rect x="12" y="27" width="16" height="4" rx="2" fill="#90caf9"/>
                  </svg>
                </span>
                <span class="role-title">Student</span>
                <span class="role-desc">For learners enrolling in courses</span>
                <span class="fa fa-check-circle checkmark"></span>
              </label>
            </div>
            <div class="form-check form-check-inline p-0 m-0">
              <input class="form-check-input" type="radio" name="role" id="instructorRadio" value="instructor">
              <label class="role-card instructor" for="instructorRadio">
                <span class="role-avatar">
                  <!-- Instructor SVG Avatar -->
                  <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="20" cy="20" r="20" fill="#eafbe7"/>
                    <ellipse cx="20" cy="25" rx="10" ry="7" fill="#fff"/>
                    <ellipse cx="20" cy="17" rx="7" ry="7" fill="#ffe0b2"/>
                    <ellipse cx="17" cy="16" rx="1.2" ry="1.5" fill="#333"/>
                    <ellipse cx="23" cy="16" rx="1.2" ry="1.5" fill="#333"/>
                    <path d="M17 20c1.5 1.5 4.5 1.5 6 0" stroke="#333" stroke-width="1" stroke-linecap="round"/>
                    <rect x="13" y="27" width="14" height="4" rx="2" fill="#a5d6a7"/>
                    <rect x="15" y="10" width="10" height="4" rx="2" fill="#388e3c"/>
                  </svg>
                </span>
                <span class="role-title">Instructor</span>
                <span class="role-desc">For teachers sharing knowledge</span>
                <span class="fa fa-check-circle checkmark"></span>
              </label>
            </div>
          </div>
        </div>
        <div id="studentFields">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="firstName" class="form-label"><b>First Name</b></label>
              <input type="text" class="form-control" id="firstName" placeholder="Enter first name" name="firstName">
            </div>
            <div class="col-md-6">
              <label for="lastName" class="form-label"><b>Last Name</b></label>
              <input type="text" class="form-control" id="lastName" placeholder="Enter last name" name="lastName">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="address" class="form-label"><b>Address</b></label>
              <input type="text" class="form-control" id="address" placeholder="Enter your address" name="address">
            </div>
            <div class="col-md-6">
              <label for="education" class="form-label"><b>Education</b></label>
              <input type="text" class="form-control" id="education" placeholder="Education" name="education">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="email" class="form-label"><b>Email</b></label>
              <input type="email" class="form-control" id="email" placeholder="name@example.com" name="email">
            </div>
            <div class="col-md-6">
              <label for="contact" class="form-label"><b>Contact Number</b></label>
              <input type="tel" class="form-control" id="contact" placeholder="+92-300-xxxxxxx" name="contact">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="password" class="form-label"><b>Password</b></label>
              <input type="password" class="form-control" id="password" placeholder="Enter your password" name="password">
            </div>
            <div class="col-md-6">
              <label for="image" class="form-label"><b>Upload Image</b></label>
              <input class="form-control" type="file" id="image" name="image">
            </div>
          </div>
        </div>
        <div id="instructorFields" style="display:none;">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="instFirstName" class="form-label"><b>First Name</b></label>
              <input type="text" class="form-control" id="instFirstName" placeholder="Enter first name" name="instFirstName">
            </div>
            <div class="col-md-6">
              <label for="instLastName" class="form-label"><b>Last Name</b></label>
              <input type="text" class="form-control" id="instLastName" placeholder="Enter last name" name="instLastName">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="instAddress" class="form-label"><b>Address</b></label>
              <input type="text" class="form-control" id="instAddress" placeholder="Enter your address" name="instAddress">
            </div>
            <div class="col-md-6">
              <label for="instQualifications" class="form-label"><b>Qualifications</b></label>
              <input type="text" class="form-control" id="instQualifications" placeholder="Qualifications" name="instQualifications">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="instExpertise" class="form-label"><b>Expertise</b></label>
              <input type="text" class="form-control" id="instExpertise" placeholder="Expertise" name="instExpertise">
            </div>
            <div class="col-md-6">
              <label for="instEmail" class="form-label"><b>Email</b></label>
              <input type="email" class="form-control" id="instEmail" placeholder="name@example.com" name="instEmail">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="instContact" class="form-label"><b>Contact Number</b></label>
              <input type="tel" class="form-control" id="instContact" placeholder="+92-300-xxxxxxx" name="instContact">
            </div>
            <div class="col-md-6">
              <label for="instPassword" class="form-label"><b>Password</b></label>
              <input type="password" class="form-control" id="instPassword" placeholder="Enter your password" name="instPassword">
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="instImage" class="form-label"><b>Upload Image</b></label>
              <input class="form-control" type="file" id="instImage" name="instImage">
            </div>
          </div>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary px-5 py-2 rounded-pill mt-4" name="Register">Register Now</button>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  // Toggle fields based on user type and enable/disable fields
  const studentRadio = document.getElementById('studentRadio');
  const instructorRadio = document.getElementById('instructorRadio');
  const studentFields = document.getElementById('studentFields');
  const instructorFields = document.getElementById('instructorFields');

  function setFieldsState() {
    const studentInputs = studentFields.querySelectorAll('input, select');
    const instructorInputs = instructorFields.querySelectorAll('input, select');
    if (studentRadio.checked) {
      studentFields.style.display = '';
      instructorFields.style.display = 'none';
      studentInputs.forEach(el => el.disabled = false);
      instructorInputs.forEach(el => el.disabled = true);
    } else if (instructorRadio.checked) {
      studentFields.style.display = 'none';
      instructorFields.style.display = '';
      studentInputs.forEach(el => el.disabled = true);
      instructorInputs.forEach(el => el.disabled = false);
    } else {
      studentFields.style.display = 'none';
      instructorFields.style.display = 'none';
      studentInputs.forEach(el => el.disabled = true);
      instructorInputs.forEach(el => el.disabled = true);
    }
  }

  // Initially disable all fields and hide both fieldsets
  window.addEventListener('DOMContentLoaded', function() {
    studentFields.style.display = 'none';
    instructorFields.style.display = 'none';
    studentFields.querySelectorAll('input, select').forEach(el => el.disabled = true);
    instructorFields.querySelectorAll('input, select').forEach(el => el.disabled = true);
  });

  studentRadio.addEventListener('change', setFieldsState);
  instructorRadio.addEventListener('change', setFieldsState);

  // On form submit, check if a radio is selected and enable relevant fields
  const form = document.getElementById('registrationForm');
  form.addEventListener('submit', function(e) {
    if (!studentRadio.checked && !instructorRadio.checked) {
      alert('Please select Student or Instructor.');
      e.preventDefault();
      return;
    }
    setFieldsState(); // Ensure correct fields are enabled before submit
  });
  </script>
</body>
</html>
