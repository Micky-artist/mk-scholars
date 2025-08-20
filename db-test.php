<?php
// Database connection test file
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test - MK Scholars</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid; }
        .success { background-color: #d4edda; color: #155724; border-left-color: #28a745; }
        .error { background-color: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .warning { background-color: #fff3cd; color: #856404; border-left-color: #ffc107; }
        .info { background-color: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }
        .debug { background-color: #f8f9fa; color: #495057; border: 1px solid #dee2e6; padding: 10px; margin: 10px 0; border-radius: 5px; font-family: monospace; }
        h1 { color: #333; text-align: center; }
        h2 { color: #555; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .button:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔌 Database Connection Test</h1>
        
        <h2>🌐 Environment Detection</h2>
        <?php
        // Include the connection file to test
        include_once("./dbconnection/connection.php");
        
        // Get server information
        $host = $_SERVER['HTTP_HOST'] ?? 'Unknown';
        $serverName = $_SERVER['SERVER_NAME'] ?? 'Unknown';
        $port = $_SERVER['SERVER_PORT'] ?? 'Unknown';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown';
        
        // Determine environment
        $isLocal = false;
        $localIndicators = ['localhost', '127.0.0.1', '::1', 'xampp', 'wamp', 'mamp', 'local', 'dev', 'development'];
        
        foreach ($localIndicators as $indicator) {
            if (stripos($host, $indicator) !== false || stripos($serverName, $indicator) !== false) {
                $isLocal = true;
                break;
            }
        }
        
        if ($port == '3306' || $port == '8080' || $port == '8000') {
            $isLocal = true;
        }
        
        $environment = $isLocal ? 'LOCAL DEVELOPMENT' : 'PRODUCTION/ONLINE';
        $environmentClass = $isLocal ? 'warning' : 'success';
        ?>
        
        <div class="status <?php echo $environmentClass; ?>">
            <strong>Environment:</strong> <?php echo $environment; ?><br>
            <strong>Host:</strong> <?php echo $host; ?><br>
            <strong>Server Name:</strong> <?php echo $serverName; ?><br>
            <strong>Port:</strong> <?php echo $port; ?><br>
            <strong>Document Root:</strong> <?php echo $documentRoot; ?>
        </div>

        <h2>🗄️ Database Connection Status</h2>
        <?php
        if (isset($conn) && $conn) {
            // Test database connection
            if (mysqli_ping($conn)) {
                $dbInfo = mysqli_get_server_info($conn);
                $dbHost = mysqli_get_host_info($conn);
                
                echo '<div class="status success">';
                echo '<strong>✅ Database Connected Successfully!</strong><br>';
                echo '<strong>Database Server:</strong> ' . $dbInfo . '<br>';
                echo '<strong>Connection Info:</strong> ' . $dbHost . '<br>';
                echo '<strong>Connection Type:</strong> ' . ($connectionType ?? 'Unknown') . '<br>';
                echo '</div>';
                
                // Test a simple query
                $result = mysqli_query($conn, "SELECT 1 as test");
                if ($result) {
                    echo '<div class="status success">✅ Database query test: SUCCESS</div>';
                    mysqli_free_result($result);
                } else {
                    echo '<div class="status error">❌ Database query test: FAILED</div>';
                }
            } else {
                echo '<div class="status error">❌ Database connection lost</div>';
            }
        } else {
            echo '<div class="status error">❌ Database connection failed</div>';
        }
        ?>

        <h2>🔧 Connection Details</h2>
        <div class="debug">
            <?php
            if (isset($connectionType)) {
                echo "Connection Type: " . $connectionType . "\n";
                
                if ($connectionType == 'PRODUCTION') {
                    echo "Production Database: u722035022_mkscholars\n";
                    echo "Production Host: localhost\n";
                    echo "Production User: u722035022_mkscholars\n";
                } else {
                    echo "Local Database: mkscholars\n";
                    echo "Local Host: localhost\n";
                    echo "Local User: root\n";
                }
            }
            ?>
        </div>

        <h2>🧪 Test Different Connections</h2>
        <div class="status info">
            <strong>Test Links:</strong><br>
            <a href="?debug=db" class="button">Show Database Debug Info</a>
            <a href="?test=local" class="button">Test Local Connection</a>
            <a href="?test=production" class="button">Test Production Connection</a>
            <a href="./" class="button">Back to Main Site</a>
        </div>

        <?php
        // Show debug info if requested
        if (isset($_GET['debug']) && $_GET['debug'] == 'db') {
            echo '<h2>🐛 Debug Information</h2>';
            echo '<div class="debug">';
            echo '<strong>Server Variables:</strong><br>';
            echo 'HTTP_HOST: ' . ($_SERVER['HTTP_HOST'] ?? 'Not set') . '<br>';
            echo 'SERVER_NAME: ' . ($_SERVER['SERVER_NAME'] ?? 'Not set') . '<br>';
            echo 'SERVER_PORT: ' . ($_SERVER['SERVER_PORT'] ?? 'Not set') . '<br>';
            echo 'REQUEST_URI: ' . ($_SERVER['REQUEST_URI'] ?? 'Not set') . '<br>';
            echo 'SCRIPT_NAME: ' . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . '<br>';
            echo 'DOCUMENT_ROOT: ' . ($_SERVER['DOCUMENT_ROOT'] ?? 'Not set') . '<br>';
            echo '</div>';
        }
        ?>

        <h2>📋 Troubleshooting Tips</h2>
        <div class="status info">
            <strong>If you're getting connection errors:</strong><br>
            • Make sure your database server is running<br>
            • Verify database credentials are correct<br>
            • Check if the database exists<br>
            • Ensure proper file permissions<br>
            • Check server error logs
        </div>

        <hr>
        <p style="text-align: center; color: #666;">
            <strong>MK Scholars Database Test Tool</strong><br>
            Use this tool to verify your database connections are working correctly.
        </p>
    </div>
</body>
</html>
