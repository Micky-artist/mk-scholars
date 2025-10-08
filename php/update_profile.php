<?php
session_start();
header('Content-Type: application/json');
include('../dbconnection/connection.php');

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$userId = $_SESSION['userId'];

// Get form data with correct field names
$userName = trim($_POST['userName'] ?? '');
$userEmail = trim($_POST['userEmail'] ?? '');
$userPhone = trim($_POST['userPhone'] ?? '');
$currentPassword = $_POST['currentPassword'] ?? '';
$newPassword = $_POST['newPassword'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

// Validate required fields
if (empty($userName) || empty($userEmail)) {
    echo json_encode(['success' => false, 'message' => 'Name and email are required']);
    exit;
}

if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Check if email already exists for another user
$stmt = $conn->prepare("SELECT NoUserId FROM normUsers WHERE NoEmail = ? AND NoUserId != ?");
$stmt->bind_param("si", $userEmail, $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit;
}
$stmt->close();

// Handle password change if provided
if (!empty($newPassword)) {
    if (empty($currentPassword)) {
        echo json_encode(['success' => false, 'message' => 'Current password is required to change password']);
        exit;
    }
    
    if ($newPassword !== $confirmPassword) {
        echo json_encode(['success' => false, 'message' => 'New passwords do not match']);
        exit;
    }
    
    if (strlen($newPassword) < 6) {
        echo json_encode(['success' => false, 'message' => 'New password must be at least 6 characters']);
        exit;
    }
    
    // Verify current password
    $stmt = $conn->prepare("SELECT NoPassword FROM normUsers WHERE NoUserId = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (!password_verify($currentPassword, $user['NoPassword'])) {
            echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
            exit;
        }
    }
    $stmt->close();
    
    // Update with new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE normUsers SET NoUsername = ?, NoEmail = ?, NoPhone = ?, NoPassword = ? WHERE NoUserId = ?");
    $stmt->bind_param("ssssi", $userName, $userEmail, $userPhone, $hashedPassword, $userId);
} else {
    // Update without password
    $stmt = $conn->prepare("UPDATE normUsers SET NoUsername = ?, NoEmail = ?, NoPhone = ? WHERE NoUserId = ?");
    $stmt->bind_param("sssi", $userName, $userEmail, $userPhone, $userId);
}

if ($stmt->execute()) {
    // Update session variables
    $_SESSION['username'] = $userName;
    $_SESSION['userName'] = $userName;
    $_SESSION['NoEmail'] = $userEmail;
    $_SESSION['userEmail'] = $userEmail;
    $_SESSION['userPhone'] = $userPhone;
    
    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}

$stmt->close();
$conn->close(); 