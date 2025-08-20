<?php
// Simple test to isolate home.php issues
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home.php Test - MK Scholars</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test { padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid; }
        .success { background-color: #d4edda; color: #155724; border-left-color: #28a745; }
        .error { background-color: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .info { background-color: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }
        h1 { color: #333; text-align: center; }
        .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Home.php Test</h1>
        
        <h2>Step 1: Test Basic PHP</h2>
        <div class="test info">
            <strong>PHP Status:</strong> ✅ Working<br>
            <strong>PHP Version:</strong> <?php echo phpversion(); ?><br>
            <strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?>
        </div>

        <h2>Step 2: Test File Includes</h2>
        <?php
        $filesToTest = [
            './dbconnection/connection.php' => 'Database Connection',
            './partials/head.php' => 'Head Partial',
            './partials/navigation.php' => 'Navigation Partial'
        ];
        
        foreach ($filesToTest as $file => $description) {
            if (file_exists($file)) {
                echo '<div class="test success">✅ ' . $description . ' - File exists</div>';
            } else {
                echo '<div class="test error">❌ ' . $description . ' - File missing</div>';
            }
        }
        ?>

        <h2>Step 3: Test Database Connection</h2>
        <?php
        try {
            include_once("./dbconnection/connection.php");
            if (isset($conn) && $conn) {
                if (mysqli_ping($conn)) {
                    echo '<div class="test success">✅ Database connection: SUCCESS</div>';
                    echo '<div class="test info">Connection Type: ' . ($connectionType ?? 'Unknown') . '</div>';
                } else {
                    echo '<div class="test error">❌ Database connection: PING FAILED</div>';
                }
            } else {
                echo '<div class="test error">❌ Database connection: NO CONNECTION</div>';
            }
        } catch (Exception $e) {
            echo '<div class="test error">❌ Database connection: EXCEPTION - ' . $e->getMessage() . '</div>';
        }
        ?>

        <h2>Step 4: Test Partial Files</h2>
        <?php
        // Test head.php
        try {
            ob_start();
            include("./partials/head.php");
            $headOutput = ob_get_clean();
            if (strpos($headOutput, '<head>') !== false) {
                echo '<div class="test success">✅ Head partial: SUCCESS</div>';
            } else {
                echo '<div class="test error">❌ Head partial: INVALID OUTPUT</div>';
            }
        } catch (Exception $e) {
            echo '<div class="test error">❌ Head partial: EXCEPTION - ' . $e->getMessage() . '</div>';
        }
        
        // Test navigation.php
        try {
            ob_start();
            include("./partials/navigation.php");
            $navOutput = ob_get_clean();
            if (strlen($navOutput) > 0) {
                echo '<div class="test success">✅ Navigation partial: SUCCESS</div>';
            } else {
                echo '<div class="test error">❌ Navigation partial: EMPTY OUTPUT</div>';
            }
        } catch (Exception $e) {
            echo '<div class="test error">❌ Navigation partial: EXCEPTION - ' . $e->getMessage() . '</div>';
        }
        ?>

        <h2>Step 5: Test Full Home.php</h2>
        <div class="test info">
            <strong>Next Step:</strong> If all tests above pass, try accessing the full home.php<br>
            <a href="./home.php" class="button">Test Full Home.php</a>
        </div>

        <h2>🔍 Debug Information</h2>
        <div class="test info">
            <strong>Server Variables:</strong><br>
            HTTP_HOST: <?php echo $_SERVER['HTTP_HOST'] ?? 'Not set'; ?><br>
            SERVER_NAME: <?php echo $_SERVER['SERVER_NAME'] ?? 'Not set'; ?><br>
            SERVER_PORT: <?php echo $_SERVER['SERVER_PORT'] ?? 'Not set'; ?><br>
            REQUEST_URI: <?php echo $_SERVER['REQUEST_URI'] ?? 'Not set'; ?><br>
            SCRIPT_NAME: <?php echo $_SERVER['SCRIPT_NAME'] ?? 'Not set'; ?>
        </div>

        <hr>
        <div style="text-align: center;">
            <a href="./" class="button">← Back to Main Site</a>
            <a href="./db-test.php" class="button">Database Test</a>
            <a href="./server-status.php" class="button">Server Status</a>
        </div>
    </div>
</body>
</html>
