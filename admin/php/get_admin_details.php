<?php
session_start();
include("../dbconnections/connection.php");
include("../php/validateAdminSession.php");

// Check if user has ManageRights permission
if (!hasPermission('ManageRights')) {
    echo '<div class="alert alert-danger">You do not have permission to view admin details.</div>';
    exit;
}

// Get admin ID from the query string
$adminId = isset($_GET['adminId']) ? (int)$_GET['adminId'] : 0;

if ($adminId > 0) {
    // Fetch the admin's details
    $sql = "SELECT u.userId, u.username, u.email, u.status, ar.* 
            FROM users u 
            LEFT JOIN AdminRights ar ON u.userId = ar.AdminId 
            WHERE u.userId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();
    
    if ($admin) {
        // Get granted courses
        $coursesSql = "SELECT c.courseId, c.courseName, aca.grantedDate 
                       FROM AdminCourseAccess aca 
                       INNER JOIN Courses c ON aca.courseId = c.courseId 
                       WHERE aca.adminId = ? 
                       ORDER BY aca.grantedDate DESC";
        $coursesStmt = $conn->prepare($coursesSql);
        $coursesStmt->bind_param("i", $adminId);
        $coursesStmt->execute();
        $coursesResult = $coursesStmt->get_result();
        $grantedCourses = [];
        while ($course = $coursesResult->fetch_assoc()) {
            $grantedCourses[] = $course;
        }
        $coursesStmt->close();
        
        // Get active permissions
        $permissions = [];
        $allRights = [
            'ManageRights', 'ManageCountries', 'ViewApplications', 'DeleteApplication',
            'EditApplication', 'PublishApplication', 'ApplicationSupportRequest', 'CourseApplication',
            'ChatGround', 'ViewUsers', 'ManageUsers', 'ViewTags', 'AddTag', 'DeleteTag',
            'ManageYoutubeVideo', 'DeleteYoutubeVideo', 'ManageUserLogs', 'AddAdmin'
        ];
        foreach ($allRights as $right) {
            if (isset($admin[$right]) && $admin[$right] == 1) {
                $permissions[] = $right;
            }
        }
        ?>
        <div class="row">
            <div class="col-md-6">
                <h5 class="section-header mb-3">Basic Information</h5>
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">User ID</th>
                        <td><?= htmlspecialchars($admin['userId']) ?></td>
                    </tr>
                    <tr>
                        <th>Username</th>
                        <td><?= htmlspecialchars($admin['username']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= htmlspecialchars($admin['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php if ($admin['status'] == 1): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Deactivated</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5 class="section-header mb-3">Permissions</h5>
                <?php if (!empty($permissions)): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($permissions as $permission): ?>
                            <span class="badge bg-primary"><?= htmlspecialchars($permission) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No permissions assigned</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="section-header mb-3">Granted Course Access</h5>
                <?php if (!empty($grantedCourses)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Course ID</th>
                                    <th>Course Name</th>
                                    <th>Access Granted Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grantedCourses as $course): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($course['courseId']) ?></td>
                                        <td><?= htmlspecialchars($course['courseName']) ?></td>
                                        <td><?= htmlspecialchars($course['grantedDate']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No course access granted</p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    } else {
        echo '<div class="alert alert-danger">Admin not found.</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid admin ID.</div>';
}
?>

