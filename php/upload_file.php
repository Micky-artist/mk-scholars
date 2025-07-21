<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: ../login.php');
    exit;
}

if (!isset($_FILES['file']) || !isset($_POST['username']) || !isset($_POST['userid'])) {
    echo "<script>window.location.href = '../conversations.php?upload_error=Missing parameters';</script>";
    exit;
}

$username = preg_replace('/[^a-zA-Z0-9_\-]/', '', $_POST['username']);
$userid = intval($_POST['userid']);
$convId = isset($_POST['convid']) && !empty($_POST['convid']) ? intval($_POST['convid']) : null;
$dir = __DIR__ . '/../uploads/' . $username . '_' . $userid . '/';
$webDir = './uploads/' . $username . '_' . $userid . '/';

// Ensure directory exists and has proper permissions
if (!is_dir($dir)) {
    if (!mkdir($dir, 0755, true)) {
        $error = "Failed to create directory: $dir. Error: " . error_get_last()['message'];
        echo "<script>window.location.href = '../conversations.php?upload_error=" . urlencode($error) . "';</script>";
        exit;
    }
    chmod($dir, 0755);
}





$originalName = basename($_FILES['file']['name']);
$target = $dir . $originalName;
$webPath = $webDir . $originalName;

if (move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
    chmod($target, 0644);
    $fileSize = filesize($target);
    $fileType = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $uploadDate = date('Y-m-d');
    $uploadTime = date('H:i:s');
    
    // Insert into Documents table
    include('../dbconnection/connection.php');
    
    // Test database connection
    if (!$conn) {
        echo "<script>window.location.href = '../conversations.php?upload_error=Database connection failed';</script>";
        exit;
    }
    
    // Handle ConvId properly - skip database insert if ConvId is null since it's a foreign key
    if ($convId === null) {
        // File uploaded successfully but no conversation ID, so just redirect
        echo "<script>window.location.href = '../conversations.php?upload_success=1';</script>";
        exit;
    }
    
    // Debug: Log the values being inserted
    error_log("Inserting file - UserId: $userid, ConvId: $convId, Name: $originalName, Path: $webPath, Type: $fileType, Size: $fileSize");
    
    // Match the database schema exactly
    $sql = "INSERT INTO Documents (UserId, ConvId, DocumentName, OriginalFileName, FilePath, FileType, FileSize, DocumentType, Description, UploadDate, UploadTime, Status, AdminComments, ReviewedBy, ReviewedDate) VALUES (?, ?, ?, ?, ?, ?, ?, '', NULL, ?, ?, 'pending', NULL, NULL, NULL)";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "<script>window.location.href = '../conversations.php?upload_error=Database prepare failed';</script>";
        exit;
    }
    
    $stmt->bind_param("iisssisss", $userid, $convId, $originalName, $originalName, $webPath, $fileType, $fileSize, $uploadDate, $uploadTime);
    
    if (!$stmt->execute()) {
        $error = "Database insert failed: " . $stmt->error;
        $stmt->close();
        $conn->close();
        echo "<script>window.location.href = '../conversations.php?upload_error=" . urlencode($error) . "';</script>";
        exit;
    } else {
        // Debug: Log successful insert
        error_log("File uploaded successfully: $originalName, Size: $fileSize, Path: $webPath");
        
        // Also insert a message to show the file in chat
        $messageSql = "INSERT INTO Message (UserId, senderId, AdminId, ConvId, MessageContent, SentDate, SentTime, MessageStatus) 
                       VALUES (?, ?, 0, ?, ?, ?, ?, 0)";
        $messageStmt = $conn->prepare($messageSql);
        if ($messageStmt) {
            $messageStmt->bind_param("iissss", $userid, $userid, $convId, $webPath, $uploadDate, $uploadTime);
            $messageStmt->execute();
            $messageStmt->close();
        }
    }
    
    $stmt->close();
    $conn->close();
    
    // Use JavaScript redirect instead of header redirect to avoid server errors
    echo "<script>window.location.href = '../conversations.php?upload_success=1';</script>";
    exit;
} else {
    echo "<script>window.location.href = '../conversations.php?upload_error=Failed to upload file';</script>";
    exit;
} 