<?php
session_start();
include('../dbconnection/connection.php');

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!empty($searchTerm)) {
    $query = "SELECT scholarshipId, scholarshipTitle, scholarshipDetails, scholarshipUpdateDate, scholarshipLink, scholarshipYoutubeLink, embededVideo, scholarshipImage, scholarshipStatus, amount, country FROM scholarships WHERE scholarshipTitle LIKE ? OR country LIKE ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $searchTerm = "%$searchTerm%";
        $stmt->bind_param("ss", $searchTerm, $searchTerm);
        $stmt->execute();
        $result = $stmt->get_result();
        $scholarships = [];
        while ($row = $result->fetch_assoc()) {
            $scholarships[] = $row;
        }
        echo json_encode($scholarships);
    } else {
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>