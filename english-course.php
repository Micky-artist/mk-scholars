<?php
session_start();
include("./dbconnection/connection.php");
include('./php/validateSession.php');

if (!isset($_SESSION['userId'])) {
    header("Location: login");
    exit;
}

$UserId = $_SESSION['userId'];
$selectUserDetails = mysqli_query($conn, "SELECT * FROM normUsers WHERE NoUserId = $UserId");
$userData = $selectUserDetails->num_rows > 0 ? mysqli_fetch_assoc($selectUserDetails) : [];

$name  = $userData['NoUsername'] ?? 'Unknown User';
$email = $userData['NoEmail'] ?? 'Unknown Email';
$phone = $userData['NoPhone'] ?? '';

$formData = [
    'courses' => $_POST['courses'] ?? ['English Communication Course'],
    'terms'   => isset($_POST['terms']),
];
$errors = [];

if (isset($_POST['checkout'])) {
    $sub = urlencode($_POST['subscription']);
    $amt = floatval($_POST['amount']);
    header("Location: ./payment/checkout?subscription={$sub}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1.0" />
    <title>Register - English Course</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
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
            margin-top: 10px;
        }

        .selected-courses {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .subscribe-btn {
            background-color: var(--primary);
            color: white;
        }

        .subscribe-btn:hover {
            background-color: #3aa9ac;
        }
    </style>
</head>

<body>
    <?php include("./partials/coursesNav.php"); ?>

    <div class="container py-5">
        <form method="POST" class="bg-white p-4 rounded shadow-sm form-section">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="mb-4">Register for English Course</h3>

                    <div class="course-card active"
                        data-course-id="2"
                        data-course-name="English Communication Course"
                        data-course-amount="20000">
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="mb-0">English Communication Course (20,000 RWF)</h4>
                            <span class="badge bg-success">Selected</span>
                        </div>
                        <div class="course-details">
                            <ul class="list-unstyled mt-3">
                                <li>✓ Starts on 15th June 2025</li>
                                <li>✓ Online Weekend and Evening Classes</li>
                                <li>✓ Learn Speaking, Listening, Reading, and Writing</li>
                                <li>✓ For students, professionals & beginners</li>
                                <li>✓ Certificate of Completion</li>
                                <li>✓ Includes assignments and downloadable resources</li>
                            </ul>
                            <p><b>Need help?</b> Call/WhatsApp: +250 798 611 161</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h3 class="mb-4">Your Details</h3>
                    <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>

                    <input type="hidden" name="subscription" value="englishcourse" />
                    <input type="hidden" name="amount" value="20000" />

                    <div class="selected-courses">
                        <h4>Course</h4>
                        <ul>
                            <li>English Communication Course</li>
                        </ul>
                    </div>

                    <div class="form-check mt-3 mb-4">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="./terms-and-conditions" target="_blank">terms & conditions</a>.
                        </label>
                    </div>

                    <button name="checkout" class="btn subscribe-btn w-100 py-2">
                        Register Now <i class="fas fa-paper-plane ms-2"></i>
                    </button>
                    <a class="btn btn-secondary w-100 mt-2" href="./index">Back to home</a>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
