<?php
session_start();
include("../dbconnections/connection.php");

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$adminId = $_SESSION['admin_id']; // Assuming admin ID is stored in session

if ($userId > 0 && $adminId > 0) {
    // Check if conversation already exists
    $checkQuery = "SELECT * FROM Conversation WHERE UserId = $userId";
    $checkResult = mysqli_query($conn, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        // Conversation exists
        $row = mysqli_fetch_assoc($checkResult);
        echo json_encode(['success' => true, 'convId' => $row['ConvId']]);
    } else {
        $startDate = date('Y-m-d');
        $startTime = date('H:i');
        // Create new conversation
        $insertQuery = "INSERT INTO Conversation (UserId, AdminId, StartDate, StartTime, ConvStatus) 
                        VALUES ($userId, $adminId, $startDate, $startTime, 0)";
        if (mysqli_query($conn, $insertQuery)) {
            $convId = mysqli_insert_id($conn);
            echo json_encode(['success' => true, 'convId' => $convId]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create conversation']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid user or admin ID']);
}

mysqli_close($conn);
?>