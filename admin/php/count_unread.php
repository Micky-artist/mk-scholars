<?php
include('../dbconnection/connection.php');

$query = "SELECT ConvId, COUNT(*) as unreadCount FROM Message WHERE MessageStatus = 0 AND AdminId = 0 GROUP BY ConvId";
$result = mysqli_query($conn, $query);

$counts = [];
while ($row = mysqli_fetch_assoc($result)) {
    $counts[$row['ConvId']] = $row['unreadCount'];
}

echo json_encode($counts);
