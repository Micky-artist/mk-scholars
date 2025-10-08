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

    // Fetch courses from database with enrollment status and payment status
$courses = [];
$enrolledCourses = [];
    $paidCourses = [];

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
            
            // Get user's paid courses from subscription table
            $subscriptionQuery = "SELECT DISTINCT Item FROM subscription WHERE UserId = ? AND SubscriptionStatus = 1 AND (expirationDate IS NULL OR expirationDate > NOW())";
            $stmt = $conn->prepare($subscriptionQuery);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $subscriptionResult = $stmt->get_result();
            
            while ($subscription = $subscriptionResult->fetch_assoc()) {
                // Check if the subscription item is a course ID
                if (is_numeric($subscription['Item'])) {
                    $paidCourses[] = (int)$subscription['Item'];
                }
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
    if ($filter === 'enrolled' && $isLoggedIn) {
        // Show courses that user is enrolled in OR has paid for
        $enrolledAndPaidCourses = array_unique(array_merge($enrolledCourses, $paidCourses));
        if (!empty($enrolledAndPaidCourses)) {
            $courseIds = implode(',', array_map('intval', $enrolledAndPaidCourses));
        $coursesQuery .= " AND c.courseId IN ($courseIds)";
        } else {
            // If no enrolled or paid courses, show empty result
            $coursesQuery .= " AND 1=0";
        }
    } elseif ($filter === 'available' && $isLoggedIn) {
        // Show courses that user is NOT enrolled in AND has NOT paid for
        $enrolledAndPaidCourses = array_unique(array_merge($enrolledCourses, $paidCourses));
        if (!empty($enrolledAndPaidCourses)) {
            $courseIds = implode(',', array_map('intval', $enrolledAndPaidCourses));
        $coursesQuery .= " AND c.courseId NOT IN ($courseIds)";
        }
        // If no enrolled or paid courses, show all courses (no additional filter)
    } elseif ($filter === 'available' && !$isLoggedIn) {
        // If not logged in, show all courses (no additional filter)
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
            // Add enrollment and payment status to each course
            $course['isEnrolled'] = $isLoggedIn && in_array($course['courseId'], $enrolledCourses);
            $course['isPaid'] = $isLoggedIn && in_array($course['courseId'], $paidCourses);
            
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
            
            // Parse course content for notes
            $courseContent = json_decode($course['courseContent'], true);
            $course['hasNotes'] = false;
            $course['courseNotes'] = [];
            
            if ($courseContent && isset($courseContent['sections']) && is_array($courseContent['sections'])) {
                foreach ($courseContent['sections'] as $section) {
                    if (isset($section['type']) && in_array($section['type'], ['text', 'file', 'image', 'video', 'audio'])) {
                        $course['hasNotes'] = true;
                        $course['courseNotes'][] = $section;
                    }
                }
            }
            
            $courses[] = $course;
        }
    }
}

