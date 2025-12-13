<?php
// Start output buffering to prevent any accidental output
ob_start();

session_start();
include("../dbconnections/connection.php");
include("./validateAdminSession.php");

// Set proper headers
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

// Enable error reporting for debugging but don't display errors
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Increase upload limits for this endpoint (subject to server policy)
@ini_set('upload_max_filesize', '50M');
@ini_set('post_max_size', '55M');
@ini_set('memory_limit', '256M');
@ini_set('max_execution_time', '300');
@ini_set('max_input_time', '300');

// Log upload attempts
error_log("Upload attempt - Course ID: $courseId, File Type: $fileType, File Name: " . ($_FILES['file']['name'] ?? 'N/A'));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$courseId = isset($_POST['courseId']) ? (int)$_POST['courseId'] : 0;
$fileType = isset($_POST['fileType']) ? $_POST['fileType'] : '';

if (!$courseId) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Course ID is required']);
    exit;
}

// Validate course access
if (!hasCourseAccess($courseId)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'You do not have access to this course.']);
    exit;
}

if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    $errorMessages = [
        UPLOAD_ERR_INI_SIZE => 'File too large (server limit)',
        UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
    ];
    
    $errorCode = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
    $errorMessage = $errorMessages[$errorCode] ?? 'Unknown upload error';
    
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $errorMessage]);
    exit;
}

$file = $_FILES['file'];
$fileName = $file['name'];
$fileSize = $file['size'];
$fileTmpName = $file['tmp_name'];

// Determine file type and upload directory
$allowedTypes = [];
$uploadDir = '';

// Auto-detect file type based on MIME type and extension
$fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$mimeType = mime_content_type($fileTmpName);

// Determine file type based on MIME type and extension
if (strpos($mimeType, 'image/') === 0 || in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
    $fileType = 'image';
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $uploadDir = '../uploads/courses/images/';
} elseif (strpos($mimeType, 'video/') === 0 || in_array($fileExtension, ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'])) {
    $fileType = 'video';
    $allowedTypes = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'webm'];
    $uploadDir = '../uploads/courses/videos/';
} elseif (strpos($mimeType, 'audio/') === 0 || in_array($fileExtension, ['mp3', 'wav', 'ogg', 'm4a', 'aac'])) {
    $fileType = 'audio';
    $allowedTypes = ['mp3', 'wav', 'ogg', 'm4a', 'aac'];
    $uploadDir = '../uploads/courses/audio/';
} elseif (in_array($fileExtension, ['pdf', 'doc', 'docx', 'txt', 'rtf', 'ppt', 'pptx', 'xls', 'xlsx'])) {
    $fileType = 'document';
    $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'ppt', 'pptx', 'xls', 'xlsx'];
    $uploadDir = '../uploads/courses/documents/';
} else {
    // Default to document type for unknown files
    $fileType = 'document';
    $allowedTypes = ['pdf', 'doc', 'docx', 'txt', 'rtf', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'avi', 'mov', 'mp3', 'wav'];
    $uploadDir = '../uploads/courses/files/';
}

// Create upload directory if it doesn't exist
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Validate file extension
if (!in_array($fileExtension, $allowedTypes)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed types: ' . implode(', ', $allowedTypes)]);
    exit;
}

// Validate file size (50MB max)
$maxSize = 50 * 1024 * 1024; // 50MB
if ($fileSize > $maxSize) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'File size too large. Maximum size is 50MB']);
    exit;
}

// Generate unique filename
$uniqueFileName = 'course_' . $courseId . '_' . time() . '_' . uniqid() . '.' . $fileExtension;
$uploadPath = $uploadDir . $uniqueFileName;

// Validate file is actually readable
if (!is_readable($fileTmpName)) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Uploaded file is not readable']);
    exit;
}

// Check if file is empty
if ($fileSize === 0) {
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Uploaded file is empty']);
    exit;
}

// Move uploaded file
if (move_uploaded_file($fileTmpName, $uploadPath)) {
    // Verify the file was moved correctly
    if (!file_exists($uploadPath)) {
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'File upload verification failed - file does not exist']);
        exit;
    }
    
    $uploadedFileSize = filesize($uploadPath);
    if ($uploadedFileSize !== $fileSize) {
        unlink($uploadPath);
        ob_clean();
        echo json_encode(['success' => false, 'message' => "File upload verification failed - size mismatch. Original: $fileSize, Uploaded: $uploadedFileSize"]);
        exit;
    }
    
    // Set proper file permissions
    chmod($uploadPath, 0644);
    
    // Save file info to database
    $relativePath = 'uploads/courses/' . basename($uploadDir) . '/' . $uniqueFileName;
    $fileDescription = pathinfo($fileName, PATHINFO_FILENAME);
    
    $insertQuery = "INSERT INTO CourseFiles (courseId, filePath, fileName, fileType, fileSize, fileDescription, uploadDate, uploadTime, uploadedBy) VALUES (?, ?, ?, ?, ?, ?, CURDATE(), CURTIME(), ?)";
    
    $stmt = mysqli_prepare($conn, $insertQuery);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssisi", $courseId, $relativePath, $fileName, $fileType, $fileSize, $fileDescription, $_SESSION['adminId']);
        
        if (mysqli_stmt_execute($stmt)) {
            $fileId = mysqli_insert_id($conn);
            error_log("File uploaded successfully - ID: $fileId, Path: $relativePath");
            ob_clean();
            echo json_encode([
                'success' => true, 
                'message' => 'File uploaded successfully',
                'fileId' => $fileId,
                'fileName' => $fileName,
                'filePath' => $relativePath,
                'fileSize' => $fileSize,
                'fileType' => $fileType,
                'fullPath' => $uploadPath
            ]);
        } else {
            // If database insert fails, remove the uploaded file
            unlink($uploadPath);
            error_log("Database insert failed: " . mysqli_error($conn));
            ob_clean();
            echo json_encode(['success' => false, 'message' => 'Error saving file info to database: ' . mysqli_error($conn)]);
        }
        mysqli_stmt_close($stmt);
    } else {
        // If database prepare fails, remove the uploaded file
        unlink($uploadPath);
        error_log("Database prepare failed: " . mysqli_error($conn));
        ob_clean();
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
} else {
    error_log("Failed to move uploaded file from $fileTmpName to $uploadPath");
    ob_clean();
    echo json_encode(['success' => false, 'message' => 'Error moving uploaded file. Check directory permissions.']);
}
?>
