<?php
// Database existence checker
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Existence Check - MK Scholars</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .status { padding: 15px; margin: 15px 0; border-radius: 5px; border-left: 4px solid; }
        .success { background-color: #d4edda; color: #155724; border-left-color: #28a745; }
        .error { background-color: #f8d7da; color: #721c24; border-left-color: #dc3545; }
        .warning { background-color: #fff3cd; color: #856404; border-left-color: #ffc107; }
        .info { background-color: #d1ecf1; color: #0c5460; border-left-color: #17a2b8; }
        h1 { color: #333; text-align: center; }
        .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .code { background: #f8f9fa; border: 1px solid #dee2e6; padding: 10px; margin: 10px 0; border-radius: 5px; font-family: monospace; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ Database Existence Check</h1>
        
        <h2>🔍 Checking Database Connections</h2>
        
        <?php
        // Test local database connection
        echo '<h3>Local Database (mkscholars)</h3>';
        try {
            $localConn = mysqli_connect('localhost', 'root', '', 'mkscholars');
            if ($localConn) {
                if (mysqli_ping($localConn)) {
                    echo '<div class="status success">✅ Local database connection: SUCCESS</div>';
                    
                    // Check if database exists
                    $result = mysqli_query($localConn, "SHOW DATABASES LIKE 'mkscholars'");
                    if (mysqli_num_rows($result) > 0) {
                        echo '<div class="status success">✅ Database "mkscholars" exists</div>';
                        
                        // Check tables
                        $tables = mysqli_query($localConn, "SHOW TABLES");
                        $tableCount = mysqli_num_rows($tables);
                        echo '<div class="status info">📊 Tables found: ' . $tableCount . '</div>';
                        
                        if ($tableCount > 0) {
                            echo '<div class="status info">📋 Table names:</div>';
                            echo '<div class="code">';
                            while ($table = mysqli_fetch_array($tables)) {
                                echo $table[0] . '<br>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="status error">❌ Database "mkscholars" does not exist</div>';
                    }
                    
                    mysqli_close($localConn);
                } else {
                    echo '<div class="status error">❌ Local database ping failed</div>';
                }
            } else {
                echo '<div class="status error">❌ Local database connection failed: ' . mysqli_connect_error() . '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="status error">❌ Local database exception: ' . $e->getMessage() . '</div>';
        }
        
        // Test production database connection
        echo '<h3>Production Database (u722035022_mkscholars)</h3>';
        try {
            $prodConn = mysqli_connect('localhost', 'u722035022_mkscholars', 'Mkscholars123@', 'u722035022_mkscholars');
            if ($prodConn) {
                if (mysqli_ping($prodConn)) {
                    echo '<div class="status success">✅ Production database connection: SUCCESS</div>';
                    
                    // Check if database exists
                    $result = mysqli_query($prodConn, "SHOW DATABASES LIKE 'u722035022_mkscholars'");
                    if (mysqli_num_rows($result) > 0) {
                        echo '<div class="status success">✅ Database "u722035022_mkscholars" exists</div>';
                        
                        // Check tables
                        $tables = mysqli_query($prodConn, "SHOW TABLES");
                        $tableCount = mysqli_num_rows($tables);
                        echo '<div class="status info">📊 Tables found: ' . $tableCount . '</div>';
                        
                        if ($tableCount > 0) {
                            echo '<div class="status info">📋 Table names:</div>';
                            echo '<div class="code">';
                            while ($table = mysqli_fetch_array($tables)) {
                                echo $table[0] . '<br>';
                            }
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="status error">❌ Database "u722035022_mkscholars" does not exist</div>';
                    }
                    
                    mysqli_close($prodConn);
                } else {
                    echo '<div class="status error">❌ Production database ping failed</div>';
                }
            } else {
                echo '<div class="status error">❌ Production database connection failed: ' . mysqli_connect_error() . '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="status error">❌ Production database exception: ' . $e->getMessage() . '</div>';
        }
        
        // Test MySQL server connection without specifying database
        echo '<h3>MySQL Server Connection (No Database)</h3>';
        try {
            $serverConn = mysqli_connect('localhost', 'root', '');
            if ($serverConn) {
                echo '<div class="status success">✅ MySQL server connection: SUCCESS</div>';
                
                // List all databases
                $result = mysqli_query($serverConn, "SHOW DATABASES");
                $dbCount = mysqli_num_rows($result);
                echo '<div class="status info">📊 Total databases found: ' . $dbCount . '</div>';
                
                if ($dbCount > 0) {
                    echo '<div class="status info">📋 Available databases:</div>';
                    echo '<div class="code">';
                    while ($db = mysqli_fetch_array($result)) {
                        $dbName = $db[0];
                        $highlight = '';
                        if ($dbName == 'mkscholars') {
                            $highlight = ' style="background: #d4edda; padding: 2px 5px; border-radius: 3px;"';
                        } elseif ($dbName == 'u722035022_mkscholars') {
                            $highlight = ' style="background: #d1ecf1; padding: 2px 5px; border-radius: 3px;"';
                        }
                        echo '<span' . $highlight . '>' . $dbName . '</span><br>';
                    }
                    echo '</div>';
                }
                
                mysqli_close($serverConn);
            } else {
                echo '<div class="status error">❌ MySQL server connection failed: ' . mysqli_connect_error() . '</div>';
            }
        } catch (Exception $e) {
            echo '<div class="status error">❌ MySQL server exception: ' . $e->getMessage() . '</div>';
        }
        ?>

        <h2>🔧 Solutions</h2>
        
        <div class="status warning">
            <strong>If databases don't exist:</strong><br>
            <strong>For Local Development:</strong><br>
            • Open phpMyAdmin (usually at http://localhost/phpmyadmin)<br>
            • Create a new database named "mkscholars"<br>
            • Import your database structure if you have a .sql file<br>
            <br>
            <strong>For Production:</strong><br>
            • Contact your hosting provider<br>
            • Verify the database "u722035022_mkscholars" exists<br>
            • Check if the user has proper permissions
        </div>

        <h2>🧪 Test Tools</h2>
        <div class="status info">
            <strong>Available Test Tools:</strong><br>
            <a href="./test-home.php" class="button">Test Home.php</a>
            <a href="./db-test.php" class="button">Database Test</a>
            <a href="./server-status.php" class="button">Server Status</a>
            <a href="./home.php" class="button">Try Home.php Again</a>
        </div>

        <hr>
        <p style="text-align: center; color: #666;">
            <strong>MK Scholars Database Checker</strong><br>
            Use this tool to verify your database setup before testing the main site.
        </p>
    </div>
</body>
</html>
