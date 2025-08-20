<?php
// Path checker to verify file locations and working directory
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Path Checker - MK Scholars</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>Path and Directory Checker</h1>
    
    <h2>Current Working Directory</h2>
    <div class="info">
        <strong>Current Directory:</strong> <?php echo getcwd(); ?><br>
        <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?><br>
        <strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME'] ?? 'Unknown'; ?><br>
        <strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? 'Unknown'; ?>
    </div>

    <h2>File Paths</h2>
    <?php
    $filesToCheck = [
        'index.php' => 'Main index file',
        'home.php' => 'Home page',
        'dbconnection/connection.php' => 'Database connection',
        'css/style.css' => 'Main stylesheet',
        'images/logo/fullLogo.png' => 'Logo image',
        'partials/head.php' => 'Header partial',
        '.htaccess' => 'Apache configuration'
    ];
    
    foreach ($filesToCheck as $file => $description) {
        $fullPath = getcwd() . '/' . $file;
        if (file_exists($file)) {
            $size = filesize($file);
            $perms = substr(sprintf('%o', fileperms($file)), -4);
            echo '<div class="success">✓ ' . $description . ' (' . $file . ')<br>';
            echo '&nbsp;&nbsp;&nbsp;&nbsp;Size: ' . number_format($size) . ' bytes, Permissions: ' . $perms . '</div>';
        } else {
            echo '<div class="error">✗ ' . $description . ' (' . $file . ') - MISSING</div>';
        }
    }
    ?>

    <h2>Directory Structure</h2>
    <?php
    $dirsToCheck = [
        'css' => 'CSS directory',
        'js' => 'JavaScript directory',
        'images' => 'Images directory',
        'dbconnection' => 'Database connection directory',
        'partials' => 'Partials directory',
        'admin' => 'Admin directory'
    ];
    
    foreach ($dirsToCheck as $dir => $description) {
        if (is_dir($dir)) {
            $perms = substr(sprintf('%o', fileperms($dir)), -4);
            echo '<div class="success">✓ ' . $description . ' (' . $dir . '/) - Permissions: ' . $perms . '</div>';
        } else {
            echo '<div class="error">✗ ' . $description . ' (' . $dir . '/) - MISSING</div>';
        }
    }
    ?>

    <h2>Include Path Test</h2>
    <?php
    $includePaths = [
        './dbconnection/connection.php',
        './partials/head.php',
        './css/style.css'
    ];
    
    foreach ($includePaths as $path) {
        if (file_exists($path)) {
            echo '<div class="success">✓ Include path works: ' . $path . '</div>';
        } else {
            echo '<div class="error">✗ Include path fails: ' . $path . '</div>';
        }
    }
    ?>

    <h2>Server Information</h2>
    <div class="info">
        <strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
        <strong>Server Protocol:</strong> <?php echo $_SERVER['SERVER_PROTOCOL'] ?? 'Unknown'; ?><br>
        <strong>Server Port:</strong> <?php echo $_SERVER['SERVER_PORT'] ?? 'Unknown'; ?><br>
        <strong>HTTP Host:</strong> <?php echo $_SERVER['HTTP_HOST'] ?? 'Unknown'; ?><br>
        <strong>HTTPS:</strong> <?php echo isset($_SERVER['HTTPS']) ? 'Yes' : 'No'; ?>
    </div>

    <hr>
    <p><a href="./">← Back to main site</a> | <a href="./server-status.php">Server Status</a></p>
</body>
</html>
