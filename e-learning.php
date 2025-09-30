<?php
session_start();
include('./dbconnection/connection.php');
include('./php/validateSession.php');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['userId']);
$userId = $isLoggedIn ? $_SESSION['userId'] : null;

// Get filter parameter
$filter = $_GET['filter'] ?? 'all'; // all, enrolled, not_enrolled

// Fetch courses from database with enrollment status
$courses = [];
$enrolledCourses = [];

if ($conn) {
    // First, get user's enrolled courses if logged in
    if ($isLoggedIn) {
        $enrollmentQuery = "SELECT courseId FROM CourseEnrollments WHERE userId = ? AND enrollmentStatus = 1";
        $stmt = $conn->prepare($enrollmentQuery);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $enrollmentResult = $stmt->get_result();
        
        while ($enrollment = $enrollmentResult->fetch_assoc()) {
            $enrolledCourses[] = $enrollment['courseId'];
        }
        $stmt->close();
    }
    
    // Build the main courses query
    $coursesQuery = "SELECT c.*, cp.amount, cp.currency, cp.pricingDescription, curr.currencySymbol 
                     FROM Courses c 
                     LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
                     LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                     WHERE c.courseDisplayStatus = 1";
    
    // Add filter conditions
    if ($filter === 'enrolled' && $isLoggedIn && !empty($enrolledCourses)) {
        $courseIds = implode(',', array_map('intval', $enrolledCourses));
        $coursesQuery .= " AND c.courseId IN ($courseIds)";
    } elseif ($filter === 'not_enrolled' && $isLoggedIn && !empty($enrolledCourses)) {
        $courseIds = implode(',', array_map('intval', $enrolledCourses));
        $coursesQuery .= " AND c.courseId NOT IN ($courseIds)";
    } elseif ($filter === 'not_enrolled' && (!$isLoggedIn || empty($enrolledCourses))) {
        // If not logged in or no enrollments, show all courses
    }
    
    $coursesQuery .= " ORDER BY c.courseCreatedDate DESC";
    
    $coursesResult = mysqli_query($conn, $coursesQuery);
    
    if ($coursesResult && mysqli_num_rows($coursesResult) > 0) {
        while ($course = mysqli_fetch_assoc($coursesResult)) {
            // Add enrollment status to each course
            $course['isEnrolled'] = $isLoggedIn && in_array($course['courseId'], $enrolledCourses);
            $courses[] = $course;
        }
    }
}

// Helper function to get button info based on enrollment status
function getButtonInfo($course, $isLoggedIn) {
    if (!$isLoggedIn) {
        return [
            'text' => 'Register to View',
            'class' => 'btn-outline-primary',
            'action' => 'login',
            'icon' => 'fas fa-user-plus'
        ];
    }
    
    if ($course['isEnrolled']) {
        return [
            'text' => 'Open Course',
            'class' => 'btn-outline-success',
            'action' => 'open',
            'icon' => 'fas fa-play'
        ];
    } else {
        return [
            'text' => 'Register Now',
            'class' => 'btn-outline-primary',
            'action' => 'register',
            'icon' => 'fas fa-user-plus'
        ];
    }
}

