<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_course'])) {
        $courseName = mysqli_real_escape_string($conn, $_POST['courseName']);
        $courseShortDescription = mysqli_real_escape_string($conn, $_POST['courseShortDescription']);
        $courseLongDescription = mysqli_real_escape_string($conn, $_POST['courseLongDescription']);
        $courseStartDate = $_POST['courseStartDate'];
        $courseRegEndDate = $_POST['courseRegEndDate'];
        $courseEndDate = $_POST['courseEndDate'];
        $courseSeats = (int)$_POST['courseSeats'];
        $courseDisplayStatus = (int)$_POST['courseDisplayStatus'];
        $courseCreatedBy = $_SESSION['adminId'];
        
        // Create course content JSON structure
        $courseContent = json_encode([
            'sections' => [],
            'theme' => [
                'primaryColor' => '#007bff',
                'secondaryColor' => '#6c757d',
                'fontFamily' => 'Arial, sans-serif',
                'headerFontSize' => '2rem',
                'bodyFontSize' => '1rem'
            ],
            'settings' => [
                'allowComments' => true,
                'showProgress' => true,
                'enableDownloads' => true
            ]
        ]);
        
        $insertQuery = "INSERT INTO Courses (courseName, courseShortDescription, courseLongDescription, courseStartDate, courseRegEndDate, courseEndDate, courseSeats, courseDisplayStatus, courseCreatedBy, courseContent) VALUES ('$courseName', '$courseShortDescription', '$courseLongDescription', '$courseStartDate', '$courseRegEndDate', '$courseEndDate', $courseSeats, $courseDisplayStatus, $courseCreatedBy, '$courseContent')";
        
        if (mysqli_query($conn, $insertQuery)) {
            $courseId = mysqli_insert_id($conn);
            $message = 'Course created successfully! Course ID: ' . $courseId;
            $messageType = 'success';
        } else {
            $message = 'Error creating course: ' . mysqli_error($conn);
            $messageType = 'error';
        }
    }
}

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12; // Courses per page
$offset = ($page - 1) * $limit;

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM Courses c";
$countResult = mysqli_query($conn, $countQuery);
$totalCourses = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalCourses / $limit);

// Get courses with pagination and optimized query
$coursesQuery = "SELECT c.courseId, c.courseName, c.courseShortDescription, c.courseStartDate, 
                        c.courseEndDate, c.courseSeats, c.courseDisplayStatus, c.courseCreatedDate,
                        c.coursePhoto, c.courseRegEndDate,
                        cp.amount, cp.currency, curr.currencySymbol 
                 FROM Courses c 
                 LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
                 LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                 ORDER BY c.courseCreatedDate DESC 
                 LIMIT $limit OFFSET $offset";
$coursesResult = mysqli_query($conn, $coursesQuery);
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include("./partials/head.php"); ?>

