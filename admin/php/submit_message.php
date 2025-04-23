<?php
include('../dbconnection/connection.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$convId   = intval($_POST['ConvId']   ?? 0);
$adminId  = intval($_POST['AdminId']  ?? 0);
$message  = trim($_POST['message']    ?? '');
$filePath = '';

// Handle file upload
if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $uploadsDir = __DIR__ . '/uploads';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }
    $filename   = time() . '_' . basename($_FILES['file']['name']);
    $targetPath = $uploadsDir . '/' . $filename;
    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        $filePath = 'uploads/' . $filename;
    }
}

$content = $filePath ?: $message;
if ($content === '') {
    exit;
}

// Insert the new message
$stmt = $conn->prepare("INSERT INTO Message 
    (ConvId, AdminId, MessageContent, SentDate, SentTime, MessageStatus)
  VALUES
    (?,      ?,       ?,              CURDATE(), CURTIME(),      0)
");
$stmt->bind_param('iis', $convId, $adminId, $content);
$stmt->execute();
$stmt->close();
