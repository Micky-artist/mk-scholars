<?php
// Simple debug script for hosted website - no dependencies
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Courses Debug Script</h1>";
echo "<p>This script helps debug course loading issues on the hosted website.</p>";

// Database configuration for production
$host = 'localhost';
$username = 'u722035022_mkscholars';
$password = 'Mkscholars123@';
$database = 'u722035022_mkscholars';
$port = 3306;

echo "<h2>Environment Information</h2>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "</p>";
echo "<p><strong>Document Root:</strong> " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</p>";
echo "<p><strong>Script Name:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "</p>";

echo "<h2>Database Connection Test</h2>";

// Test database connection
$conn = mysqli_connect($host, $username, $password, $database, $port);

if ($conn) {
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    echo "<p><strong>MySQL Version:</strong> " . mysqli_get_server_info($conn) . "</p>";
    echo "<p><strong>Database:</strong> " . $database . "</p>";
    echo "<p><strong>Host:</strong> " . $host . ":" . $port . "</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_connect_error() . "</p>";
    echo "<p><strong>Error Number:</strong> " . mysqli_connect_errno() . "</p>";
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
        if ($check) {
            echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
        }
    }
}

echo "<h2>Courses Table Structure</h2>";
$structure = mysqli_query($conn, "DESCRIBE Courses");
if ($structure) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($structure)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ Could not get table structure</p>";
    echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
}

echo "<h2>Course Count Test</h2>";
$countQuery = "SELECT COUNT(*) as total FROM Courses";
$countResult = mysqli_query($conn, $countQuery);
if ($countResult) {
    $total = mysqli_fetch_assoc($countResult)['total'];
    echo "<p><strong>Total courses:</strong> $total</p>";
} else {
    echo "<p style='color: red;'>✗ Count query failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
}

$openCountQuery = "SELECT COUNT(*) as open FROM Courses WHERE courseDisplayStatus = 1";
$openCountResult = mysqli_query($conn, $openCountQuery);
if ($openCountResult) {
    $open = mysqli_fetch_assoc($openCountResult)['open'];
    echo "<p><strong>Open courses (status=1):</strong> $open</p>";
} else {
    echo "<p style='color: red;'>✗ Open count query failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
}

$closedCountQuery = "SELECT COUNT(*) as closed FROM Courses WHERE courseDisplayStatus = 2";
$closedCountResult = mysqli_query($conn, $closedCountQuery);
if ($closedCountResult) {
    $closed = mysqli_fetch_assoc($closedCountResult)['closed'];
    echo "<p><strong>Closed courses (status=2):</strong> $closed</p>";
} else {
    echo "<p style='color: red;'>✗ Closed count query failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
}

$inactiveCountQuery = "SELECT COUNT(*) as inactive FROM Courses WHERE courseDisplayStatus = 0";
$inactiveCountResult = mysqli_query($conn, $inactiveCountQuery);
if ($inactiveCountResult) {
    $inactive = mysqli_fetch_assoc($inactiveCountResult)['inactive'];
    echo "<p><strong>Inactive courses (status=0):</strong> $inactive</p>";
} else {
    echo "<p style='color: red;'>✗ Inactive count query failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
}

echo "<h2>Sample Course Data</h2>";
$sampleQuery = "SELECT courseId, courseName, courseDisplayStatus, courseCreatedDate FROM Courses ORDER BY courseCreatedDate DESC LIMIT 10";
$sampleResult = mysqli_query($conn, $sampleQuery);
if ($sampleResult && mysqli_num_rows($sampleResult) > 0) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Created</th></tr>";
    while ($row = mysqli_fetch_assoc($sampleResult)) {
        $statusText = $row['courseDisplayStatus'] == 1 ? 'Open' : ($row['courseDisplayStatus'] == 2 ? 'Closed' : 'Inactive');
        $statusColor = $row['courseDisplayStatus'] == 1 ? 'green' : ($row['courseDisplayStatus'] == 2 ? 'orange' : 'red');
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['courseId']) . "</td>";
        echo "<td>" . htmlspecialchars($row['courseName']) . "</td>";
        echo "<td style='color: $statusColor; font-weight: bold;'>" . $statusText . "</td>";
        echo "<td>" . htmlspecialchars($row['courseCreatedDate']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>✗ No courses found or query failed</p>";
    if ($sampleResult) {
        echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
    }
}

echo "<h2>Complex Query Test</h2>";
$complexQuery = "SELECT c.courseId, c.courseName, c.courseDisplayStatus, cp.amount, cp.currency, curr.currencySymbol 
                 FROM Courses c 
                 LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
                 LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                 WHERE c.courseDisplayStatus = 1 
                 ORDER BY c.courseCreatedDate DESC
                 LIMIT 5";

$complexResult = mysqli_query($conn, $complexQuery);
if ($complexResult) {
    $complexCount = mysqli_num_rows($complexResult);
    echo "<p style='color: green;'>✓ Complex query successful - found $complexCount courses</p>";
    
    if ($complexCount > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th><th>Amount</th><th>Currency</th></tr>";
        while ($row = mysqli_fetch_assoc($complexResult)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['courseId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['courseName']) . "</td>";
            echo "<td style='color: green; font-weight: bold;'>Open</td>";
            echo "<td>" . htmlspecialchars($row['amount'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['currency'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ No open courses found with pricing data</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Complex query failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
}

echo "<h2>Pricing Table Check</h2>";
$pricingQuery = "SELECT COUNT(*) as pricingCount FROM CoursePricing";
$pricingResult = mysqli_query($conn, $pricingQuery);
if ($pricingResult) {
    $pricingCount = mysqli_fetch_assoc($pricingResult)['pricingCount'];
    echo "<p><strong>CoursePricing records:</strong> $pricingCount</p>";
} else {
    echo "<p style='color: red;'>✗ CoursePricing query failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
}

echo "<h2>Currencies Table Check</h2>";
$currenciesQuery = "SELECT COUNT(*) as currenciesCount FROM Currencies";
$currenciesResult = mysqli_query($conn, $currenciesQuery);
if ($currenciesResult) {
    $currenciesCount = mysqli_fetch_assoc($currenciesResult)['currenciesCount'];
    echo "<p><strong>Currencies records:</strong> $currenciesCount</p>";
} else {
    echo "<p style='color: red;'>✗ Currencies query failed</p>";
    echo "<p><strong>Error:</strong> " . mysqli_error($conn) . "</p>";
}

mysqli_close($conn);

echo "<h2>Debug Complete</h2>";
echo "<p>If you see any red errors above, those are the issues preventing courses from loading.</p>";
echo "<p>If everything shows green checkmarks, the database is working correctly.</p>";
?>
