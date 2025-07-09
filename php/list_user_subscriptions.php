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
    $subscriptions[] = [
        'Item' => $row['Item'],
        'SubscriptionStatus' => $row['SubscriptionStatus'],
        'subscriptionDate' => $row['subscriptionDate'],
        'expirationDate' => $row['expirationDate']
    ];
}

$stmt->close();
$conn->close();

echo json_encode($subscriptions); 