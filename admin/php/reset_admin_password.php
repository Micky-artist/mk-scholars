<?php
session_start();
include("../dbconnections/connection.php");
include("../php/validateAdminSession.php");

// Check if user has ManageRights permission
if (!hasPermission('ManageRights')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminId = isset($_POST['adminId']) ? (int)$_POST['adminId'] : 0;
    $newPassword = isset($_POST['newPassword']) ? trim($_POST['newPassword']) : '';
    
    // Validate inputs
    if ($adminId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid admin ID']);
        exit;
    }
    
    if (empty($newPassword)) {
        echo json_encode(['success' => false, 'message' => 'Password is required']);
        exit;
    }
    
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
        exit;
    }
    
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update the password
    $updateSql = "UPDATE users SET password = ? WHERE userId = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $hashedPassword, $adminId);
    
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Password reset successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $updateStmt->error]);
    }
    $updateStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

