<?php
session_start();
include('../dbconnection/connection.php');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$userId = $_SESSION['userId'];

$stmt = $conn->prepare("SELECT username, email FROM users WHERE userId = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    echo json_encode($result->fetch_assoc());
} else {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
}
