<?php
header('Content-Type: application/json');
include('../dbconnection/connection.php');

if (!isset($_GET['userId'])) {
    echo json_encode(['error' => 'Missing userId']);
    exit;
}

$userId = intval($_GET['userId']);

$stmt = $conn->prepare("SELECT Item, SubscriptionStatus, subscriptionDate, expirationDate FROM subscription WHERE UserId = ? ORDER BY subscriptionDate DESC");
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
    
    $subscriptions[] = [
        'Item' => $row['Item'],
        'SubscriptionStatus' => $statusText,
        'subscriptionDate' => $subscriptionDate,
        'expirationDate' => $expirationDate
    ];
}

$stmt->close();
$conn->close();

echo json_encode($subscriptions); 