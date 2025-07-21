<?php
session_start();
header('Content-Type: application/json');

// Function to get base URL for file operations
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pathInfo = pathinfo($scriptName);
    $basePath = dirname($pathInfo['dirname']);
    
    // If we're in php folder, go up one level to root
    if (strpos($basePath, '/php') !== false) {
        $basePath = dirname($basePath);
    }
    
    return $protocol . '://' . $host . $basePath;
}

include('../dbconnection/connection.php');

// Check if admin is accessing (for admin panel) or user is accessing (for user panel)
$isAdmin = isset($_SESSION['adminId']);
$isUser = isset($_SESSION['userId']);

if (!$isAdmin && !$isUser) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}
if (!isset($_GET['userid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing userid']);
    exit;
}
$userid = intval($_GET['userid']);
$convid = isset($_GET['convid']) ? intval($_GET['convid']) : null;

$files = [];

if ($convid !== null) {
    // If specific conversation, get documents from that conversation
    $sql = "SELECT DocumentId, UserId, ConvId, DocumentName, OriginalFileName, FilePath, FileType, FileSize, UploadDate, UploadTime FROM Documents WHERE ConvId = ?";
    $params = [$convid];
    $types = 'i';
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $files[] = [
            'id' => $row['DocumentId'],
            'url' => $row['FilePath'],
            'name' => $row['DocumentName'],
            'originalName' => $row['OriginalFileName'],
            'size' => $row['FileSize'],
            'type' => $row['FileType'],
            'uploadDate' => $row['UploadDate'],
            'uploadTime' => $row['UploadTime'],
            'convid' => $row['ConvId'],
            'uploadedByMe' => ($row['UserId'] == $userid),
            'source' => 'documents_table'
        ];
    }
    $stmt->close();
    
    // Also get files shared through messages in this conversation
    $msgSql = "SELECT MessageContent, SentDate, SentTime, UserId FROM Message WHERE ConvId = ? AND MessageContent LIKE './uploads/%' ORDER BY SentDate, SentTime";
    $msgStmt = $conn->prepare($msgSql);
    $msgStmt->bind_param('i', $convid);
    $msgStmt->execute();
    $msgResult = $msgStmt->get_result();
    
    while ($row = $msgResult->fetch_assoc()) {
        $filePath = $row['MessageContent'];
        $fileName = basename($filePath);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Try to get file size if file exists
        $fullPath = __DIR__ . '/../' . $filePath;
        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
        
        $files[] = [
            'id' => 'msg_' . $row['UserId'] . '_' . strtotime($row['SentDate'] . ' ' . $row['SentTime']),
            'url' => $filePath,
            'name' => $fileName,
            'originalName' => $fileName,
            'size' => $fileSize,
            'type' => $fileExt,
            'uploadDate' => $row['SentDate'],
            'uploadTime' => $row['SentTime'],
            'convid' => $convid,
            'uploadedByMe' => ($row['UserId'] == $userid),
            'source' => 'message_table'
        ];
    }
    $msgStmt->close();
    
} else {
    // Get all documents related to the user - comprehensive approach
    // This includes:
    // 1. Documents uploaded by the user
    // 2. Documents shared in conversations where the user participates
    // 3. Documents shared by admins in the user's conversations
    $sql = "SELECT DISTINCT d.DocumentId, d.UserId, d.ConvId, d.DocumentName, d.OriginalFileName, d.FilePath, d.FileType, d.FileSize, d.UploadDate, d.UploadTime 
            FROM Documents d 
            WHERE d.UserId = ? 
            OR d.ConvId IN (
                SELECT ConvId 
                FROM Conversation 
                WHERE UserId = ?
            )
            OR d.ConvId IN (
                SELECT DISTINCT m.ConvId 
                FROM Message m 
                JOIN Conversation c ON m.ConvId = c.ConvId 
                WHERE c.UserId = ? 
                AND m.MessageContent LIKE './uploads/%'
            )";
    $params = [$userid, $userid, $userid];
    $types = 'iii';
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $files[] = [
            'id' => $row['DocumentId'],
            'url' => $row['FilePath'],
            'name' => $row['DocumentName'],
            'originalName' => $row['OriginalFileName'],
            'size' => $row['FileSize'],
            'type' => $row['FileType'],
            'uploadDate' => $row['UploadDate'],
            'uploadTime' => $row['UploadTime'],
            'convid' => $row['ConvId'],
            'uploadedByMe' => ($row['UserId'] == $userid),
            'source' => 'documents_table'
        ];
    }
    $stmt->close();
    
    // Also get files shared through messages in all user's conversations
    $msgSql = "SELECT DISTINCT m.MessageContent, m.SentDate, m.SentTime, m.UserId, m.ConvId 
               FROM Message m 
               JOIN Conversation c ON m.ConvId = c.ConvId 
               WHERE c.UserId = ? 
               AND m.MessageContent LIKE './uploads/%' 
               ORDER BY m.SentDate DESC, m.SentTime DESC";
    $msgStmt = $conn->prepare($msgSql);
    $msgStmt->bind_param('i', $userid);
    $msgStmt->execute();
    $msgResult = $msgStmt->get_result();
    
    while ($row = $msgResult->fetch_assoc()) {
        $filePath = $row['MessageContent'];
        $fileName = basename($filePath);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Try to get file size if file exists
        $fullPath = __DIR__ . '/../' . $filePath;
        $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;
        
        $files[] = [
            'id' => 'msg_' . $row['UserId'] . '_' . strtotime($row['SentDate'] . ' ' . $row['SentTime']),
            'url' => $filePath,
            'name' => $fileName,
            'originalName' => $fileName,
            'size' => $fileSize,
            'type' => $fileExt,
            'uploadDate' => $row['SentDate'],
            'uploadTime' => $row['SentTime'],
            'convid' => $row['ConvId'],
            'uploadedByMe' => ($row['UserId'] == $userid),
            'source' => 'message_table'
        ];
    }
    $msgStmt->close();
}

// Remove duplicates based on URL and sort by date
$uniqueFiles = [];
$seenUrls = [];
foreach ($files as $file) {
    if (!in_array($file['url'], $seenUrls)) {
        $uniqueFiles[] = $file;
        $seenUrls[] = $file['url'];
    }
}

// Sort by upload date (newest first)
usort($uniqueFiles, function($a, $b) {
    $dateA = strtotime($a['uploadDate'] . ' ' . $a['uploadTime']);
    $dateB = strtotime($b['uploadDate'] . ' ' . $b['uploadTime']);
    return $dateB - $dateA;
});

$conn->close();
echo json_encode($uniqueFiles); 