<?php
session_start();

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type for JSON response
header('Content-Type: application/json');

// Set Rwanda timezone
date_default_timezone_set('Africa/Kigali');

// Include database connection with error handling
try {
    include('../dbconnection/connection.php');
    
    // Check if connection exists
    if (!isset($conn) || !$conn) {
        throw new Exception('Database connection failed');
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
    exit;
}

// Validate session
if (!isset($_SESSION['userId'])) {
    echo json_encode(['error' => 'User session not found']);
    exit;
}

// Validate POST data
if (!isset($_POST['UserId'], $_POST['ConvId'])) {
    echo json_encode(['error' => 'Missing required data (UserId, ConvId)']);
    exit;
}

$UserId = intval($_POST['UserId']);
$ConvId = intval($_POST['ConvId']);
$AdminId = intval($_POST['AdminId'] ?? 0);
$message = trim($_POST['message'] ?? '');

// Validate UserId matches session
if ($UserId != $_SESSION['userId']) {
    echo json_encode(['error' => 'User ID mismatch']);
    exit;
}

if (empty($message)) {
    echo json_encode(['error' => 'Message cannot be empty']);
    exit;
}

// Validate conversation exists and user has access
$checkConvSql = "SELECT ConvId FROM Conversation WHERE ConvId = ? AND UserId = ?";
$checkStmt = $conn->prepare($checkConvSql);
$checkStmt->bind_param("ii", $ConvId, $UserId);
$checkStmt->execute();
$convResult = $checkStmt->get_result();

if ($convResult->num_rows === 0) {
    echo json_encode(['error' => 'Invalid conversation or access denied']);
    exit;
}
$checkStmt->close();

// Use Rwanda timezone for message date and time
$SentDate = date('Y-m-d');
$SentTime = date('H:i:s');

// Handle file upload
$uploadPath = '';
if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
    $targetDir = "../uploads/";
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

    $filename = basename($_FILES["file"]["name"]);
    $targetFile = $targetDir . time() . "_" . $filename;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
        $uploadPath = str_replace("../", "./", $targetFile);
    }
}

// Handle file upload vs text message
if ($uploadPath) {
    $finalMessage = $uploadPath; // Don't sanitize file paths
} else {
    $finalMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); // Sanitize text messages
}

// Use prepared statement for security
$sql = "INSERT INTO Message (UserId, senderId, AdminId, ConvId, MessageContent, SentDate, SentTime, MessageStatus)
        VALUES (?, ?, ?, ?, ?, ?, ?, 0)";

try {
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }
    
    $stmt->bind_param("iiissss", $UserId, $UserId, $AdminId, $ConvId, $finalMessage, $SentDate, $SentTime);
    
    if ($stmt->execute()) {
        $messageId = $conn->insert_id;
        
        // Log successful message
        error_log("Message sent successfully - User: $UserId, Conv: $ConvId, Message ID: $messageId");
        
        echo json_encode([
            'success' => true,
            'message' => 'Message sent successfully',
            'messageId' => $messageId,
            'sentDate' => $SentDate,
            'sentTime' => date('h:i A', strtotime($SentTime)),
            'messageContent' => $finalMessage
        ]);
    } else {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Message send error - User: $UserId, Conv: $ConvId, Error: " . $e->getMessage());
    echo json_encode([
        'error' => 'Failed to send message: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
