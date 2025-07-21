<?php
// Check if required database tables exist
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("./dbconnections/connection.php");

echo "<h2>Database Tables Check</h2>";

if (!$conn) {
    echo "<p>✗ Database connection failed</p>";
    exit;
}

// List of required tables
$requiredTables = [
    'AdminRights',
    'Conversation', 
    'Message',
    'Documents',
    'normUsers'
];

foreach ($requiredTables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "<p>✓ Table exists: $table</p>";
        
        // Check table structure
        $structure = $conn->query("DESCRIBE $table");
        if ($structure) {
            echo "<p style='margin-left: 20px;'>Columns: ";
            $columns = [];
            while ($row = $structure->fetch_assoc()) {
                $columns[] = $row['Field'];
            }
            echo implode(', ', $columns) . "</p>";
        }
    } else {
        echo "<p>✗ Table missing: $table</p>";
    }
}

// Test some basic queries
echo "<h3>Query Tests</h3>";

try {
    $result = $conn->query("SELECT COUNT(*) as count FROM Conversation");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ Conversation table has " . $row['count'] . " records</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ Conversation query failed: " . $e->getMessage() . "</p>";
}

try {
    $result = $conn->query("SELECT COUNT(*) as count FROM Message");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ Message table has " . $row['count'] . " records</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ Message query failed: " . $e->getMessage() . "</p>";
}

try {
    $result = $conn->query("SELECT COUNT(*) as count FROM Documents");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>✓ Documents table has " . $row['count'] . " records</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ Documents query failed: " . $e->getMessage() . "</p>";
}

echo "<p><strong>Table check complete.</strong></p>";
?> 