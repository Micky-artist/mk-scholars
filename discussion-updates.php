<?php
// Include session configuration for persistent sessions
include("./config/session.php");
include('./dbconnection/connection.php');
include('./php/validateSession.php');

header('Content-Type: application/json');

$courseId = isset($_GET['courseId']) ? (int)$_GET['courseId'] : 0;
$lastUpdate = isset($_GET['lastUpdate']) ? $_GET['lastUpdate'] : '';

if (!$courseId) {
    echo json_encode(['hasUpdates' => false, 'currentTime' => date('c')]);
    exit;
}

// Check if there are any new discussions since last update
$checkQuery = "SELECT COUNT(*) as count FROM DiscussionBoard 
               WHERE courseId = ? AND createdDate > ? AND isApproved = 1";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("is", $courseId, $lastUpdate);
$checkStmt->execute();
$result = $checkStmt->get_result();
$row = $result->fetch_assoc();
$checkStmt->close();

$hasUpdates = $row['count'] > 0;

echo json_encode([
    'hasUpdates' => $hasUpdates,
    'currentTime' => date('c'),
    'newCount' => $row['count']
]);
?>
