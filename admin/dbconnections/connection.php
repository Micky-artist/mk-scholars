<?php
date_default_timezone_set('Africa/Kigali');

// Function to detect if we're online (production) or offline (local development)
function isOnline() {
    // Check if we're on localhost or local development environment
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
    
    // List of local development indicators
    $localIndicators = [
        'localhost',
        '127.0.0.1',
        '::1',
        'xampp',
        'wamp',
        'mamp',
        'local',
        'dev',
        'development'
    ];
    
    // Check if current host contains any local indicators
    foreach ($localIndicators as $indicator) {
        if (stripos($host, $indicator) !== false || stripos($serverName, $indicator) !== false) {
            return false; // We're offline (local development)
        }
    }
    
    // Additional check for common local development ports
    $port = $_SERVER['SERVER_PORT'] ?? '80';
    if ($port == '3306' || $port == '8080' || $port == '8000') {
        return false; // Likely local development
    }
    
    // Default to online if we can't determine otherwise
    return true;
}

// Database configuration based on environment
if (isOnline()) {
    // Production/Online database credentials
    $conn = mysqli_connect('localhost','u722035022_mkscholars','Mkscholars123@','u722035022_mkscholars');
    $connectionType = 'PRODUCTION';
} else {
    // Local/Offline database credentials
    $conn = mysqli_connect('localhost','root','','mkscholars');
    $connectionType = 'LOCAL';
}

// Check connection and handle errors gracefully
if (!$conn) {
    error_log("Admin database connection failed ({$connectionType}): " . mysqli_connect_error());
    $conn = null; // Set to null instead of crashing
} else {
    error_log("Admin database connected successfully ({$connectionType})");
}

// Debug information (remove in production)
if (isset($_GET['debug']) && $_GET['debug'] == 'db') {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
    echo "<strong>Admin Database Connection Debug:</strong><br>";
    echo "Environment: " . ($connectionType) . "<br>";
    echo "Status: " . ($conn ? 'Connected' : 'Failed') . "<br>";
    echo "Server: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "<br>";
    echo "Port: " . ($_SERVER['SERVER_PORT'] ?? 'Unknown') . "<br>";
    echo "</div>";
}
?>


$class='';
$msg='';
$username='';
$email='';