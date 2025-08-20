<?php
session_start();
include('./dbconnection/connection.php');

// Simple chat test page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Test - MK Scholars</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Chat System Test</h1>
    
    <div class="test-section info">
        <h3>🔍 System Information</h3>
        <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
        <p><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
        <p><strong>Timezone:</strong> <?php echo date_default_timezone_get(); ?></p>
        <p><strong>Session Status:</strong> <?php echo isset($_SESSION['userId']) ? 'Active (User ID: ' . $_SESSION['userId'] . ')' : 'No active session'; ?></p>
    </div>

    <div class="test-section <?php echo isset($conn) && $conn ? 'success' : 'error'; ?>">
        <h3>🔌 Database Connection</h3>
        <?php if (isset($conn) && $conn): ?>
            <p>✅ Database connection successful</p>
            <p><strong>Connection Info:</strong> <?php echo $conn->host_info; ?></p>
        <?php else: ?>
            <p>❌ Database connection failed</p>
        <?php endif; ?>
    </div>

    <?php if (isset($conn) && $conn): ?>
    <div class="test-section">
        <h3>📊 Database Tables Check</h3>
        <?php
        $tables = ['Conversation', 'Message', 'users'];
        foreach ($tables as $table) {
            $query = "SHOW TABLES LIKE '$table'";
            $result = mysqli_query($conn, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                echo "<p>✅ Table '$table' exists</p>";
                
                // Check table structure for Message table
                if ($table === 'Message') {
                    $structQuery = "DESCRIBE $table";
                    $structResult = mysqli_query($conn, $structQuery);
                    echo "<details><summary>Message table structure</summary><pre>";
                    while ($row = mysqli_fetch_assoc($structResult)) {
                        echo $row['Field'] . " - " . $row['Type'] . " - " . $row['Null'] . " - " . $row['Key'] . "\n";
                    }
                    echo "</pre></details>";
                }
            } else {
                echo "<p>❌ Table '$table' does not exist</p>";
            }
        }
        ?>
    </div>

    <?php if (isset($_SESSION['userId'])): ?>
    <div class="test-section">
        <h3>💬 User Conversations</h3>
        <?php
        $userId = $_SESSION['userId'];
        $convQuery = "SELECT * FROM Conversation WHERE UserId = $userId";
        $convResult = mysqli_query($conn, $convQuery);
        
        if ($convResult && mysqli_num_rows($convResult) > 0) {
            while ($conv = mysqli_fetch_assoc($convResult)) {
                echo "<p>📞 Conversation ID: " . $conv['ConvId'] . " - Status: " . $conv['ConvStatus'] . "</p>";
                
                // Check messages count
                $msgQuery = "SELECT COUNT(*) as msg_count FROM Message WHERE ConvId = " . $conv['ConvId'];
                $msgResult = mysqli_query($conn, $msgQuery);
                $msgCount = mysqli_fetch_assoc($msgResult)['msg_count'];
                echo "<p>💬 Messages in this conversation: $msgCount</p>";
            }
        } else {
            echo "<p>📝 No conversations found for this user</p>";
        }
        ?>
    </div>
    <?php endif; ?>

    <div class="test-section">
        <h3>📁 File System Check</h3>
        <?php
        $paths = [
            './php/submit_message.php',
            './php/chat_stream.php',
            './uploads/',
            './dbconnection/connection.php'
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                if (is_dir($path)) {
                    $perms = substr(sprintf('%o', fileperms($path)), -4);
                    echo "<p>✅ Directory '$path' exists (Permissions: $perms)</p>";
                } else {
                    $size = filesize($path);
                    echo "<p>✅ File '$path' exists (Size: $size bytes)</p>";
                }
            } else {
                echo "<p>❌ Path '$path' does not exist</p>";
            }
        }
        ?>
    </div>
    <?php endif; ?>

    <div class="test-section info">
        <h3>🧪 Quick Tests</h3>
        <button onclick="testAjax()">Test AJAX Connection</button>
        <button onclick="testEventSource()">Test EventSource (SSE)</button>
        <div id="test-results"></div>
    </div>

    <script>
        function testAjax() {
            const results = document.getElementById('test-results');
            results.innerHTML = '<p>Testing AJAX...</p>';
            
            fetch('./php/submit_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'test=1'
            })
            .then(response => response.text())
            .then(data => {
                results.innerHTML = `<pre>AJAX Response: ${data}</pre>`;
            })
            .catch(error => {
                results.innerHTML = `<p style="color: red;">AJAX Error: ${error}</p>`;
            });
        }

        function testEventSource() {
            const results = document.getElementById('test-results');
            results.innerHTML = '<p>Testing EventSource...</p>';
            
            if (typeof(EventSource) !== "undefined") {
                const eventSource = new EventSource('./php/chat_stream.php?convId=1&lastMessageId=0');
                
                let timeout = setTimeout(() => {
                    eventSource.close();
                    results.innerHTML += '<p style="color: orange;">EventSource timeout after 5 seconds</p>';
                }, 5000);
                
                eventSource.onopen = function() {
                    clearTimeout(timeout);
                    results.innerHTML = '<p style="color: green;">✅ EventSource connection opened</p>';
                    setTimeout(() => eventSource.close(), 2000);
                };
                
                eventSource.onmessage = function(event) {
                    results.innerHTML += `<pre>SSE Data: ${event.data}</pre>`;
                };
                
                eventSource.onerror = function() {
                    clearTimeout(timeout);
                    results.innerHTML = '<p style="color: red;">❌ EventSource connection error</p>';
                    eventSource.close();
                };
            } else {
                results.innerHTML = '<p style="color: red;">❌ EventSource not supported</p>';
            }
        }
    </script>
</body>
</html>
