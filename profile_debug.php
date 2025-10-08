<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['userId'];
$userName = $_SESSION['userName'] ?? $_SESSION['username'] ?? '';
$userEmail = $_SESSION['userEmail'] ?? $_SESSION['NoEmail'] ?? '';
$userPhone = $_SESSION['userPhone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <title>Profile Debug | MK Scholars</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Simple Navigation -->
            <nav class="col-md-3 col-lg-2 bg-light p-4">
                <h5>Debug Navigation</h5>
                <ul class="list-unstyled">
                    <li><a href="./dashboard.php">Dashboard</a></li>
                    <li><a href="./e-learning.php">E-Learning</a></li>
                    <li><a href="./conversations.php">Conversations</a></li>
                    <li><a href="./apply.php">Apply</a></li>
                    <li><a href="./profile.php">Profile</a></li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 p-4">
                <h1>Profile Debug Page</h1>
                <p>User ID: <?php echo $userId; ?></p>
                <p>User Name: <?php echo htmlspecialchars($userName); ?></p>
                <p>User Email: <?php echo htmlspecialchars($userEmail); ?></p>
                
                <div class="mt-4">
                    <h3>Test Subscription Loading</h3>
                    <button class="btn btn-primary" onclick="testLoadSubscriptions()">Test Load Subscriptions</button>
                    <div id="testResult" class="mt-3"></div>
                </div>
                
                <div class="mt-4">
                    <h3>Console Logs</h3>
                    <div id="consoleLogs" class="bg-dark text-light p-3" style="height: 200px; overflow-y: auto; font-family: monospace;"></div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Override console.log to display in the page
        const originalLog = console.log;
        const originalError = console.error;
        const logContainer = document.getElementById('consoleLogs');
        
        function addToLog(message, type = 'log') {
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.innerHTML = `[${timestamp}] ${type.toUpperCase()}: ${message}`;
            logEntry.style.color = type === 'error' ? '#ff6b6b' : '#51cf66';
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }
        
        console.log = function(...args) {
            originalLog.apply(console, args);
            addToLog(args.join(' '), 'log');
        };
        
        console.error = function(...args) {
            originalError.apply(console, args);
            addToLog(args.join(' '), 'error');
        };

        let isLoadingSubscriptions = false;

        function testLoadSubscriptions() {
            if (isLoadingSubscriptions) {
                console.log('Subscription request already in progress, skipping...');
                return;
            }
            
            isLoadingSubscriptions = true;
            console.log('Starting subscription test...');
            
            $('#testResult').html('<div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>');
            
            $.ajax({
                url: 'php/list_user_subscriptions.php',
                method: 'GET',
                data: { userId: <?php echo (int)$userId; ?> },
                dataType: 'json',
                timeout: 5000,
                success: function(data) {
                    console.log('Success! Data received:', data);
                    $('#testResult').html('<div class="alert alert-success">Success! Check console for data.</div>');
                    isLoadingSubscriptions = false;
                },
                error: function(xhr, status, error) {
                    console.error('Error:', status, error, xhr.responseText);
                    $('#testResult').html('<div class="alert alert-danger">Error: ' + error + '</div>');
                    isLoadingSubscriptions = false;
                }
            });
        }

        // Test on page load
        $(document).ready(function() {
            console.log('Debug page loaded');
            console.log('User ID:', <?php echo (int)$userId; ?>);
        });
    </script>
</body>
</html>
