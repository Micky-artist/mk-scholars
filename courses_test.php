<?php
// Ultra-simple courses test - no images, no external dependencies
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Courses Test | MK Scholars</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .course-card { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; border: 1px solid #ddd; }
        .course-title { font-size: 1.2em; font-weight: bold; margin-bottom: 10px; }
        .course-desc { color: #666; margin-bottom: 10px; }
        .course-price { color: #007bff; font-weight: bold; }
        .error { background: #ffebee; color: #c62828; padding: 15px; border-radius: 8px; margin: 10px 0; }
        .success { background: #e8f5e8; color: #2e7d32; padding: 15px; border-radius: 8px; margin: 10px 0; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>Courses Test Page</h1>
            <p>Testing course loading without images or external dependencies</p>
        </div>";

// Test database connection
$conn = null;
$error = null;

try {
    // Direct database connection
    $host = 'localhost';
    $username = 'u722035022_mkscholars';
    $password = 'Mkscholars123@';
    $database = 'u722035022_mkscholars';
    $port = 3306;
    
    $conn = mysqli_connect($host, $username, $password, $database, $port);
    
    if (!$conn) {
        throw new Exception("Database connection failed: " . mysqli_connect_error());
    }
    
    echo "<div class='success'>✓ Database connection successful</div>";
    
    // Test basic query
    $query = "SELECT courseId, courseName, courseShortDescription, courseDisplayStatus FROM Courses WHERE courseDisplayStatus = 1 ORDER BY courseCreatedDate DESC LIMIT 10";
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($conn));
    }
    
    $courses = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    if (empty($courses)) {
        echo "<div class='error'>No open courses found in database</div>";
    } else {
        echo "<div class='success'>Found " . count($courses) . " open courses</div>";
        
        foreach ($courses as $course) {
            echo "<div class='course-card'>";
            echo "<div class='course-title'>" . htmlspecialchars($course['courseName']) . "</div>";
            echo "<div class='course-desc'>" . htmlspecialchars($course['courseShortDescription'] ?? 'No description') . "</div>";
            echo "<div class='course-price'>Status: " . ($course['courseDisplayStatus'] == 1 ? 'Open' : 'Closed') . "</div>";
            echo "</div>";
        }
    }
    
} catch (Exception $e) {
    echo "<div class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    $error = $e->getMessage();
}

if ($conn) {
    mysqli_close($conn);
}

echo "
    </div>
</body>
</html>";
?>
