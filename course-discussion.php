<?php
// Include session configuration for persistent sessions
include("./config/session.php");
include('./dbconnection/connection.php');
include('./php/validateSession.php');

// Get course ID from URL
$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$courseId) {
    header("Location: e-learning.php");
    exit;
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['userId']);
$userId = $isLoggedIn ? $_SESSION['userId'] : null;

if (!$isLoggedIn) {
    header("Location: login.php?next=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Get course details
$courseQuery = "SELECT * FROM Courses WHERE courseId = ?";
$courseStmt = $conn->prepare($courseQuery);
$courseStmt->bind_param("i", $courseId);
$courseStmt->execute();
$courseResult = $courseStmt->get_result();

if ($courseResult->num_rows === 0) {
    header("Location: e-learning.php");
    exit;
}

$course = $courseResult->fetch_assoc();
$courseStmt->close();

// Check if user has access to this course (enrolled or paid)
$hasAccess = false;

// Check enrollment
$enrollmentQuery = "SELECT COUNT(*) as count FROM CourseEnrollments WHERE courseId = ? AND userId = ? AND enrollmentStatus = 1";
$enrollmentStmt = $conn->prepare($enrollmentQuery);
$enrollmentStmt->bind_param("ii", $courseId, $userId);
$enrollmentStmt->execute();
$enrollmentResult = $enrollmentStmt->get_result();
$enrollmentCount = $enrollmentResult->fetch_assoc()['count'];
$enrollmentStmt->close();

if ($enrollmentCount > 0) {
    $hasAccess = true;
}

// Check payment
if (!$hasAccess) {
    $paymentQuery = "SELECT COUNT(*) as count FROM subscription WHERE Item = ? AND UserId = ? AND SubscriptionStatus = 1 AND (expirationDate IS NULL OR expirationDate > NOW())";
    $paymentStmt = $conn->prepare($paymentQuery);
    $paymentStmt->bind_param("ii", $courseId, $userId);
    $paymentStmt->execute();
    $paymentResult = $paymentStmt->get_result();
    $paymentCount = $paymentResult->fetch_assoc()['count'];
    $paymentStmt->close();
    
    if ($paymentCount > 0) {
        $hasAccess = true;
    }
}

if (!$hasAccess) {
    header("Location: e-learning.php?error=access_denied");
    exit;
}

// Debug: Check if DiscussionBoard table exists
$tableCheck = "SHOW TABLES LIKE 'DiscussionBoard'";
$tableResult = $conn->query($tableCheck);
if ($tableResult->num_rows === 0) {
    error_log("ERROR: DiscussionBoard table does not exist! Creating it...");
    
    // Create DiscussionBoard table (without foreign key constraints to avoid issues)
    $createTable = "CREATE TABLE DiscussionBoard (
        discussionId INT AUTO_INCREMENT PRIMARY KEY,
        courseId INT NOT NULL,
        userId INT NOT NULL,
        messageTitle VARCHAR(255) NOT NULL,
        messageBody LONGTEXT NOT NULL,
        messageDate DATE NOT NULL,
        messageTime TIME NOT NULL,
        messageLikes INT DEFAULT 0,
        messageReport INT DEFAULT 0,
        isPinned TINYINT(1) DEFAULT 0,
        parentDiscussionId INT DEFAULT NULL,
        isApproved TINYINT(1) DEFAULT 1,
        createdDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_course_discussion (courseId),
        INDEX idx_user_discussion (userId),
        INDEX idx_parent_discussion (parentDiscussionId)
    )";
    
    if ($conn->query($createTable)) {
        error_log("DiscussionBoard table created successfully");
    } else {
        error_log("Error creating DiscussionBoard table: " . mysqli_error($conn));
    }
}

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['create_discussion'])) {
        $messageTitle = mysqli_real_escape_string($conn, $_POST['messageTitle']);
        $messageBody = mysqli_real_escape_string($conn, $_POST['messageBody']);
        $parentDiscussionId = isset($_POST['parentDiscussionId']) ? (int)$_POST['parentDiscussionId'] : null;
        
        
        if (!$userId) {
            $message = 'Error: User ID not found in session. Please login again.';
            $messageType = 'danger';
        } else {
            $insertQuery = "INSERT INTO DiscussionBoard (courseId, userId, messageTitle, messageBody, messageDate, messageTime, isPinned, parentDiscussionId, isApproved) VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), 0, ?, 1)";
            $insertStmt = $conn->prepare($insertQuery);
            
            if (!$insertStmt) {
                $message = 'Error preparing statement: ' . mysqli_error($conn);
                $messageType = 'danger';
            } else {
                $insertStmt->bind_param("iisi", $courseId, $userId, $messageTitle, $messageBody, $parentDiscussionId);
                
                if ($insertStmt->execute()) {
                    $message = 'Discussion created successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error creating discussion: ' . mysqli_error($conn);
                    $messageType = 'danger';
                }
                $insertStmt->close();
            }
        }
    }
}

