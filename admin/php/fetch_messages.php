<?php
session_start();
include("../dbconnections/connection.php");

// Validate admin session
if (!isset($_SESSION['adminId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Admin not authenticated']);
    exit;
}

if (empty($_GET['ConvId'])) { 
    echo json_encode([]); 
    exit; 
}

$convId = intval($_GET['ConvId']);
$lastMessageId = isset($_GET['lastMessageId']) ? intval($_GET['lastMessageId']) : 0;

// Build query based on whether we want all messages or only new ones
if ($lastMessageId > 0) {
    // Get only messages newer than lastMessageId
    $stmt = $conn->prepare("SELECT MessageId, AdminId, MessageContent, SentDate, SentTime
        FROM Message WHERE ConvId = ? AND MessageId > ? ORDER BY SentDate, SentTime");
    $stmt->bind_param('ii', $convId, $lastMessageId);
} else {
    // Get all messages
    $stmt = $conn->prepare("SELECT MessageId, AdminId, MessageContent, SentDate, SentTime
        FROM Message WHERE ConvId = ? ORDER BY SentDate, SentTime");
    $stmt->bind_param('i', $convId);
}

$stmt->execute();
$res = $stmt->get_result();
$messages = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Mark as read only if getting all messages (not polling)
if ($lastMessageId == 0) {
    $conn->query("UPDATE Message SET MessageStatus=1 WHERE ConvId={$convId}");
}

header('Content-Type: application/json');
echo json_encode($messages);
?>
