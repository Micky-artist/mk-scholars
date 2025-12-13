<?php
session_start();
include("../dbconnections/connection.php");
include("../php/validateAdminSession.php");

// Check if user has ManageRights permission
if (!hasPermission('ManageRights')) {
    echo '<div class="alert alert-danger">You do not have permission to view admin rights.</div>';
    exit;
}

// Get admin ID from the query string
$adminId = isset($_GET['adminId']) ? (int)$_GET['adminId'] : 0;

if ($adminId > 0) {
    // Fetch the admin's rights using prepared statement
    $sql = "SELECT * FROM AdminRights WHERE AdminId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    // Render the modal content
    ?>
    <div class="row g-4">
        <!-- Application Management -->
        <div class="col-md-6">
            <h5 class="section-header">Application Controls</h5>
            <?php $appRights = ['ViewApplications', 'DeleteApplication', 'EditApplication', 'PublishApplication', 'CourseApplication', 'ApplicationSupportRequest']; ?>
            <?php foreach ($appRights as $right): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label><?= $right ?></label>
                    <label class="d-flex align-items-center">
                        <input type="checkbox" name="<?= $right ?>" value="1" class="d-none" 
                               <?= isset($admin[$right]) && $admin[$right] == 1 ? 'checked' : '' ?>>
                        <div class="permission-toggle"></div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- User Management -->
        <div class="col-md-6">
            <h5 class="section-header">User Controls</h5>
            <?php $userRights = ['ViewUsers', 'ManageUsers', 'ManageRights', 'AddAdmin']; ?>
            <?php foreach ($userRights as $right): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label><?= $right ?></label>
                    <label class="d-flex align-items-center">
                        <input type="checkbox" name="<?= $right ?>" value="1" class="d-none" 
                               <?= isset($admin[$right]) && $admin[$right] == 1 ? 'checked' : '' ?>>
                        <div class="permission-toggle"></div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Content Management -->
        <div class="col-md-6">
            <h5 class="section-header">Content Management</h5>
            <?php $contentRights = ['ManageYoutubeVideo', 'DeleteYoutubeVideo', 'ViewTags', 'AddTag', 'DeleteTag']; ?>
            <?php foreach ($contentRights as $right): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label><?= $right ?></label>
                    <label class="d-flex align-items-center">
                        <input type="checkbox" name="<?= $right ?>" value="1" class="d-none" 
                               <?= isset($admin[$right]) && $admin[$right] == 1 ? 'checked' : '' ?>>
                        <div class="permission-toggle"></div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- System Management -->
        <div class="col-md-6">
            <h5 class="section-header">System Management</h5>
            <?php $systemRights = ['ManageCountries', 'ManageUserLogs', 'ChatGround']; ?>
            <?php foreach ($systemRights as $right): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label><?= $right ?></label>
                    <label class="d-flex align-items-center">
                        <input type="checkbox" name="<?= $right ?>" value="1" class="d-none" 
                               <?= isset($admin[$right]) && $admin[$right] == 1 ? 'checked' : '' ?>>
                        <div class="permission-toggle"></div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Course Access Management -->
    <?php
    // Check if user has ManageRights permission (super admin)
    if (hasPermission('ManageRights')) {
        // Fetch all courses
        $coursesSql = "SELECT courseId, courseName, courseDisplayStatus FROM Courses ORDER BY courseName";
        $coursesResult = $conn->query($coursesSql);
        
        // Fetch granted course access for this admin
        $grantedCourses = [];
        $grantedSql = "SELECT courseId FROM AdminCourseAccess WHERE adminId = ?";
        $grantedStmt = $conn->prepare($grantedSql);
        $grantedStmt->bind_param("i", $adminId);
        $grantedStmt->execute();
        $grantedResult = $grantedStmt->get_result();
        while ($row = $grantedResult->fetch_assoc()) {
            $grantedCourses[] = $row['courseId'];
        }
        $grantedStmt->close();
        ?>
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="section-header">Course Access Management</h5>
                <p class="text-muted mb-3">Grant access to specific courses for this admin. Admins can only view courses they have been granted access to.</p>
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3" style="max-height: 400px; overflow-y: auto;">
                            <?php if ($coursesResult && $coursesResult->num_rows > 0): ?>
                                <?php while ($course = $coursesResult->fetch_assoc()): ?>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                            <div>
                                                <label class="mb-0 fw-bold"><?= htmlspecialchars($course['courseName']) ?></label>
                                                <small class="text-muted d-block">
                                                    <?php
                                                    $statusText = '';
                                                    switch($course['courseDisplayStatus']) {
                                                        case 1: $statusText = 'Open'; break;
                                                        case 2: $statusText = 'Closed'; break;
                                                        default: $statusText = 'Inactive'; break;
                                                    }
                                                    echo $statusText;
                                                    ?>
                                                </small>
                                            </div>
                                            <label class="d-flex align-items-center course-access-label" style="cursor: pointer; margin: 0;">
                                                <input type="checkbox" 
                                                       name="courseAccess[]" 
                                                       value="<?= $course['courseId'] ?>" 
                                                       class="d-none course-access-checkbox" 
                                                       data-course-id="<?= $course['courseId'] ?>"
                                                       <?= in_array($course['courseId'], $grantedCourses) ? 'checked' : '' ?>>
                                                <div class="permission-toggle course-toggle" style="pointer-events: auto; user-select: none;"></div>
                                            </label>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <p class="text-muted">No courses available.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Course access initialization is handled in manageAdminAccess.php after modal content loads -->
        <?php
    }
    ?>
    <?php
}
// Note: Don't close connection as it may be used elsewhere
?>