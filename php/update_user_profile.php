<?php
session_start();
include('../dbconnection/connection.php');

if (!isset($_SESSION['userId'])) {
    http_response_code(401);
    exit('Unauthorized');
}

$userId = $_SESSION['userId'];
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($username) || empty($email)) {
    http_response_code(400);
    exit('Username and email are required');
}

// Optional: Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    exit('Invalid email format');
}

// Update logic
if (!empty($password)) {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE userId = ?");
    $stmt->bind_param("sssi", $username, $email, $hashedPassword, $userId);
} else {
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE userId = ?");
    $stmt->bind_param("ssi", $username, $email, $userId);
}

if ($stmt->execute()) {
    echo "Profile updated";
} else {
    http_response_code(500);
    echo "Update failed";
}
