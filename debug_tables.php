<?php
session_start();
include('./dbconnection/connection.php');

if (!isset($_SESSION['userId'])) {
    echo "User not logged in";
    exit;
}

$userId = $_SESSION['userId'];
echo "<h3>Database Table Debug for User ID: $userId</h3>";

// Check if subscription table exists and show its structure
echo "<h4>Subscription Table Structure:</h4>";
$result = mysqli_query($conn, "DESCRIBE subscription");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error describing subscription table: " . mysqli_error($conn);
}

// Check if Courses table exists and show its structure
echo "<h4>Courses Table Structure:</h4>";
$result = mysqli_query($conn, "DESCRIBE Courses");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error describing Courses table: " . mysqli_error($conn);
}

// Check what's actually in the subscription table
echo "<h4>Raw Subscription Data for User $userId:</h4>";
$result = mysqli_query($conn, "SELECT * FROM subscription WHERE UserId = $userId");
if ($result) {
    echo "<table border='1'>";
    if ($result->num_rows > 0) {
        $first = true;
        while ($row = mysqli_fetch_assoc($result)) {
            if ($first) {
                echo "<tr>";
                foreach (array_keys($row) as $key) {
                    echo "<th>" . htmlspecialchars($key) . "</th>";
                }
                echo "</tr>";
                $first = false;
            }
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='10'>No subscriptions found for this user</td></tr>";
    }
    echo "</table>";
} else {
    echo "Error querying subscription table: " . mysqli_error($conn);
}

// Check what's in the Courses table
echo "<h4>Courses Table Sample Data:</h4>";
$result = mysqli_query($conn, "SELECT courseId, courseName FROM Courses LIMIT 5");
if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>courseId</th><th>courseName</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['courseId']) . "</td>";
        echo "<td>" . htmlspecialchars($row['courseName']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error querying Courses table: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
