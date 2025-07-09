<?php
header('Content-Type: application/json');
if (!isset($_POST['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing file parameter']);
    exit;
}
$file = $_POST['file'];
$uploadsDir = realpath(__DIR__ . '/../uploads');
$target = realpath(__DIR__ . '/../' . ltrim($file, './'));
if (!$target || strpos($target, $uploadsDir) !== 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file path']);
    exit;
}
if (file_exists($target)) {
    if (unlink($target)) {
        echo json_encode(['success' => true]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete file']);
        exit;
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'File not found']);
    exit;
} 