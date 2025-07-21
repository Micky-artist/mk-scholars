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

$convId = isset($_GET['ConvId']) ? intval($_GET['ConvId']) : 0;
$lastMessageId = isset($_GET['lastMessageId']) ? intval($_GET['lastMessageId']) : 0;
$adminId = isset($_SESSION['admin_id']) ? intval($_SESSION['admin_id']) : 0;

if ($convId <= 0) {
    echo json_encode(['error' => 'Invalid conversation ID']);
    exit;
}

// Check for new messages since lastMessageId
$sql = "SELECT MessageId, UserId, MessageContent, SentDate, SentTime 
        FROM Message 
        WHERE ConvId = ? AND MessageId > ? 
        ORDER BY MessageId ASC";

// Also get total unread count for this conversation
$unreadSql = "SELECT COUNT(*) as unreadCount 
              FROM Message 
              WHERE ConvId = ? AND UserId != ? AND MessageStatus = 0";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $convId, $lastMessageId);
$stmt->execute();
$result = $stmt->get_result();

$newMessages = [];
while ($row = $result->fetch_assoc()) {
    $newMessages[] = [
        'MessageId' => $row['MessageId'],
        'UserId' => $row['UserId'],
        'MessageContent' => $row['MessageContent'],
        'SentDate' => $row['SentDate'],
        'SentTime' => $row['SentTime']
    ];
}

$stmt->close();

// Get unread count
$unreadStmt = $conn->prepare($unreadSql);
$unreadStmt->bind_param("ii", $convId, $adminId);
$unreadStmt->execute();
$unreadResult = $unreadStmt->get_result();
$unreadCount = $unreadResult->fetch_assoc()['unreadCount'];

$unreadStmt->close();
$conn->close();

echo json_encode([
    'newMessages' => $newMessages,
    'count' => count($newMessages),
    'unreadCount' => $unreadCount
]); 