// Get discussions for this course
$discussionsQuery = "SELECT d.*, 
                            CASE 
                                WHEN d.userId IN (SELECT userId FROM users) THEN 'admin'
                                WHEN d.userId IN (SELECT NoUserId FROM normUsers) THEN 'student'
                                ELSE 'unknown'
                            END as userType,
                            CASE 
                                WHEN d.userId IN (SELECT userId FROM users) THEN (SELECT username FROM users WHERE userId = d.userId)
                                WHEN d.userId IN (SELECT NoUserId FROM normUsers) THEN (SELECT NoUsername FROM normUsers WHERE NoUserId = d.userId)
                                ELSE 'Unknown User'
                            END as username
                     FROM DiscussionBoard d 
                     WHERE d.courseId = ? AND d.isApproved = 1
                     ORDER BY d.isPinned DESC, d.messageDate DESC, d.messageTime DESC";
$discussionsStmt = $conn->prepare($discussionsQuery);
$discussionsStmt->bind_param("i", $courseId);
$discussionsStmt->execute();
$discussionsResult = $discussionsStmt->get_result();
$discussions = $discussionsResult->fetch_all(MYSQLI_ASSOC);
$discussionsStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discussion Board - <?php echo htmlspecialchars($course['courseName']); ?></title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.3);
        }

        [data-theme="dark"] {
            --bg-primary: #1a1a1a;
            --bg-secondary: #2d2d2d;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(45, 45, 45, 0.9);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }

        /* Universal Navigation Styling */
        .sidebar {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(15px);
            border-right: 1px solid var(--glass-border);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            width: 250px;
            transition: all 0.3s ease;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s;
        }

        .sidebar-nav {
            background: transparent !important;
            padding: 1rem 0;
        }

        .sidebar-item {
            margin: 0.25rem 0;
        }

        .sidebar-link {
            color: var(--text-primary) !important;
            padding: 0.75rem 1rem !important;
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-link:hover {
            background: rgba(59, 130, 246, 0.1) !important;
            color: #3b82f6 !important;
            transform: translateX(5px);
            text-decoration: none;
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
            color: white !important;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
        }

        .nav-small-cap {
            color: var(--text-secondary) !important;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            padding: 1rem 1rem 0.5rem 1rem;
            margin-top: 1rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .hide-menu {
            font-weight: 500;
            font-size: 0.9rem;
        }

        .sidebar-link i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
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
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.15);
            background: var(--bg-secondary);
        }

        .discussion-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-left: 4px solid transparent;
        }

        .discussion-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            border-left-color: #3b82f6;
        }

        .discussion-card.pinned {
            border-left: 4px solid #ffc107;
            background: linear-gradient(135deg, var(--glass-bg) 0%, rgba(255, 193, 7, 0.1) 100%);
        }

        .discussion-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-avatar.admin {
            background: linear-gradient(135deg, #dc2626, #991b1b);
        }

        .user-details h6 {
            margin: 0;
            color: var(--text-primary);
            font-weight: 600;
        }

        .user-details small {
            color: var(--text-secondary);
        }

        .discussion-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
        }

        .discussion-body {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .discussion-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid var(--glass-border);
        }

        .discussion-stats {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .pinned-badge {
            background: #ffc107;
            color: #000;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .btn-floating {
            position: fixed;
            bottom: 20px;
            right: 80px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .real-time-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 1000;
            display: none;
        }

        .real-time-indicator.active {
            display: block;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }

            .btn-floating {
                right: 20px;
                bottom: 80px;
            }
        }
    </style>
</head>

