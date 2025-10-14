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
        .discussion-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }

        .discussion-card {
            text-decoration: none;
            color: inherit;
            border-radius: 14px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .discussion-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            text-decoration: none;
        }

        .discussion-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(59, 130, 246, 0.12);
            color: #3b82f6;
        }

        .discussion-title {
            font-weight: 600;
            margin: 0.25rem 0 0.1rem 0;
        }

        .discussion-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-secondary);
            font-size: 0.85rem;
        }

        .open-btn {
            margin-top: 0.25rem;
            align-self: flex-start;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            color: white;
            padding: 0.4rem 0.75rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body data-theme="light">
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
                            <a class="discussion-card glass-panel" href="course-discussion.php?id=<?php echo $course['courseId']; ?>">
                                <div class="discussion-icon"><i class="fas fa-comments"></i></div>
                                <div class="discussion-title"><?php echo htmlspecialchars($course['courseName']); ?></div>
                                <div class="discussion-meta">
                                    <span>Open discussion</span>
                                    <i class="fas fa-arrow-right"></i>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No accessible discussions</h5>
                        <p class="text-muted">Enroll in a course or subscribe to access its discussion board.</p>
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
});
</script>
</body>
</html>


