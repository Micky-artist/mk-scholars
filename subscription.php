<?php
// Include session configuration for persistent sessions
include("./config/session.php");
include("./dbconnection/connection.php");

// Check if user is logged in (optional - page is accessible to all)
// Note: We do NOT include validateSession.php as it redirects non-logged-in users
$isLoggedIn = isset($_SESSION['userId']) && !empty($_SESSION['userId']);

// Fetch user data only if logged in
$name = 'Unknown User';
$email = 'Unknown Email';
$phone = '';

if ($isLoggedIn) {
    $UserId = $_SESSION['userId'];
    $selectUserDetails = mysqli_query($conn, "SELECT * FROM normUsers WHERE NoUserId = $UserId");
    if ($selectUserDetails && $selectUserDetails->num_rows > 0) {
        $userData = mysqli_fetch_assoc($selectUserDetails);
        $name  = $userData['NoUsername'] ?? 'Unknown User';
        $email = $userData['NoEmail']   ?? 'Unknown Email';
        $phone = $userData['NoPhone']   ?? '';
    }
}

// Get course parameter
$courseId = $_GET['course'] ?? '';
$courseData = null;

// Fetch course data from database if courseId is provided
if ($courseId && is_numeric($courseId)) {
    $courseQuery = "SELECT c.*, cp.amount, cp.currency, cp.pricingDescription, cp.currency, cp.coursePaymentCodeName, cp.pricingDescription 
                    FROM Courses c 
                    LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
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
            'paymentCode' => $pricing['coursePaymentCodeName'] ?: $pricing['pricingDescription'] ?: 'Course Access',
            'displayName' => $pricing['pricingDescription'] ?: 'Course Access',
            'description' => $pricing['pricingDescription'] ?: 'Full course access with all materials'
        ];
    }
    $stmt->close();
    
    // Use database course data
    $currentCourse = [
        'courseName' => $courseData['courseName'],
        'courseDescription' => $courseData['courseLongDescription'],
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
    'courses' => $_GET['courses'] ?? [$currentCourse['courseName']],
    'terms'   => isset($_GET['terms']),
];
$errors = [];

if (isset($_GET['checkout'])) {
    $sub = urlencode($_GET['subscription']);
    $courseId = $_GET['course'];
    header("Location: ./payment/checkout.php?course={$courseId}&subscription={$sub}");
    exit;
}

// Build course URL for WhatsApp sharing
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$scriptPath = str_replace('/subscription.php', '', $_SERVER['PHP_SELF']);
$courseUrl = $baseUrl . $scriptPath . "/course-details?id=" . ($courseId && is_numeric($courseId) ? $courseId : '');
if (!$courseId || !is_numeric($courseId)) {
    // For default courses, use subscription page URL
    $courseUrl = $baseUrl . $_SERVER['REQUEST_URI'];
}

// Prepare share data for WhatsApp
$shareData = [
    'title' => $currentCourse['courseName'],
    'url' => $courseUrl,
    'description' => $currentCourse['courseDescription'] ?? ''
];
$shareDataJson = json_encode($shareData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

// Open Graph metadata preparation
$pageUrl = $baseUrl . $_SERVER['REQUEST_URI'];
$ogTitle = ($currentCourse['courseName'] ?? 'Course') . ' - MK Scholars';
$ogDescription = $currentCourse['courseDescription'] ?? '';
$ogImageUrl = '';

// Build course image URL if available from database
if (!empty($courseData) && !empty($courseData['coursePhoto'])) {
    $storedPath = ltrim($courseData['coursePhoto'], '/');
    if (function_exists('isOnline') && isOnline()) {
        // Use admin subdomain in production
        $host = $_SERVER['HTTP_HOST'] ?? 'mkscholars.com';
        $domain = strtolower($host);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = preg_replace('/^admin\./', '', $domain);
        $adminHost = 'admin.' . $domain;
        $ogImageUrl = 'https://' . $adminHost . '/' . $storedPath;
    } else {
        // Local path fallback
        $ogImageUrl = './admin/' . $storedPath;
    }
}

// Ensure absolute URL for image
if (!empty($ogImageUrl) && strpos($ogImageUrl, 'http') !== 0) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'mkscholars.com';
    $ogImageUrl = $protocol . '://' . $host . '/' . ltrim($ogImageUrl, './');
}