<body data-theme="light">
    <!-- Real-time indicator -->
    <div class="real-time-indicator" id="realTimeIndicator">
        <i class="fas fa-circle me-1"></i>
        Live Updates
    </div>

    <!-- Theme Toggle Button -->
    <button class="btn btn-secondary theme-toggle">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Floating action button -->
    <button class="btn btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#createDiscussionModal">
        <i class="fas fa-plus"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php 
            // Set current page for navigation highlighting
            $_GET['page'] = 'e-learning';
            include("./partials/universalNavigation.php"); 
            ?>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary me-3 d-md-none" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div>
                            <h3 class="mb-0">Discussion Board</h3>
                            <p class="text-muted mb-0"><?php echo htmlspecialchars($course['courseName']); ?></p>
                        </div>
                    </div>
                    <a href="e-learning.php" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Courses
                    </a>
                </div>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>


                <!-- Discussions List -->
                <div class="discussions-container">
                    <?php if (!empty($discussions)): ?>
                        <?php foreach ($discussions as $discussion): ?>
                            <div class="discussion-card <?php echo $discussion['isPinned'] ? 'pinned' : ''; ?>" data-discussion-id="<?php echo $discussion['discussionId']; ?>">
                                <div class="discussion-header">
                                    <div class="user-info">
                                        <div class="user-avatar <?php echo $discussion['userType'] === 'admin' ? 'admin' : ''; ?>">
                                            <?php echo strtoupper(substr($discussion['username'], 0, 1)); ?>
                                        </div>
                                        <div class="user-details">
                                            <h6><?php echo htmlspecialchars($discussion['username']); ?></h6>
                                            <small>
                                                <?php echo $discussion['userType'] === 'admin' ? 'Administrator' : 'Student'; ?>
                                                • <?php echo date('M j, Y g:i A', strtotime($discussion['messageDate'] . ' ' . $discussion['messageTime'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <?php if ($discussion['isPinned']): ?>
                                        <span class="pinned-badge">
                                            <i class="fas fa-thumbtack me-1"></i>Pinned
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="discussion-title">
                                    <?php echo htmlspecialchars($discussion['messageTitle']); ?>
                                </div>

                                <div class="discussion-body">
                                    <?php echo nl2br(htmlspecialchars($discussion['messageBody'])); ?>
                                </div>

                                <div class="discussion-meta">
                                    <div class="discussion-stats">
                                        <div class="stat-item">
                                            <i class="fas fa-thumbs-up"></i>
                                            <span><?php echo $discussion['messageLikes']; ?></span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-comment"></i>
                                            <span>0</span>
                                        </div>
                                        <div class="stat-item">
                                            <i class="fas fa-flag"></i>
                                            <span><?php echo $discussion['messageReport']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No discussions yet</h5>
                            <p class="text-muted">Start the conversation by creating the first discussion post.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Create Discussion Modal -->
    <div class="modal fade" id="createDiscussionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Discussion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="messageTitle" class="form-label">Discussion Title</label>
                            <input type="text" class="form-control" id="messageTitle" name="messageTitle" required>
                        </div>
                        <div class="mb-3">
                            <label for="messageBody" class="form-label">Message</label>
                            <textarea class="form-control" id="messageBody" name="messageBody" rows="6" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="create_discussion" class="btn btn-primary">Create Discussion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
                
                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                            sidebar.classList.remove('show');
                        }
                    }
                });
            }
        });

        // Real-time updates
        let lastUpdateTime = new Date().toISOString();

        function checkForUpdates() {
            fetch('discussion-updates.php?courseId=<?php echo $courseId; ?>&lastUpdate=' + lastUpdateTime)
                .then(response => response.json())
                .then(data => {
                    if (data.hasUpdates) {
                        location.reload();
                    }
                    lastUpdateTime = data.currentTime;
                })
                .catch(error => console.error('Error checking for updates:', error));
        }

        function showRealTimeIndicator() {
            const indicator = document.getElementById('realTimeIndicator');
            indicator.classList.add('active');
            setTimeout(() => {
                indicator.classList.remove('active');
            }, 3000);
        }

        // Check for updates every 5 seconds
        setInterval(checkForUpdates, 5000);

        // Show real-time indicator on page load
        showRealTimeIndicator();

        // Auto-refresh every 30 seconds
        setInterval(() => {
            location.reload();
        }, 30000);
    </script>
</body>
</html>
