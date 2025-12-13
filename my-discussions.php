<?php
include("./config/session.php");
include('./dbconnection/connection.php');
include('./php/validateSession.php');

$userId = $_SESSION['userId'] ?? 0;
if (!$userId) {
    header('Location: login.php?next=' . urlencode('/mkscholars/my-discussions.php'));
    exit;
}

// Fetch courses user can access (enrolled or active subscription)
$courses = [];
// Enrollments
$enrollQuery = "SELECT c.courseId, c.courseName FROM Courses c 
                INNER JOIN CourseEnrollments e ON e.courseId = c.courseId 
                WHERE e.userId = ? AND e.enrollmentStatus = 1";
$stmt = $conn->prepare($enrollQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $courses[$row['courseId']] = $row; }
$stmt->close();

// Active subscriptions
$subQuery = "SELECT c.courseId, c.courseName FROM Courses c 
             INNER JOIN subscription s ON s.Item = c.courseId 
             WHERE s.UserId = ? AND s.SubscriptionStatus = 1 AND (s.expirationDate IS NULL OR s.expirationDate > NOW())";
$stmt = $conn->prepare($subQuery);
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) { $courses[$row['courseId']] = $row; }
$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Discussions</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(0, 0, 0, 0.06);
        }

        [data-theme="dark"] {
            --bg-primary: #121212;
            --bg-secondary: #1e1e1e;
            --text-primary: #e5e7eb;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(30, 30, 30, 0.8);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
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
        .discussion-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .discussion-card {
            text-decoration: none;
            color: inherit;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            transition: all 0.3s ease;
            background: var(--glass-bg);
            border: 2px solid var(--glass-border);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .discussion-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
            border-color: rgba(59, 130, 246, 0.3);
            text-decoration: none;
        }

        .discussion-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(29, 78, 216, 0.15));
            color: #3b82f6;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .discussion-title {
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .discussion-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .open-btn {
            width: 100%;
            border: none;
            color: white;
            padding: 0.875rem 1.25rem;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .open-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            color: white;
            text-decoration: none;
        }

        .open-btn.discussion-btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        }

        .open-btn.discussion-btn:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }

        .open-btn.submission-btn {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .open-btn.submission-btn:hover {
            background: linear-gradient(135deg, #059669, #047857);
        }

        .open-btn i {
            font-size: 1.1rem;
        }
    </style>
</head>
<body data-theme="light">
    <!-- Theme Toggle Button -->
    <button class="btn btn-secondary theme-toggle glass-panel">
        <i class="fas fa-moon"></i>
    </button>

<div class="container-fluid">
    <div class="row">
        <?php $_GET['page'] = 'my-discussions'; include('./partials/universalNavigation.php'); ?>
        <main class="col-md-9 col-lg-10 main-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                    <i class="fas fa-bars"></i>
                </button>
                <h3 class="mb-0">Discussion Board</h3>
            </div>

            <div class="glass-panel p-4">
                <?php if (!empty($courses)): ?>
                    <div class="discussion-cards-grid">
                        <?php foreach ($courses as $course): ?>
                            <div class="discussion-card">
                                <div class="d-flex align-items-start mb-2">
                                    <div class="discussion-icon">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="discussion-title"><?php echo htmlspecialchars($course['courseName']); ?></div>
                                    </div>
                                </div>
                                <div class="action-buttons">
                                    <a href="course-discussion.php?id=<?php echo $course['courseId']; ?>" class="open-btn discussion-btn">
                                        <i class="fas fa-comments"></i>
                                        <span>Discussion Board</span>
                                    </a>
                                    <a href="submission.php?course=<?php echo $course['courseId']; ?>" class="open-btn submission-btn">
                                        <i class="fas fa-file-upload"></i>
                                        <span>Assignment Submission</span>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No accessible courses</h5>
                        <p class="text-muted">Enroll in a course or subscribe to access its discussion board and assignments.</p>
                        <a href="e-learning.php" class="btn btn-primary">Browse Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggleBtn = document.querySelector('.sidebar-toggle');
    var sidebar = document.querySelector('.sidebar');
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
    }
    // Theme toggle (match dashboard)
    var themeToggle = document.querySelector('.theme-toggle');
    var body = document.body;
    var saved = localStorage.getItem('theme') || 'light';
    body.setAttribute('data-theme', saved);
    updateThemeIcon();
    if (themeToggle) {
        themeToggle.addEventListener('click', function(){
            var current = body.getAttribute('data-theme');
            var next = current === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon();
        });
    }
    function updateThemeIcon(){
        if (!themeToggle) return;
        var isDark = body.getAttribute('data-theme') === 'dark';
        themeToggle.innerHTML = isDark ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
    }
});
</script>
</body>
</html>