<style>
    /* Modern Clean Design - No Gradients */
    :root {
        --primary-color: #2563eb;
        --primary-hover: #1d4ed8;
        --secondary-color: #64748b;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --light-bg: #f8fafc;
        --card-bg: #ffffff;
        --border-color: #e2e8f0;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
    }

    body {
        background-color: var(--light-bg);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    .page-title {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 1.875rem;
        margin: 0;
    }

    .course-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
        overflow: visible;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .course-card:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-2px);
        border-color: var(--primary-color);
    }

    .course-header {
        background: var(--primary-color);
        color: white;
        padding: 1.5rem;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        position: relative;
    }

    .course-actions {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 10;
    }

    .status-badge {
        position: absolute;
        top: 1rem;
        right: 4rem;
        z-index: 5;
    }

    .badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.375rem 0.75rem;
        border-radius: var(--radius-sm);
    }

    .course-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        color: white;
        padding-right: 8rem;
        line-height: 1.3;
    }

    .course-description {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0;
        line-height: 1.5;
    }

    .course-content {
        padding: 1.5rem;
    }

    .course-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .meta-item {
        text-align: center;
        padding: 0.75rem;
        background: var(--light-bg);
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
    }

    .meta-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.25rem;
    }

    .meta-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .course-stats {
        display: flex;
        justify-content: space-around;
        padding: 1rem;
        background: var(--light-bg);
        border-top: 1px solid var(--border-color);
        border-radius: 0 0 var(--radius-lg) var(--radius-lg);
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-color);
        display: block;
    }

    .stat-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Buttons */
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        border-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-outline-primary {
        color: var(--primary-color);
        border-color: var(--primary-color);
        border-radius: var(--radius-md);
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .btn-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.75rem;
    }

    /* Dropdown */
    .dropdown-menu {
        z-index: 1050;
        min-width: 200px;
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-md);
        padding: 0.5rem 0;
        margin-top: 0.25rem;
    }

    .dropdown-item {
        padding: 0.75rem 1rem;
        font-size: 0.875rem;
        color: var(--text-primary);
        transition: all 0.2s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }

    .dropdown-item:hover {
        background-color: var(--light-bg);
        color: var(--primary-color);
    }

    .dropdown-item.text-danger:hover {
        background-color: #fef2f2;
        color: var(--danger-color);
    }

    .dropdown-toggle::after {
        display: none;
    }

    .course-actions .btn {
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        backdrop-filter: blur(10px);
    }

    .course-actions .btn:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        color: white;
    }

    /* Ensure containers don't clip dropdowns */
    .row, .col-lg-4, .col-md-6, .card {
        overflow: visible;
    }

    .course-actions .dropdown-menu {
        position: absolute !important;
        top: 100% !important;
        right: 0 !important;
        left: auto !important;
        z-index: 1060 !important;
    }

    /* Modal */
    .modal-content {
        border-radius: var(--radius-lg);
        border: none;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        background: var(--primary-color);
        color: white;
        border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        border-bottom: none;
        padding: 1.5rem;
    }

    .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }

    .btn-close {
        filter: invert(1);
    }

    /* Form Controls */
    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    /* Alert */
    .alert {
        border-radius: var(--radius-md);
        border: none;
        font-weight: 500;
    }

    /* Section Title */
    .section-title {
        color: var(--text-primary);
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 60px;
        height: 3px;
        background: var(--primary-color);
        border-radius: var(--radius-sm);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 3rem;
        color: var(--border-color);
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .course-meta {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: stretch;
        }
        
        .action-buttons .btn {
            margin-bottom: 0.5rem;
        }
        
        .course-title {
            padding-right: 6rem;
            font-size: 1.1rem;
        }
        
        .status-badge {
            right: 3.5rem;
        }
        
        .course-actions {
            right: 0.5rem;
        }
    }
    
    @media (max-width: 480px) {
        .course-title {
            padding-right: 5rem;
            font-size: 1rem;
        }
        
        .status-badge {
            right: 3rem;
        }
        
        .course-actions {
            right: 0.25rem;
        }
    }
