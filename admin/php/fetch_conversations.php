<?php
session_start();
include("../dbconnections/connection.php");

// Validate admin session
if (!isset($_SESSION['adminId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Admin not authenticated']);
    exit;
}

// Get conversations with user info and unread counts
$convSql = "SELECT c.ConvId, u.NoUsername, u.NoUserId, lm.lastMessageId,
         COALESCE(uc.unreadCount,0) AS unreadCount
    FROM Conversation c
    JOIN normUsers u ON c.UserId=u.NoUserId
    JOIN (SELECT ConvId,MAX(MessageId) AS lastMessageId FROM Message GROUP BY ConvId) lm
      ON c.ConvId=lm.ConvId
    LEFT JOIN (SELECT ConvId,COUNT(*) AS unreadCount FROM Message WHERE MessageStatus=0 GROUP BY ConvId) uc
      ON c.ConvId=uc.ConvId
   ORDER BY lm.lastMessageId DESC
";

$result = $conn->query($convSql);
$conversations = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $conversations[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($conversations);
?>
