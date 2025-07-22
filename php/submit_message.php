<?php
session_start();
include('../dbconnection/connection.php');

// Set Rwanda timezone
date_default_timezone_set('Africa/Kigali');

if (!isset($_POST['UserId'], $_POST['ConvId'])) {
    echo json_encode(['error' => 'Missing data']);
    exit;
}

$UserId = $_POST['UserId'];
$ConvId = $_POST['ConvId'];
$AdminId = $_POST['AdminId'] ?? 0;
$message = trim($_POST['message'] ?? '');

if (empty($message)) {
    echo json_encode(['error' => 'Message cannot be empty']);
    exit;
}

// Use Rwanda timezone for message date and time
$SentDate = date('Y-m-d');
$SentTime = date('H:i:s');

// Handle file upload
$uploadPath = '';
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

    $filename = basename($_FILES["file"]["name"]);
    $targetFile = $targetDir . time() . "_" . $filename;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        $uploadPath = str_replace("../", "./", $targetFile);
    }
}

$finalMessage = $uploadPath ? $uploadPath : $message;

// Use prepared statement for security
$sql = "INSERT INTO Message (UserId, senderId, AdminId, ConvId, MessageContent, SentDate, SentTime, MessageStatus)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiissss", $UserId, $UserId, $AdminId, $ConvId, $finalMessage, $SentDate, $SentTime);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Message sent successfully',
        'messageId' => $conn->insert_id
    ]);
} else {
    echo json_encode([
        'error' => 'Failed to send message: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
