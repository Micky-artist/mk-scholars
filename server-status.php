<?php
// Server status checker
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Status - MK Scholars</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; border-left: 4px solid; }
        .success { background-color: #d4edda; color: #155724; border-left-color: #28a745; }
        .error { background-color: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .warning { background-color: #fff3cd; color: #856404; border-left-color: #ffc107; }
        .info { background-color: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }
        .production { background-color: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }
        .local { background-color: #fff3cd; color: #856404; border-left-color: #ffc107; }
        h1 { color: #333; text-align: center; }
        h2 { color: #555; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .button { display: inline-block; padding: 8px 16px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; font-size: 14px; }
        .button:hover { background: #0056b3; }
        .environment-badge { display: inline-block; padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; margin-left: 10px; }
        .env-production { background: #28a745; color: white; }
        .env-local { background: #ffc107; color: #212529; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🖥️ Server Status Check</h1>
        
        <h2>🌐 Environment Detection</h2>
        <?php
        // Determine environment
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $port = $_SERVER['SERVER_PORT'] ?? '80';
        
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
        $environmentClass = $isLocal ? 'local' : 'production';
        $envBadgeClass = $isLocal ? 'env-local' : 'env-production';
        ?>
        
        <div class="status <?php echo $environmentClass; ?>">
            <strong>Environment:</strong> <?php echo $environment; ?>
            <span class="environment-badge <?php echo $envBadgeClass; ?>"><?php echo $isLocal ? 'LOCAL' : 'PRODUCTION'; ?></span><br>
            <strong>Host:</strong> <?php echo $host; ?><br>
            <strong>Server Name:</strong> <?php echo $serverName; ?><br>
            <strong>Port:</strong> <?php echo $port; ?><br>
            <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?>
        </div>

        <h2>📊 PHP Information</h2>
        <div class="status info">
            <strong>PHP Version:</strong> <?php echo phpversion(); ?><br>
            <strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?><br>
            <strong>Current Time:</strong> <?php echo date('Y-m-d H:i:s'); ?><br>
            <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?><br>
            <strong>Script Path:</strong> <?php echo __FILE__; ?>
        </div>

        <h2>🗄️ Database Connection</h2>
        <?php
        try {
            include_once("./dbconnection/connection.php");
            if ($conn && mysqli_ping($conn)) {
                $dbInfo = mysqli_get_server_info($conn);
                $dbHost = mysqli_get_host_info($conn);
                
                echo '<div class="status success">';
                echo '<strong>Database connection: SUCCESS</strong><br>';
                echo '<strong>Database Server:</strong> ' . $dbInfo . '<br>';
                echo '<strong>Connection Info:</strong> ' . $dbHost . '<br>';
                echo '<strong>Connection Type:</strong> ' . ($connectionType ?? 'Unknown') . '<br>';
                echo '<strong>Database:</strong> ' . ($isLocal ? 'mkscholars (local)' : 'u722035022_mkscholars (production)') . '<br>';
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
                echo '<div class="status error">Database connection: FAILED</div>';
            }
        } catch (Exception $e) {
            echo '<div class="status error">Database connection: ERROR - ' . $e->getMessage() . '</div>';
        }
        ?>

        <h2>📁 File System</h2>
        <?php
        $criticalFiles = [
            'index.php' => 'Main index file',
            'home.php' => 'Home page',
            'dbconnection/connection.php' => 'Database connection',
            'css/style.css' => 'Main stylesheet',
            'images/logo/fullLogo.png' => 'Logo image',
            'admin/dbconnections/connection.php' => 'Admin database connection'
        ];
        
        foreach ($criticalFiles as $file => $description) {
            if (file_exists($file)) {
                $size = filesize($file);
                $perms = substr(sprintf('%o', fileperms($file)), -4);
                echo '<div class="status success">✓ ' . $description . ' (' . $file . ')<br>';
                echo '&nbsp;&nbsp;&nbsp;&nbsp;Size: ' . number_format($size) . ' bytes, Permissions: ' . $perms . '</div>';
            } else {
                echo '<div class="status error">✗ ' . $description . ' (' . $file . ') - MISSING</div>';
            }
        }
        ?>

        <h2>📂 Directory Structure</h2>
        <?php
        $dirsToCheck = [
            'css' => 'CSS directory',
            'js' => 'JavaScript directory',
            'images' => 'Images directory',
            'dbconnection' => 'Database connection directory',
            'partials' => 'Partials directory',
            'admin' => 'Admin directory',
            'admin/dbconnections' => 'Admin database connections'
        ];
        
        foreach ($dirsToCheck as $dir => $description) {
            if (is_dir($dir)) {
                $perms = substr(sprintf('%o', fileperms($dir)), -4);
                echo '<div class="status success">✓ ' . $description . ' (' . $dir . '/) - Permissions: ' . $perms . '</div>';
            } else {
                echo '<div class="status error">✗ ' . $description . ' (' . $dir . '/) - MISSING</div>';
            }
        }
        ?>

        <h2>🔧 Server Modules</h2>
        <?php
        $requiredModules = ['mod_rewrite', 'mod_php'];
        foreach ($requiredModules as $module) {
            if (function_exists('apache_get_modules')) {
                $modules = apache_get_modules();
                if (in_array($module, $modules)) {
                    echo '<div class="status success">✓ ' . $module . ' is enabled</div>';
                } else {
                    echo '<div class="status warning">⚠ ' . $module . ' is not enabled</div>';
                }
            } else {
                echo '<div class="status info">ℹ Cannot check Apache modules (not running on Apache)</div>';
                break;
            }
        }
        ?>

        <h2>📝 Error Log</h2>
        <div class="status info">
            <strong>Error Log Location:</strong> <?php echo ini_get('error_log'); ?><br>
            <strong>Display Errors:</strong> <?php echo ini_get('display_errors') ? 'ON' : 'OFF'; ?><br>
            <strong>Log Errors:</strong> <?php echo ini_get('log_errors') ? 'ON' : 'OFF'; ?><br>
            <strong>Error Reporting Level:</strong> <?php echo ini_get('error_reporting'); ?>
        </div>

        <h2>🧪 Testing Tools</h2>
        <div class="status info">
            <strong>Available Test Tools:</strong><br>
            <a href="./db-test.php" class="button">Database Connection Test</a>
            <a href="./test-server.php" class="button">Basic PHP Test</a>
            <a href="./path-check.php" class="button">Path Checker</a>
            <a href="./index-fallback.php" class="button">Fallback Page</a>
        </div>

        <h2>🔍 Quick Actions</h2>
        <div class="status info">
            <strong>Quick Tests:</strong><br>
            <a href="?debug=db" class="button">Show Database Debug</a>
            <a href="?test=connection" class="button">Test Connection</a>
            <a href="./" class="button">Test Main Site</a>
        </div>

        <?php
        // Show debug info if requested
        if (isset($_GET['debug']) && $_GET['debug'] == 'db') {
            echo '<h2>🐛 Debug Information</h2>';
            echo '<div class="status info">';
            echo '<strong>Database Connection Details:</strong><br>';
            if (isset($connectionType)) {
                echo 'Connection Type: ' . $connectionType . '<br>';
                if ($connectionType == 'PRODUCTION') {
                    echo 'Production Database: u722035022_mkscholars<br>';
                    echo 'Production Host: localhost<br>';
                    echo 'Production User: u722035022_mkscholars<br>';
                } else {
                    echo 'Local Database: mkscholars<br>';
                    echo 'Local Host: localhost<br>';
                    echo 'Local User: root<br>';
                }
            }
            echo '</div>';
        }
        ?>

        <hr>
        <p style="text-align: center; color: #666;">
            <strong>MK Scholars Server Status Tool</strong><br>
            Environment: <?php echo $environment; ?> | 
            <a href="./">← Back to main site</a> | 
            <a href="./db-test.php">Database Test</a>
        </p>
    </div>
</body>
</html>
