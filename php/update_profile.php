<?php
header('Content-Type: application/json');
include('../dbconnection/connection.php');

if (!isset($_POST['userId']) || !isset($_POST['NoUsername']) || !isset($_POST['NoEmail']) || !isset($_POST['NoPhone'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$userId = intval($_POST['userId']);
$username = trim($_POST['NoUsername']);
$email = trim($_POST['NoEmail']);
$phone = trim($_POST['NoPhone']);

// Validate inputs
if (empty($username) || empty($email) || empty($phone)) {
    echo json_encode(['error' => 'All fields are required']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Check if email already exists for another user
$stmt = $conn->prepare("SELECT NoUserId FROM normUsers WHERE NoEmail = ? AND NoUserId != ?");
$stmt->bind_param("si", $email, $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['error' => 'Email already exists']);
    exit;
}
$stmt->close();

// Update user profile
$stmt = $conn->prepare("UPDATE normUsers SET NoUsername = ?, NoEmail = ?, NoPhone = ? WHERE NoUserId = ?");
$stmt->bind_param("sssi", $username, $email, $phone, $userId);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to update profile']);
}

$stmt->close();
$conn->close(); 