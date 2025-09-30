<?php
session_start();
include("./dbconnection/connection.php");
include('./php/validateSession.php');

if(!isset($_SESSION['userId'])){
    header("Location: login");
    exit;
}

$UserId = $_SESSION['userId'];
$selectUserDetails = mysqli_query($conn, "SELECT * FROM normUsers WHERE NoUserId = $UserId");
if($selectUserDetails->num_rows > 0){
    $userData = mysqli_fetch_assoc($selectUserDetails);
}

$name  = $userData['NoUsername'] ?? 'Unknown User';
$email = $userData['NoEmail']   ?? 'Unknown Email';
$phone = $userData['NoPhone']   ?? '';

// Get course parameter
$courseId = $_GET['course'] ?? '';
$courseData = null;

// Fetch course data from database if courseId is provided
if ($courseId && is_numeric($courseId)) {
    $courseQuery = "SELECT c.*, cp.amount, cp.currency, cp.pricingDescription, curr.currencySymbol 
                    FROM Courses c 
                    LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
                    LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                    WHERE c.courseId = ? AND c.courseDisplayStatus = 1";
    
    $stmt = $conn->prepare($courseQuery);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $courseResult = $stmt->get_result();
    
    if ($courseResult->num_rows > 0) {
        $courseData = $courseResult->fetch_assoc();
    }
    $stmt->close();
}

// Default course data if no course found or for static courses
$defaultCourses = [
    'deutsch-academy' => [
        'courseName' => 'Study Deutsch in MK Deutsch Academy',
        'courseDescription' => 'Master German Language for Academic & Career Success',
        'pricingOptions' => [
            ['name' => 'Complete Program', 'amount' => 25000, 'currency' => 'RWF', 'description' => 'Full German language program A1 to B2 levels']
        ],
        'features' => [
            'Certified Instructors',
            '25 Seats Available', 
            'Official Certificate',
            'Flexible Schedule'
        ],
        'contact' => 'For more info WhatsApp/call: +250 798 611 161'
    ],
    'ucat' => [
        'courseName' => 'UCAT Online Coaching Course',
        'courseDescription' => 'For Future Medical Students',
        'pricingOptions' => [
            ['name' => 'Prepared Notes and Answers', 'amount' => 7500, 'currency' => 'RWF', 'description' => 'Complete study materials and answers'],
            ['name' => 'Online Coaching with a Teacher', 'amount' => 15000, 'currency' => 'RWF', 'description' => 'Personalized coaching with expert instructor']
        ],
        'features' => [
            'Verbal Reasoning – Reading and analyzing quickly',
            'Decision Making – Solving problems logically',
            'Quantitative Reasoning – Working with numbers',
            'Abstract Reasoning – Spotting patterns',
            'Situational Judgement – Ethical scenarios'
        ],
        'contact' => 'For more info WhatsApp/call: +250 798 611 161'
    ],
    'alu-english-program' => [
        'courseName' => 'ALU English Proficiency Program',
        'courseDescription' => 'Boost Your English Skills for Academic & Career Success',
        'pricingOptions' => [
            ['name' => '10 Days Practice', 'amount' => 15000, 'currency' => 'RWF', 'description' => 'Comprehensive 10-day practice program'],
            ['name' => 'Sample Questions', 'amount' => 15000, 'currency' => 'RWF', 'description' => 'Detailed sample questions and explanations']
        ],
        'features' => [
            'Live Virtual Classes',
            '40 Seats Available',
            'Practice Materials',
            'Success Guaranteed'
        ],
        'contact' => 'For more info WhatsApp/call: +250 798 611 161'
    ],
    'coding-course' => [
        'courseName' => 'Coding Bootcamp',
        'courseDescription' => 'For Beginners & Tech Enthusiasts',
        'pricingOptions' => [
            ['name' => 'Complete Package', 'amount' => 25000, 'currency' => 'RWF', 'description' => 'Full coding course with HTML, CSS, JavaScript, React JS, MySQL, and Node.js']
        ],
        'features' => [
            'Live Mentoring',
            '30 Seats Available',
            'PDF Notes & Assignments',
            'Flexible Schedule'
        ],
        'contact' => 'For more info WhatsApp/call: +250 798 611 161'
    ],
    'english-course' => [
        'courseName' => 'English Communication Course',
        'courseDescription' => 'For All Levels – Learn to Speak & Write Confidently',
        'pricingOptions' => [
            ['name' => 'Complete Package', 'amount' => 15000, 'currency' => 'RWF', 'description' => 'Comprehensive English speaking, listening, reading, and writing course']
        ],
        'features' => [
            'Expert Instructors',
            '20 Seats Available',
            'Practice Materials',
            'Flexible Schedule'
        ],
        'contact' => 'For more info WhatsApp/call: +250 798 611 161'
    ]
];