// Fallback image
if (empty($ogImageUrl)) {
    $ogImageUrl = 'https://mkscholars.com/images/logo/logoRound.png';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Course Registration - <?php echo htmlspecialchars($currentCourse['courseName']); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($ogDescription); ?>">
  <!-- Open Graph metadata for social sharing -->
  <meta property="og:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars($ogDescription); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars($ogImageUrl); ?>">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo htmlspecialchars($pageUrl); ?>">
  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?php echo htmlspecialchars($ogTitle); ?>">
  <meta name="twitter:description" content="<?php echo htmlspecialchars($ogDescription); ?>">
  <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImageUrl); ?>">
  <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.3);
            --neumorphic-shadow: 5px 5px 10px #d1d5db, -5px -5px 10px #ffffff;
            --primary-color: #3b82f6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(31, 41, 55, 0.9);
            --glass-border: rgba(255, 255, 255, 0.1);
            --neumorphic-shadow: 5px 5px 10px #0a0c10, -5px -5px 10px #283447;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
            padding-top: 80px;
            /* Fixed navigation height */
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 1rem;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .hero-content p {
            font-size: 1rem;
            margin-bottom: 0;
        }

        .course-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 0.75rem;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .course-card:hover::before {
            left: 100%;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
        }

        .course-card.active {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(59, 130, 246, 0.1) 100%);
        }

        .course-card.selected {
            border-color: var(--success-color);
            background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(16, 185, 129, 0.1) 100%);
        }

        .course-details {
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .course-card.active .course-details {
            max-height: 300px;
            margin-top: 0.75rem;
        }

        .pricing-option {
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            padding: 0.75rem;
            margin: 0.25rem 0;
            transition: all 0.3s ease;
            background: var(--glass-bg);
        }

        .pricing-option:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .pricing-option.selected {
            border-color: var(--success-color);
            background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(16, 185, 129, 0.1) 100%);
        }

        .price-tag {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .user-info-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .selected-courses {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 0.75rem;
            margin: 0.75rem 0;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            border: none;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
            color: white;
        }

        .btn-secondary-custom {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            padding: 0.6rem 1.5rem;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .btn-secondary-custom:hover {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .validation-error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 1px solid #fecaca;
            border-left: 4px solid var(--danger-color);
            color: #991b1b;
            padding: 1rem;
            border-radius: 12px;
            margin: 1rem 0;
        }

        .course-card.required-highlight {
            border-color: var(--danger-color) !important;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%) !important;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feature-list li::before {
            content: '✓';
            color: var(--success-color);
            font-weight: bold;
            font-size: 1.1rem;
        }

        .contact-info {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1d4ed8 100%);
            color: white;
            padding: 0.75rem;
            border-radius: 10px;
            margin-top: 0.75rem;
        }

        .course-description {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 0.75rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            max-height: 200px;
            overflow-y: auto;
        }

        .badge-custom {
            background: linear-gradient(135deg, var(--success-color) 0%, #059669 100%);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .btn-choose {
            background: var(--primary-color);
            border: none;
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }

        .btn-choose:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        .btn-choose.selected {
            background: var(--success-color);
        }

        .btn-choose.selected:hover {
            background: #059669;
        }

        /* WhatsApp Share Button */
        .whatsapp-share-button {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: white;
            border: none;
            padding: 0.875rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            margin-top: 0.75rem;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            text-decoration: none;
        }

        .whatsapp-share-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
            background: linear-gradient(135deg, #20BA5A, #0FA76E);
            color: white;
        }

        .whatsapp-share-button:active {
            transform: translateY(0);
        }

        .whatsapp-share-button i {
            font-size: 1.2rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }

            .hero-section {
                padding: 1rem 0;
            }

            .hero-content h1 {
                font-size: 1.5rem;
            }

            .course-card {
                padding: 0.75rem;
        }

        .pricing-option {
                padding: 0.5rem;
            }

            .user-info-card {
                padding: 0.75rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <?php include("./partials/navigation.php"); ?>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="display-4 fw-bold mb-3">
                    <i class="fas fa-graduation-cap me-3"></i>
                    Course Registration
                </h1>
                <p class="lead mb-0">Join thousands of students advancing their careers with our expert-led courses</p>
            </div>
        </div>
    </div>

  <div class="container">
        <form method="GET" class="glass-panel p-4">
      <div class="row">
        <!-- Course Selection -->
                <div class="col-lg-6">
                    <h4 class="mb-3">
                        <i class="fas fa-book me-2"></i>
                        Register For <?php echo htmlspecialchars($currentCourse['courseName']); ?>
                    </h4>

          <!-- Main Course Card -->
          <div class="course-card active"
               data-course-id="main"
               data-course-name="<?php echo htmlspecialchars($currentCourse['courseName']); ?>"
               data-course-amount="0">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                                <div class="neumorphic-icon me-3">
                                    <i class="fas fa-graduation-cap text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1"><?php echo htmlspecialchars($currentCourse['courseName']); ?></h4>
                                </div>
              </div>
                            <span class="badge-custom">Active</span>
            </div>
            <div class="course-details">
              <hr>
                            
                            <div class="mt-3">
                                <h6 class="mb-2">What you'll learn:</h6>
                                <div class="course-description">
                                    <?php echo nl2br(htmlspecialchars($currentCourse['courseDescription'])); ?>
                                </div>
                            </div>
                            <div class="contact-info">
                                <h6 class="mb-2">
                                    <i class="fas fa-phone me-2"></i>
                                    Contact Information
                                </h6>
                                <p class="mb-0"><?php echo htmlspecialchars($currentCourse['contact']); ?></p>
              </div>
            </div>
          </div>

          <!-- Pricing Options -->
          <?php if (count($currentCourse['pricingOptions']) > 1): ?>
                        <h5 class="mt-3 mb-2">
                            <i class="fas fa-tags me-2"></i>
                            Choose Your Package
                        </h5>
                        <?php foreach ($currentCourse['pricingOptions'] as $index => $option): ?>
              <div class="course-card pricing-option"
                   data-course-id="<?php echo $index + 1; ?>"
                   data-course-name="<?php echo htmlspecialchars($option['paymentCode']); ?>"
                                data-course-display="<?php echo htmlspecialchars($option['displayName']); ?>"
                   data-course-amount="<?php echo $option['amount']; ?>">
                <div class="d-flex align-items-center justify-content-between">
                                    <div class="flex-grow-1">
                                        <h5 class="mb-2"><?php echo htmlspecialchars($option['displayName']); ?></h5>
                                        <p class="text-muted mb-2"><?php echo htmlspecialchars($option['description']); ?></p>
                                        <div class="price-tag">
                                            <?php echo number_format($option['amount']); ?> <?php echo htmlspecialchars($option['currency']); ?>
                                        </div>
                  </div>
                                    <button type="button" class="btn-choose choose-course">
                                        <i class="fas fa-check me-1"></i>
                                        Select
                                    </button>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Single pricing option (not preselected) -->
            <?php $option = $currentCourse['pricingOptions'][0]; ?>
            <div class="course-card pricing-option"
                 data-course-id="1"
                 data-course-name="<?php echo htmlspecialchars($option['paymentCode']); ?>"
                            data-course-display="<?php echo htmlspecialchars($option['displayName']); ?>"
                 data-course-amount="<?php echo $option['amount']; ?>">
              <div class="d-flex align-items-center justify-content-between">
                                <div class="flex-grow-1">
                                    <h5 class="mb-2"><?php echo htmlspecialchars($option['displayName']); ?></h5>
                                    <p class="text-muted mb-2"><?php echo htmlspecialchars($option['description']); ?></p>
                                    <div class="price-tag">
                                        <?php echo number_format($option['amount']); ?> <?php echo htmlspecialchars($option['currency']); ?>
                                    </div>
                </div>
                                <button type="button" class="btn-choose choose-course">
                                    <i class="fas fa-check me-1"></i>
                                    Select
                                </button>
              </div>
            </div>
          <?php endif; ?>
        </div>

                <!-- User Details & Registration -->
                <div class="col-lg-6">
                    <?php if ($isLoggedIn): ?>
                    <h4 class="mb-3">
                        <i class="fas fa-user me-2"></i>
                        Your Details
                    </h4>

                    <div class="user-info-card">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Name</small>
                                        <div class="fw-semibold"><?= htmlspecialchars($name) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Email</small>
                                        <div class="fw-semibold"><?= htmlspecialchars($email) ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <div>
                                        <small class="text-muted">Phone</small>
                                        <div class="fw-semibold"><?= htmlspecialchars($phone) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Not logged in?</strong> You can still view course details. 
                        <a href="./login?next=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="alert-link">Login</a> to see your details and register.
                    </div>
                    <?php endif; ?>

                    <!-- Hidden fields -->
                    <input type="hidden" name="subscription" id="subscription-input"
                        value="">
          <input type="hidden" name="amount" id="amount-input"
                 value="">
                    <input type="hidden" id="course-id" value="<?php echo isset($courseId) ? (int)$courseId : 0; ?>">

                    <!-- Selected Package -->
          <div class="selected-courses">
                        <h6 class="mb-2">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Selected Package
                        </h6>
                        <div id="selected-courses-list">
                            <?php foreach ($formData['courses'] as $c): ?>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span><?= htmlspecialchars($c) ?></span>
                                </div>
              <?php endforeach; ?>
                        </div>
          </div>

          <!-- Validation Error Message -->
                    <div id="validation-error" class="validation-error d-none">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Please select a pricing option to continue.
          </div>

                    <!-- Terms and Conditions -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
            <label class="form-check-label" for="terms">
                            By enrolling you allow that you have read, understood and agreed to the
                            <a href="./terms-and-conditions" class="text-primary">terms & conditions</a>
            </label>
                        <?php if (isset($errors['terms'])): ?>
              <div class="text-danger"><?= $errors['terms'] ?></div>
            <?php endif; ?>
          </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <?php if ($isLoggedIn): ?>
                        <button type="submit" name="checkout" class="btn-primary-custom w-100">
                            <i class="fas fa-rocket me-2"></i>
                            Register Now!
                        </button>
                        <?php else: ?>
                        <a href="./login?next=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn-primary-custom w-100 text-center">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Login to Register
                        </a>
                        <?php endif; ?>
                        <a class="btn-secondary-custom w-100 text-center" href="./e-learning">
                            <i class="fas fa-arrow-left me-2"></i>
                            Back to Courses
                        </a>
                        <button onclick="shareOnWhatsAppFromData(<?php echo htmlspecialchars($shareDataJson, ENT_QUOTES, 'UTF-8'); ?>)" 
                                class="whatsapp-share-button" 
                                title="Share on WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                            Share on WhatsApp
                        </button>
                    </div>
        </div>
      </div>
    </form>
  </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let selectedSet = new Set(["<?php echo htmlspecialchars($currentCourse['courseName']); ?>"]);
    let hasSelectedOption = false;
    const nameInput = document.getElementById("subscription-input"),
          amtInput = document.getElementById("amount-input"),
          validationError = document.getElementById("validation-error"),
          registerBtn = document.querySelector('button[name="checkout"]');

        function updateList() {
            const container = document.getElementById("selected-courses-list");
            container.innerHTML = "";
            selectedSet.forEach(c => {
                container.insertAdjacentHTML("beforeend", `
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span>${c}</span>
                    </div>
                `);
      });
    }

    function validateSelection() {
            hasSelectedOption = selectedSet.size > 1;
      
      if (hasSelectedOption) {
        validationError.classList.add('d-none');
        registerBtn.disabled = false;
        registerBtn.classList.remove('btn-secondary');
                registerBtn.classList.add('btn-primary-custom');
                registerBtn.style.display = 'flex';
        
        document.querySelectorAll('.course-card').forEach(card => {
          card.classList.remove('required-highlight');
        });
      } else {
                validationError.classList.add('d-none');
        registerBtn.disabled = true;
                registerBtn.classList.remove('btn-primary-custom');
        registerBtn.classList.add('btn-secondary');
                registerBtn.style.display = 'none';
        
        document.querySelectorAll('.pricing-option').forEach(card => {
                    card.classList.remove('required-highlight');
        });
      }
    }

    function hideValidationError() {
      validationError.classList.add('d-none');
    }

    // Keep main course expanded
    document.querySelectorAll('.course-card[data-course-id="main"] .course-details')
            .forEach(d => d.style.maxHeight = "500px");

    updateList();
        validateSelection();

        document.querySelectorAll('.choose-course').forEach(btn => {
            btn.addEventListener('click', e => {
        const card = e.target.closest('.course-card');

        // Remove active on pricing options
        document.querySelectorAll('.pricing-option')
                    .forEach(c => c.classList.remove('active', 'selected'));
        card.classList.add('active', 'selected');

        const sub = card.dataset.courseName,
                    displayName = card.dataset.courseDisplay,
              amt = card.dataset.courseAmount;

                selectedSet = new Set(["<?php echo htmlspecialchars($currentCourse['courseName']); ?>", displayName]);
                nameInput.value = sub; // sub is paymentCode (coursePaymentCodeName)
        amtInput.value = amt;
        updateList();

        // Toggle button text & colors
                document.querySelectorAll('.choose-course').forEach(b => {
                    b.innerHTML = '<i class="fas fa-check me-1"></i>Select';
                    b.classList.remove("selected");
                });
                e.target.innerHTML = '<i class="fas fa-check me-1"></i>Selected';
                e.target.classList.add("selected");

        validateSelection();
      });
    });

        // Form submission: validate and redirect to checkout URL explicitly
    document.querySelector('form').addEventListener('submit', function(e) {
      if (!hasSelectedOption) {
        e.preventDefault();
                return false;
            }
            // Redirect to normal checkout page
            var subCode = nameInput.value;
            var courseIdEl = document.getElementById('course-id');
            var cid = courseIdEl ? courseIdEl.value : '';
            if (cid && subCode) {
                e.preventDefault();
                // Redirect to normal checkout with course and subscription parameters
                const url = './payment/checkout.php?course=' + encodeURIComponent(cid) + '&subscription=' + encodeURIComponent(subCode);
                window.location.href = url;
        return false;
      }
    });

    // Hide validation error when user starts selecting
    document.querySelectorAll('.choose-course').forEach(btn => {
      btn.addEventListener('click', hideValidationError);
    });

    // WhatsApp Share Function (matching courses.php logic)
    function shareOnWhatsAppFromData(data) {
        try {
            const courseUrl = data.url || '';
            const title = data.title || 'Course';
            
            // Build the share message - matching courses.php format
            const message = 'Check out this course: ' + courseUrl;
            
            // Create WhatsApp share URL - using api.whatsapp.com like courses.php
            const whatsappUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(message);
            
            // Open in new window/tab (matching courses.php behavior)
            window.open(whatsappUrl, '_blank');
            
        } catch (error) {
            console.error('WhatsApp share error:', error);
            alert('Unable to share on WhatsApp. Please try again.');
        }
    }
  </script>
</body>

</html>