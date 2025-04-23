<?php
include('../dbconnection/connection.php');
session_start();
if (empty($_GET['ConvId'])) {
    echo json_encode([]);
    exit;
}
$convId = intval($_GET['ConvId']);

// 1) Fetch messages
$stmt = $conn->prepare("
  SELECT AdminId, MessageContent, SentDate, SentTime
    FROM Message
   WHERE ConvId = ?
ORDER BY SentDate, SentTime
");
$stmt->bind_param('i', $convId);
$stmt->execute();
$res = $stmt->get_result();
$messages = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// 2) Mark them read
$upd = $conn->prepare("UPDATE Message SET MessageStatus = 1 WHERE ConvId = ?");
$upd->bind_param('i', $convId);
$upd->execute();
$upd->close();

// 3) Return JSON
header('Content-Type: application/json');
echo json_encode($messages);
