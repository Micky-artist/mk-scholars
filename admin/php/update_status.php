<?php 
include("../dbconnections/connection.php");
    

    // Check if request is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get application ID and new status
        $applicationId = isset($_POST['applicationId']) ? (int)$_POST['applicationId'] : 0;
        $newStatus = isset($_POST['newStatus']) ? (int)$_POST['newStatus'] : 0;
        
        // Validate input
        if ($applicationId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid application ID']);
            exit;
        }
        
        if ($newStatus < 0 || $newStatus > 4) {
            echo json_encode(['success' => false, 'message' => 'Invalid status value']);
            exit;
        }
        
        // Update status in database
        $query = "UPDATE scholarships SET scholarshipStatus = ? WHERE scholarshipId = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Error preparing statement: ' . $conn->error]);
            exit;
        }
        
        $stmt->bind_param("ii", $newStatus, $applicationId);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating status: ' . $stmt->error]);
        }
        
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }

    $conn->close();
    ?> 
    ?>