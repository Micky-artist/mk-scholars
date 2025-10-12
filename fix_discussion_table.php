<?php
// Fix DiscussionBoard table foreign key constraints
include('./dbconnection/connection.php');

if (!$conn) {
    die("Database connection failed");
}

echo "<h2>Fixing DiscussionBoard Table</h2>";

// Check if table exists
$tableCheck = "SHOW TABLES LIKE 'DiscussionBoard'";
$tableResult = $conn->query($tableCheck);

if ($tableResult->num_rows === 0) {
    echo "<p>DiscussionBoard table does not exist. Creating it...</p>";
    
    // Create table without foreign key constraints
    $createTable = "CREATE TABLE DiscussionBoard (
        discussionId INT AUTO_INCREMENT PRIMARY KEY,
        courseId INT NOT NULL,
        userId INT NOT NULL,
        messageTitle VARCHAR(255) NOT NULL,
        messageBody LONGTEXT NOT NULL,
        messageDate DATE NOT NULL,
        messageTime TIME NOT NULL,
        messageLikes INT DEFAULT 0,
        messageReport INT DEFAULT 0,
        isPinned TINYINT(1) DEFAULT 0,
        parentDiscussionId INT DEFAULT NULL,
        isApproved TINYINT(1) DEFAULT 1,
        createdDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_course_discussion (courseId),
        INDEX idx_user_discussion (userId),
        INDEX idx_parent_discussion (parentDiscussionId)
    )";
    
    if ($conn->query($createTable)) {
        echo "<p style='color: green;'>✓ DiscussionBoard table created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating table: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>DiscussionBoard table exists. Checking for foreign key constraints...</p>";
    
    // Get foreign key constraints
    $fkQuery = "SELECT 
        CONSTRAINT_NAME,
        TABLE_NAME,
        COLUMN_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'DiscussionBoard' 
    AND REFERENCED_TABLE_NAME IS NOT NULL";
    
    $fkResult = $conn->query($fkQuery);
    
    if ($fkResult && $fkResult->num_rows > 0) {
        echo "<p>Found foreign key constraints. Removing them...</p>";
        
        while ($fk = $fkResult->fetch_assoc()) {
            $constraintName = $fk['CONSTRAINT_NAME'];
            echo "<p>Removing constraint: $constraintName</p>";
            
            $dropFK = "ALTER TABLE DiscussionBoard DROP FOREIGN KEY $constraintName";
            if ($conn->query($dropFK)) {
                echo "<p style='color: green;'>✓ Removed constraint: $constraintName</p>";
            } else {
                echo "<p style='color: red;'>✗ Error removing constraint $constraintName: " . mysqli_error($conn) . "</p>";
            }
        }
    } else {
        echo "<p>No foreign key constraints found.</p>";
    }
    
    // Add indexes if they don't exist
    $indexes = [
        "ALTER TABLE DiscussionBoard ADD INDEX idx_course_discussion (courseId)",
        "ALTER TABLE DiscussionBoard ADD INDEX idx_user_discussion (userId)",
        "ALTER TABLE DiscussionBoard ADD INDEX idx_parent_discussion (parentDiscussionId)"
    ];
    
    foreach ($indexes as $indexQuery) {
        if ($conn->query($indexQuery)) {
            echo "<p style='color: green;'>✓ Index added successfully</p>";
        } else {
            // Index might already exist, that's okay
            if (strpos(mysqli_error($conn), 'Duplicate key name') !== false) {
                echo "<p style='color: blue;'>ℹ Index already exists</p>";
            } else {
                echo "<p style='color: red;'>✗ Error adding index: " . mysqli_error($conn) . "</p>";
            }
        }
    }
}

// Test inserting a sample discussion
echo "<h3>Testing Discussion Insertion</h3>";

// Test with admin user (assuming admin ID 1 exists)
$testQuery = "INSERT INTO DiscussionBoard (courseId, userId, messageTitle, messageBody, messageDate, messageTime, isPinned, parentDiscussionId, isApproved) VALUES (1, 1, 'Test Discussion', 'This is a test discussion', CURDATE(), CURTIME(), 0, NULL, 1)";

if ($conn->query($testQuery)) {
    $insertId = mysqli_insert_id($conn);
    echo "<p style='color: green;'>✓ Test discussion inserted successfully (ID: $insertId)</p>";
    
    // Clean up test data
    $cleanup = "DELETE FROM DiscussionBoard WHERE discussionId = $insertId";
    if ($conn->query($cleanup)) {
        echo "<p style='color: blue;'>ℹ Test data cleaned up</p>";
    }
} else {
    echo "<p style='color: red;'>✗ Error inserting test discussion: " . mysqli_error($conn) . "</p>";
}

echo "<h3>Table Structure</h3>";
$describeQuery = "DESCRIBE DiscussionBoard";
$describeResult = $conn->query($describeQuery);

if ($describeResult) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $describeResult->fetch_assoc()) {
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
}

echo "<p><strong>Fix completed!</strong> You can now delete this file.</p>";
?>
