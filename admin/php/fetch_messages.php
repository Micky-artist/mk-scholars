<?php
session_start();
include("../dbconnections/connection.php");
if (empty($_GET['ConvId'])) { echo json_encode([]); exit; }
$convId = intval($_GET['ConvId']);

// $stmt = $conn->prepare("SELECT u.userId, u.username, m.AdminId, m.MessageContent, m.SentDate, m.SentTime
//     FROM Message m
//     JOIN users u ON m.AdminId = u.userId
//     WHERE m.ConvId = ?
//     ORDER BY m.SentDate, m.SentTime");

$stmt = $conn->prepare("SELECT AdminId, MessageContent, SentDate, SentTime
    FROM Message WHERE ConvId = ? ORDER BY SentDate, SentTime");

$stmt->bind_param('i',$convId);
$stmt->execute();
$res = $stmt->get_result();
$messages = $res->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// mark as read
$conn->query("UPDATE Message SET MessageStatus=1 WHERE ConvId={$convId}");

header('Content-Type: application/json');
echo json_encode($messages);
