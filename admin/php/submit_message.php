<?php
session_start();
require_once '../dbconnections/connection.php';  // adjust path as needed

// Validate admin session
if (!isset($_SESSION['adminId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Admin not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit;
}

// fetch & validate inputs
$convId  = isset($_POST['ConvId'])  ? (int) $_POST['ConvId']  : 0;
$adminId = isset($_SESSION['adminId']) ? (int) $_SESSION['adminId'] : 0;
$message = trim($_POST['message'] ?? '');

// if any required piece is missing, abort
if (! $convId || ! $adminId || $message === '') {
    exit;
}

$senderId = $adminId;
$sentTime = date('H:i');

// prepare & execute
$sql = "
    INSERT INTO Message
        (UserId, senderId, AdminId, ConvId, MessageContent, SentDate, SentTime, MessageStatus)
    VALUES
        (0,       ?,        ?,       ?,      ?,              CURDATE(), ?,       0)
";

if (! $stmt = $conn->prepare($sql)) {
    error_log('Prepare failed: ' . $conn->error);
    exit;
}

$stmt->bind_param(
    'iiiss',
    $senderId,   // maps to senderId
    $adminId,    // maps to AdminId
    $convId,     // maps to ConvId
    $message,    // maps to MessageContent
    $sentTime    // maps to SentTime
);

if (! $stmt->execute()) {
    error_log('Execute failed: ' . $stmt->error);
}

$stmt->close();
