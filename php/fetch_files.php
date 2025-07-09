<?php
include('../dbconnection/connection.php');
header('Content-Type: application/json');

$convId = isset($_GET['ConvId']) ? intval($_GET['ConvId']) : 0;
$files = [];
if ($convId > 0) {
    $query = "SELECT MessageContent, SentDate, SentTime, UserId FROM Message WHERE ConvId = $convId AND MessageContent LIKE './uploads/%' ORDER BY SentDate, SentTime";
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $files[] = $row;
        }
    }
}
echo json_encode($files);
$conn->close(); 