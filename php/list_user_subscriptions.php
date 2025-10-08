<?php
header('Content-Type: application/json');
include('../dbconnection/connection.php');

if (!isset($_GET['userId'])) {
    echo json_encode(['error' => 'Missing userId']);
    exit;
}

$userId = intval($_GET['userId']);

// Check if connection is valid
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$stmt = $conn->prepare("SELECT s.Item, s.SubscriptionStatus, s.subscriptionDate, s.expirationDate, c.courseName 
                       FROM subscription s 
                       LEFT JOIN Courses c ON s.Item = c.courseId 
                       WHERE s.UserId = ? 
                       ORDER BY s.subscriptionDate DESC");

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$subscriptions = [];
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

$stmt->close();
$conn->close();

echo json_encode($subscriptions); 