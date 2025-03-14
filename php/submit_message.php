<?php
include("../dbconnection/connection.php");
if (!isset($_POST['UserId']) || !isset($_POST['AdminId']) || !isset($_POST['ConvId']) || !isset($_POST['message'])) {
    echo "Error: Missing required parameters";
    exit;
}

$UserId = filter_var($_POST['UserId'], FILTER_SANITIZE_NUMBER_INT);
$AdminId = filter_var($_POST['AdminId'], FILTER_SANITIZE_NUMBER_INT);
$ConvId = filter_var($_POST['ConvId'], FILTER_SANITIZE_NUMBER_INT);
$MessageContent = htmlspecialchars($_POST['message'], ENT_QUOTES, 'UTF-8');
$SentDate = date("Y-m-d");
$SentTime = date("H:i");
$MessageStatus = 0;

// Use prepared statement to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO Message(UserId, senderId, AdminId, ConvId, MessageContent, SentDate, SentTime, MessageStatus) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iiiisssi", $UserId, $UserId, $AdminId, $ConvId, $MessageContent, $SentDate, $SentTime, $MessageStatus);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>