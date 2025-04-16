<?php
session_start();
include("./dbconnection/connection.php");

if(!isset($_SESSION['userId'])){
    header("Location: login");
    exit;
}
// Handle form submission
$showSuccessModal = false;
$errors = [];

$UserId = $_SESSION['userId'];

$selectUserDetails = mysqli_query($conn, "SELECT * FROM normUsers WHERE NoUserId = $UserId");
if($selectUserDetails -> num_rows > 0){
    $userData = mysqli_fetch_assoc($selectUserDetails);
}

// Use session values for personal details.
$name  = $userData['NoUsername'] ?? 'Unknown User';
$email = $userData['NoEmail'] ?? 'Unknown Email';
$phone = $userData['NoPhone'] ?? '';


// Preserve form data on validation errors
$formData = [
    'name'    => $_POST['name'] ?? '',
    'email'   => $_POST['email'] ?? '',
    'phone'   => $_POST['phone'] ?? '',
    'courseId'=> $_POST['courseId'] ?? 1, // Default to UCAT course (ID 1)
    'courses' => $_POST['courses'] ?? ['UCAT Online Coaching Course'], // Always include UCAT
    'comment' => $_POST['comment'] ?? '',
    'terms'   => isset($_POST['terms']),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $name    = trim($formData['name']);
    $email   = trim($formData['email']);
    $phone   = trim($formData['phone']);
    $courseId= (int)$formData['courseId'];
    $courses = $formData['courses'];
    $comment = trim($formData['comment']);
    $terms   = $formData['terms'];

    // Validation rules
    if (empty($name)) $errors['name'] = 'Please enter your name';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address';
    if (empty($phone)) $errors['phone'] = 'Please enter your phone number';
    if (!in_array($courseId, [1, 2, 3, 4])) $errors['course'] = 'Please select a valid course';
    if (count($courses) < 2 || !in_array('UCAT Online Coaching Course', $courses)) $errors['courses'] = 'Please select either the 15 Days or 30 Days Course (15.000 RWF)';
    if (strlen($comment) > 1000) $errors['comment'] = 'Comment must be 1000 characters or less';
    if (!$terms) $errors['terms'] = 'You must accept the terms';

    if (empty($errors)) {
        // Convert courses array to a string for database storage
        $coursesString = is_array($courses) ? implode(', ', $courses) : 'UCAT Online Coaching Course';

        // Limit courses string to 255 characters
        if (strlen($coursesString) > 255) {
            $coursesString = substr($coursesString, 0, 252) . '...';
        }

        $date = date("Y-m-d");
        $time = date("H:i:s");

        // Use prepared statements to prevent SQL injection
        // (Note: The CourseId in the query is set to 2 here per original code; adjust as needed.)
        $stmt = mysqli_prepare($conn, "INSERT INTO applicationsSurvey (FullNames, Email, Phone, CourseId, ApplicationContent, Comment, SubmitDate, SubmitTime, applicationStatus) VALUES (?, ?, ?, 2, ?, ?, ?, ?, 0)");

        if ($stmt) {
            // Bind parameters with appropriate types
            mysqli_stmt_bind_param(
                $stmt,
                "sssssss",
                $name,          // s = string
                $email,         // s = string
                $phone,         // s = string
                $coursesString, // s = string (course titles)
                $comment,       // s = string
                $date,          // s = string
                $time           // s = string
            );

            // Execute the statement
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                $showSuccessModal = true;
                // Reset form data after successful submission
                $formData = [
                    'name'    => '',
                    'email'   => '',
                    'phone'   => '',
                    'courseId'=> 1,
                    'courses' => ['UCAT Online Coaching Course'],
                    'comment' => '',
                    'terms'   => false,
                ];
            } else {
                $errors['database'] = "Error: " . mysqli_stmt_error($stmt);
            }

            // Close statement
            mysqli_stmt_close($stmt);
        } else {
            $errors['database'] = "Database error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn with Us!</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Chewy&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #4bc2c5;
            --secondary: #ff7a7a;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .course-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            margin: 15px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 3px solid transparent;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        .course-card.active {
            border-color: var(--primary);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .course-details {
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .course-card.active .course-details {
            max-height: 500px;
            margin-top: 15px;
        }
        .form-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
        .selected-courses {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .selected-courses h4 {
            margin-bottom: 10px;
        }
        .selected-courses ul {
            list-style: none;
            padding: 0;
        }
        .selected-courses ul li {
            margin: 5px 0;
        }
        .thank-you-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
            display: none;
        }
        .thank-you-popup.active {
            display: block;
        }
        .thank-you-popup h2 {
            color: #4bc2c5;
            margin-bottom: 20px;
        }
        .thank-you-popup p {
            font-size: 18px;
            color: #333;
        }
        .thank-you-popup button {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            border-radius: 25px;
            background: #4bc2c5;
            color: white;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .thank-you-popup button:hover {
            background: #3aa9ac;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            padding: .2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            text-decoration: none;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }
        .brand-text {
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .navbar-nav {
            display: flex;
            flex-direction: row;
            gap: 2rem;
            align-items: center;
        }
        .nav-link {
            color: #4a4a4a;
            text-decoration: none;
            font-weight: 500;
            position: relative;
            padding: 0.5rem 0;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .nav-button {
            background: #2196F3;
            color: white;
            border: none;
            padding: 0.8rem 1.8rem;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }
        .nav-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            color: var(--primary);
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include("./partials/coursesNav.php"); ?>

    <div class="container">
        <form method="POST" class="form-section mb-4">
            <div class="row">
                <!-- Course Selection -->
                <div class="col-md-6">
                    <h3 class="mb-4">Register For UCAT Course</h3>

                    <!-- UCAT Course (Always Selected) -->
                    <div class="course-card active" data-course-id="1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h4 class="mb-0 ms-3">UCAT Online Coaching Course</h4>
                            </div>
                            <span class="badge bg-success">Active</span>
                        </div>
                        <div class="course-details">
                            <hr>
                            <p class="text-muted">You'll learn:</p>
                            <ul class="list-unstyled">
                                <li>✓ Verbal Reasoning – Reading and analyzing information quickly</li>
                                <li>✓ Decision Making – Solving problems using logic and reasoning</li>
                                <li>✓ Quantitative Reasoning – Working with numbers and data</li>
                                <li>✓ Abstract Reasoning – Identifying patterns and solving puzzles</li>
                                <li>✓ Situational Judgement – Understanding ethical and professional scenarios</li>
                            </ul>
                            <div>
                                <h5><b>Note: Before you pay WhatsApp or call us on: +250 798 611 161</b></h5>
                            </div>
                        </div>
                    </div>
                   
                    <!-- 15 Days Course (8000 RWF) -->
                    <div class="course-card" data-course-id="2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h4 class="mb-0">15 Days Course (8000 RWF)</h4>
                            </div>
                            <button type="button" class="btn btn-dark btn-sm choose-course">Select</button>
                        </div>
                    </div>
                    
                    <!-- 30 Days Course (15.000 RWF) -->
                    <div class="course-card" data-course-id="3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h4 class="mb-0">30 Days Course (15.000 RWF)</h4>
                            </div>
                            <button type="button" class="btn btn-dark btn-sm choose-course">Select</button>
                        </div>
                    </div>
                    
                    <!-- Only Notes (4000 RWF) -->
                    <div class="course-card" data-course-id="4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <h4 class="mb-0">Only Notes (4000 RWF)</h4>
                            </div>
                            <button type="button" class="btn btn-dark btn-sm choose-course">Select</button>
                        </div>
                    </div>
                </div>

                <!-- User Details (Automatically populated from session) -->
                <div class="col-md-6">
                    <h3 class="mb-4">Your Details</h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars($name); ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($email); ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($phone); ?></p>
                    <!-- Hidden fields to pass user data -->
                    <input type="hidden" name="name" value="<?= htmlspecialchars($name); ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email); ?>">
                    <input type="hidden" name="phone" value="<?= htmlspecialchars($phone); ?>">
                    <!-- Hidden input for courseId; default value now matches UCAT (ID 1) -->
                    <input type="hidden" name="courseId" value="1">

                    <!-- Selected Courses (display only) -->
                    <div class="selected-courses">
                        <h4>Choices</h4>
                        <ul id="selected-courses-list">
                            <?php foreach ($formData['courses'] as $course): ?>
                                <li><?= htmlspecialchars($course) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <!-- Terms & Conditions -->
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" <?= $formData['terms'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="./terms-and-conditions" target="_blank" class="text-decoration-none">terms & conditions</a> and <a href="./privacy-policy" target="_blank" class="text-decoration-none">Privacy Policy</a>
                        </label>
                        <?php if (isset($errors['terms'])): ?>
                            <div class="text-danger"><?= $errors['terms'] ?></div>
                        <?php endif; ?>
                    </div>

                    <button style="margin:0 0 10px 0" type="submit" class="btn btn-info w-100 rounded-pill py-2">
                        Enroll Now! <i class="fas fa-paper-plane ms-2"></i>
                    </button>
                    <a class="btn btn-secondary w-100 rounded-pill py-2" href="./index">Back to home</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Thank You Popup (if needed) -->
    <div class="thank-you-popup">
        <h2>Thank You! 🎉</h2>
        <p>We appreciate your interest in our courses. We'll get back to you soon!</p>
        <button onclick="closeThankYouPopup()">Close</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Course Selection Logic
        let selectedCourseId = 1; // Default to UCAT course
        const selectedCourses = new Set(['UCAT Online Coaching Course']); // Always include UCAT

        document.querySelectorAll('.choose-course').forEach(button => {
            button.addEventListener('click', (e) => {
                const courseCard = e.target.closest('.course-card');
                const courseId = courseCard.dataset.courseId;
                const courseName = courseCard.querySelector('h4').innerText;

                // Remove active class from all course cards except UCAT
                document.querySelectorAll('.course-card').forEach(card => {
                    if (card.dataset.courseId !== "1") {
                        card.classList.remove('active');
                    }
                });

                // Add active class to selected course
                courseCard.classList.add('active');
                selectedCourseId = courseId;

                // Update the selected courses set
                selectedCourses.delete('15 Days Course (8000 RWF)');
                selectedCourses.delete('30 Days Course (15.000 RWF)');
                selectedCourses.delete('Only Notes (4000 RWF)');
                selectedCourses.add(courseName);

                // Update the hidden input for courseId
                document.querySelector('input[name="courseId"]').value = selectedCourseId;

                // Update the selected courses display
                updateSelectedCoursesList();

                // Update button states
                document.querySelectorAll('.choose-course').forEach(btn => {
                    if (btn === e.target) {
                        btn.textContent = 'Selected';
                        btn.classList.remove('btn-dark');
                        btn.classList.add('btn-success');
                    } else {
                        btn.textContent = 'Select';
                        btn.classList.remove('btn-success');
                        btn.classList.add('btn-dark');
                    }
                });
            });
        });

        // Function to update the selected courses list and hidden inputs
        function updateSelectedCoursesList() {
            const selectedCoursesList = document.getElementById('selected-courses-list');
            selectedCoursesList.innerHTML = '';

            // Add selected courses to the list
            selectedCourses.forEach(course => {
                const li = document.createElement('li');
                li.textContent = course;
                selectedCoursesList.appendChild(li);
            });

            // Add hidden inputs for each selected course
            selectedCourses.forEach(course => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'courses[]';
                input.value = course;
                selectedCoursesList.appendChild(input);
            });
        }

        // Initialize with UCAT course selected
        document.querySelector('input[name="courseId"]').value = selectedCourseId;
        updateSelectedCoursesList();

        function closeThankYouPopup() {
            document.querySelector('.thank-you-popup').classList.remove('active');
        }
    </script>
</body>

</html>