// Determine which course data to use
$currentCourse = null;
if ($courseData) {
    // Fetch all pricing options for this course
    $pricingQuery = "SELECT cp.*, curr.currencySymbol 
                     FROM CoursePricing cp 
                     LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                     WHERE cp.courseId = ? 
                     ORDER BY cp.amount ASC";
    
    $stmt = $conn->prepare($pricingQuery);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $pricingResult = $stmt->get_result();
    
    $pricingOptions = [];
    while ($pricing = $pricingResult->fetch_assoc()) {
        $pricingOptions[] = [
            'name' => $pricing['pricingDescription'] ?: 'Course Access',
            'amount' => $pricing['amount'],
            'currency' => $pricing['currencySymbol'] ?: $pricing['currency'] ?: 'RWF',
            'paymentCode' => $pricing['coursePaymentCodeName'],
            'description' => $pricing['pricingDescription'] ?: 'Full course access with all materials'
        ];
    }
    $stmt->close();
    
    // Use database course data
    $currentCourse = [
        'courseName' => $courseData['courseName'],
        'courseDescription' => $courseData['courseDescription'],
        'pricingOptions' => $pricingOptions,
        'features' => json_decode($courseData['courseFeatures'] ?? '[]', true) ?: ['Course Materials', 'Expert Support', 'Certificate'],
        'contact' => 'For more info WhatsApp/call: +250 798 611 161'
    ];
} elseif (isset($defaultCourses[$courseId])) {
    // Use static course data
    $currentCourse = $defaultCourses[$courseId];
} else {
    // Default to UCAT if no course specified
    $currentCourse = $defaultCourses['ucat'];
}

$formData = [
    'courses' => $_POST['courses'] ?? [$currentCourse['courseName']],
    'terms'   => isset($_POST['terms']),
];
$errors = [];

