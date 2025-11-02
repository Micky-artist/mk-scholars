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
    <?php
}
// Note: Don't close connection as it may be used elsewhere
?>