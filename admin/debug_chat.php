<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Admin Chat Debug</h1>";

// 1. Check session
echo "<h2>1. Session Check</h2>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Admin ID: " . ($_SESSION['adminId'] ?? 'NOT SET') . "<br>";
echo "Admin Name: " . ($_SESSION['AdminName'] ?? 'NOT SET') . "<br>";
echo "Account Status: " . ($_SESSION['accountstatus'] ?? 'NOT SET') . "<br>";

// 2. Check database connection
echo "<h2>2. Database Connection</h2>";
include("./dbconnections/connection.php");
if ($conn) {
    echo "✓ Database connected successfully<br>";
} else {
    echo "✗ Database connection failed<br>";
    exit;
}

// 3. Check if required tables exist
echo "<h2>3. Database Tables</h2>";
$tables = ['Conversation', 'Message', 'normUsers', 'AdminRights', 'Documents'];
foreach ($tables as $table) {
    $result = $conn->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        echo "✓ Table '$table' exists<br>";
    } else {
        echo "✗ Table '$table' missing<br>";
    }
}

// 4. Check conversation query
echo "<h2>4. Conversation Query Test</h2>";
$convSql = "SELECT c.ConvId,u.NoUsername,u.NoUserId,lm.lastMessageId,
         COALESCE(uc.unreadCount,0) AS unreadCount
    FROM Conversation c
    JOIN normUsers u ON c.UserId=u.NoUserId
    JOIN (SELECT ConvId,MAX(MessageId) AS lastMessageId FROM Message GROUP BY ConvId) lm
      ON c.ConvId=lm.ConvId
    LEFT JOIN (SELECT ConvId,COUNT(*) AS unreadCount FROM Message WHERE MessageStatus=0 GROUP BY ConvId) uc
      ON c.ConvId=uc.ConvId
   ORDER BY lm.lastMessageId DESC";

$selectConvos = $conn->query($convSql);
if ($selectConvos) {
    echo "✓ Conversation query executed successfully<br>";
    echo "Number of conversations: " . $selectConvos->num_rows . "<br>";
    
    if ($selectConvos->num_rows > 0) {
        echo "<h3>Conversations found:</h3>";
        while ($row = $selectConvos->fetch_assoc()) {
            echo "- ConvId: " . $row['ConvId'] . ", User: " . $row['NoUsername'] . ", Unread: " . $row['unreadCount'] . "<br>";
        }
    } else {
        echo "No conversations found<br>";
    }
} else {
    echo "✗ Conversation query failed: " . $conn->error . "<br>";
}

// 5. Check individual tables
echo "<h2>5. Individual Table Counts</h2>";

// Check Conversation table
$result = $conn->query("SELECT COUNT(*) as count FROM Conversation");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Conversation table: " . $row['count'] . " records<br>";
} else {
    echo "Conversation table query failed: " . $conn->error . "<br>";
}

// Check normUsers table
$result = $conn->query("SELECT COUNT(*) as count FROM normUsers");
if ($result) {
    $row = $result->fetch_assoc();
    echo "normUsers table: " . $row['count'] . " records<br>";
} else {
    echo "normUsers table query failed: " . $conn->error . "<br>";
}

// Check Message table
$result = $conn->query("SELECT COUNT(*) as count FROM Message");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Message table: " . $row['count'] . " records<br>";
} else {
    echo "Message table query failed: " . $conn->error . "<br>";
}

// 6. Check if there are any conversations without messages
echo "<h2>6. Conversations without Messages</h2>";
$result = $conn->query("SELECT c.ConvId, u.NoUsername 
                       FROM Conversation c 
                       LEFT JOIN Message m ON c.ConvId = m.ConvId 
                       JOIN normUsers u ON c.UserId = u.NoUserId 
                       WHERE m.ConvId IS NULL");
if ($result && $result->num_rows > 0) {
    echo "Conversations without messages:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- ConvId: " . $row['ConvId'] . ", User: " . $row['NoUsername'] . "<br>";
    }
} else {
    echo "No conversations without messages found<br>";
}

// 7. Check if there are any users without conversations
echo "<h2>7. Users without Conversations</h2>";
$result = $conn->query("SELECT u.NoUserId, u.NoUsername 
                       FROM normUsers u 
                       LEFT JOIN Conversation c ON u.NoUserId = c.UserId 
                       WHERE c.UserId IS NULL");
if ($result && $result->num_rows > 0) {
    echo "Users without conversations:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- UserId: " . $row['NoUserId'] . ", Username: " . $row['NoUsername'] . "<br>";
    }
} else {
    echo "No users without conversations found<br>";
}

// 8. Test a simpler query
echo "<h2>8. Simple Query Test</h2>";
$simpleQuery = "SELECT c.ConvId, u.NoUsername 
                FROM Conversation c 
                JOIN normUsers u ON c.UserId = u.NoUserId 
                LIMIT 5";
$result = $conn->query($simpleQuery);
if ($result) {
    echo "✓ Simple query works<br>";
    echo "Results: " . $result->num_rows . " rows<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- ConvId: " . $row['ConvId'] . ", User: " . $row['NoUsername'] . "<br>";
    }
} else {
    echo "✗ Simple query failed: " . $conn->error . "<br>";
}

// 9. Check for any recent activity
echo "<h2>9. Recent Activity</h2>";
$result = $conn->query("SELECT m.*, u.NoUsername 
                       FROM Message m 
                       JOIN Conversation c ON m.ConvId = c.ConvId 
                       JOIN normUsers u ON c.UserId = u.NoUserId 
                       ORDER BY m.SentDate DESC, m.SentTime DESC 
                       LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "Recent messages:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['SentDate'] . " " . $row['SentTime'] . " | " . $row['NoUsername'] . ": " . substr($row['MessageContent'], 0, 50) . "...<br>";
    }
} else {
    echo "No recent messages found<br>";
}

echo "<h2>Debug Complete</h2>";
?> 