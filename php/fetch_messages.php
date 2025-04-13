<?php
session_start();
include('../dbconnection/connection.php');

if (!isset($_SESSION['userId']) || !isset($_GET['ConvId'])) {
    echo json_encode([]);
    exit;
}

$convId = intval($_GET['ConvId']);
$UserId = $_SESSION['userId'];

$query = "SELECT * FROM Message WHERE ConvId = $convId ORDER BY SentDate, SentTime";
$result = mysqli_query($conn, $query);

$messages = [];

while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = [
        'UserId' => $row['UserId'],
        'MessageContent' => htmlspecialchars($row['MessageContent']),
        'SentDate' => $row['SentDate'],
        'SentTime' => date("h:i A", strtotime($row['SentTime']))
    ];
}

echo json_encode($messages);
