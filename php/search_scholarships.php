<?php
include('../dbconnection/connection.php');

$searchTerm = isset($_GET['q']) ? $_GET['q'] : '';
$results = [];

if(!empty($searchTerm)) {
    $stmt = $conn->prepare("SELECT scholarshipId, scholarshipTitle, scholarshipUpdateDate, 
                           amount, country FROM scholarships 
                           WHERE scholarshipTitle LIKE ? OR country LIKE ?
                           LIMIT 10");
    $searchParam = "%$searchTerm%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($results);
?>