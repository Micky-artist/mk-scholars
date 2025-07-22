<?php
session_start();
require_once '../dbconnections/connection.php';  // adjust path as needed

// Set Rwanda timezone
date_default_timezone_set('Africa/Kigali');

// Validate admin session
if (!isset($_SESSION['adminId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Admin not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// fetch & validate inputs
$convId  = isset($_POST['ConvId'])  ? (int) $_POST['ConvId']  : 0;
$adminId = isset($_SESSION['adminId']) ? (int) $_SESSION['adminId'] : 0;
$message = trim($_POST['message'] ?? '');

// if any required piece is missing, abort
if (! $convId || ! $adminId || $message === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

$senderId = $adminId;
// Use Rwanda timezone for message time
$sentTime = date('H:i:s');
$sentDate = date('Y-m-d');

// prepare & execute
$sql = "
    INSERT INTO Message
        (UserId, senderId, AdminId, ConvId, MessageContent, SentDate, SentTime, MessageStatus)
    VALUES
        (0,       ?,        ?,       ?,      ?,              ?,        ?,       0)
";

if (! $stmt = $conn->prepare($sql)) {
    error_log('Prepare failed: ' . $conn->error);
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare failed']);
    exit;
}

$stmt->bind_param(
    'iiisss',
    $senderId,   // maps to senderId
    $adminId,    // maps to AdminId
    $convId,     // maps to ConvId
    $message,    // maps to MessageContent
    $sentDate,   // maps to SentDate
    $sentTime    // maps to SentTime
);

if (! $stmt->execute()) {
    error_log('Execute failed: ' . $stmt->error);
    http_response_code(500);
    echo json_encode(['error' => 'Failed to send message']);
    exit;
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
?>
