<?php
session_start();
include('../dbconnection/connection.php');

if (!isset($_SESSION['adminId']) || !isset($_GET['ConvId'])) {
    http_response_code(400);
    exit('Missing conversation ID or not logged in.');
}

$convId = intval($_GET['ConvId']);
$adminId = intval($_SESSION['adminId']);

$stmt = $conn->prepare("SELECT * FROM Message WHERE ConvId = $convoId ORDER BY MessageId ASC");
$stmt->bind_param("i", $convId);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'UserId' => $row['UserId'],
        'AdminId' => $row['AdminId'],
        'MessageContent' => htmlspecialchars($row['MessageContent']),
        'SentDate' => $row['SentDate'],
        'SentTime' => date("h:i A", strtotime($row['SentTime'])),
    ];
}

echo json_encode($messages);
