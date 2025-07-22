<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');

// Set Rwanda timezone
date_default_timezone_set('Africa/Kigali');

session_start();
include('../dbconnections/connection.php');

// Validate admin session
if (!isset($_SESSION['adminId'])) {
    http_response_code(401);
    exit;
}

// Get conversation ID and last message ID
$convId = isset($_GET['convId']) ? intval($_GET['convId']) : 0;
$lastMessageId = isset($_GET['lastMessageId']) ? intval($_GET['lastMessageId']) : 0;

if (!$convId) {
    exit;
}

// Function to send SSE data
function sendSSE($data) {
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Send initial connection confirmation
sendSSE(['type' => 'connected', 'message' => 'SSE connection established']);

// Simple polling approach - much faster
$lastCheck = 0;

while (true) {
    // Check if client is still connected
    if (connection_aborted()) {
        break;
    }

    // Check every 2 seconds
    if (time() - $lastCheck >= 2) {
        $lastCheck = time();
        
        // Simple query to get new messages
        $stmt = $conn->prepare("SELECT MessageId, AdminId, MessageContent, SentDate, SentTime 
                               FROM Message 
                               WHERE ConvId = ? AND MessageId > ? 
                               ORDER BY SentDate, SentTime");
        
        if ($stmt) {
            $stmt->bind_param('ii', $convId, $lastMessageId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $newMessages = [];
            while ($row = $result->fetch_assoc()) {
                $newMessages[] = $row;
            }
            $stmt->close();
            
            // Send new messages if any
            if (!empty($newMessages)) {
                sendSSE([
                    'type' => 'messages',
                    'messages' => $newMessages
                ]);
                
                // Mark as read
                $updateStmt = $conn->prepare("UPDATE Message SET MessageStatus = 1 WHERE ConvId = ?");
                if ($updateStmt) {
                    $updateStmt->bind_param('i', $convId);
                    $updateStmt->execute();
                    $updateStmt->close();
                }
            }
        }
    }
    
    // Sleep for 1 second
    sleep(1);
}

$conn->close();
?> 