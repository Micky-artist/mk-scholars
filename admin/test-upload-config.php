<?php
// Test PHP upload configuration
echo "<h2>PHP Upload Configuration</h2>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>max_file_uploads</td><td>" . ini_get('max_file_uploads') . "</td></tr>";
echo "<tr><td>max_execution_time</td><td>" . ini_get('max_execution_time') . "</td></tr>";
echo "<tr><td>memory_limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td>file_uploads</td><td>" . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</td></tr>";
echo "<tr><td>upload_tmp_dir</td><td>" . (ini_get('upload_tmp_dir') ?: 'Default') . "</td></tr>";
echo "</table>";

echo "<h2>Directory Permissions</h2>";
$uploadDir = './uploads/courses/images/';
echo "<p>Upload Directory: " . realpath($uploadDir) . "</p>";
echo "<p>Directory Exists: " . (file_exists($uploadDir) ? 'Yes' : 'No') . "</p>";
echo "<p>Directory Writable: " . (is_writable($uploadDir) ? 'Yes' : 'No') . "</p>";
echo "<p>Directory Permissions: " . substr(sprintf('%o', fileperms($uploadDir)), -4) . "</p>";

echo "<h2>Test Upload Form</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
    echo "<h3>Upload Test Results</h3>";
    $file = $_FILES['test_file'];
    echo "<p>File Name: " . $file['name'] . "</p>";
    echo "<p>File Size: " . $file['size'] . " bytes</p>";
    echo "<p>File Type: " . $file['type'] . "</p>";
    echo "<p>Upload Error: " . $file['error'] . "</p>";
    echo "<p>Temporary Name: " . $file['tmp_name'] . "</p>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $uploadPath = $uploadDir . 'test_' . time() . '_' . $file['name'];
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            echo "<p style='color: green;'>Upload successful! File saved to: " . $uploadPath . "</p>";
            echo "<p>File exists: " . (file_exists($uploadPath) ? 'Yes' : 'No') . "</p>";
            echo "<p>File size: " . filesize($uploadPath) . " bytes</p>";
        } else {
            echo "<p style='color: red;'>Upload failed!</p>";
        }
    } else {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File too large (server limit)',
            UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
            UPLOAD_ERR_PARTIAL => 'File partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        ];
        echo "<p style='color: red;'>Upload error: " . ($errorMessages[$file['error']] ?? 'Unknown error') . "</p>";
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <p>Select a test file to upload:</p>
    <input type="file" name="test_file" required>
    <br><br>
    <input type="submit" value="Test Upload">
</form>