// Helper function to get image URL
function getImageUrl($path = '') {
    if (isOnline()) {
        return 'https://admin.mkscholars.com/' . ltrim($path, './');
    } else {
        return './' . ltrim($path, './');
    }
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

// Helper function to format price
function formatPrice($amount, $currencySymbol, $currency) {
    if ($amount && $amount > 0) {
        $symbol = $currencySymbol ?: $currency;
        return $symbol . ' ' . number_format($amount, 0);
    }
    return 'Free';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning | MK Scholars</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.3);
            --neumorphic-shadow: 5px 5px 10px #d1d5db, -5px -5px 10px #ffffff;
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

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--neumorphic-shadow);
            min-height: 100vh;
        }

        .neumorphic-icon {
            width: 60px;
            height: 60px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--neumorphic-shadow);
        }

        .nav-link {
            color: var(--text-primary);
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: var(--glass-bg);
            color: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: var(--neumorphic-shadow);
        }

        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                z-index: 1050;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-toggle {
                display: block;
            }
        }

        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ==== Course Grid ==== */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin: 1rem 0;
        }

        .course-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: var(--bg-secondary);
            color: var(--text-primary);
            display: flex;
            min-height: 300px;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .course-left-panel {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 200px;
            width: 35%;
        }

        .course-brand {
            margin-bottom: 2rem;
        }

        .course-brand-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.1;
        }

        .course-brand-subtitle {
            font-size: 0.75rem;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .course-brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .course-contact {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .course-right-panel {
            background: white;
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .course-pricing-tags {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .price-tag {
            background: #dcfce7;
            color: #166534;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #bbf7d0;
        }

        .price-tag.secondary {
            background: #fed7aa;
            color: #c2410c;
            border-color: #fdba74;
        }

        .course-features {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .feature-box {
            background: var(--gray-50);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-700);
            flex: 1;
        }

        .feature-icon {
            color: var(--primary);
            font-size: 1rem;
        }

        .course-header {
            margin-bottom: 1.5rem;
        }

        .course-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .course-subtitle {
            font-size: 1rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .course-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: var(--success);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .enrollment-badge {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: var(--success);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .course-description {
            font-size: 0.9rem;
            color: var(--gray-700);
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .course-deadline {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }

        .course-actions {
            margin-top: auto;
        }

        .enroll-button {
            background: #3b82f6;
            color: var(--white);
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }

        .enroll-button:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .courses-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .course-card {
                flex-direction: column;
                min-height: auto;
            }

            .course-left-panel {
                width: 100%;
                min-width: auto;
                padding: 1.5rem;
            }

            .course-right-panel {
                padding: 1.5rem;
            }

            .course-features {
                flex-direction: column;
                gap: 0.5rem;
            }

            .course-pricing-tags {
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .course-left-panel {
                padding: 1rem;
            }

            .course-right-panel {
                padding: 1rem;
            }

            .course-brand-title {
                font-size: 1.5rem;
            }

            .course-title {
                font-size: 1.25rem;
            }

            .course-features {
                flex-direction: column;
            }
        }

        .card-title {
            color: var(--text-primary);
        }

        .card-text {
            color: var(--text-secondary);
        }

        .card-img-top {
            height: 150px;
            object-fit: cover;
            width: 100%;
        }

        @media (max-width: 768px) {
            .card-img-top {
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            .card-img-top {
                height: 100px;
            }
        }
    </style>
</head>

<body data-theme="light">
    <!-- Theme Toggle Button -->
    <button style="color: orange;" class="btn btn-secondary theme-toggle glass-panel">
        <i class="fas fa-moon"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include("./partials/dashboardNavigation.php"); ?>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h3 class="mb-0">E-Learning Courses</h3>
                </div>

                <!-- Filter Section -->
                <div class="glass-panel p-3 mb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                        <h5 class="mb-3 mb-md-0">Filter Courses</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="?filter=all" class="btn btn-sm <?php echo $filter === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                <i class="fas fa-list me-1"></i> All Courses
                            </a>
                            <?php if ($isLoggedIn): ?>
                                <a href="?filter=enrolled" class="btn btn-sm <?php echo $filter === 'enrolled' ? 'btn-success' : 'btn-outline-success'; ?>">
                                    <i class="fas fa-check-circle me-1"></i> My Courses
                                </a>
                                <a href="?filter=not_enrolled" class="btn btn-sm <?php echo $filter === 'not_enrolled' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                                    <i class="fas fa-plus-circle me-1"></i> Available Courses
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                            <span class="text-muted"><?php echo count($courses); ?> course(s) found</span>
                            <?php if ($isLoggedIn): ?>
                                <span class="text-success">
                                    <i class="fas fa-user-check me-1"></i>
                                    <?php echo count($enrolledCourses); ?> enrolled
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="courses-grid">
                    <!-- Dynamic Courses from Database -->
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <div class="course-card">
                                <!-- Left Panel - Brand Section -->
                                <div class="course-left-panel">
                                    <div class="course-brand">
                                        <div class="course-brand-title"><?php echo strtoupper(substr($course['courseName'], 0, 5)); ?></div>
                                        <div class="course-brand-subtitle">COACHING WITH</div>
                                        <div class="course-brand-name">MK SCHOLARS</div>
                                    </div>
                                    <div class="course-contact">0798611161</div>
                                </div>
                                
                                <!-- Right Panel - Course Details -->
                                <div class="course-right-panel">
                                    <div class="course-badge <?php echo getStatusClass($course['courseDisplayStatus']); ?>">
                                        <?php echo getStatusText($course['courseDisplayStatus']); ?>
                                    </div>
                                    
                                    <?php if ($course['isEnrolled']): ?>
                                        <div class="enrollment-badge">
                                            <i class="fas fa-check-circle"></i>
                                            Enrolled
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="course-header">
                                        <h2 class="course-title"><?php echo htmlspecialchars($course['courseName']); ?></h2>
                                        <p class="course-subtitle"><?php echo htmlspecialchars($course['courseDescription']); ?></p>
                                    </div>
                                    
                                    <div class="course-pricing-tags">
                                        <div class="price-tag">
                                            <?php echo formatPrice($course['amount'], $course['currencySymbol'], $course['currency']); ?> - Complete Package
                                        </div>
                                        <?php if ($course['pricingDescription']): ?>
                                            <div class="price-tag secondary">
                                                <?php echo htmlspecialchars($course['pricingDescription']); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="course-description">
                                        Comprehensive online coaching designed to help students achieve their academic goals. Includes expert guidance, practice materials, and personalized support.
                                    </div>
                                    
                                    <div class="course-features">
                                        <div class="feature-box">
                                            <i class="fas fa-graduation-cap feature-icon"></i>
                                            <span>Online Learning</span>
                                        </div>
                                        <div class="feature-box">
                                            <i class="fas fa-users feature-icon"></i>
                                            <span>30 Seats Available</span>
                                        </div>
                                    </div>
                                    
                                    <div class="course-deadline">
                                        <i class="fas fa-hourglass-half"></i>
                                        <span>Registration Ends: <?php echo date('F j, Y', strtotime($course['courseRegEndDate'])); ?></span>
                                    </div>
                                    
                                    <div class="course-actions">
                                        <?php 
                                        $buttonInfo = getButtonInfo($course, $isLoggedIn);
                                        $buttonUrl = '';
                                        
                                        switch($buttonInfo['action']) {
                                            case 'login':
                                                $buttonUrl = './login';
                                                break;
                                            case 'open':
                                                $buttonUrl = './course-details?id=' . $course['courseId'];
                                                break;
                                            case 'register':
                                                $buttonUrl = './subscription?course=' . $course['courseId'];
                                                break;
                                        }
                                        ?>
                                        <button onclick="window.location.href='<?php echo $buttonUrl; ?>'" class="enroll-button">
                                            <i class="fas fa-arrow-right"></i>
                                            <?php echo $buttonInfo['text']; ?> (Iyandikishe)
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No courses available</h4>
                                <p class="text-muted">Check back later for new courses!</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const body = document.body;
            const themeToggle = document.querySelector('.theme-toggle');
            const savedTheme = localStorage.getItem('theme') || 'light';
            body.setAttribute('data-theme', savedTheme);
            updateToggleIcon();

            themeToggle.addEventListener('click', () => {
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateToggleIcon();
            });

            function updateToggleIcon() {
                const icon = themeToggle.querySelector('i');
                icon.className = body.getAttribute('data-theme') === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }

            // Sidebar toggle functionality
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                });
            }

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>