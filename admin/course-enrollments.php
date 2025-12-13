<?php
session_start();
include('./dbconnections/connection.php');
include('./php/validateAdminSession.php');

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($courseId <= 0) {
    header('Location: course-management.php');
    exit;
}

// Validate course access before proceeding
validateCourseAccess($courseId);

// Fetch course
$course = null;
$courseRes = mysqli_query($conn, "SELECT courseId, courseName, courseStartDate, courseEndDate FROM Courses WHERE courseId = $courseId LIMIT 1");
if ($courseRes && mysqli_num_rows($courseRes) === 1) {
    $course = mysqli_fetch_assoc($courseRes);
} else {
    header('Location: course-management.php');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Count total enrollments from subscription table (active & not expired)
$countSql = "SELECT COUNT(*) AS total FROM subscription WHERE Item = $courseId AND SubscriptionStatus = 1 AND (expirationDate IS NULL OR expirationDate > NOW())";
$countRes = mysqli_query($conn, $countSql);
$total = ($countRes && ($row = mysqli_fetch_assoc($countRes))) ? (int)$row['total'] : 0;
$totalPages = max(1, (int)ceil($total / $perPage));

// Fetch enrolled students with user info from subscription table
$enrollSql = "
    SELECT s.SubId, s.UserId, s.subscriptionDate, s.expirationDate, u.NoUsername AS username, u.NoEmail AS email
    FROM subscription s
    INNER JOIN normUsers u ON u.NoUserId = s.UserId
    WHERE s.Item = $courseId AND s.SubscriptionStatus = 1 AND (s.expirationDate IS NULL OR s.expirationDate > NOW())
    ORDER BY s.subscriptionDate DESC
    LIMIT $perPage OFFSET $offset
";
$enrollRes = mysqli_query($conn, $enrollSql);
$enrollments = [];
if ($enrollRes) {
    while ($r = mysqli_fetch_assoc($enrollRes)) {
        $enrollments[] = $r;
    }
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include('./partials/head.php'); ?>
<body>
<div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <?php include('./partials/header.php'); ?>
    <?php include('./partials/navbar.php'); ?>

    <div class="page-wrapper">
        <div class="page-breadcrumb">
            <div class="row">
                <div class="col-12 d-flex no-block align-items-center">
                    <h4 class="page-title">Enrollments - <?php echo htmlspecialchars($course['courseName']); ?></h4>
                    <div class="ms-auto text-end">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="./home">Home</a></li>
                                <li class="breadcrumb-item"><a href="course-management.php">Course Management</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Enrollments</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            Total Enrollments: <?php echo $total; ?>
                        </h5>
                        <a href="course-management.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
                    </div>

                    <?php if (!empty($enrollments)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Enrolled On</th>
                                        <th>Expires</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($enrollments as $idx => $e): ?>
                                    <tr>
                                        <td><?php echo $offset + $idx + 1; ?></td>
                                        <td><?php echo htmlspecialchars($e['username']); ?></td>
                                        <td><?php echo htmlspecialchars($e['email']); ?></td>
                                        <td><?php echo date('M j, Y', strtotime($e['subscriptionDate'])); ?></td>
                                        <td><?php echo $e['expirationDate'] ? date('M j, Y', strtotime($e['expirationDate'])) : '—'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($totalPages > 1): ?>
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                <li class="page-item"><a class="page-link" href="?id=<?php echo $courseId; ?>&page=<?php echo $page - 1; ?>">&laquo;</a></li>
                                <?php endif; ?>
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?id=<?php echo $courseId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>
                                <?php if ($page < $totalPages): ?>
                                <li class="page-item"><a class="page-link" href="?id=<?php echo $courseId; ?>&page=<?php echo $page + 1; ?>">&raquo;</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-user-slash fa-2x mb-2"></i>
                            <div>No active enrollments yet.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="./assets/libs/jquery/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


