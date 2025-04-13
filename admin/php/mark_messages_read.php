<?php
session_start();
include('../dbconnection/connection.php');

if (!isset($_SESSION['adminId']) || !isset($_GET['ConvId'])) {
    exit("Invalid request");
}

$convId = intval($_GET['ConvId']);
$adminId = intval($_SESSION['adminId']);

$stmt = $conn->prepare("UPDATE Message SET MessageStatus = 1 WHERE ConvId = ? AND MessageStatus = 0 AND AdminId = 0");
$stmt->bind_param("i", $convId);
$stmt->execute();