if (isset($_POST['checkout'])) {
    $sub = urlencode($_POST['subscription']);
    $courseId = $_GET['course'];
    header("Location: ./payment/checkout?course={$courseId}&subscription={$sub}");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Course Registration - <?php echo htmlspecialchars($currentCourse['courseName']); ?></title>
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

        .navbar {
            margin-bottom: 1cm;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.4);
            padding: .2rem 2rem;
            top: 0 !important;
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
            display: flex !important;
            flex-direction: row;
            gap: 2rem;
            align-items: center;
            transition: all 0.3s ease;
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
            transition: all 0.3s ease;
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

        /* Validation Error Styles */
        #validation-error {
            border-left: 4px solid #dc3545;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .course-card.required-highlight {
            border-color: #dc3545 !important;
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%) !important;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .pricing-option {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 15px;
            margin: 10px 0;
            transition: all 0.3s ease;
        }

        .pricing-option:hover {
            border-color: var(--primary);
            transform: translateY(-2px);
        }

        .pricing-option.selected {
            border-color: var(--primary);
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .price-tag {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
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
          <h3 class="mb-4">Register For <?php echo htmlspecialchars($currentCourse['courseName']); ?></h3>

          <!-- Main Course Card -->
          <div class="course-card active"
               data-course-id="main"
               data-course-name="<?php echo htmlspecialchars($currentCourse['courseName']); ?>"
               data-course-amount="0">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <h4 class="mb-0 ms-3"><?php echo htmlspecialchars($currentCourse['courseName']); ?></h4>
              </div>
              <span class="badge bg-success">Active</span>
            </div>
            <div class="course-details">
              <hr>
              <p class="text-muted"><?php echo htmlspecialchars($currentCourse['courseDescription']); ?></p>
              <p class="text-muted">You'll learn:</p>
              <ul class="list-unstyled">
                <?php foreach($currentCourse['features'] as $feature): ?>
                  <li>✓ <?php echo htmlspecialchars($feature); ?></li>
                <?php endforeach; ?>
              </ul>
              <div>
                <h5>
                  <b><?php echo htmlspecialchars($currentCourse['contact']); ?></b>
                </h5>
              </div>
            </div>
          </div>

          <!-- Pricing Options -->
          <?php if (count($currentCourse['pricingOptions']) > 1): ?>
            <?php foreach($currentCourse['pricingOptions'] as $index => $option): ?>
              <div class="course-card pricing-option"
                   data-course-id="<?php echo $index + 1; ?>"
                   data-course-name="<?php echo htmlspecialchars($option['paymentCode']); ?>"
                   data-course-amount="<?php echo $option['amount']; ?>">
                <div class="d-flex align-items-center justify-content-between">
                  <div>
                    <h4 class="mb-1"><?php echo htmlspecialchars($option['name']); ?></h4>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($option['description']); ?></p>
                    <span class="price-tag"><?php echo number_format($option['amount']); ?> <?php echo htmlspecialchars($option['currency']); ?></span>
                  </div>
                  <button type="button" class="btn btn-dark btn-sm choose-course">Select</button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Single pricing option -->
            <?php $option = $currentCourse['pricingOptions'][0]; ?>
            <div class="course-card pricing-option selected"
                 data-course-id="1"
                 data-course-name="<?php echo htmlspecialchars($option['paymentCode']); ?>"
                 data-course-amount="<?php echo $option['amount']; ?>">
              <div class="d-flex align-items-center justify-content-between">
                <div>
                  <h4 class="mb-1"><?php echo htmlspecialchars($option['name']); ?></h4>
                  <p class="text-muted mb-0"><?php echo htmlspecialchars($option['description']); ?></p>
                  <span class="price-tag"><?php echo number_format($option['amount']); ?> <?php echo htmlspecialchars($option['currency']); ?></span>
                </div>
                <button type="button" class="btn btn-success btn-sm choose-course">Selected</button>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- User Details & Enroll -->
        <div class="col-md-6">
          <h3 class="mb-4">Your Details</h3>
          <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($phone) ?></p>

          <!-- hidden fields -->
          <input type="hidden" name="subscription" id="subscription-input"
                 value="<?php echo htmlspecialchars($currentCourse['courseName']); ?>">
          <input type="hidden" name="amount" id="amount-input"
                 value="<?php echo $currentCourse['pricingOptions'][0]['amount']; ?>">

          <!-- display selected -->
          <div class="selected-courses">
            <h4>Selected Package</h4>
            <ul id="selected-courses-list">
              <?php foreach($formData['courses'] as $c): ?>
                <li><?= htmlspecialchars($c) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Validation Error Message -->
          <div id="validation-error" class="alert alert-danger d-none" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Please select a pricing option to continue.
          </div>

          <div class="form-check mb-4">
            <label class="form-check-label" for="terms">
              By enrolling you allow that you have read, understood and agreed to the <a href="./terms-and-conditions">terms & conditions</a>
            </label>
            <?php if(isset($errors['terms'])): ?>
              <div class="text-danger"><?= $errors['terms'] ?></div>
            <?php endif; ?>
          </div>

          <button name="checkout"
                  class="btn btn-info w-100 rounded-pill py-2">
            Register Now! <i class="fas fa-paper-plane ms-2"></i>
          </button>
          <a class="btn btn-secondary w-100 rounded-pill py-2 mt-2"
             href="./courses">Back to Courses</a>
        </div>
      </div>
    </form>
  </div>

  <div class="thank-you-popup">
    <h2>Thank You! 🎉</h2>
    <p>We appreciate your interest in our courses. We'll get back to you soon!</p>
    <button onclick="closeThankYouPopup()">Close</button>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let selectedSet = new Set(["<?php echo htmlspecialchars($currentCourse['courseName']); ?>"]);
    let hasSelectedOption = false;
    const nameInput = document.getElementById("subscription-input"),
          amtInput = document.getElementById("amount-input"),
          validationError = document.getElementById("validation-error"),
          registerBtn = document.querySelector('button[name="checkout"]');

    function updateList(){
      const ul = document.getElementById("selected-courses-list");
      ul.innerHTML = "";
      selectedSet.forEach(c=>{
        ul.insertAdjacentHTML("beforeend",`<li>${c}</li>`);
      });
    }

    function validateSelection() {
      // Check if a pricing option is selected
      hasSelectedOption = selectedSet.size > 1; // More than just the main course
      
      if (hasSelectedOption) {
        validationError.classList.add('d-none');
        registerBtn.disabled = false;
        registerBtn.classList.remove('btn-secondary');
        registerBtn.classList.add('btn-info');
        
        // Remove highlight from course cards
        document.querySelectorAll('.course-card').forEach(card => {
          card.classList.remove('required-highlight');
        });
      } else {
        validationError.classList.remove('d-none');
        registerBtn.disabled = true;
        registerBtn.classList.remove('btn-info');
        registerBtn.classList.add('btn-secondary');
        
        // Highlight the pricing option cards
        document.querySelectorAll('.pricing-option').forEach(card => {
          card.classList.add('required-highlight');
        });
      }
    }

    function hideValidationError() {
      validationError.classList.add('d-none');
    }

    // Keep main course expanded
    document.querySelectorAll('.course-card[data-course-id="main"] .course-details')
      .forEach(d=>d.style.maxHeight="500px");

    updateList();
    validateSelection(); // Initial validation

    document.querySelectorAll('.choose-course').forEach(btn=>{
      btn.addEventListener('click', e=>{
        const card = e.target.closest('.course-card');

        // Remove active on pricing options
        document.querySelectorAll('.pricing-option')
          .forEach(c=>c.classList.remove('active', 'selected'));
        card.classList.add('active', 'selected');

        const sub = card.dataset.courseName,
              amt = card.dataset.courseAmount;

        selectedSet = new Set(["<?php echo htmlspecialchars($currentCourse['courseName']); ?>", sub]);
        nameInput.value = sub;
        amtInput.value = amt;
        updateList();

        // Toggle button text & colors
        document.querySelectorAll('.choose-course').forEach(b=>{
          b.textContent="Select";
          b.classList.replace("btn-success","btn-dark");
        });
        e.target.textContent="Selected";
        e.target.classList.replace("btn-dark","btn-success");
        
        // Validate selection after change
        validateSelection();
      });
    });

    // Form submission validation
    document.querySelector('form').addEventListener('submit', function(e) {
      if (!hasSelectedOption) {
        e.preventDefault();
        validationError.classList.remove('d-none');
        
        // Scroll to validation error
        validationError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Highlight the pricing option cards
        document.querySelectorAll('.pricing-option').forEach(card => {
          card.classList.add('required-highlight');
        });
        
        return false;
      }
    });

    // Hide validation error when user starts selecting
    document.querySelectorAll('.choose-course').forEach(btn => {
      btn.addEventListener('click', hideValidationError);
    });
  </script>
</body>
</html>
