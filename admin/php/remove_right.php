<?php
// session_start();

// // Database connection
// $host = 'localhost';
// $db = 'mkscholars';
// $user = 'root';
// $pass = '';
// $conn = new mysqli($host, $user, $pass, $db);

// if ($conn->connect_error) {
//     die(json_encode(['success' => false, 'message' => 'Database connection failed']));
// }
include("../dbconnections/connection.php");

// Get admin ID and right from the query string
$adminId = isset($_GET['adminId']) ? (int)$_GET['adminId'] : 0;
$right = isset($_GET['right']) ? $_GET['right'] : '';

if ($adminId > 0 && !empty($right)) {
    // Update the right to 0 in the database
    $sql = "UPDATE AdminRights SET $right = 0 WHERE AdminId = $adminId";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}

$conn->close();
?>