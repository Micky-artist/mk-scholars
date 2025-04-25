<?php
session_start();
include("../dbconnections/connection.php");
if ($_SERVER['REQUEST_METHOD']!=='POST') exit;

$convId  = intval($_POST['ConvId']  ?? 0);
$adminId = intval($_SESSION['adminId'] ?? 0);
$userId  = 0;
$msg     = trim($_POST['message']   ?? '');
if (!$convId || !$adminId || !$userId || $msg==='') exit;

// Insert into ALL required columns
$stmt = $conn->prepare("INSERT INTO Message
    (UserId,senderId,AdminId,ConvId,MessageContent,SentDate,SentTime,MessageStatus)
  VALUES
    (?,?,?,?,?,CURDATE(),CURTIME(),0)
");
$stmt->bind_param('iiiis',$userId,$adminId,$adminId,$convId,$msg);
$stmt->execute();
$stmt->close();
