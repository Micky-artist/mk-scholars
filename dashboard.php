<?php
// Include session configuration for persistent sessions
include("./config/session.php");
include('./dbconnection/connection.php');
include('./php/validateSession.php');
// include('./php/sendMessage.php');

// Fetch latest 5 courses for dashboard
$latestCourses = [];
if ($conn) {
    $latestCoursesQuery = "SELECT c.*, 
                           (SELECT COUNT(*) FROM CourseEnrollments ce WHERE ce.courseId = c.courseId AND ce.enrollmentStatus = 1) as enrollmentCount
                     FROM Courses c 
                     WHERE c.courseDisplayStatus = 1 
                     ORDER BY c.courseCreatedDate DESC 
                     LIMIT 5";
    
    $latestCoursesResult = mysqli_query($conn, $latestCoursesQuery);
    
    if ($latestCoursesResult && mysqli_num_rows($latestCoursesResult) > 0) {
        while ($course = mysqli_fetch_assoc($latestCoursesResult)) {
            // Get pricing for each course
            $pricingQuery = "SELECT cp.amount, cp.currency, cp.pricingDescription, cp.isFree, curr.currencySymbol
                             FROM CoursePricing cp 
                             LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                             WHERE cp.courseId = ? 
                             ORDER BY cp.amount ASC 
                             LIMIT 1";
            $pricingStmt = $conn->prepare($pricingQuery);
            $pricingStmt->bind_param("i", $course['courseId']);
            $pricingStmt->execute();
            $pricingResult = $pricingStmt->get_result();
            
            $course['pricing'] = null;
            if ($pricingResult && mysqli_num_rows($pricingResult) > 0) {
                $course['pricing'] = mysqli_fetch_assoc($pricingResult);
            }
            $pricingStmt->close();
            
            $latestCourses[] = $course;
        }
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
    <title>MK Dashboard</title>
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
            box-shadow: 5px 5px 10px #d1d5db, -5px -5px 10px #ffffff;
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

        /* Latest Courses Styles */
        .latest-courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .course-card-small {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 0.75rem;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }

        .course-card-small:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
        }

        .course-title-small {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.4rem;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .course-meta-small {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .course-price-small {
            font-size: 0.8rem;
            font-weight: 600;
            color: #3b82f6;
        }

        .course-students-small {
            font-size: 0.7rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .course-date-small {
            font-size: 0.7rem;
            color: var(--text-secondary);
        }

        .view-more-btn {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .view-more-btn:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-1px);
            color: white;
            text-decoration: none;
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

            .latest-courses-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
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
            <?php 
            // Set current page for navigation highlighting
            $_GET['page'] = 'dashboard';
            include("./partials/universalNavigation.php"); 
            ?>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h3 class="mb-0">Dashboard</h3>
                    <div class="glass-panel px-3 py-2 notification-btn" style="cursor: pointer;">
                        <i class="fas fa-bell text-muted"></i>
                    </div>
                </div>



                <!-- Latest Courses Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="glass-panel p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Latest Courses</h5>
                                <a href="./e-learning" class="view-more-btn">
                                    <i class="fas fa-arrow-right"></i>
                                    View More
                                </a>
                            </div>
                            
                            <?php if (!empty($latestCourses)): ?>
                                <div class="latest-courses-grid">
                                    <?php foreach ($latestCourses as $course): ?>
                                        <a href="./e-learning" class="course-card-small">
                                            <div class="course-title-small">
                                                <?php echo htmlspecialchars($course['courseName']); ?>
                                            </div>
                                            <div class="course-meta-small">
                                                <span class="course-price-small">
                                                    <?php 
                                                    if ($course['pricing']) {
                                                        echo formatPrice($course['pricing']['amount'], $course['pricing']['currencySymbol'], $course['pricing']['currency']);
                                                    } else {
                                                        echo 'Free';
                                                    }
                                                    ?>
                                                </span>
                                                <span class="course-students-small">
                                                    <i class="fas fa-users"></i>
                                                    <?php echo $course['enrollmentCount']; ?>
                                                </span>
                                            </div>
                                            <div class="course-date-small">
                                                <i class="fas fa-calendar me-1"></i>
                                                <?php echo date('M j, Y', strtotime($course['courseCreatedDate'])); ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-book-open fa-2x mb-2"></i>
                                    <p class="mb-0">No courses available yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 g-4">
                    <div class="col-lg-4">
                        <div class="glass-panel p-4 h-100">
                            <h5 class="mb-4">Application Requests</h5>
                            <?php
                            $UserId = $_SESSION['userId'];
                            $Status =  0;
                            $Percentage = '';
                            $SelectApplicationRequest = mysqli_query($conn, "SELECT a.*, s.* FROM ApplicationRequests a JOIN scholarships s ON s.scholarshipId = a.ApplicationId WHERE UserId=$UserId ORDER BY RequestId DESC");
                            if ($SelectApplicationRequest->num_rows > 0) {
                                while ($ApplicationRequests = mysqli_fetch_assoc($SelectApplicationRequest)) {

                                    switch ($ApplicationRequests['Status']) {
                                        case 0:
                                            $Status =  'Submited';
                                            $Percentage = 10;
                                            break;
                                        case 1:
                                            $Status = 'seen';
                                            $Percentage = 40;
                                            break;
                                        case 2:
                                        case 3:
                                            $Status = 'In-Progress';
                                            $Percentage = 70;
                                            break;
                                        case 4:
                                            $Status = 'Completed';
                                            $Percentage = 100;
                                            break;
                                        default:
                                            $Status = "There was some Issue";
                                            $Percentage = 10;
                                            break;
                                    }

                            ?>

                                    <div class="app-card p-3 mb-3 rounded-3">
                                        <div class="d-flex align-items-center">
                                            <div class="neumorphic-icon me-3">
                                                <i class="fas fa-list text-info"></i>
                                            </div>
                                            <div class="w-100">
                                                <h6 class="mb-0"><?php echo $ApplicationRequests['scholarshipTitle']; ?></h6>
                                                <small class="text-muted">Application Date: <?php echo $ApplicationRequests['RequestDate']; ?></small>
                                                <div class="progress mt-1 mb-1">
                                                    <div class="progress-bar bg-success" style="width: <?php echo $Percentage; ?>%; background:green !important;"></div>
                                                </div>

                                                <div style="font-size: 14px !important;">
                                                    Status: <?php echo $Status; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php
                                }
                            }
                            ?>

                            <!-- <div class="app-card p-3 mb-3 rounded-3">
                                <div class="d-flex align-items-center">
                                    <div class="neumorphic-icon me-3">
                                        <i class="fas fa-cloud-upload-alt text-danger"></i>
                                    </div>
                                    <div class="w-100">
                                        <h6 class="mb-0">Cloud Storage</h6>
                                        <small class="text-muted">Syncing files</small>
                                        <div class="progress-glass mt-2">
                                            <div class="progress-bar bg-info" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <!-- <div class="col-md-6">
                        <div class="glass-panel p-4">
                            <h5><i class="fas fa-chart-line me-2"></i>Performance</h5>
                            <canvas id="performanceChart" style="height: 200px;"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-panel p-4">
                            <h5><i class="fas fa-tasks me-2"></i>Active Projects</h5>
                            <div class="progress-glass mt-3">
                                <div class="progress-bar bg-warning" style="width: 30%"></div>
                            </div>
                            <small class="text-muted">Project Alpha - 30% complete</small>
                        </div>
                    </div> -->

                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
</body>

</html>