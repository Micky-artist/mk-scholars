<?php
session_start();
header('Content-Type: application/json');

include("../dbconnections/connection.php");
include("../php/validateAdminSession.php");

// Check if user has ManageRights permission
if (!hasPermission('ManageRights')) {
    echo json_encode(['success' => false, 'message' => 'You do not have permission to manage rights.']);
    exit;
}

// Get admin ID and right from the query string
$adminId = isset($_GET['adminId']) ? (int)$_GET['adminId'] : 0;
$right = isset($_GET['right']) ? trim($_GET['right']) : '';

// Validate input
if ($adminId <= 0 || empty($right)) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Whitelist of allowed right names to prevent SQL injection
$allowedRights = [
    'ManageRights', 'ManageCountries', 'ViewApplications', 'DeleteApplication',
    'EditApplication', 'PublishApplication', 'ApplicationSupportRequest', 'CourseApplication',
    'ChatGround', 'ViewUsers', 'ManageUsers', 'ViewTags', 'AddTag', 'DeleteTag',
    'ManageYoutubeVideo', 'DeleteYoutubeVideo', 'ManageUserLogs', 'AddAdmin'
];

if (!in_array($right, $allowedRights)) {
    echo json_encode(['success' => false, 'message' => 'Invalid right name']);
    exit;
}

// Check if AdminRights record exists
$checkSql = "SELECT AdminId FROM AdminRights WHERE AdminId = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $adminId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows == 0) {
    // If no record exists, create one with this right set to 0
    $checkStmt->close();
    $insertSql = "INSERT INTO AdminRights (AdminId, `$right`) VALUES (?, 0)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("i", $adminId);
    
    if ($insertStmt->execute()) {
        $insertStmt->close();
        echo json_encode(['success' => true, 'message' => 'Right revoked successfully']);
    } else {
        $insertStmt->close();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    $checkStmt->close();
    
    // Update the right to 0 using prepared statement
    $updateSql = "UPDATE AdminRights SET `$right` = 0 WHERE AdminId = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("i", $adminId);
    
    if ($updateStmt->execute()) {
        $updateStmt->close();
        echo json_encode(['success' => true, 'message' => 'Right revoked successfully']);
    } else {
        $updateStmt->close();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
    }
}
// Note: Don't close connection as it may be used elsewhere
?>