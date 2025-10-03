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
    // Get only messages newer than lastMessageId with admin names
    $stmt = $conn->prepare("SELECT m.MessageId, m.AdminId, m.MessageContent, m.SentDate, m.SentTime,
        COALESCE(a.username, 'Admin') as AdminName
        FROM Message m
        LEFT JOIN users a ON m.AdminId = a.userId
        WHERE m.ConvId = ? AND m.MessageId > ? 
        ORDER BY m.SentDate, m.SentTime");
    $stmt->bind_param('ii', $convId, $lastMessageId);
} else {
    // Get all messages with admin names
    $stmt = $conn->prepare("SELECT m.MessageId, m.AdminId, m.MessageContent, m.SentDate, m.SentTime,
        COALESCE(a.username, 'Admin') as AdminName
        FROM Message m
        LEFT JOIN users a ON m.AdminId = a.userId
        WHERE m.ConvId = ? 
        ORDER BY m.SentDate, m.SentTime");
    $stmt->bind_param('i', $convId);
}

$stmt->execute();
$res = $stmt->get_result();
$messages = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Add admin names (time conversion will be handled on client-side)
foreach ($messages as &$message) {
    // Ensure AdminName is set
    if (empty($message['AdminName'])) {
        $message['AdminName'] = 'Admin';
    }
}

// Mark as read only if getting all messages (not polling)
if ($lastMessageId == 0) {
    $conn->query("UPDATE Message SET MessageStatus=1 WHERE ConvId={$convId}");
}

header('Content-Type: application/json');
echo json_encode($messages);
?>
