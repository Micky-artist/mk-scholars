<?php
session_start();
include("../dbconnections/connection.php");
include("../php/validateAdminSession.php");

// Set content type to JSON
header('Content-Type: application/json');

// Check if user has ManageRights permission (super admin only)
if (!hasPermission('ManageRights')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

// Check if AdminCourseAccess table exists
$tableCheck = $conn->query("SHOW TABLES LIKE 'AdminCourseAccess'");
if (!$tableCheck || $tableCheck->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'AdminCourseAccess table does not exist. Please run the SQL migration first.']);
    exit;
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminId = isset($_POST['adminId']) ? (int)$_POST['adminId'] : 0;
    $courseId = isset($_POST['courseId']) ? (int)$_POST['courseId'] : 0;
    $grant = isset($_POST['grant']) ? (int)$_POST['grant'] : 0;
    $grantedBy = $_SESSION['adminId'] ?? null;
    
    // Validate inputs
    if ($adminId <= 0 || $courseId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid admin or course ID']);
        exit;
    }
    
    if ($grant == 1) {
        // Grant access - insert or update
        $checkSql = "SELECT accessId FROM AdminCourseAccess WHERE adminId = ? AND courseId = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $adminId, $courseId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkStmt->close();
        
        if ($checkResult->num_rows > 0) {
            // Already granted, just update grantedBy and date
            $updateSql = "UPDATE AdminCourseAccess SET grantedBy = ?, grantedDate = CURRENT_TIMESTAMP WHERE adminId = ? AND courseId = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("iii", $grantedBy, $adminId, $courseId);
            
            if ($updateStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Course access granted']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $updateStmt->error]);
            }
            $updateStmt->close();
        } else {
            // Insert new access
            $insertSql = "INSERT INTO AdminCourseAccess (adminId, courseId, grantedBy) VALUES (?, ?, ?)";
            $insertStmt = $conn->prepare($insertSql);
            $insertStmt->bind_param("iii", $adminId, $courseId, $grantedBy);
            
            if ($insertStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Course access granted']);
            } else {
                $errorMsg = $insertStmt->error;
                $insertStmt->close();
                echo json_encode(['success' => false, 'message' => 'Error inserting access: ' . $errorMsg]);
                exit;
            }
            $insertStmt->close();
        }
    } else {
        // Revoke access - delete
        $deleteSql = "DELETE FROM AdminCourseAccess WHERE adminId = ? AND courseId = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $adminId, $courseId);
        
        if ($deleteStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Course access revoked']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $deleteStmt->error]);
        }
        $deleteStmt->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

