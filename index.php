<?php
// Check if there are any critical errors
$hasError = false;

// Try to include the database connection
try {
    include_once("./dbconnection/connection.php");
    
    // Check if connection is working
    if ($conn && mysqli_ping($conn)) {
        // Database is working, redirect to home
        echo '<script type="text/javascript">
            window.location.href="home";
        </script>';
    } else {
        $hasError = true;
    }
} catch (Exception $e) {
    $hasError = true;
    error_log("Error in index.php: " . $e->getMessage());
}

// If there's an error, show fallback page
if ($hasError) {
    include_once("./index-fallback.php");
}
?>