<?php
session_start();
include('../dbconnection/connection.php');

$searchTerm = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!empty($searchTerm)) {
    // Join scholarships with countries table to fetch CountryName
    $query = "SELECT s.*, c.CountryName 
              FROM scholarships s 
              JOIN countries c ON s.country = c.countryId 
              WHERE s.scholarshipTitle LIKE ? OR c.CountryName LIKE ?";
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