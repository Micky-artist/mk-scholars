<?php
session_start();
include("../dbconnections/connection.php");

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$adminId = $_SESSION['adminId']; // Assuming admin ID is stored in session

// Check if userId is valid
if ($userId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID']);
    exit;
}

$checkQuery = "SELECT * FROM Conversation WHERE UserId = $userId";
$checkResult = mysqli_query($conn, $checkQuery);

if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    // Conversation exists
    $row = mysqli_fetch_assoc($checkResult);
    echo json_encode(['success' => true, 'UserId' => $row['UserId']]);
} else {
    $startDate = date('Y-m-d');
    $startTime = date('H:i:s');
    // Create new conversation
    $insertQuery = "INSERT INTO Conversation (UserId, AdminId, StartDate, StartTime, ConvStatus) VALUES ($userId, $adminId, '$startDate', '$startTime', 0)";
    if (mysqli_query($conn, $insertQuery)) {
        // Get the newly inserted user ID
        echo json_encode(['success' => true, 'UserId' => $userId]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create conversation: ' . mysqli_error($conn)]);
    }
}

mysqli_close($conn);
?>