<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');
header('Access-Control-Allow-Origin: *');
header('X-Accel-Buffering: no'); // For nginx

// Set Rwanda timezone
date_default_timezone_set('Africa/Kigali');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in SSE stream

try {
    include('../dbconnection/connection.php');
    
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection failed');
    }
} catch (Exception $e) {
    echo "data: " . json_encode(['error' => 'Database connection error: ' . $e->getMessage()]) . "\n\n";
    exit;
}

$convId = isset($_GET['convId']) ? intval($_GET['convId']) : 0;
$lastMessageId = isset($_GET['lastMessageId']) ? intval($_GET['lastMessageId']) : 0;

if ($convId <= 0) {
    echo "data: " . json_encode(['error' => 'Invalid conversation ID']) . "\n\n";
    exit;
}

// Validate conversation exists
$checkSql = "SELECT ConvId FROM Conversation WHERE ConvId = ?";
$checkStmt = $conn->prepare($checkSql);
$checkStmt->bind_param("i", $convId);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    echo "data: " . json_encode(['error' => 'Conversation not found']) . "\n\n";
    exit;
}
$checkStmt->close();

// Function to send SSE data
function sendSSE($data) {
    echo "data: " . json_encode($data) . "\n\n";
    ob_flush();
    flush();
}

// Send initial connection confirmation
sendSSE(['type' => 'connected', 'message' => 'SSE connection established']);

// Simple polling approach
$lastCheck = 0;
$lastTypingCheck = 0;

while (true) {
    // Check if client is still connected
    if (connection_aborted()) {
        break;
    }

    // Check every 2 seconds
    if (time() - $lastCheck >= 2) {
        $lastCheck = time();
        
        // Check for new messages
        try {
            $sql = "SELECT MessageId, UserId, AdminId, MessageContent, SentDate, SentTime FROM Message WHERE ConvId = ? AND MessageId > ? ORDER BY SentDate, SentTime";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Failed to prepare message query: ' . $conn->error);
            }
            
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
                sendSSE([
                    'type' => 'messages',
                    'messages' => $newMessages,
                    'lastMessageId' => $lastMessageId
                ]);
            }
        } catch (Exception $e) {
            sendSSE([
                'type' => 'error',
                'message' => 'Database query error: ' . $e->getMessage()
            ]);
        }
    }
    
    // Check for typing indicators every 3 seconds
    if (time() - $lastTypingCheck >= 3) {
        $lastTypingCheck = time();
        $typingFile = "../uploads/typing_status.json";
        
        if (file_exists($typingFile)) {
            $typingStatuses = json_decode(file_get_contents($typingFile), true) ?: [];
            
            // Find typing indicators for this conversation
            foreach ($typingStatuses as $key => $status) {
                if ($status['convId'] == $convId && $status['isTyping'] && (time() - $status['timestamp']) < 10) {
                    sendSSE([
                        'type' => 'typing',
                        'userId' => $status['userId'],
                        'isTyping' => true
                    ]);
                }
            }
        }
    }
    
    // Sleep for 1 second
    sleep(1);
}

$conn->close();
?> 