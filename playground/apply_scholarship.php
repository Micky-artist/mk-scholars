<?php
session_start();
include('./dbconnection/connection.php');

header('Content-Type: application/json');

if (!isset($_SESSION['userId'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$scholarshipId = $data['scholarshipId'];
$userId = $_SESSION['userId'];

// Check if the user has already applied
$checkQuery = "SELECT * FROM applications WHERE userId = ? AND scholarshipId = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $userId, $scholarshipId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already applied for this scholarship']);
    exit;
}

// Insert new application
$insertQuery = "INSERT INTO applications (userId, scholarshipId, applicationDate, status) 
                VALUES (?, ?, NOW(), 'pending')";
$stmt = $conn->prepare($insertQuery);
$stmt->bind_param("ii", $userId, $scholarshipId);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Application submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error submitting application']);
}
?>