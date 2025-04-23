<?php
session_start();
include("../dbconnections/connection.php");
if (isset($_GET['subId'])) {
    $subId = (int) $_GET['subId'];

    $stmt = $conn->prepare("
      UPDATE subscription
      SET SubscriptionStatus = 0
      WHERE SubId = ?
    ");
    $stmt->bind_param('i', $subId);
    $stmt->execute();
    $stmt->close();
}

// redirect back
header('Location: ../subscriptions');
exit;
