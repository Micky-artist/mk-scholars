<?php
session_start();
include('../dbconnection/connection.php');

if (!isset($_POST['UserId'], $_POST['ConvId'])) {
    echo 'Missing data';
    exit;
}

$UserId = $_POST['UserId'];
$ConvId = $_POST['ConvId'];
$AdminId = $_POST['AdminId'] ?? 0;
$message = trim($_POST['message']);
$SentDate = date('Y-m-d');
$SentTime = date('H:i');

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

$sql = "INSERT INTO Message (UserId, senderId, AdminId, ConvId, MessageContent, SentDate, SentTime, MessageStatus)
        VALUES ('$UserId', '$UserId', '$AdminId', '$ConvId', '$finalMessage', '$SentDate', '$SentTime', 0)";

if (mysqli_query($conn, $sql)) {
    echo "Message sent";
} else {
    echo "Error: " . mysqli_error($conn);
}
