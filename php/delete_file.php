<?php
session_start();
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

// Check if this is an admin deletion
$isAdminDelete = isset($_POST['adminDelete']) && $_POST['adminDelete'] === 'true';

if ($isAdminDelete) {
    // Simple admin check - just verify admin session exists
    if (!isset($_SESSION['adminId']) || !isset($_SESSION['AdminName'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Admin not logged in or session expired']);
        exit;
    }
    
    // Include admin database connection
    include('../admin/dbconnections/connection.php');
    
    // Function to get base URL for admin operations
    function getAdminBaseUrl() {
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
} else {
    // Regular user deletion - check if user is logged in
    if (!isset($_SESSION['userId'])) {
        http_response_code(401);
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
}

if (!isset($_POST['file'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing file parameter']);
    exit;
}

$file = $_POST['file'];
$docId = isset($_POST['docId']) ? intval($_POST['docId']) : null;

$uploadsDir = realpath(__DIR__ . '/../uploads');
$target = realpath(__DIR__ . '/../' . ltrim($file, './'));

if (!$target || strpos($target, $uploadsDir) !== 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid file path']);
    exit;
}

if (file_exists($target)) {
    if (unlink($target)) {
        // If we have a document ID, also remove from database
        if ($docId && is_numeric($docId)) {
            // Use the appropriate database connection based on who is deleting
            if ($isAdminDelete) {
                // Admin connection is already included above
                $sql = "DELETE FROM Documents WHERE DocumentId = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $docId);
                $stmt->execute();
                $stmt->close();
            } else {
                // User connection
                include('../dbconnection/connection.php');
                $sql = "DELETE FROM Documents WHERE DocumentId = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $docId);
                $stmt->execute();
                $stmt->close();
                $conn->close();
            }
        }
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