<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide errors from users
ini_set('log_errors', 1);

// Include session configuration for persistent sessions
include("./config/session.php");

// Initialize variables
$courses = [];
$error = null;
$hasError = false;

try {
    // Include database connection
    if (!include("./dbconnection/connection.php")) {
        throw new Exception("Database connection file not found");
    }

    // Check if database connection is available
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Debug mode - add ?debug=1 to URL to see debug info
    $debugMode = isset($_GET['debug']) && $_GET['debug'] == '1';
    
    if ($debugMode) {
        error_log("=== COURSES PAGE DEBUG ===");
        error_log("Environment: " . (isOnline() ? 'PRODUCTION' : 'LOCAL'));
        error_log("Database connection: " . ($conn ? 'SUCCESS' : 'FAILED'));
        error_log("Server: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown'));
        error_log("Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'));
        
        // Check if Courses table exists
        $tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'Courses'");
        if ($tableCheck && mysqli_num_rows($tableCheck) > 0) {
            error_log("Courses table: EXISTS");
        } else {
            error_log("Courses table: NOT FOUND");
        }
        
        // Check table structure
        $structureCheck = mysqli_query($conn, "DESCRIBE Courses");
        if ($structureCheck) {
            error_log("Courses table structure check: SUCCESS");
        } else {
            error_log("Courses table structure check: FAILED - " . mysqli_error($conn));
        }
    }

    // First, get count of open courses only
    $countQuery = "SELECT COUNT(*) as openCoursesCount FROM Courses WHERE courseDisplayStatus = 1";
    $countResult = mysqli_query($conn, $countQuery);
    $openCoursesCount = 0;
    
    if ($countResult) {
        $countData = mysqli_fetch_assoc($countResult);
        $openCoursesCount = (int)$countData['openCoursesCount'];
        
        if ($debugMode) {
            error_log("Open courses count: " . $openCoursesCount);
        }
    } else {
        $countError = mysqli_error($conn);
        error_log("Count query failed: " . $countError);
        
        if ($debugMode) {
            error_log("Count query: " . $countQuery);
            error_log("Count error: " . $countError);
        }
    }

    // First, fetch unique courses from database - only open courses
    $coursesQuery = "SELECT * FROM Courses WHERE courseDisplayStatus = 1 ORDER BY courseCreatedDate DESC";
    
    if ($debugMode) {
        error_log("Courses query: " . $coursesQuery);
    }
    
    $coursesResult = mysqli_query($conn, $coursesQuery);
    
    if (!$coursesResult) {
        $queryError = mysqli_error($conn);
        error_log("Courses query failed: " . $queryError);
        throw new Exception("Database query failed: " . $queryError);
    }
    
    $coursesFound = mysqli_num_rows($coursesResult);
    
    if ($debugMode) {
        error_log("Unique courses found: " . $coursesFound);
    }
    
    if ($coursesFound > 0) {
        while ($course = mysqli_fetch_assoc($coursesResult)) {
            // Validate course data
            if (empty($course['courseName'])) {
                error_log("Course with invalid name found: " . json_encode($course));
                continue;
            }
            
            // Fetch pricing options for this course
            $pricingQuery = "SELECT cp.*, curr.currencySymbol 
                           FROM CoursePricing cp 
                           LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                           WHERE cp.courseId = ? 
                           ORDER BY cp.amount ASC";
            
            $pricingStmt = mysqli_prepare($conn, $pricingQuery);
            if ($pricingStmt) {
                mysqli_stmt_bind_param($pricingStmt, "i", $course['courseId']);
                mysqli_stmt_execute($pricingStmt);
                $pricingResult = mysqli_stmt_get_result($pricingStmt);
                
                $pricingOptions = [];
                if ($pricingResult && mysqli_num_rows($pricingResult) > 0) {
                    while ($pricing = mysqli_fetch_assoc($pricingResult)) {
                        $pricingOptions[] = $pricing;
                    }
                }
                mysqli_stmt_close($pricingStmt);
                
                // Add pricing options to course
                $course['pricingOptions'] = $pricingOptions;
                
                // For backward compatibility, add the first pricing option to the main course object
                if (!empty($pricingOptions)) {
                    $firstPricing = $pricingOptions[0];
                    $course['amount'] = $firstPricing['amount'];
                    $course['currency'] = $firstPricing['currency'];
                    $course['currencySymbol'] = $firstPricing['currencySymbol'];
                    $course['pricingDescription'] = $firstPricing['pricingDescription'];
                }
            }
            
            $courses[] = $course;
        }
        
        if ($debugMode) {
            error_log("Courses with pricing processed: " . count($courses));
            if (!empty($courses)) {
                error_log("First course pricing options: " . count($courses[0]['pricingOptions'] ?? []));
            }
        }
    }

} catch (Exception $e) {
    $error = $e->getMessage();
    $hasError = true;
    error_log("Courses page error: " . $error);
}

// Helper: detect offline/online and build admin image URL
function isLocalHost() {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false;
}

function getBaseDomain($host) {
    $host = strtolower($host);
    if (strpos($host, '://') !== false) {
        $host = parse_url($host, PHP_URL_HOST) ?? $host;
    }
    $host = preg_replace('/^www\./', '', $host);
    $host = preg_replace('/^admin\./', '', $host);
    return $host;
}

function getCourseImageUrl($storedPath) {
    if (!$storedPath) {
        return '';
    }
    // Ensure we use the DB-provided relative path like 'uploads/courses/images/xyz.jpg'
    $relativePath = ltrim($storedPath, '/');
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (isLocalHost()) {
        // Local: admin app is a folder in this project
        return './admin/' . $relativePath;
    }
    // Online: admin is a subdomain
    $domain = getBaseDomain($host);
    $adminHost = 'admin.' . $domain;
    return 'https://' . $adminHost . '/' . $relativePath;
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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses | MK Scholars</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #3b82f6;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --white: #ffffff;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.3);
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--gray-800);
            line-height: 1.6;
            padding-top: 120px;
            min-height: 100vh;
        }

        /* ==== Main Container ==== */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* ==== Page Header ==== */
        .page-header {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-lg);
            text-align: center;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 1.1rem;
            margin: 0;
        }

        /* ==== Course Grid ==== */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        /* ==== Modern Course Cards ==== */
        .course-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 0;
            margin: 0;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            min-height: 500px;
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        /* ==== Course Image Section ==== */
        .course-image {
            width: 100%;
            height: 180px;
            background: #f3f4f6;
            border-bottom: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Course brand removed with images */

        /* Course brand styles removed with images */

        /* ==== Course Content Section ==== */
        .course-content {
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* ==== Course Header ==== */
        .course-header {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .course-badge {
            position: absolute;
            top: -1rem;
            right: 0;
            background: var(--success);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: var(--shadow-md);
        }

        .course-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2; /* show first 2 lines only */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 2.4em; /* reserve space for two lines */
        }

        .course-subtitle {
            font-size: 1rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
            font-weight: 500;
        }

        /* ==== Course Pricing ==== */
        .course-pricing {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .price-main {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }

        .price-description {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin: 0;
        }

        /* ==== Multiple Pricing Options ==== */
        .pricing-options {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .pricing-option {
            background: white;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
        }

        .pricing-option.featured {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border-color: var(--primary);
        }

        .pricing-option:hover {
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        }

        .price-amount {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }

        .pricing-option.featured .price-amount {
            color: white;
        }

        .price-desc {
            font-size: 0.8rem;
            color: var(--gray-600);
            font-weight: 500;
        }

        .pricing-option.featured .price-desc {
            color: rgba(255, 255, 255, 0.9);
        }

        /* ==== Course Description ==== */
        .course-description {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        /* ==== Course Features ==== */
        .course-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .feature-icon {
            color: var(--primary);
            font-size: 1rem;
        }

        /* ==== Course Deadline ==== */
        .course-deadline {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            background: var(--gray-50);
            border-radius: 8px;
        }

        /* ==== Course Actions ==== */
        .course-actions {
            margin-top: auto;
        }

        .enroll-button {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            box-shadow: var(--shadow-md);
        }

        .enroll-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .enroll-button:active {
            transform: translateY(0);
        }

        .enroll-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* ==== WhatsApp Share Button ==== */
        .whatsapp-share-button {
            background: linear-gradient(135deg, #25D366, #128C7E);
            color: var(--white);
            border: none;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            margin-top: 0.75rem;
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
        }

        .whatsapp-share-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
            background: linear-gradient(135deg, #20BA5A, #0FA76E);
        }

        .whatsapp-share-button:active {
            transform: translateY(0);
        }

        .whatsapp-share-button i {
            font-size: 1.2rem;
        }

        /* ==== Error Handling Styles ==== */
        .error-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 3rem;
            margin: 2rem 0;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }

        .error-icon {
            font-size: 4rem;
            color: var(--danger);
            margin-bottom: 1rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .error-message {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }

        .retry-button {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .retry-button:hover {
            background: var(--primary-dark);
        }

        .loading-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--gray-200);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ==== Responsive Design ==== */
        @media (max-width: 768px) {
            body { 
                padding-top: 100px; 
            }
            
            .main-container {
                padding: 1rem;
            }

            .page-header {
                padding: 1.5rem;
            }

            .page-title {
                font-size: 2rem;
            }

            .courses-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .course-card {
                min-height: auto;
            }

            .course-content {
                padding: 1.5rem;
            }

            .course-features {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            body { 
                padding-top: 90px; 
            }

            .main-container {
                padding: 0.5rem;
            }

            .page-header {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.75rem;
            }

            .course-content {
                padding: 1rem;
            }

            .course-title {
                font-size: 1.25rem;
            }

            .enroll-button {
                padding: 0.875rem 1.5rem;
                font-size: 0.9rem;
            }

            .whatsapp-share-button {
                padding: 0.75rem 1.25rem;
                font-size: 0.85rem;
            }
        }
    </style>
</head>

<body>
    <!-- ==== Universal Navigation ==== -->
    <?php include("./partials/navigation.php") ?>

    <!-- ==== Courses Section ==== -->
    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">Available Courses</h1>
            <p class="page-subtitle">
                <?php if ($hasError): ?>
                    Unable to load course count
                <?php else: ?>
                    <?php echo $openCoursesCount; ?> open course(s) available for enrollment
                <?php endif; ?>
            </p>
            
            <!-- Debug Information (only show if debug=1 in URL) -->
            <?php if (isset($debugMode) && $debugMode): ?>
                <div style="background: #f0f0f0; padding: 15px; margin-top: 20px; border-radius: 8px; font-family: monospace; font-size: 12px;">
                    <h4>Debug Information:</h4>
                    <p><strong>Environment:</strong> <?php echo isOnline() ? 'PRODUCTION' : 'LOCAL'; ?></p>
                    <p><strong>Database Connection:</strong> <?php echo $conn ? 'SUCCESS' : 'FAILED'; ?></p>
                    <p><strong>Open Courses Count:</strong> <?php echo $openCoursesCount; ?></p>
                    <p><strong>Courses Found:</strong> <?php echo count($courses); ?></p>
                    <p><strong>Server:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'Unknown'; ?></p>
                    <p><strong>Error:</strong> <?php echo $error ? htmlspecialchars($error) : 'None'; ?></p>
                    <?php if ($conn): ?>
                        <p><strong>Database:</strong> <?php echo mysqli_get_server_info($conn); ?></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Error Handling Container -->
        <div id="errorContainer" style="display: none;">
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                                        </div>
                <h2 class="error-title">Something went wrong</h2>
                <p class="error-message" id="errorMessage">We're having trouble loading the courses. Please try again.</p>
                <button class="retry-button" onclick="retryLoadCourses()">
                    <i class="fas fa-refresh me-2"></i>Try Again
                </button>
                                    </div>
                                </div>

        <!-- Loading Container -->
        <div id="loadingContainer" style="display: none;">
            <div class="loading-container">
                <div class="loading-spinner"></div>
                                        </div>
                                    </div>

        <!-- PHP Error Display -->
        <?php if ($hasError): ?>
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h2 class="error-title">Unable to Load Courses</h2>
                <p class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </p>
                <button class="retry-button" onclick="location.reload()">
                    <i class="fas fa-refresh me-2"></i>Refresh Page
                </button>
                                </div>
                            <?php endif; ?>

        <!-- Courses Grid -->
        <div class="courses-grid" id="coursesGrid" <?php echo $hasError ? 'style="display: none;"' : ''; ?>>
            <!-- Dynamic Courses from Database Only -->
            <?php if (!$hasError && !empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <!-- Course Image -->
                        <?php 
                            $imageUrl = '';
                            if (!empty($course['coursePhoto'])) {
                                $imageUrl = getCourseImageUrl($course['coursePhoto']);
                            }
                        ?>
                        <?php if ($imageUrl): ?>
                        <div class="course-image">
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars($course['courseName']); ?>">
                        </div>
                        <?php endif; ?>
                        
                        <!-- Course Content -->
                        <div class="course-content">
                            <div class="course-header">
                            <div class="course-badge <?php echo getStatusClass($course['courseDisplayStatus']); ?>">
                                <?php echo getStatusText($course['courseDisplayStatus']); ?>
                            </div>
                                <h2 class="course-title"><?php echo htmlspecialchars($course['courseName']); ?></h2>
                                <p class="course-subtitle"><?php echo htmlspecialchars($course['courseDescription']); ?></p>
                            </div>
                            
                            <!-- Pricing section intentionally removed on this page -->
                            
                            <div class="course-description">
                                <?php echo htmlspecialchars($course['courseShortDescription'] ?? 'Course description not available'); ?>
                            </div>
                            
                            <div class="course-features">
                                <?php
                                // Read tags from CourseTags table for this course
                                $tags = [];
                                $tagSql = "SELECT courseTagIcon, tagDescription, tagColor FROM CourseTags WHERE courseId = ? AND isActive = 1 ORDER BY courseTagId DESC LIMIT 6";
                                if ($tagStmt = mysqli_prepare($conn, $tagSql)) {
                                    mysqli_stmt_bind_param($tagStmt, 'i', $course['courseId']);
                                    mysqli_stmt_execute($tagStmt);
                                    $tagRes = mysqli_stmt_get_result($tagStmt);
                                    while ($t = mysqli_fetch_assoc($tagRes)) { $tags[] = $t; }
                                    mysqli_stmt_close($tagStmt);
                                }

                                if (!empty($tags)) {
                                    foreach ($tags as $t) {
                                        $iconClass = !empty($t['courseTagIcon']) ? $t['courseTagIcon'] : 'fas fa-tag';
                                        $desc = !empty($t['tagDescription']) ? $t['tagDescription'] : 'Tag';
                                        $color = !empty($t['tagColor']) ? $t['tagColor'] : '#3b82f6';
                                        echo '<div class="feature-item">'
                                            . '<i class="' . htmlspecialchars($iconClass) . ' feature-icon" style="color:' . htmlspecialchars($color) . ';"></i>'
                                            . '<span>' . htmlspecialchars($desc) . '</span>'
                                            . '</div>';
                                    }
                                }
                                ?>
                            </div>
                            
                            <div class="course-deadline">
                                <i class="fas fa-hourglass-half"></i>
                                <span>Registration Ends: <?php echo date('F j, Y', strtotime($course['courseRegEndDate'])); ?></span>
                            </div>
                            
                            <div class="course-actions">
                                <?php
                                    $isLoggedIn = isset($_SESSION) && isset($_SESSION['userId']);
                                    $next = urlencode('/mkscholars/courses');
                                    
                                    // Build course URL for sharing
                                    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
                                    $scriptPath = str_replace('/courses.php', '', $_SERVER['PHP_SELF']);
                                    $courseUrl = $baseUrl . $scriptPath . "/course-details?id=" . $course['courseId'];
                                    
                                    // Prepare data attributes for sharing (using JSON encoding for safe JavaScript)
                                    $shareData = [
                                        'title' => $course['courseName'],
                                        'image' => $imageUrl ?: '',
                                        'url' => $courseUrl,
                                        'description' => $course['courseShortDescription'] ?? $course['courseDescription'] ?? ''
                                    ];
                                    $shareDataJson = json_encode($shareData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
                                ?>
                                <button onclick="enrollCourse(<?php echo $course['courseId']; ?>, <?php echo $isLoggedIn ? 'true' : 'false'; ?>)" 
                                        class="enroll-button" 
                                        data-course-id="<?php echo $course['courseId']; ?>">
                                    <i class="fas fa-arrow-right"></i>
                                    Register Now (Iyandikishe)
                                </button>
                                <button onclick="shareOnWhatsAppFromData(<?php echo htmlspecialchars($shareDataJson, ENT_QUOTES, 'UTF-8'); ?>)" 
                                        class="whatsapp-share-button" 
                                        title="Share on WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                    Share on WhatsApp
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php elseif (!$hasError): ?>
                <div class="error-container">
                    <div class="error-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h2 class="error-title">No courses available</h2>
                    <p class="error-message">Check back later for new courses!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- ==== Enhanced JavaScript with Error Handling ==== -->
    <script>
        // Global error handling
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
            showError('An unexpected error occurred. Please refresh the page.');
        });

        // Unhandled promise rejection handling
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            showError('A network error occurred. Please check your connection.');
        });

        // Error display function
        function showError(message) {
            const errorContainer = document.getElementById('errorContainer');
            const errorMessage = document.getElementById('errorMessage');
            const coursesGrid = document.getElementById('coursesGrid');
            
            if (errorContainer && errorMessage) {
                errorMessage.textContent = message;
                errorContainer.style.display = 'block';
                if (coursesGrid) coursesGrid.style.display = 'none';
            }
        }

        // Hide error function
        function hideError() {
            const errorContainer = document.getElementById('errorContainer');
            const coursesGrid = document.getElementById('coursesGrid');
            
            if (errorContainer) errorContainer.style.display = 'none';
            if (coursesGrid) coursesGrid.style.display = 'grid';
        }

        // Show loading function
        function showLoading() {
            const loadingContainer = document.getElementById('loadingContainer');
            const coursesGrid = document.getElementById('coursesGrid');
            
            if (loadingContainer) loadingContainer.style.display = 'block';
            if (coursesGrid) coursesGrid.style.display = 'none';
        }

        // Hide loading function
        function hideLoading() {
            const loadingContainer = document.getElementById('loadingContainer');
            const coursesGrid = document.getElementById('coursesGrid');
            
            if (loadingContainer) loadingContainer.style.display = 'none';
            if (coursesGrid) coursesGrid.style.display = 'grid';
        }

        // Retry function
        function retryLoadCourses() {
            hideError();
            showLoading();
            
            // Simulate retry (in real implementation, this would reload data)
            setTimeout(() => {
                hideLoading();
                location.reload();
            }, 1000);
        }

        // WhatsApp Share Function (matching scholarship-details.php logic)
        function shareOnWhatsAppFromData(data) {
            try {
                const courseUrl = data.url || '';
                const title = data.title || 'Course';
                
                // Build the share message - matching scholarship-details.php format
                const message = 'Check out this course: ' + courseUrl;
                
                // Create WhatsApp share URL - using api.whatsapp.com like scholarship-details.php
                const whatsappUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(message);
                
                // Open in new window/tab (matching scholarship-details.php behavior)
                window.open(whatsappUrl, '_blank');
                
            } catch (error) {
                console.error('WhatsApp share error:', error);
                showError('Unable to share on WhatsApp. Please try again.');
            }
        }
        
        // Legacy function for backward compatibility
        function shareOnWhatsApp(title, imageUrl, courseUrl, description) {
            shareOnWhatsAppFromData({
                title: title,
                image: imageUrl,
                url: courseUrl,
                description: description
            });
        }

        // Enhanced course enrollment function
        function enrollCourse(courseId, isLoggedIn) {
            try {
                if (!courseId) {
                    throw new Error('Invalid course ID');
                }

                const button = document.querySelector(`[data-course-id="${courseId}"]`);
                if (!button) {
                    throw new Error('Course button not found');
                }

                const originalText = button.innerHTML;
                    
                    // Add loading state
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                button.disabled = true;
                    
                // Simulate processing delay
                    setTimeout(() => {
                    try {
                        if (isLoggedIn) {
                            // Redirect to subscription page
                            window.location.href = `./subscription?course=${courseId}`;
                        } else {
                            // Redirect to login page
                            const next = encodeURIComponent('/mkscholars/courses');
                            window.location.href = `./login?next=${next}`;
                        }
                    } catch (error) {
                        console.error('Navigation error:', error);
                        showError('Unable to navigate to the enrollment page. Please try again.');
                        
                        // Restore button state
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                }, 1000);

            } catch (error) {
                console.error('Enrollment error:', error);
                showError('Unable to process enrollment. Please try again.');
            }
        }

        // Image error handling removed - no images

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            try {
                // Image error handling removed - no images

                // Add click handlers to enroll buttons
            const enrollButtons = document.querySelectorAll('.enroll-button');
            enrollButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const courseId = this.getAttribute('data-course-id');
                        const isLoggedIn = this.onclick.toString().includes('true');
                        enrollCourse(parseInt(courseId), isLoggedIn);
                });
            });

                // Add smooth scroll behavior
                document.documentElement.style.scrollBehavior = 'smooth';

                // Add keyboard navigation support
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        hideError();
                    }
                });

                console.log('Courses page initialized successfully');

            } catch (error) {
                console.error('Initialization error:', error);
                showError('Page initialization failed. Please refresh the page.');
            }
        });

        // Network status monitoring
        window.addEventListener('online', function() {
            console.log('Network connection restored');
        });

        window.addEventListener('offline', function() {
            showError('You are currently offline. Please check your internet connection.');
        });
    </script>

</body>

</html>