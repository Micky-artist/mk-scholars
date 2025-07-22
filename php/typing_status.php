<?php
session_start();
include('../dbconnection/connection.php');

// Set Rwanda timezone
date_default_timezone_set('Africa/Kigali');

// Validate user session
if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$convId = isset($_POST['convId']) ? intval($_POST['convId']) : 0;
$userId = isset($_POST['userId']) ? intval($_POST['userId']) : 0;
$isTyping = isset($_POST['isTyping']) ? (bool)$_POST['isTyping'] : false;

if (!$convId || !$userId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters']);
    exit;
}

// Store typing status in a simple way (you could use Redis or a database table for better performance)
$typingKey = "typing_status_{$convId}_{$userId}";
$typingData = [
    'userId' => $userId,
    'convId' => $convId,
    'isTyping' => $isTyping,
    'timestamp' => time()
];

// For now, we'll use a simple file-based approach
// In production, consider using Redis or a database table
$typingFile = "../uploads/typing_status.json";
$typingStatuses = [];

if (file_exists($typingFile)) {
    $typingStatuses = json_decode(file_get_contents($typingFile), true) ?: [];
}

// Update typing status
$typingStatuses[$typingKey] = $typingData;

// Clean up old entries (older than 10 seconds)
$currentTime = time();
$typingStatuses = array_filter($typingStatuses, function($status) use ($currentTime) {
    return ($currentTime - $status['timestamp']) < 10;
});

file_put_contents($typingFile, json_encode($typingStatuses));

echo json_encode(['success' => true]);
?> 