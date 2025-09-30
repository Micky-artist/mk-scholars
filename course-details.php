<?php
session_start();
include './dbconnection/connection.php';

// Get course ID from URL parameter
$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$courseId) {
    header("Location: ./courses");
    exit;
}

// Fetch course details from database
$course = null;
$courseTags = [];
$pricingOptions = [];

if ($conn) {
    // Get course details with pricing and currency info
    $courseQuery = "SELECT c.*, cp.amount, cp.currency, cp.pricingDescription, cp.discountAmount, cp.discountStartDate, cp.discountEndDate, cp.isFree, curr.currencySymbol 
                    FROM Courses c 
                    LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
                    LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                    WHERE c.courseId = $courseId AND c.courseDisplayStatus = 1";
    
    $courseResult = mysqli_query($conn, $courseQuery);
    
    if ($courseResult && mysqli_num_rows($courseResult) > 0) {
        $course = mysqli_fetch_assoc($courseResult);
        
        // Get all pricing options for this course
        $pricingQuery = "SELECT cp.*, curr.currencySymbol 
                        FROM CoursePricing cp 
                        LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                        WHERE cp.courseId = $courseId 
                        ORDER BY cp.amount ASC";
        
        $pricingResult = mysqli_query($conn, $pricingQuery);
        if ($pricingResult) {
            while ($pricing = mysqli_fetch_assoc($pricingResult)) {
                $pricingOptions[] = $pricing;
            }
        }
        
        // Get course tags
        $tagsQuery = "SELECT * FROM CourseTags WHERE courseId = $courseId AND isActive = 1 ORDER BY createdDate ASC";
        $tagsResult = mysqli_query($conn, $tagsQuery);
        if ($tagsResult) {
            while ($tag = mysqli_fetch_assoc($tagsResult)) {
                $courseTags[] = $tag;
            }
        }
    } else {
        header("Location: ./courses");
        exit;
    }
} else {
    header("Location: ./courses");
    exit;
}

// Helper function to get image URL
function getImageUrl($path = '') {
    if (isOnline()) {
        return 'https://admin.mkscholars.com/' . ltrim($path, './');
    } else {
        return './' . ltrim($path, './');
    }
}

// Helper function to format price
function formatPrice($amount, $currencySymbol, $currency) {
    if ($amount && $amount > 0) {
        $symbol = $currencySymbol ?: $currency;
        return $symbol . ' ' . number_format($amount, 0);
    }
    return 'Free';
}

// Helper function to get status text
function getStatusText($status) {
    switch($status) {
        case 1: return 'Open';
        case 2: return 'Closed';
        default: return 'Inactive';
    }
}

