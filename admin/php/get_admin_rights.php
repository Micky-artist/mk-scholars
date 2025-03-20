<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'mkscholars';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Database connection failed');
}

// Get admin ID from the query string
$adminId = isset($_GET['adminId']) ? (int)$_GET['adminId'] : 0;


if ($adminId > 0) {
    // Fetch the admin's rights
    $sql = "SELECT * FROM AdminRights WHERE AdminId = $adminId";
    $result = $conn->query($sql);
    $admin = $result->fetch_assoc();

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
                        <input type="checkbox" name="<?= $right ?>" class="d-none" 
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
                        <input type="checkbox" name="<?= $right ?>" class="d-none" 
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
                        <input type="checkbox" name="<?= $right ?>" class="d-none" 
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
                        <input type="checkbox" name="<?= $right ?>" class="d-none" 
                               <?= isset($admin[$right]) && $admin[$right] == 1 ? 'checked' : '' ?>>
                        <div class="permission-toggle"></div>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

$conn->close();
?>