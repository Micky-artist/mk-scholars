<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['testFile'])) {
    $file = $_FILES['testFile'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadDir = './uploads/courses/images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = 'test_' . time() . '_' . uniqid() . '.jpg';
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            if (file_exists($uploadPath) && filesize($uploadPath) === $file['size']) {
                chmod($uploadPath, 0644);
                $message = 'File uploaded successfully! Path: ' . $uploadPath;
                $messageType = 'success';
            } else {
                $message = 'File upload verification failed.';
                $messageType = 'error';
            }
        } else {
            $message = 'Error moving file. Check permissions.';
            $messageType = 'error';
        }
    } else {
        $message = 'Upload error: ' . $file['error'];
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>File Upload Test</h2>
        
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="testFile" class="form-label">Select Image File</label>
                <input type="file" class="form-control" id="testFile" name="testFile" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload Test File</button>
        </form>
        
        <div class="mt-4">
            <h4>Upload Directory Status</h4>
            <p><strong>Directory exists:</strong> <?php echo file_exists('./uploads/courses/images/') ? 'Yes' : 'No'; ?></p>
            <p><strong>Directory writable:</strong> <?php echo is_writable('./uploads/courses/images/') ? 'Yes' : 'No'; ?></p>
            <p><strong>PHP Upload Max Filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
            <p><strong>PHP Post Max Size:</strong> <?php echo ini_get('post_max_size'); ?></p>
        </div>
    </div>
</body>
</html>
