<?php
session_start();
include('./dbconnection/connection.php');

if (!isset($_SESSION['userId'])) {
    echo "User not logged in";
    exit;
}

$userId = $_SESSION['userId'];
echo "<h3>Testing Subscriptions for User ID: $userId</h3>";

// Test the subscription query
$stmt = $conn->prepare("SELECT s.Item, s.SubscriptionStatus, s.subscriptionDate, s.expirationDate, c.courseName 
                       FROM subscription s 
                       LEFT JOIN Courses c ON s.Item = c.courseId 
                       WHERE s.UserId = ? 
                       ORDER BY s.subscriptionDate DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

echo "<h4>Raw Query Results:</h4>";
echo "<table border='1'>";
echo "<tr><th>Item</th><th>SubscriptionStatus</th><th>subscriptionDate</th><th>expirationDate</th><th>courseName</th></tr>";

$count = 0;
while ($row = $result->fetch_assoc()) {
    $count++;
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['Item']) . "</td>";
    echo "<td>" . htmlspecialchars($row['SubscriptionStatus']) . "</td>";
    echo "<td>" . htmlspecialchars($row['subscriptionDate']) . "</td>";
    echo "<td>" . htmlspecialchars($row['expirationDate']) . "</td>";
    echo "<td>" . htmlspecialchars($row['courseName']) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><strong>Total subscriptions found: $count</strong></p>";

// Test the JSON output
echo "<h4>JSON Output:</h4>";
$subscriptions = [];
$result->data_seek(0); // Reset result pointer

while ($row = $result->fetch_assoc()) {
    // Convert status number to text
    $statusText = '';
    switch ($row['SubscriptionStatus']) {
        case 1:
            $statusText = 'Active';
            break;
        case 0:
            $statusText = 'Expired';
            break;
        default:
            $statusText = 'Unknown';
            break;
    }
    
    // Format dates
    $subscriptionDate = $row['subscriptionDate'] ? date('M d, Y', strtotime($row['subscriptionDate'])) : 'N/A';
    $expirationDate = $row['expirationDate'] ? date('M d, Y', strtotime($row['expirationDate'])) : 'N/A';
    
    // Use course name if available, otherwise fall back to Item
    $itemName = $row['courseName'] ?: $row['Item'];
    
    $subscriptions[] = [
        'Item' => $itemName,
        'SubscriptionStatus' => $statusText,
        'subscriptionDate' => $subscriptionDate,
        'expirationDate' => $expirationDate
    ];
}

echo "<pre>" . json_encode($subscriptions, JSON_PRETTY_PRINT) . "</pre>";

$stmt->close();
$conn->close();
?>