// Helper function to get status class
function getStatusClass($status) {
    switch($status) {
        case 1: return 'bg-success';
        case 2: return 'bg-warning';
        default: return 'bg-secondary';
    }
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['userId']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($course['courseName']); ?> - Course Details</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="https://mkscholars.com/images/logo/logoRound.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --accent: #0E77C2;
            --bg: #fff;
            --fg: #333;
            --primary-blue: #0E77C2;
            --dark-blue: #083352;
            --orange: #FF6B35;
            --gold: #FFD700;
        }

        html,
        body {
            height: auto;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            overflow-y: auto;
            background: #f4f4f4;
            color: var(--fg);
            width: 100%;
        }

        .tabs {
            display: flex;
            margin-bottom: 1rem;
            border-bottom: 2px solid #ccc;
        }

        .tab {
            padding: .75rem 1.5rem;
            background: #eee;
            cursor: pointer;
            transition: background .2s;
            margin-right: 2px;
        }

        .tab.active {
            background: var(--bg);
            border-top: 2px solid #0E77C2;
            border-left: 2px solid #0E77C2;
            border-right: 2px solid #0E77C2;
            font-weight: bold;
        }

        .panel {
            display: none;
        }

        .panel.active {
            display: block;
        }

        pre,
        .note-section {
            max-width: 100%;
        }

        /* Mobile Responsive Design */
        @media (max-width: 768px) {
            html, body {
                min-height: 100vh;
                height: auto;
                overflow-x: hidden;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .container-fluid {
                padding: 0;
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            
            .row {
                margin: 0;
                width: 100%;
            }
            
            main {
                padding: 1rem !important;
                width: 100%;
                max-width: 100%;
            }

            .top-navbar {
                position: sticky;
                top: 0;
                z-index: 1000;
            }
            
            .tabs {
                flex-wrap: wrap;
                gap: 0.5rem;
                margin-bottom: 1rem;
            }
            
            .tab {
                flex: 1;
                min-width: 120px;
                text-align: center;
                font-size: 0.9rem;
                padding: 0.5rem 1rem;
            }
            
            .note-section {
                padding: 1rem;
                margin: 0;
                width: 100%;
                max-width: 100%;
            }
            
            .section-title {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .sub-title {
                font-size: 1.2rem;
                margin-bottom: 0.5rem;
            }
            
            pre {
                overflow-x: auto;
                white-space: pre-wrap;
                word-wrap: break-word;
            }
        }

        @media (max-width: 480px) {
            .container-fluid {
                padding: 0;
            }
            
            main {
                padding: 0.5rem !important;
            }
            
            .tab {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }
            
            .section-title {
                font-size: 1.3rem;
            }
            
            .sub-title {
                font-size: 1.1rem;
            }
            
            .note-section {
                padding: 0.5rem;
            }
        }

        .note-section {
            background: #fff;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: #0E77C2;
            font-size: 1.75rem;
            margin-bottom: .75rem;
            border-bottom: 3px solid #0E77C2;
            padding-bottom: .5rem;
        }

        .sub-title {
            color: #FF6B35;
            font-size: 1.25rem;
            margin-top: 1rem;
        }

        .intro,
        .why,
        .format,
        .strategies {
            margin-bottom: 1.25rem;
        }

        .example {
            background: #e9f9f9;
            padding: 1rem;
            border-left: 5px solid #0E77C2;
            border-radius: 4px;
            margin-top: 1rem;
        }

        .example-title {
            color: #0E77C2;
            font-size: 1.25rem;
        }

        .passage,
        .question,
        .answer,
        .solution {
            padding: .75rem;
            margin: .75rem 0;
            border-radius: 4px;
        }

        .passage {
            background: #fff8e1;
        }

        .question {
            background: #ffecb3;
        }

        .answer {
            background: #dcedc8;
        }

        .solution {
            background: #ffcdd2;
        }

        .locked {
            text-align: center;
            padding: 2rem;
            color: #888;
            font-size: 1.25rem;
        }

        .subscribe-btn {
            width: 100%;
            display: block;
            text-align: center;
            padding: .75rem;
            background: #0E77C2;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 1rem 0;
            transition: background .2s;
        }

        .subscribe-btn:hover {
            background: #083352;
        }

        /* Navigation Styles - MK Brand Colors */
        .top-navbar {
            background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            color: white !important;
            font-weight: 600;
            text-decoration: none;
        }

        .navbar-brand:hover {
            color: #e0e7ff !important;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
        }

        .mobile-menu-btn {
            background: none;
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .mobile-menu-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: rgba(255, 255, 255, 0.5);
        }

        /* Mobile Sidebar */
        .mobile-sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            display: none;
        }

        .mobile-sidebar-overlay.show {
            display: block;
        }

        .mobile-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100%;
            background: white;
            z-index: 1051;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .mobile-sidebar-overlay.show .mobile-sidebar {
            transform: translateX(0);
        }

        .mobile-sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
            color: white;
        }

        .mobile-sidebar-content {
            padding: 1rem;
        }

        .close-sidebar {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: white;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close-sidebar:hover {
            color: #FFD700;
        }

        /* Adjust main content for navbar */
        .main-content-wrapper {
            margin-top: 0;
        }

        /* Course Header Styles */
        .course-header {
            background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }

        .course-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .course-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .course-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .course-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .course-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .tag {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .pricing-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .pricing-option {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .pricing-option:hover {
            border-color: #0E77C2;
            box-shadow: 0 2px 8px rgba(14, 119, 194, 0.1);
        }

        .pricing-option.featured {
            border-color: #0E77C2;
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%);
        }

        .price {
            font-size: 2rem;
            font-weight: 700;
            color: #0E77C2;
        }

        .price-description {
            color: #666;
            margin-bottom: 1rem;
        }

        .enroll-btn {
            width: 100%;
            background: #0E77C2;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .enroll-btn:hover {
            background: #083352;
            transform: translateY(-2px);
        }

        .enroll-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .login-required {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .login-required a {
            color: #0E77C2;
            text-decoration: none;
            font-weight: 600;
        }

        .login-required a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark top-navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="./">
                <img src="./images/logo/logoRound.png" alt="MK Scholars" width="30" height="30" class="me-2">
                MK Scholars
            </a>
            
            <!-- Mobile menu button (only on mobile) -->
            <button class="mobile-menu-btn d-md-none" type="button" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Mobile Navigation Links (only when sidebar is hidden) -->
            <div class="navbar-nav ms-auto d-flex d-md-none">
                <a class="nav-link" href="./courses">Courses</a>
                <a class="nav-link" href="./e-learning">E-Learning</a>
                <?php if ($isLoggedIn): ?>
                    <a class="nav-link" href="./dashboard">Dashboard</a>
                    <a class="nav-link" href="./php/logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="./login">Login</a>
                    <a class="nav-link" href="./sign-up">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Mobile Sidebar Overlay -->
    <div class="mobile-sidebar-overlay" id="mobileSidebarOverlay">
        <div class="mobile-sidebar">
            <div class="mobile-sidebar-header">
                <h5 class="mb-0">Navigation</h5>
                <button class="close-sidebar" id="closeSidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mobile-sidebar-content">
                <a href="./courses" class="btn btn-outline-primary w-100 mb-2">All Courses</a>
                <a href="./e-learning" class="btn btn-outline-primary w-100 mb-2">E-Learning</a>
                <?php if ($isLoggedIn): ?>
                    <a href="./dashboard" class="btn btn-outline-primary w-100 mb-2">Dashboard</a>
                    <a href="./php/logout.php" class="btn btn-outline-danger w-100">Logout</a>
                <?php else: ?>
                    <a href="./login" class="btn btn-outline-primary w-100 mb-2">Login</a>
                    <a href="./sign-up" class="btn btn-outline-success w-100">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Course Header -->
    <div class="course-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <?php if ($course['coursePhoto']): ?>
                        <img src="<?php echo getImageUrl($course['coursePhoto']); ?>" alt="<?php echo htmlspecialchars($course['courseName']); ?>" class="course-image">
                    <?php else: ?>
                        <img src="./images/courses/placeholder.jpg" alt="<?php echo htmlspecialchars($course['courseName']); ?>" class="course-image">
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <h1 class="course-title"><?php echo htmlspecialchars($course['courseName']); ?></h1>
                    <p class="course-subtitle"><?php echo htmlspecialchars($course['courseShortDescription']); ?></p>
                    
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Starts: <?php echo date('M j, Y', strtotime($course['courseStartDate'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span><?php echo $course['courseSeats']; ?> Seats Available</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Registration Ends: <?php echo date('M j, Y', strtotime($course['courseRegEndDate'])); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-circle text-<?php echo getStatusClass($course['courseDisplayStatus']); ?>"></i>
                            <span><?php echo getStatusText($course['courseDisplayStatus']); ?></span>
                        </div>
                    </div>
                    
                    <?php if (!empty($courseTags)): ?>
                        <div class="course-tags">
                            <?php foreach ($courseTags as $tag): ?>
                                <div class="tag" style="background: <?php echo $tag['tagColor']; ?>20; border: 1px solid <?php echo $tag['tagColor']; ?>;">
                                    <?php if ($tag['courseTagIcon']): ?>
                                        <i class="<?php echo htmlspecialchars($tag['courseTagIcon']); ?>"></i>
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($tag['tagDescription']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid main-content-wrapper">
        <div class="row">
            <!-- Main Content -->
            <main class="col-12 p-4">
                <div class="tabs">
                    <div class="tab active" data-index="0">Course Details</div>
                    <div class="tab" data-index="1">Pricing Options</div>
                    <div class="tab" data-index="2">Course Content</div>
                </div>
                
                <!-- Course Details Tab -->
                <div class="panel active" data-index="0">
                    <div class="note-section">
                        <h2 class="section-title">Course Overview</h2>
                        <div class="intro">
                            <p><?php echo nl2br(htmlspecialchars($course['courseLongDescription'])); ?></p>
                        </div>
                        
                        <div class="format">
                            <h3 class="sub-title">Course Information</h3>
                            <ul>
                                <li><strong>Start Date:</strong> <?php echo date('F j, Y', strtotime($course['courseStartDate'])); ?></li>
                                <li><strong>End Date:</strong> <?php echo date('F j, Y', strtotime($course['courseEndDate'])); ?></li>
                                <li><strong>Registration Deadline:</strong> <?php echo date('F j, Y', strtotime($course['courseRegEndDate'])); ?></li>
                                <li><strong>Available Seats:</strong> <?php echo $course['courseSeats']; ?></li>
                                <li><strong>Status:</strong> <?php echo getStatusText($course['courseDisplayStatus']); ?></li>
                            </ul>
                        </div>
                        
                        <?php if ($course['coursePaymentCodeName']): ?>
                            <div class="example">
                                <h3 class="example-title">Payment Information</h3>
                                <p><strong>Payment Code:</strong> <?php echo htmlspecialchars($course['coursePaymentCodeName']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Pricing Options Tab -->
                <div class="panel" data-index="1">
                    <div class="note-section">
                        <h2 class="section-title">Pricing Options</h2>
                        
                        <?php if (!empty($pricingOptions)): ?>
                            <div class="row">
                                <?php foreach ($pricingOptions as $index => $pricing): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="pricing-option <?php echo $index === 0 ? 'featured' : ''; ?>">
                                            <h4 class="mb-3"><?php echo htmlspecialchars($pricing['pricingDescription'] ?: 'Course Access'); ?></h4>
                                            <div class="price">
                                                <?php echo formatPrice($pricing['amount'], $pricing['currencySymbol'], $pricing['currency']); ?>
                                            </div>
                                            
                                            <?php if ($pricing['discountAmount'] > 0): ?>
                                                <div class="text-muted">
                                                    <small>Discount: <?php echo formatPrice($pricing['discountAmount'], $pricing['currencySymbol'], $pricing['currency']); ?></small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="mt-3">
                                                <?php if ($isLoggedIn): ?>
                                                    <button class="enroll-btn" onclick="enrollCourse(<?php echo $courseId; ?>, <?php echo $pricing['coursePricingId']; ?>)">
                                                        <i class="fas fa-shopping-cart me-2"></i>Enroll Now
                                                    </button>
                                                <?php else: ?>
                                                    <div class="login-required">
                                                        <i class="fas fa-lock me-2"></i>
                                                        <a href="./login">Login</a> or <a href="./sign-up">Sign Up</a> to enroll in this course
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center">
                                <p class="text-muted">No pricing options available for this course.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Course Content Tab -->
                <div class="panel" data-index="2">
                    <div class="note-section">
                        <h2 class="section-title">Course Content</h2>
                        
                        <?php if ($isLoggedIn): ?>
                            <div class="intro">
                                <p>This course includes comprehensive content designed to help you succeed. The course content will be available after enrollment.</p>
                            </div>
                            
                            <div class="example">
                                <h3 class="example-title">What You'll Learn</h3>
                                <ul>
                                    <li>Comprehensive course materials and resources</li>
                                    <li>Interactive lessons and practical exercises</li>
                                    <li>Expert guidance and support</li>
                                    <li>Progress tracking and assessments</li>
                                    <li>Certificate of completion</li>
                                </ul>
                            </div>
                            
                            <div class="strategies">
                                <h3 class="sub-title">Course Features</h3>
                                <ul>
                                    <li><strong>Self-Paced Learning:</strong> Study at your own pace with flexible scheduling</li>
                                    <li><strong>Expert Instructors:</strong> Learn from experienced professionals</li>
                                    <li><strong>Interactive Content:</strong> Engaging materials and practical exercises</li>
                                    <li><strong>Community Support:</strong> Connect with fellow learners</li>
                                    <li><strong>Mobile Access:</strong> Learn anywhere, anytime</li>
                                </ul>
                            </div>
                        <?php else: ?>
                            <div class="locked">
                                <i class="fas fa-lock fa-3x mb-3"></i>
                                <h3>Course Content Preview</h3>
                                <p>To access the full course content, you need to enroll in this course.</p>
                                <div class="login-required">
                                    <a href="./login" class="btn btn-primary me-2">Login</a>
                                    <a href="./sign-up" class="btn btn-outline-primary">Sign Up</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab').forEach(tab =>
            tab.addEventListener('click', () => {
                const idx = tab.dataset.index;
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                document.querySelector(`.panel[data-index="${idx}"]`).classList.add('active');
            })
        );

        // Mobile Sidebar functionality
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');
        const closeSidebar = document.getElementById('closeSidebar');

        // Open mobile sidebar
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function() {
                mobileSidebarOverlay.classList.add('show');
                document.body.style.overflow = 'hidden';
            });
        }

        // Close mobile sidebar
        if (closeSidebar) {
            closeSidebar.addEventListener('click', function() {
                mobileSidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            });
        }

        // Close sidebar when clicking overlay
        if (mobileSidebarOverlay) {
            mobileSidebarOverlay.addEventListener('click', function(e) {
                if (e.target === mobileSidebarOverlay) {
                    mobileSidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            });
        }

        // Close sidebar on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileSidebarOverlay.classList.contains('show')) {
                mobileSidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
            }
        });

        // Enroll course function
        function enrollCourse(courseId, pricingId) {
            if (confirm('Are you sure you want to enroll in this course?')) {
                // Here you would typically redirect to a payment page or process enrollment
                alert('Enrollment functionality will be implemented here. Course ID: ' + courseId + ', Pricing ID: ' + pricingId);
                // window.location.href = './enroll?course=' + courseId + '&pricing=' + pricingId;
            }
        }
    </script>
</body>

</html>
