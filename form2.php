<?php
session_start();
include("./dbconnection/connection.php");

// Handle form submission
$showSuccessModal = false;
$errors = [];

// Preserve form data on validation errors
$formData = [
    'name' => $_POST['name'] ?? '',
    'email' => $_POST['email'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'courses' => $_POST['courses'] ?? [],
    'comment' => $_POST['comment'] ?? '',
    'terms' => isset($_POST['terms']),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $name = trim($formData['name']);
    $email = trim($formData['email']);
    $phone = trim($formData['phone']);
    $courses = $formData['courses'];
    $comment = trim($formData['comment']);
    $terms = $formData['terms'];

    if (empty($name)) $errors['name'] = 'Please enter your name';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address';
    if (empty($phone)) $errors['phone'] = 'Please enter your phone number';
    if (empty($courses)) $errors['courses'] = 'Please select at least one course';
    
    // Changed from word count to character count
    if (strlen($comment) > 1000) $errors['comment'] = 'Comment must be 1000 characters or less';
    if (!$terms) $errors['terms'] = 'You must accept the terms';

    if (empty($errors)) {
        // Convert courses array to string and limit to varchar(255)
        $coursesString = is_array($courses) ? implode(', ', $courses) : '';
        if (strlen($coursesString) > 255) {
            $coursesString = substr($coursesString, 0, 252) . '...';
        }

        $date = date("Y-m-d");
        $time = date("H:i:s");
        
        // Use prepared statements to prevent SQL injection
        $stmt = mysqli_prepare($conn, "INSERT INTO applicationsSurvey(Email, Phone, ApplicationContent, Comment, SubmitDate, SubmitTime, applicationStatus) VALUES (?, ?, ?, ?, ?, ?, 0)");
        
        if ($stmt) {
            // Bind parameters with appropriate types
            mysqli_stmt_bind_param($stmt, "ssssss", 
                $email,     // s = string
                $phone,     // s = string
                $coursesString, // s = string
                $comment,   // s = string (fixed variable name from $Comment to $comment)
                $date,      // s = string
                $time       // s = string
            );
            
            // Execute the statement
            $result = mysqli_stmt_execute($stmt);
            
            if ($result) {
                $showSuccessModal = true;
                // Reset form data after successful submission
                $formData = [
                    'name' => '',
                    'email' => '',
                    'phone' => '',
                    'courses' => [],
                    'comment' => '',
                    'terms' => false,
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
    <title>Learn with Us! 🌍</title>
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
            min-height: 100vh;
        }

        .hero-title {
            font-family: 'Chewy', cursive;
            color: var(--primary);
            text-shadow: 2px 2px 0px rgba(0, 0, 0, 0.1);
            font-size: 2.5rem;
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

        .course-icon {
            font-size: 2rem;
            margin-bottom: 15px;
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

        .badge-certificate {
            background: linear-gradient(45deg, #ff6b6b, #ff8e8e);
            font-family: 'Chewy';
            letter-spacing: 1px;
        }

        .form-section {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .success-modal {
            background: linear-gradient(135deg, #4bc2c5 0%, #6dd5fa 100%);
            color: white;
            border-radius: 20px;
        }

        .custom-checkbox {
            position: relative;
            display: inline-block;
            width: 24px;
            height: 24px;
        }

        .custom-checkbox input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            position: absolute;
            top: 0;
            left: 0;
            height: 24px;
            width: 24px;
            background-color: #eee;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .custom-checkbox:hover input[type="checkbox"]~.checkmark {
            background-color: #ccc;
        }

        .custom-checkbox input[type="checkbox"]:checked~.checkmark {
            background-color: #2196F3;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
        }

        .custom-checkbox input[type="checkbox"]:checked~.checkmark:after {
            display: block;
        }

        .custom-checkbox .checkmark:after {
            left: 8px;
            top: 4px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        .checkbox-container {
            display: flex;
            align-items: center;
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

        .word-count {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>

<body class="py-5">
    <div class="container">
        <h1 class="hero-title text-center mb-5">Start Your Learning Journey! with <br> MK Scholars 🚀</h1>

        <form method="POST" class="form-section p-4 mb-4">
            <div class="row">
                <!-- Course Selection -->
                <div class="col-md-6">
                    <h3 class="mb-4">Choose Your Course</h3>

                    <div class="course-card" data-course="english">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="./images/home/en.png" alt="" width="30" height="30">
                                <h4 class="mb-0 ms-3">English Mastery</h4>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm choose-course">Choose</button>
                        </div>
                        <div class="course-details">
                            <hr>
                            <p class="text-muted">You'll learn:</p>
                            <ul class="list-unstyled">
                                <li>✓ Grammar fundamentals</li>
                                <li>✓ Business communication</li>
                                <li>✓ Pronunciation mastery</li>
                                <li>✓ Cultural immersion</li>
                            </ul>
                            <span class="badge badge-certificate rounded-pill">Certificate Included</span>
                        </div>
                    </div>

                    <!-- Repeat similar blocks for other courses -->
                    <!-- French Course -->
                    <div class="course-card" data-course="french">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="./images/home/fr.png" alt="" width="30" height="30">
                                <h4 class="mb-0 ms-3">French Mastery</h4>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm choose-course">Choose</button>
                        </div>
                        <div class="course-details">
                            <hr>
                            <p class="text-muted">You'll learn:</p>
                            <ul class="list-unstyled">
                                <li>✓ Grammar fundamentals</li>
                                <li>✓ Business communication</li>
                                <li>✓ Pronunciation mastery</li>
                                <li>✓ Cultural immersion</li>
                            </ul>
                            <span class="badge badge-certificate rounded-pill">Certificate Included</span>
                        </div>
                    </div>

                    <!-- German Course -->
                    <div class="course-card" data-course="german">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img src="./images/home/ge.png" alt="" width="30" height="30">
                                <h4 class="mb-0 ms-3">German Mastery</h4>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm choose-course">Choose</button>
                        </div>
                        <div class="course-details">
                            <hr>
                            <p class="text-muted">You'll learn:</p>
                            <ul class="list-unstyled">
                                <li>✓ Grammar fundamentals</li>
                                <li>✓ Business communication</li>
                                <li>✓ Pronunciation mastery</li>
                                <li>✓ Cultural immersion</li>
                            </ul>
                            <span class="badge badge-certificate rounded-pill">Certificate Included</span>
                        </div>
                    </div>

                    <!-- Coding Course -->
                    <div class="course-card" data-course="coding">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="course-icon fas fa-code text-danger me-3"></i>
                                <h4 class="mb-0">Coding Bootcamp</h4>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm choose-course">Choose</button>
                        </div>
                        <div class="course-details">
                            <hr>
                            <p class="text-muted">You'll learn:</p>
                            <ul class="list-unstyled">
                                <li>✓ Web development fundamentals</li>
                                <li>✓ JavaScript & Python</li>
                                <li>✓ Database management</li>
                                <li>✓ Project development</li>
                            </ul>
                            <span class="badge badge-certificate rounded-pill">Certificate Included</span>
                        </div>
                    </div>
                </div>

                <!-- Personal Details -->
                <div class="col-md-6">
                    <h3 class="mb-4">Your Details</h3>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control rounded-pill" value="<?= htmlspecialchars($formData['name']) ?>" required>
                        <?php if (isset($errors['name'])): ?>
                            <div class="text-danger"><?= $errors['name'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control rounded-pill" value="<?= htmlspecialchars($formData['email']) ?>" required>
                        <?php if (isset($errors['email'])): ?>
                            <div class="text-danger"><?= $errors['email'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control rounded-pill" value="<?= htmlspecialchars($formData['phone']) ?>" required>
                        <?php if (isset($errors['phone'])): ?>
                            <div class="text-danger"><?= $errors['phone'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Comment Section -->
                    <div class="mb-3">
                        <label class="form-label">Your Comment (max 200 words)</label>
                        <textarea name="comment" class="form-control" rows="4" maxlength="1000"><?= htmlspecialchars($formData['comment']) ?></textarea>
                        <div class="word-count" id="word-count">Words: 0/200</div>
                        <?php if (isset($errors['comment'])): ?>
                            <div class="text-danger"><?= $errors['comment'] ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Selected Courses -->
                    <div class="selected-courses">
                        <h4>Selected Courses</h4>
                        <ul id="selected-courses-list">
                            <?php foreach ($formData['courses'] as $course): ?>
                                <li><?= htmlspecialchars($course) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" <?= $formData['terms'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="text-decoration-none">terms & conditions</a>
                        </label>
                        <?php if (isset($errors['terms'])): ?>
                            <div class="text-danger"><?= $errors['terms'] ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" style="background-color: purple;" class="btn btn-primary w-100 rounded-pill py-2">
                        Enroll Now! <i class="fas fa-paper-plane ms-2"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Thank You Popup -->
    <div class="thank-you-popup">
        <h2>Thank You! 🎉</h2>
        <p>We appreciate your interest in our courses. We'll get back to you soon!</p>
        <button onclick="closeThankYouPopup()">Close</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Course Selection
        document.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('click', () => {
                document.querySelectorAll('.course-card').forEach(c => c.classList.remove('active'));
                card.classList.add('active');
                document.querySelector('[name="course"]').value = card.dataset.course;
            });
        });
        // Handle course selection
        const selectedCourses = new Set(<?= json_encode($formData['courses']) ?>);

        document.querySelectorAll('.choose-course').forEach(button => {
            button.addEventListener('click', () => {
                const courseCard = button.closest('.course-card');
                const courseName = courseCard.querySelector('h4').innerText;

                if (selectedCourses.has(courseName)) {
                    selectedCourses.delete(courseName);
                    button.textContent = 'Choose';
                    button.classList.remove('btn-success');
                    button.classList.add('btn-primary');
                } else {
                    selectedCourses.add(courseName);
                    button.textContent = 'Selected';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-success');
                }

                updateSelectedCoursesList();
            });
        });

        function updateSelectedCoursesList() {
            const selectedCoursesList = document.getElementById('selected-courses-list');
            selectedCoursesList.innerHTML = '';
            selectedCourses.forEach(course => {
                const li = document.createElement('li');
                li.textContent = course;
                selectedCoursesList.appendChild(li);
            });

            // Update hidden input for courses
            const coursesInput = document.createElement('input');
            coursesInput.type = 'hidden';
            coursesInput.name = 'courses[]';
            coursesInput.value = Array.from(selectedCourses).join(',');
            selectedCoursesList.appendChild(coursesInput);
        }

        // Word count for comment
        const commentTextarea = document.querySelector('textarea[name="comment"]');
        const charCountDisplay = document.getElementById('word-count');
        const maxCharacters = 200; // Set your desired maximum character limit here

        commentTextarea.addEventListener('input', () => {
            const characters = commentTextarea.value.length;
            charCountDisplay.textContent = `Characters: ${characters}/${maxCharacters}`;

            // Optional: Add visual feedback when approaching the limit
            if (characters > maxCharacters) {
                charCountDisplay.classList.add('text-danger');
            } else if (characters > maxCharacters * 0.9) {
                charCountDisplay.classList.add('text-warning');
                charCountDisplay.classList.remove('text-danger');
            } else {
                charCountDisplay.classList.remove('text-warning', 'text-danger');
            }
        });

        // Show thank you popup on form submission
        <?php if ($showSuccessModal): ?>
            document.querySelector('.thank-you-popup').classList.add('active');
        <?php endif; ?>

        function closeThankYouPopup() {
            document.querySelector('.thank-you-popup').classList.remove('active');
        }
    </script>
</body>

</html>