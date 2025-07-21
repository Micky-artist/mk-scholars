<?php
session_start();
include("../dbconnections/connection.php");
include("./validateAdminSession.php");

header('Content-Type: application/json');

if (!hasPermission('ChatGround')) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$convId = isset($_POST['ConvId']) ? intval($_POST['ConvId']) : 0;
$adminId = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 0;

if ($convId <= 0 || $adminId <= 0) {
    echo json_encode(['error' => 'Invalid parameters']);
    exit;
}

// Mark all messages in this conversation as read (where admin is not the sender)
$sql = "UPDATE Message 
        SET MessageStatus = 1 
        WHERE ConvId = ? AND UserId != ? AND MessageStatus = 0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $convId, $adminId);
$result = $stmt->execute();

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Messages marked as read']);
} else {
    echo json_encode(['error' => 'Failed to mark messages as read']);
}

$stmt->close();
$conn->close();
