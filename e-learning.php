<?php
session_start();
include('./dbconnection/connection.php');
include('./php/validateSession.php');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['userId']);
$userId = $isLoggedIn ? $_SESSION['userId'] : null;

// Get filter parameter
$filter = $_GET['filter'] ?? 'all'; // all, enrolled, not_enrolled
$search = $_GET['search'] ?? '';

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
    
    // Build the main courses query - get all courses first
    $coursesQuery = "SELECT c.*, 
                           (SELECT COUNT(*) FROM CourseEnrollments ce WHERE ce.courseId = c.courseId AND ce.enrollmentStatus = 1) as enrollmentCount
                     FROM Courses c 
                     WHERE c.courseDisplayStatus = 1";
    
    $params = [];
    $paramTypes = "";
    
    // Add search filter
    if (!empty($search)) {
        $coursesQuery .= " AND (c.courseName LIKE ? OR c.courseShortDescription LIKE ? OR c.courseLongDescription LIKE ?)";
        $searchTerm = "%$search%";
        $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
        $paramTypes .= "sss";
    }
    
    // Add enrollment filter
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
    
    if (!empty($params)) {
        $stmt = $conn->prepare($coursesQuery);
        $stmt->bind_param($paramTypes, ...$params);
        $stmt->execute();
        $coursesResult = $stmt->get_result();
    } else {
        $coursesResult = mysqli_query($conn, $coursesQuery);
    }
    
    if ($coursesResult && mysqli_num_rows($coursesResult) > 0) {
        while ($course = mysqli_fetch_assoc($coursesResult)) {
            // Add enrollment status to each course
            $course['isEnrolled'] = $isLoggedIn && in_array($course['courseId'], $enrolledCourses);
            
            // Get all pricing options for this course
            $pricingQuery = "SELECT cp.amount, cp.currency, cp.pricingDescription, cp.isFree, curr.currencySymbol
                             FROM CoursePricing cp 
                             LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                             WHERE cp.courseId = ? 
                             ORDER BY cp.amount ASC";
            $pricingStmt = $conn->prepare($pricingQuery);
            $pricingStmt->bind_param("i", $course['courseId']);
            $pricingStmt->execute();
            $pricingResult = $pricingStmt->get_result();
            
            $course['pricingOptions'] = [];
            if ($pricingResult && mysqli_num_rows($pricingResult) > 0) {
                while ($pricing = mysqli_fetch_assoc($pricingResult)) {
                    $course['pricingOptions'][] = $pricing;
                }
            }
            $pricingStmt->close();
            
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

// Helper function to format price
function formatPrice($amount, $currencySymbol, $currency) {
    if ($amount && $amount > 0) {
        $symbol = $currencySymbol ?: $currency;
        return $symbol . ' ' . number_format($amount, 0);
    }
    return 'Free';
}

// Helper function to get pricing display
function getPricingDisplay($pricingOptions) {
    if (empty($pricingOptions)) {
        return ['text' => 'Free', 'description' => 'No pricing available'];
    }
    
    if (count($pricingOptions) == 1) {
        $pricing = $pricingOptions[0];
        return [
            'text' => formatPrice($pricing['amount'], $pricing['currencySymbol'], $pricing['currency']),
            'description' => $pricing['pricingDescription'] ?: 'Standard pricing'
        ];
    }
    
    // Multiple pricing options
    $minPrice = min(array_column($pricingOptions, 'amount'));
    $maxPrice = max(array_column($pricingOptions, 'amount'));
    
    if ($minPrice == $maxPrice) {
        $pricing = $pricingOptions[0];
        return [
            'text' => formatPrice($pricing['amount'], $pricing['currencySymbol'], $pricing['currency']),
            'description' => 'Multiple options available'
        ];
    }
    
    $minPricing = null;
    $maxPricing = null;
    foreach ($pricingOptions as $pricing) {
        if ($pricing['amount'] == $minPrice) $minPricing = $pricing;
        if ($pricing['amount'] == $maxPrice) $maxPricing = $pricing;
    }
    
    $minSymbol = $minPricing['currencySymbol'] ?: $minPricing['currency'];
    $maxSymbol = $maxPricing['currencySymbol'] ?: $maxPricing['currency'];
    
    return [
        'text' => $minSymbol . ' ' . number_format($minPrice, 0) . ' - ' . $maxSymbol . ' ' . number_format($maxPrice, 0),
        'description' => 'Multiple pricing options available'
    ];
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
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-right: 1px solid var(--glass-border);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            width: 250px;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s;
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

        .notification-box {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1050;
        }

        .neumorphic-icon {
            width: 40px;
            height: 40px;
            background: var(--glass-bg);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--neumorphic-shadow);
        }

        .app-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            transition: all 0.3s;
        }

        .progress-glass {
            background: rgba(255, 255, 255, 0.1);
            height: 8px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .notification-box {
                width: 90%;
                right: 5%;
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        /* Search Bar */
        .search-section {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .search-box {
            position: relative;
            margin-bottom: 1rem;
        }

        .search-input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 2px solid var(--glass-border);
            border-radius: 12px;
            font-size: 1rem;
            background: var(--glass-bg);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .filter-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 0.5rem 1rem;
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            background: var(--glass-bg);
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .filter-btn:hover,
        .filter-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        /* Course Grid */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(360px, 1fr));
            gap: 1rem;
            align-items: stretch;
        }

        .course-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
        }

        .course-header {
            padding: 1rem;
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-badge {
            position: absolute;
            top: 0.75rem;
            right: 0.75rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .course-badge.open {
            background: #dcfce7;
            color: #166534;
        }

        .course-badge.closed {
            background: #fef3c7;
            color: #92400e;
        }

        .enrollment-badge {
            position: absolute;
            top: 0.75rem;
            left: 0.75rem;
            background: #3b82f6;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .course-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            line-height: 1.3;
            min-height: 2.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-subtitle {
            color: var(--text-secondary);
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
            min-height: 1.2rem;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            min-height: 4rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.8rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            border: 1px solid var(--glass-border);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .meta-icon {
            color: #3b82f6;
            font-size: 0.875rem;
        }

        .course-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 0.75rem;
            flex: 1;
            min-height: 3rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-pricing {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 0.75rem;
            margin: 0 1rem 1rem;
            border-radius: 12px;
            border-left: 4px solid #3b82f6;
            min-height: 3.5rem;
        }

        .price-main {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
        }

        .price-description {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .pricing-options-list {
            margin: 0;
        }

        .pricing-option {
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            transition: all 0.3s ease;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
        }

        .pricing-option:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .price-amount {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .price-desc {
            margin-top: 0.25rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }

        .course-actions {
            padding: 0 1rem 1rem;
            margin-top: auto;
        }

        .action-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .action-buttons .w-100 {
            flex: 1;
        }

        .btn-primary-custom {
            background: #3b82f6;
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            flex: 1;
            font-size: 0.9rem;
        }

        .btn-primary-custom:hover {
            background: #2563eb;
            transform: translateY(-1px);
            color: white;
        }

        .btn-secondary-custom {
            background: white;
            border: 1px solid #e2e8f0;
            color: #64748b;
            padding: 0.75rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-secondary-custom:hover {
            background: #f8fafc;
            border-color: #3b82f6;
            color: #3b82f6;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .courses-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .filter-buttons {
                justify-content: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .course-meta {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
            
            .meta-item {
                white-space: normal;
                text-overflow: unset;
                overflow: visible;
            }
        }
    </style>
</head>

<body data-theme="light">
    <!-- Theme Toggle Button -->
    <button style="color: orange;" class="btn btn-secondary theme-toggle glass-panel">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Notification Box -->
    <div class="glass-panel notification-box p-3">
        <h5>Notifications</h5>
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bell text-warning me-2"></i>
                    <div>
                        <small>New message received</small>
                        <div class="text-muted">2 minutes ago</div>
                    </div>
                </div>
            </a>
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center">
                    <i class="fas fa-tasks text-success me-2"></i>
                    <div>
                        <small>Task completed</small>
                        <div class="text-muted">1 hour ago</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

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
                    <h3 class="mb-0">E-Learning</h3>
                    <div class="glass-panel px-3 py-2 notification-btn" style="cursor: pointer;">
                        <i class="fas fa-bell text-muted"></i>
                    </div>
                </div>
        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" class="mb-3">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="search-input" placeholder="Search courses..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
            </form>

            <div class="filter-buttons">
                <a href="?filter=all&search=<?php echo urlencode($search); ?>" 
                   class="filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i> All Courses
                </a>
                <?php if ($isLoggedIn): ?>
                    <a href="?filter=enrolled&search=<?php echo urlencode($search); ?>" 
                       class="filter-btn <?php echo $filter === 'enrolled' ? 'active' : ''; ?>">
                        <i class="fas fa-check-circle"></i> My Courses
                    </a>
                    <a href="?filter=not_enrolled&search=<?php echo urlencode($search); ?>" 
                       class="filter-btn <?php echo $filter === 'not_enrolled' ? 'active' : ''; ?>">
                        <i class="fas fa-plus-circle"></i> Available
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="courses-grid">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <div class="course-header">
                            <div class="course-badge <?php echo $course['courseDisplayStatus'] == 1 ? 'open' : 'closed'; ?>">
                                <?php echo $course['courseDisplayStatus'] == 1 ? 'Open' : 'Closed'; ?>
                            </div>
                            
                            <?php if ($course['isEnrolled']): ?>
                                <div class="enrollment-badge">
                                    <i class="fas fa-check-circle"></i>
                                    Enrolled
                                </div>
                            <?php endif; ?>
                            
                            <h3 class="course-title"><?php echo htmlspecialchars($course['courseName']); ?></h3>
                            <p class="course-subtitle"><?php echo htmlspecialchars($course['courseShortDescription']); ?></p>
                            
                            <div class="course-meta">
                                <div class="meta-item">
                                    <i class="fas fa-play meta-icon"></i>
                                    <span>Starts: <?php echo date('M j, Y', strtotime($course['courseStartDate'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-flag-checkered meta-icon"></i>
                                    <span>Ends: <?php echo date('M j, Y', strtotime($course['courseEndDate'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar-times meta-icon"></i>
                                    <span>Register by: <?php echo date('M j, Y', strtotime($course['courseRegEndDate'])); ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users meta-icon"></i>
                                    <span><?php echo $course['enrollmentCount']; ?> enrolled</span>
                                </div>
                            </div>
                            
                            <p class="course-description">
                                <?php echo htmlspecialchars($course['courseShortDescription']); ?>
                            </p>
                        </div>
                        
                        <div class="course-pricing">
                            <?php if (!empty($course['pricingOptions'])): ?>
                                <div class="pricing-options-list">
                                    <?php foreach ($course['pricingOptions'] as $index => $pricing): ?>
                                        <div class="pricing-option <?php echo $index > 0 ? 'mt-2' : ''; ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="price-amount">
                                                        <?php echo formatPrice($pricing['amount'], $pricing['currencySymbol'], $pricing['currency']); ?>
                                                    </div>
                                                    <?php if ($pricing['pricingDescription']): ?>
                                                        <div class="price-desc">
                                                            <small class="text-muted"><?php echo htmlspecialchars($pricing['pricingDescription']); ?></small>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($pricing['isFree']): ?>
                                                    <span class="badge bg-success">Free</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="price-main">Free</div>
                                <div class="price-description">No pricing available</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="course-actions">
                            <div class="action-buttons">
                                <?php 
                                $buttonInfo = getButtonInfo($course, $isLoggedIn);
                                $buttonUrl = '';
                                
                                switch($buttonInfo['action']) {
                                    case 'login':
                                        $next = urlencode('/mkscholars/e-learning');
                                        $buttonUrl = './login?next=' . $next;
                                        break;
                                    case 'open':
                                        $buttonUrl = './course-details?id=' . $course['courseId'];
                                        break;
                                    case 'register':
                                        $buttonUrl = './subscription?course=' . $course['courseId'];
                                        break;
                                }
                                ?>
                                <a href="<?php echo $buttonUrl; ?>" class="btn-primary-custom w-100">
                                    <i class="<?php echo $buttonInfo['icon']; ?>"></i>
                                    <?php echo $buttonInfo['text']; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-graduation-cap"></i>
                    <h4>No courses found</h4>
                    <p>Try adjusting your search criteria or check back later for new courses!</p>
                    <a href="?filter=all" class="btn-primary-custom" style="display: inline-flex; width: auto; padding: 0.75rem 1.5rem;">
                        <i class="fas fa-refresh"></i>
                        Reset Filters
                    </a>
                </div>
            <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Toggle
        const themeToggle = document.querySelector('.theme-toggle');
        const body = document.body;
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
            const currentTheme = body.getAttribute('data-theme');
            themeToggle.innerHTML = currentTheme === 'light' ?
                '<i class="fas fa-moon"></i>' :
                '<i class="fas fa-sun"></i>';
        }

        // Notifications
        const notificationBtn = document.querySelector('.notification-btn');
        const notificationBox = document.querySelector('.notification-box');

        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationBox.style.display = notificationBox.style.display === 'block' ? 'none' : 'block';
        });

        // Close notifications when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target)) {
                notificationBox.style.display = 'none';
            }
        });

        // Mobile Sidebar Toggle
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const mainContent = document.querySelector('.main-content');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });

        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.form.submit();
                }, 500);
            });
        }
    </script>
</body>
</html>