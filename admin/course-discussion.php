<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

// Get course ID from URL
$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$courseId) {
    header("Location: course-management.php");
    exit;
}

// Get course details
$courseQuery = "SELECT * FROM Courses WHERE courseId = ?";
$courseStmt = $conn->prepare($courseQuery);
$courseStmt->bind_param("i", $courseId);
$courseStmt->execute();
$courseResult = $courseStmt->get_result();

if ($courseResult->num_rows === 0) {
    header("Location: course-management.php");
    exit;
}

$course = $courseResult->fetch_assoc();
$courseStmt->close();

// Debug: Check if DiscussionBoard table exists and has correct structure
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
} else {
    error_log("DiscussionBoard table exists");
    
    // Check table structure
    $structureCheck = "DESCRIBE DiscussionBoard";
    $structureResult = $conn->query($structureCheck);
    if ($structureResult) {
        $columns = $structureResult->fetch_all(MYSQLI_ASSOC);
        error_log("DiscussionBoard columns: " . print_r($columns, true));
    }
}

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['create_discussion'])) {
        $messageTitle = mysqli_real_escape_string($conn, $_POST['messageTitle']);
        $messageBody = mysqli_real_escape_string($conn, $_POST['messageBody']);
        $isPinned = isset($_POST['isPinned']) ? 1 : 0;
        $parentDiscussionId = isset($_POST['parentDiscussionId']) ? (int)$_POST['parentDiscussionId'] : null;
        
        
        // Use the correct session variable (adminId with lowercase 'a')
        $adminId = isset($_SESSION['adminId']) ? $_SESSION['adminId'] : 0;
        
        if ($adminId == 0) {
            $message = 'Error: Admin ID not found in session. Please login again.';
            $messageType = 'danger';
        } else {
            $insertQuery = "INSERT INTO DiscussionBoard (courseId, userId, messageTitle, messageBody, messageDate, messageTime, isPinned, parentDiscussionId, isApproved) VALUES (?, ?, ?, ?, CURDATE(), CURTIME(), ?, ?, 1)";
            $insertStmt = $conn->prepare($insertQuery);
            
            if (!$insertStmt) {
                $message = 'Error preparing statement: ' . mysqli_error($conn);
                $messageType = 'danger';
            } else {
                $insertStmt->bind_param("iisii", $courseId, $adminId, $messageTitle, $messageBody, $isPinned, $parentDiscussionId);
                
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
    
    if (isset($_POST['delete_discussion'])) {
        $discussionId = (int)$_POST['discussionId'];
        $deleteQuery = "DELETE FROM DiscussionBoard WHERE discussionId = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $discussionId);
        
        if ($deleteStmt->execute()) {
            $message = 'Discussion deleted successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error deleting discussion: ' . mysqli_error($conn);
            $messageType = 'danger';
        }
        $deleteStmt->close();
    }
    
    if (isset($_POST['toggle_pin'])) {
        $discussionId = (int)$_POST['discussionId'];
        $isPinned = (int)$_POST['isPinned'];
        $updateQuery = "UPDATE DiscussionBoard SET isPinned = ? WHERE discussionId = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $isPinned, $discussionId);
        $updateStmt->execute();
        $updateStmt->close();
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
                     WHERE d.courseId = ? 
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Admin Sidebar Styling */
        .left-sidebar {
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
            .left-sidebar {
                transform: translateX(-100%);
                width: 280px;
            }
            
            .left-sidebar.show {
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
            border-radius: 15px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
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
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .user-avatar.admin {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
        }

        .user-details h6 {
            margin: 0;
            color: var(--text-primary);
            font-weight: 600;
        }

        .user-details small {
            color: var(--text-secondary);
        }

        .discussion-actions {
            display: flex;
            gap: 0.5rem;
            margin-left: auto;
        }

        .discussion-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
            line-height: 1.3;
        }

        .discussion-body {
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 1.5rem;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem;
            border-radius: 8px;
            border-left: 3px solid #e5e7eb;
        }

        .discussion-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
            margin-top: 0.5rem;
        }

        .discussion-stats {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            transition: all 0.2s ease;
        }

        .stat-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
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
            bottom: 30px;
            right: 30px;
            width: 65px;
            height: 65px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .btn-floating:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            color: white;
        }

        .real-time-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 0.75rem 1.25rem;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 1000;
            display: none;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .real-time-indicator.active {
            display: block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .typing-indicator {
            color: var(--text-secondary);
            font-style: italic;
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>

<body data-theme="light">
    <!-- Real-time indicator -->
    <div class="real-time-indicator" id="realTimeIndicator">
        <i class="fas fa-circle me-1"></i>
        Live Updates
    </div>

    <!-- Floating action button -->
    <button class="btn btn-primary btn-floating" data-bs-toggle="modal" data-bs-target="#createDiscussionModal">
        <i class="fas fa-plus"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include("./partials/navbar.php"); ?>

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
                    <a href="course-management.php" class="btn btn-outline-secondary">
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
                                    <div class="discussion-actions">
                                        <?php if ($discussion['isPinned']): ?>
                                            <span class="pinned-badge">
                                                <i class="fas fa-thumbtack me-1"></i>Pinned
                                            </span>
                                        <?php endif; ?>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="discussionId" value="<?php echo $discussion['discussionId']; ?>">
                                                        <input type="hidden" name="isPinned" value="<?php echo $discussion['isPinned'] ? '0' : '1'; ?>">
                                                        <button type="submit" name="toggle_pin" class="dropdown-item">
                                                            <i class="fas fa-thumbtack me-2"></i>
                                                            <?php echo $discussion['isPinned'] ? 'Unpin' : 'Pin'; ?>
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this discussion?')">
                                                        <input type="hidden" name="discussionId" value="<?php echo $discussion['discussionId']; ?>">
                                                        <button type="submit" name="delete_discussion" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i>Delete
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
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
                            <div class="glass-panel p-5">
                                <i class="fas fa-comments fa-4x text-muted mb-4" style="color: #9ca3af;"></i>
                                <h4 class="text-muted mb-3">No discussions yet</h4>
                                <p class="text-muted mb-4">Start the conversation by creating the first discussion post.</p>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDiscussionModal">
                                    <i class="fas fa-plus me-2"></i>Create First Discussion
                                </button>
                            </div>
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
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isPinned" name="isPinned">
                            <label class="form-check-label" for="isPinned">
                                Pin this discussion to the top
                            </label>
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
        // Mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.left-sidebar');
            
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
        let isTyping = false;
        let typingTimeout;

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
