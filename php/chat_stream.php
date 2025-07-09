<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

include('../dbconnection/connection.php');

$convId = isset($_GET['convId']) ? intval($_GET['convId']) : 0;
$lastMessageId = isset($_GET['lastMessageId']) ? intval($_GET['lastMessageId']) : 0;

if ($convId <= 0) {
    echo "data: {\"error\": \"Invalid conversation ID\"}\n\n";
    exit;
}

// Keep connection alive and check for new messages
while (true) {
    // Check for new messages
    $sql = "SELECT MessageId, UserId, MessageContent, SentDate, SentTime FROM Message WHERE ConvId = ? AND MessageId > ? ORDER BY SentDate, SentTime";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $convId, $lastMessageId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $newMessages = [];
    while ($row = $result->fetch_assoc()) {
        $newMessages[] = $row;
        $lastMessageId = max($lastMessageId, $row['MessageId']);
    }
    $stmt->close();
    
    if (!empty($newMessages)) {
        echo "data: " . json_encode($newMessages) . "\n\n";
        ob_flush();
        flush();
    }
    
    // Check connection status
    if (connection_aborted()) {
        break;
    }
    
    // Wait 2 seconds before next check
    sleep(2);
}

$conn->close(); 