<?php
// Simple error checking file for admin panel
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Admin Error Check</h2>";

// Check PHP version
echo "<p>PHP Version: " . phpversion() . "</p>";

// Check if session can be started
try {
    session_start();
    echo "<p>✓ Session started successfully</p>";
} catch (Exception $e) {
    echo "<p>✗ Session error: " . $e->getMessage() . "</p>";
}

// Check database connection
try {
    include("./dbconnections/connection.php");
    if ($conn) {
        echo "<p>✓ Database connection successful</p>";
        
        // Test a simple query
        $result = $conn->query("SELECT 1");
        if ($result) {
            echo "<p>✓ Database query test successful</p>";
        } else {
            echo "<p>✗ Database query test failed</p>";
        }
    } else {
        echo "<p>✗ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ Database error: " . $e->getMessage() . "</p>";
}

// Check if admin session validation file exists
if (file_exists("./php/validateAdminSession.php")) {
    echo "<p>✓ Admin session validation file exists</p>";
} else {
    echo "<p>✗ Admin session validation file missing</p>";
}

// Check if required directories exist
$requiredDirs = [
    "./dbconnections/",
    "./php/",
    "../uploads/",
    "../dbconnection/"
];

foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        echo "<p>✓ Directory exists: $dir</p>";
    } else {
        echo "<p>✗ Directory missing: $dir</p>";
    }
}

// Check file permissions
$uploadDir = "../uploads/";
if (is_writable($uploadDir)) {
    echo "<p>✓ Upload directory is writable</p>";
} else {
    echo "<p>✗ Upload directory is not writable</p>";
}

echo "<p><strong>Error check complete.</strong></p>";
?> 