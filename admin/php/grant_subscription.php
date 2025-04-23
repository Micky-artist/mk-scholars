<?php
session_start();
include("../dbconnections/connection.php");
if (isset($_GET['subId'])) {
    $subId = (int) $_GET['subId'];
    // In a real app, pull adminId from your logged-in session
    $adminId       = 1;
    $today         = date('Y-m-d');
    // e.g. set expiry 30 days from now
    $expiration    = date('Y-m-d', strtotime('+30 days'));

    $stmt = $conn->prepare("
      UPDATE subscription
      SET 
        SubscriptionStatus = 1,
        adminId           = ?,
        subscriptionDate  = ?,
        expirationDate    = ?
      WHERE SubId = ?
    ");
    $stmt->bind_param('issi', $adminId, $today, $expiration, $subId);
    $stmt->execute();
    $stmt->close();
}

// redirect back
header('Location: ../subscriptions');
exit;