</style>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <?php include("./partials/header.php"); ?>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <?php include("./partials/navbar.php"); ?>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Course Management</h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="./home">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Course Management</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="page-title">Course Management</h2>
                            <div class="action-buttons">
                                <a href="create-course.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create New Course
                                </a>
                                <button type="button" class="btn btn-outline-primary" onclick="location.reload()">
                                    <i class="fas fa-sync me-2"></i>Refresh
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Courses Grid -->
                <div class="row">
                    <?php if ($coursesResult && mysqli_num_rows($coursesResult) > 0): ?>
                        <?php while ($course = mysqli_fetch_assoc($coursesResult)): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="card course-card">
                                    <div class="course-header position-relative">
                                        <div class="status-badge">
                                            <?php
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($course['courseDisplayStatus']) {
                                                case 1:
                                                    $statusClass = 'bg-success';
                                                    $statusText = 'Open';
                                                    break;
                                                case 2:
                                                    $statusClass = 'bg-warning';
                                                    $statusText = 'Closed';
                                                    break;
                                                default:
                                                    $statusClass = 'bg-secondary';
                                                    $statusText = 'Inactive';
                                            }
                                            ?>
                                            <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                        </div>
                                        
                                        <div class="course-actions">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="#" onclick="viewCourseDetails(<?php echo $course['courseId']; ?>)">
                                                        <i class="fas fa-eye me-2"></i>View Details
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item" href="edit-course.php?id=<?php echo $course['courseId']; ?>">
                                                        <i class="fas fa-edit me-2"></i>Edit Course Details
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="course-editor.php?id=<?php echo $course['courseId']; ?>">
                                                        <i class="fas fa-file-alt me-2"></i>Edit Course Content
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="course-files.php?id=<?php echo $course['courseId']; ?>">
                                                        <i class="fas fa-folder me-2"></i>Manage Files
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="course-enrollments.php?id=<?php echo $course['courseId']; ?>">
                                                        <i class="fas fa-users me-2"></i>View Enrollments
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteCourse(<?php echo $course['courseId']; ?>)">
                                                        <i class="fas fa-trash me-2"></i>Delete Course
                                                    </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <h5 class="course-title"><?php echo htmlspecialchars($course['courseName']); ?></h5>
                                        <p class="course-description"><?php echo htmlspecialchars(substr($course['courseShortDescription'], 0, 100)) . '...'; ?></p>
                                    </div>
                                    
                                    <div class="course-content">
                                        <div class="course-meta">
                                            <div class="meta-item">
                                                <div class="meta-label">Start Date</div>
                                                <div class="meta-value"><?php echo date('M j, Y', strtotime($course['courseStartDate'])); ?></div>
                                            </div>
                                            <div class="meta-item">
                                                <div class="meta-label">End Date</div>
                                                <div class="meta-value"><?php echo date('M j, Y', strtotime($course['courseEndDate'])); ?></div>
                                            </div>
                                            <div class="meta-item">
                                                <div class="meta-label">Seats</div>
                                                <div class="meta-value"><?php echo $course['courseSeats']; ?></div>
                                            </div>
                                            <div class="meta-item">
                                                <div class="meta-label">Price</div>
                                                <div class="meta-value">
                                                    <?php if ($course['amount'] && $course['amount'] > 0): ?>
                                                        <?php 
                                                        $currencySymbol = $course['currencySymbol'] ?: $course['currency'];
                                                        $formattedAmount = number_format($course['amount'], 2);
                                                        echo $currencySymbol . ' ' . $formattedAmount;
                                                        ?>
                                                    <?php else: ?>
                                                        Free
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="#" onclick="viewCourseDetails(<?php echo $course['courseId']; ?>)" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i>View Details
                                            </a>
                                            <small class="text-muted">
                                                Created <?php echo date('M j, Y', strtotime($course['courseCreatedDate'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="course-stats">
                                        <div class="stat-item">
                                            <div class="stat-number">0</div>
                                            <div class="stat-label">Enrollments</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">0</div>
                                            <div class="stat-label">Lessons</div>
                                        </div>
                                        <div class="stat-item">
                                            <div class="stat-number">0</div>
                                            <div class="stat-label">Files</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="fas fa-graduation-cap"></i>
                                <h4>No Courses Found</h4>
                                <p>Create your first course to get started with course management.</p>
                                <a href="create-course.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Create New Course
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="row mt-4">
                        <div class="col-12">
                            <nav aria-label="Course pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $startPage = max(1, $page - 2);
                                    $endPage = min($totalPages, $page + 2);
                                    
                                    for ($i = $startPage; $i <= $endPage; $i++):
                                    ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalCourses); ?> 
                                    of <?php echo $totalCourses; ?> courses
                                </small>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>

    <!-- Create Course Modal -->
    <div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createCourseModalLabel">Create New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="courseName" class="form-label">Course Name *</label>
                                <input type="text" class="form-control" id="courseName" name="courseName" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="courseSeats" class="form-label">Available Seats *</label>
                                <input type="number" class="form-control" id="courseSeats" name="courseSeats" min="1" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="courseShortDescription" class="form-label">Short Description *</label>
                            <textarea class="form-control" id="courseShortDescription" name="courseShortDescription" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="courseLongDescription" class="form-label">Long Description *</label>
                            <textarea class="form-control" id="courseLongDescription" name="courseLongDescription" rows="4" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="courseStartDate" class="form-label">Start Date *</label>
                                <input type="date" class="form-control" id="courseStartDate" name="courseStartDate" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="courseRegEndDate" class="form-label">Registration End Date *</label>
                                <input type="date" class="form-control" id="courseRegEndDate" name="courseRegEndDate" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="courseEndDate" class="form-label">End Date *</label>
                                <input type="date" class="form-control" id="courseEndDate" name="courseEndDate" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="courseDisplayStatus" class="form-label">Course Status *</label>
                            <select class="form-control" id="courseDisplayStatus" name="courseDisplayStatus" required>
                                <option value="0">Not Active</option>
                                <option value="1" selected>Open for Registration</option>
                                <option value="2">Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="create_course" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
    <script src="./dist/js/waves.js"></script>
    <script src="./dist/js/sidebarmenu.js"></script>
    <script src="./dist/js/custom.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <script>
        // Initialize dropdowns when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize all dropdowns
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl);
            });

            // Debug: Log dropdown initialization
            console.log('Initialized', dropdownList.length, 'dropdowns');
        });

        function deleteCourse(courseId) {
            if (confirm('Are you sure you want to delete this course? This action cannot be undone.')) {
                // Add delete functionality here
                console.log('Delete course:', courseId);
            }
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // View course details function
        function viewCourseDetails(courseId) {
            // Fetch course details via AJAX
            fetch(`php/get-course-details.php?id=${courseId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateCourseDetailsModal(data.course);
                        const modal = new bootstrap.Modal(document.getElementById('courseDetailsModal'));
                        modal.show();
                    } else {
                        alert('Error loading course details: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading course details');
                });
        }

        // Store current course ID for edit function
        let currentCourseId = null;

        // Populate course details modal
        function populateCourseDetailsModal(course) {
            currentCourseId = course.courseId;
            document.getElementById('courseDetailsTitle').textContent = course.courseName;
            document.getElementById('courseDetailsDescription').textContent = course.courseLongDescription || course.courseShortDescription;
            document.getElementById('courseDetailsStartDate').textContent = formatDate(course.courseStartDate);
            document.getElementById('courseDetailsRegEndDate').textContent = formatDate(course.courseRegEndDate);
            document.getElementById('courseDetailsEndDate').textContent = formatDate(course.courseEndDate);
            document.getElementById('courseDetailsSeats').textContent = course.courseSeats;
            if (course.amount && course.amount > 0) {
                const currencySymbol = course.currencySymbol || course.currency || 'USD';
                const formattedAmount = parseFloat(course.amount).toFixed(2);
                document.getElementById('courseDetailsPrice').textContent = `${currencySymbol} ${formattedAmount}`;
                document.getElementById('courseDetailsCurrency').textContent = '';
            } else {
                document.getElementById('courseDetailsPrice').textContent = 'Free';
                document.getElementById('courseDetailsCurrency').textContent = '';
            }
            document.getElementById('courseDetailsStatus').textContent = getStatusText(course.courseDisplayStatus);
            document.getElementById('courseDetailsStatus').className = `badge ${getStatusClass(course.courseDisplayStatus)}`;
            document.getElementById('courseDetailsCreated').textContent = formatDate(course.courseCreatedDate);
            document.getElementById('courseDetailsPhoto').src = course.coursePhoto ? `<?php echo getImageUrl(''); ?>${course.coursePhoto}` : '<?php echo getAssetUrl('assets/images/placeholder-course.jpg'); ?>';
            document.getElementById('courseDetailsPhoto').alt = course.courseName;
        }

        // Edit course from modal
        function editCourseFromModal() {
            if (currentCourseId) {
                window.location.href = `edit-course.php?id=${currentCourseId}`;
            }
        }

        // Helper functions
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });
        }

        function getStatusText(status) {
            // Convert to number to handle string values
            const statusNum = parseInt(status);
            switch(statusNum) {
                case 1: return 'Open';
                case 2: return 'Closed';
                default: return 'Inactive';
            }
        }

        function getStatusClass(status) {
            // Convert to number to handle string values
            const statusNum = parseInt(status);
            switch(statusNum) {
                case 1: return 'bg-success';
                case 2: return 'bg-warning';
                default: return 'bg-secondary';
            }
        }
    </script>

    <!-- Course Details Modal -->
    <div class="modal fade" id="courseDetailsModal" tabindex="-1" aria-labelledby="courseDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseDetailsTitle">Course Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img id="courseDetailsPhoto" src="" alt="Course Photo" class="img-fluid rounded mb-3" style="max-height: 200px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Description</h6>
                                <p id="courseDetailsDescription" class="mb-0"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">Course Schedule</h6>
                                    <div class="mb-2">
                                        <strong>Start Date:</strong> <span id="courseDetailsStartDate"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Registration End:</strong> <span id="courseDetailsRegEndDate"></span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>End Date:</strong> <span id="courseDetailsEndDate"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">Course Information</h6>
                                    <div class="mb-2">
                                        <strong>Available Seats:</strong> <span id="courseDetailsSeats"></span>
                                    </div>
                                    <div class="mb-2">
                                        <strong>Price:</strong> <span id="courseDetailsPrice"></span> <span id="courseDetailsCurrency"></span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>Status:</strong> <span id="courseDetailsStatus" class="badge"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">Additional Information</h6>
                                    <div class="mb-0">
                                        <strong>Created:</strong> <span id="courseDetailsCreated"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="editCourseFromModal()">Edit Course</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
