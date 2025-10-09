<?php
// Simple debug script to test courses on hosted website
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Courses Debug Script</h1>";
echo "<p>This script helps debug course loading issues on the hosted website.</p>";

// Include database connection
$conn = null;
$includeSuccess = false;

try {
    $includeSuccess = include("./dbconnection/connection.php");
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error including database connection: " . $e->getMessage() . "</p>";
}

if (!$includeSuccess || !$conn) {
    echo "<p style='color: orange;'>⚠ Database connection file failed, trying direct connection...</p>";
    
    // Direct database connection
    $host = 'localhost';
    $username = 'u722035022_mkscholars';
    $password = 'Mkscholars123@';
    $database = 'u722035022_mkscholars';
    $port = 3306;
    
    $conn = mysqli_connect($host, $username, $password, $database, $port);
    
    if (!$conn) {
        echo "<p style='color: red;'>✗ Direct database connection also failed: " . mysqli_connect_error() . "</p>";
        exit;
    } else {
        echo "<p style='color: green;'>✓ Direct database connection successful</p>";
    }
}

// Ensure isOnline function is available
if (!function_exists('isOnline')) {
    function isOnline() {
        // Get server information
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
        $port = $_SERVER['SERVER_PORT'] ?? '80';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        
        // List of local development indicators
        $localIndicators = [
            'localhost',
            '127.0.0.1',
            '::1',
            '0.0.0.0',
            'xampp',
            'wamp',
            'mamp',
            'laragon',
            'local',
            'dev',
            'development',
            'test',
            'staging.local',
            '.local',
            '.dev',
            '.test'
        ];
        
        // Check host and server name for local indicators
        foreach ($localIndicators as $indicator) {
            if (stripos($host, $indicator) !== false || 
                stripos($serverName, $indicator) !== false ||
                stripos($serverAddr, $indicator) !== false) {
                return false; // We're offline (local development)
            }
        }
        
        // Check for common local development ports
        $localPorts = ['3306', '8080', '8000', '3000', '5000', '9000', '8888', '8889'];
        if (in_array($port, $localPorts)) {
            return false; // Likely local development
        }
        
        // Check document root for local development paths
        $localPaths = ['xampp', 'wamp', 'mamp', 'laragon', 'htdocs', 'www', 'public_html'];
        foreach ($localPaths as $path) {
            if (stripos($documentRoot, $path) !== false) {
                return false; // Likely local development
            }
        }
        
        // Check for common production indicators
        $productionIndicators = [
            '.com',
            '.org',
            '.net',
            '.io',
            'www.',
            'https://'
        ];
        
        $isProduction = false;
        foreach ($productionIndicators as $indicator) {
            if (stripos($host, $indicator) !== false) {
                $isProduction = true;
                break;
            }
        }
        
        // If we have production indicators, we're likely online
        if ($isProduction) {
            return true;
        }
        
        // Final check: if we can't determine, default to online (production)
        return true;
    }
}

echo "<h2>Database Connection Test</h2>";
if ($conn) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    echo "<p><strong>Server:</strong> " . mysqli_get_server_info($conn) . "</p>";
    echo "<p><strong>Environment:</strong> " . (isOnline() ? 'PRODUCTION' : 'LOCAL') . "</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_connect_error() . "</p>";
    exit;
}

echo "<h2>Table Existence Check</h2>";
$tables = ['Courses', 'CoursePricing', 'Currencies'];
foreach ($tables as $table) {
    $check = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if ($check && mysqli_num_rows($check) > 0) {
        echo "<p style='color: green;'>✓ Table '$table' exists</p>";
    } else {
        echo "<p style='color: red;'>✗ Table '$table' not found</p>";
    }
}

echo "<h2>Courses Table Structure</h2>";
$structure = mysqli_query($conn, "DESCRIBE Courses");
if ($structure) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($structure)) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ Could not get table structure: " . mysqli_error($conn) . "</p>";
}

echo "<h2>Course Count Test</h2>";
$countQuery = "SELECT COUNT(*) as total FROM Courses";
$countResult = mysqli_query($conn, $countQuery);
if ($countResult) {
    $total = mysqli_fetch_assoc($countResult)['total'];
    echo "<p><strong>Total courses:</strong> $total</p>";
} else {
    echo "<p style='color: red;'>✗ Count query failed: " . mysqli_error($conn) . "</p>";
}

$openCountQuery = "SELECT COUNT(*) as open FROM Courses WHERE courseDisplayStatus = 1";
$openCountResult = mysqli_query($conn, $openCountQuery);
if ($openCountResult) {
    $open = mysqli_fetch_assoc($openCountResult)['open'];
    echo "<p><strong>Open courses:</strong> $open</p>";
} else {
    echo "<p style='color: red;'>✗ Open count query failed: " . mysqli_error($conn) . "</p>";
}

echo "<h2>Sample Course Data</h2>";
$sampleQuery = "SELECT courseId, courseName, courseDisplayStatus, courseCreatedDate FROM Courses LIMIT 5";
$sampleResult = mysqli_query($conn, $sampleQuery);
if ($sampleResult && mysqli_num_rows($sampleResult) > 0) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Created</th></tr>";
    while ($row = mysqli_fetch_assoc($sampleResult)) {
        $statusText = $row['courseDisplayStatus'] == 1 ? 'Open' : ($row['courseDisplayStatus'] == 2 ? 'Closed' : 'Inactive');
        echo "<tr>";
        echo "<td>" . $row['courseId'] . "</td>";
        echo "<td>" . htmlspecialchars($row['courseName']) . "</td>";
        echo "<td>" . $statusText . "</td>";
        echo "<td>" . $row['courseCreatedDate'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ No courses found or query failed: " . mysqli_error($conn) . "</p>";
}

echo "<h2>Complex Query Test</h2>";
$complexQuery = "SELECT c.*, cp.amount, cp.currency, cp.pricingDescription, curr.currencySymbol 
                 FROM Courses c 
                 LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
                 LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                 WHERE c.courseDisplayStatus = 1 
                 ORDER BY c.courseCreatedDate DESC
                 LIMIT 3";

$complexResult = mysqli_query($conn, $complexQuery);
if ($complexResult) {
    $complexCount = mysqli_num_rows($complexResult);
    echo "<p style='color: green;'>✓ Complex query successful - found $complexCount courses</p>";
    
    if ($complexCount > 0) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Amount</th><th>Currency</th><th>Description</th></tr>";
        while ($row = mysqli_fetch_assoc($complexResult)) {
            echo "<tr>";
            echo "<td>" . $row['courseId'] . "</td>";
            echo "<td>" . htmlspecialchars($row['courseName']) . "</td>";
            echo "<td>" . ($row['amount'] ?? 'N/A') . "</td>";
            echo "<td>" . ($row['currency'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['pricingDescription'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>✗ Complex query failed: " . mysqli_error($conn) . "</p>";
}

echo "<h2>Environment Information</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>MySQL Version:</strong> " . mysqli_get_server_info($conn) . "</p>";
echo "<p><strong>Server:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "</p>";

mysqli_close($conn);
?>
