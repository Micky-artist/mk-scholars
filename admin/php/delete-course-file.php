<?php
session_start();
include("../dbconnections/connection.php");
include("validateAdminSession.php");

// Set proper headers for JSON response
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Enable output buffering to prevent any accidental output
ob_start();

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['fileId']) || !is_numeric($input['fileId'])) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid file ID']);
    exit;
}

$fileId = (int)$input['fileId'];

try {
    // Get file information from database
    $fileQuery = "SELECT * FROM CourseFiles WHERE fileId = $fileId";
    $fileResult = mysqli_query($conn, $fileQuery);
    
    if (!$fileResult || mysqli_num_rows($fileResult) === 0) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'File not found']);
        exit;
    }
    
    $file = mysqli_fetch_assoc($fileResult);
    $filePath = $file['filePath'];
    
    // Delete file from filesystem
    $fullPath = '../' . $filePath;
    if (file_exists($fullPath)) {
        if (!unlink($fullPath)) {
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Failed to delete file from filesystem']);
            exit;
        }
    }
    
    // Delete file record from database
    $deleteQuery = "DELETE FROM CourseFiles WHERE fileId = $fileId";
    if (!mysqli_query($conn, $deleteQuery)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Failed to delete file record from database']);
        exit;
    }
    
    ob_clean();
    echo json_encode(['success' => true, 'message' => 'File deleted successfully']);
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