// Helper function to get button info based on enrollment and payment status
function getButtonInfo($course, $isLoggedIn) {
    if (!$isLoggedIn) {
        return [
            'text' => 'Register to View',
            'class' => 'btn-outline-primary',
            'action' => 'login',
            'icon' => 'fas fa-user-plus'
        ];
    }
    
    // Check if user has access (either enrolled or paid)
    $hasAccess = $course['isEnrolled'] || $course['isPaid'];
    
    if ($hasAccess) {
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

// Helper function to render course notes with student view logic
function renderCourseNotes($course, $isPaid) {
    if (!$course['hasNotes']) {
        return '<div class="text-muted text-center py-3"><i class="fas fa-file-alt me-2"></i>No course materials available</div>';
    }
    
    if (!$isPaid) {
        return '<div class="text-center py-3">
                    <i class="fas fa-lock fa-2x text-muted mb-2"></i>
                    <p class="text-muted mb-0">Course materials are available after payment</p>
                </div>';
    }
    
    $notesHtml = '<div class="course-notes">';
    
    foreach ($course['courseNotes'] as $section) {
        // Check section visibility for student view
        $now = new DateTime();
        $sectionPublishDate = isset($section['publishDate']) ? new DateTime($section['publishDate']) : null;
        $sectionUnpublishDate = isset($section['unpublishDate']) ? new DateTime($section['unpublishDate']) : null;
        
        if (isset($section['visibilityMode'])) {
            if ($section['visibilityMode'] === 'hidden') {
                continue; // Skip hidden sections
            }
            
            if ($section['visibilityMode'] === 'scheduled') {
                if ($sectionPublishDate && $now < $sectionPublishDate) {
                    continue; // Skip sections not yet published
                }
                
                if ($sectionUnpublishDate && $now > $sectionUnpublishDate) {
                    continue; // Skip sections that have been unpublished
                }
            }
        }
        
        $notesHtml .= '<div class="note-section mb-3 p-3 border rounded">';
        $notesHtml .= '<h6 class="mb-2"><i class="fas fa-file-alt me-2"></i>' . htmlspecialchars($section['title'] ?? 'Untitled Section') . '</h6>';
        
        if (isset($section['content']) && !empty($section['content'])) {
            $notesHtml .= '<div class="note-content">' . nl2br(htmlspecialchars($section['content'])) . '</div>';
        }
        
        // Handle files
        if (isset($section['files']) && is_array($section['files'])) {
            $notesHtml .= '<div class="note-files mt-2">';
            foreach ($section['files'] as $file) {
                $fileUrl = './admin/uploads/' . $file['filePath'];
                $fileIcon = getFileIcon($file['fileName']);
                $fileSize = isset($file['fileSize']) ? formatFileSize($file['fileSize']) : '';
                
                $notesHtml .= '<div class="file-item mb-2">
                    <a href="' . $fileUrl . '" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="' . $fileIcon . ' me-2"></i>' . htmlspecialchars($file['fileName']) . 
                        ($fileSize ? ' <small>(' . $fileSize . ')</small>' : '') . '
                    </a>
                </div>';
            }
            $notesHtml .= '</div>';
        }
        
        $notesHtml .= '</div>';
    }
    
    $notesHtml .= '</div>';
    return $notesHtml;
}

// Helper function to get file icon
function getFileIcon($fileName) {
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    switch ($extension) {
        case 'pdf': return 'fas fa-file-pdf';
        case 'doc':
        case 'docx': return 'fas fa-file-word';
        case 'xls':
        case 'xlsx': return 'fas fa-file-excel';
        case 'ppt':
        case 'pptx': return 'fas fa-file-powerpoint';
        case 'jpg':
        case 'jpeg':
        case 'png':
        case 'gif': return 'fas fa-file-image';
        case 'mp4':
        case 'avi':
        case 'mov': return 'fas fa-file-video';
        case 'mp3':
        case 'wav': return 'fas fa-file-audio';
        case 'zip':
        case 'rar': return 'fas fa-file-archive';
        default: return 'fas fa-file';
    }
}

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
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
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 0.75rem;
            align-items: stretch;
        }

        .course-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 260px;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
        }

        .course-header {
            padding: 0.6rem;
            position: relative;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .course-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.65rem;
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
            top: 0.5rem;
            left: 0.5rem;
            background: #3b82f6;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .course-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.3rem;
            line-height: 1.2;
            min-height: 2.2rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-subtitle {
            color: var(--text-secondary);
            margin-bottom: 0.4rem;
            font-size: 0.9rem;
            min-height: 1.1rem;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.4rem;
            margin-bottom: 0.4rem;
            min-height: 2.8rem;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: var(--text-secondary);
            font-size: 0.8rem;
            padding: 0.3rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
            border: 1px solid var(--glass-border);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .meta-icon {
            color: #3b82f6;
            font-size: 0.9rem;
        }

        .course-description {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.4;
            margin-bottom: 0.6rem;
            flex: 1;
            min-height: 2.8rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-pricing {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 0.6rem;
            margin: 0 0.8rem 0.8rem;
            border-radius: 10px;
            border-left: 4px solid #3b82f6;
            min-height: 3rem;
        }

        .price-main {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.2rem;
        }

        .price-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }

        .pricing-options-list {
            margin: 0;
        }

        .pricing-option {
            padding: 0.3rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 5px;
            transition: all 0.3s ease;
            min-height: 1.8rem;
            display: flex;
            align-items: center;
        }

        .pricing-option:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-1px);
        }

        .price-amount {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .price-desc {
            margin-top: 0.2rem;
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.2rem 0.4rem;
        }


        .course-actions {
            padding: 0 0.6rem 0.6rem;
            margin-top: auto;
        }

        .action-buttons {
            display: flex;
            gap: 0.4rem;
        }

        .action-buttons .w-100 {
            flex: 1;
        }

        .btn-primary-custom {
            background: #3b82f6;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.3rem;
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
                gap: 0.4rem;
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
            <?php include("./partials/universalNavigation.php"); ?>

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
                    <a href="?filter=available&search=<?php echo urlencode($search); ?>" 
                       class="filter-btn <?php echo $filter === 'available' ? 'active' : ''; ?>">
                        <i class="fas fa-plus-circle"></i> Available
                                </a>
                            <?php endif; ?>
                        </div>
            
            <!-- Debug info (remove in production) -->
                            <?php if ($isLoggedIn): ?>
            <div class="mt-2 text-muted small">
                <small>Filter: <?php echo $filter; ?> | Enrolled: <?php echo count($enrolledCourses); ?> | Paid: <?php echo count($paidCourses); ?> | Total: <?php echo count($courses); ?></small>
            </div>
                            <?php endif; ?>
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
                                
                            <?php if ($course['isEnrolled'] || $course['isPaid']): ?>
                                        <div class="enrollment-badge">
                                            <i class="fas fa-check-circle"></i>
                                    <?php echo $course['isPaid'] ? 'Paid' : 'Enrolled'; ?>
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
                                        $buttonUrl = 'javascript:void(0)';
                                        $courseNotesJson = htmlspecialchars(json_encode($course['courseNotes']), ENT_QUOTES, 'UTF-8');
                                        $hasAccess = $course['isEnrolled'] || $course['isPaid'];
                                        $onclick = "previewCourse(" . $course['courseId'] . ", '" . addslashes($course['courseName']) . "', " . $courseNotesJson . ", " . ($hasAccess ? 'true' : 'false') . ")";
                                                break;
                                            case 'register':
                                                $buttonUrl = './subscription?course=' . $course['courseId'];
                                                break;
                                        }
                                        ?>
                                <a href="<?php echo $buttonUrl; ?>" 
                                   class="btn-primary-custom w-100"
                                   <?php if (isset($onclick)): ?>onclick="<?php echo $onclick; ?>"<?php endif; ?>>
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

        // Course preview function with student view logic
        function previewCourse(courseId, courseName, courseNotesJson, isPaid) {
            // Open preview in new window with blank URL
            const previewWindow = window.open('', '_blank');
            
            if (!previewWindow) {
                alert('Please allow popups to view course content');
                return;
            }
            
            // Parse the JSON data
            let courseNotes;
            try {
                courseNotes = typeof courseNotesJson === 'string' ? JSON.parse(courseNotesJson) : courseNotesJson;
            } catch (e) {
                console.error('Error parsing course notes:', e);
                courseNotes = [];
            }
            
            // Filter notes based on student view logic
            const visibleNotes = courseNotes.filter(section => {
                // Check section visibility for student view
                const now = new Date();
                const sectionPublishDate = section.publishDate ? new Date(section.publishDate) : null;
                const sectionUnpublishDate = section.unpublishDate ? new Date(section.unpublishDate) : null;
                
                if (section.visibilityMode === 'hidden') {
                    return false; // Skip hidden sections
                }
                
                if (section.visibilityMode === 'scheduled') {
                    if (sectionPublishDate && now < sectionPublishDate) {
                        return false; // Skip sections not yet published
                    }
                    
                    if (sectionUnpublishDate && now > sectionUnpublishDate) {
                        return false; // Skip sections that have been unpublished
                    }
                }
                
                return true;
            });
            
            // Generate content for each section
            const sectionsHtml = visibleNotes.map(section => {
                let contentHtml = '';
                
                if (section.type === 'image' && section.files && section.files.length > 0) {
                    // Display uploaded images
                    contentHtml = '<div class="image-gallery">';
                    section.files.forEach(file => {
                        const imageUrl = `./admin/uploads/${file.filePath}`;
                        contentHtml += `
                            <div class="image-item mb-3">
                                <img src="${imageUrl}" alt="${file.fileName}" class="img-fluid rounded" style="max-width: 300px; height: auto;">
                                <div class="text-muted small mt-1">${file.fileName}</div>
                            </div>
                        `;
                    });
                    contentHtml += '</div>';
                } else if (section.type === 'video' && section.files && section.files.length > 0) {
                    // Display uploaded videos
                    contentHtml = '<div class="video-gallery">';
                    section.files.forEach(file => {
                        const videoUrl = `./admin/uploads/${file.filePath}`;
                        contentHtml += `
                            <div class="video-item mb-3">
                                <video controls class="img-fluid rounded" style="max-height: 300px; width: 100%;">
                                    <source src="${videoUrl}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="text-muted small mt-1">${file.fileName}</div>
                            </div>
                        `;
                    });
                    contentHtml += '</div>';
                } else if (section.type === 'audio' && section.files && section.files.length > 0) {
                    // Display uploaded audio
                    contentHtml = '<div class="audio-gallery">';
                    section.files.forEach(file => {
                        const audioUrl = `./admin/uploads/${file.filePath}`;
                        contentHtml += `
                            <div class="audio-item mb-3">
                                <audio controls class="w-100">
                                    <source src="${audioUrl}" type="audio/mpeg">
                                    Your browser does not support the audio tag.
                                </audio>
                                <div class="text-muted small mt-1">${file.fileName}</div>
                            </div>
                        `;
                    });
                    contentHtml += '</div>';
                } else if (section.type === 'file' && section.files && section.files.length > 0) {
                    // Display uploaded files
                    contentHtml = '<div class="file-gallery">';
                    section.files.forEach(file => {
                        const fileUrl = `./admin/uploads/${file.filePath}`;
                        const fileSize = file.fileSize ? `(${(file.fileSize / 1024 / 1024).toFixed(2)} MB)` : '';
                        contentHtml += `
                            <div class="file-item mb-2">
                                <a href="${fileUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-download me-2"></i>${file.fileName}
                                </a>
                                <span class="text-muted small ms-2">${fileSize}</span>
                            </div>
                        `;
                    });
                    contentHtml += '</div>';
                } else {
                    // Display text content
                    contentHtml = `<div class="text-content">${section.content || ''}</div>`;
                }
                
                return `
                    <div class="section">
                        <h2>${section.title || 'Untitled Section'}</h2>
                        ${contentHtml}
                    </div>
                `;
            }).join('');
            
            // Generate the preview HTML
            const previewHtml = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${courseName} - Course Preview</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
                    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
                    <style>
                        body { 
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            padding: 1rem;
                            font-size: 0.9rem;
                            min-height: 100vh;
                        }
                        .container {
                            background: rgba(255, 255, 255, 0.95);
                            border-radius: 15px;
                            padding: 2rem;
                            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                            backdrop-filter: blur(10px);
                            border: 1px solid rgba(255, 255, 255, 0.2);
                        }
                        h1, h2, h3 { 
                            color: #667eea; 
                            font-weight: 600;
                        }
                        h1 { 
                            font-size: 2rem; 
                            margin-bottom: 1.5rem; 
                            text-align: center;
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            -webkit-background-clip: text;
                            -webkit-text-fill-color: transparent;
                            background-clip: text;
                        }
                        h2 { 
                            font-size: 1.3rem; 
                            margin-bottom: 1rem; 
                            color: #667eea;
                            border-bottom: 2px solid #e9ecef;
                            padding-bottom: 0.5rem;
                        }
                        .section { 
                            margin-bottom: 2rem; 
                            padding: 1.5rem; 
                            border: 1px solid #e9ecef; 
                            border-radius: 12px; 
                            background: white;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
                            transition: transform 0.2s ease, box-shadow 0.2s ease;
                        }
                        .section:hover {
                            transform: translateY(-2px);
                            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
                        }
                        .image-gallery .image-item {
                            text-align: center;
                            margin-bottom: 1rem;
                        }
                        .image-gallery .image-item img {
                            max-width: 250px !important;
                            border-radius: 8px;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                        }
                        .video-gallery .video-item {
                            text-align: center;
                            margin-bottom: 1rem;
                        }
                        .video-gallery .video-item video {
                            max-width: 300px;
                            border-radius: 8px;
                            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                        }
                        .audio-gallery .audio-item {
                            margin-bottom: 1rem;
                        }
                        .file-gallery .file-item {
                            display: inline-block;
                            margin-right: 1rem;
                            margin-bottom: 0.75rem;
                        }
                        .file-gallery .btn {
                            font-size: 0.8rem;
                            padding: 0.4rem 0.8rem;
                            border-radius: 6px;
                            transition: all 0.2s ease;
                        }
                        .file-gallery .btn:hover {
                            transform: translateY(-1px);
                            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                        }
                        .text-content {
                            line-height: 1.6;
                            color: #333;
                        }
                        .no-content {
                            text-align: center;
                            padding: 3rem;
                            color: #6c757d;
                        }
                        .no-content i {
                            font-size: 3rem;
                            margin-bottom: 1rem;
                            opacity: 0.5;
                        }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h1><i class="fas fa-graduation-cap me-2"></i>${courseName}</h1>
                        ${visibleNotes.length > 0 ? sectionsHtml : '<div class="no-content"><i class="fas fa-book-open"></i><h3>No course materials available</h3><p>Course content will be added soon.</p></div>'}
                    </div>
</body>
                </html>
            `;
            
            // Write the content to the preview window
            previewWindow.document.write(previewHtml);
            previewWindow.document.close();
            
            // Focus the preview window
            previewWindow.focus();
        }
    </script>
</body>
</html>