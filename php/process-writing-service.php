<?php
// Process Writing Service Application Form
session_start();

// Include database connection
include("../dbconnection/connection.php");

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and validate input data
    $service_type = isset($_POST['service_type']) ? htmlspecialchars($_POST['service_type'], ENT_QUOTES, 'UTF-8') : '';
    $full_name = isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name'], ENT_QUOTES, 'UTF-8') : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone'], ENT_QUOTES, 'UTF-8') : '';
    $deadline = isset($_POST['deadline']) ? htmlspecialchars($_POST['deadline'], ENT_QUOTES, 'UTF-8') : '';
    $document_length = isset($_POST['document_length']) ? htmlspecialchars($_POST['document_length'], ENT_QUOTES, 'UTF-8') : '';
    $requirements = isset($_POST['requirements']) ? htmlspecialchars($_POST['requirements'], ENT_QUOTES, 'UTF-8') : '';
    $budget = isset($_POST['budget']) ? htmlspecialchars($_POST['budget'], ENT_QUOTES, 'UTF-8') : '';
    
    // Validation
    $errors = [];
    
    if (empty($service_type)) {
        $errors[] = "Service type is required";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required";
    }
    
    if (empty($requirements)) {
        $errors[] = "Requirements and details are required";
    }
    
    // If no errors, proceed with database insertion
    if (empty($errors)) {
        
        // Prepare SQL statement
        $sql = "INSERT INTO writing_service_applications (
            service_type, 
            full_name, 
            email, 
            phone, 
            deadline, 
            document_length, 
            requirements, 
            budget, 
            application_date,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')";
        
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssssss", 
                $service_type, 
                $full_name, 
                $email, 
                $phone, 
                $deadline, 
                $document_length, 
                $requirements, 
                $budget
            );
            
            if ($stmt->execute()) {
                // Success - redirect with success message
                $_SESSION['writing_service_success'] = "Your writing service application has been submitted successfully! We'll contact you within 24 hours.";
                header("Location: ../writing-services?success=1");
                exit();
            } else {
                // Database error
                $_SESSION['writing_service_error'] = "Database error: " . $stmt->error;
                header("Location: ../writing-services?error=1");
                exit();
            }
            
            $stmt->close();
        } else {
            // Prepare statement error
            $_SESSION['writing_service_error'] = "Database preparation error";
            header("Location: ../writing-services?error=1");
            exit();
        }
        
    } else {
        // Validation errors
        $_SESSION['writing_service_errors'] = $errors;
        $_SESSION['writing_service_form_data'] = $_POST;
        header("Location: ../writing-services?error=1");
        exit();
    }
    
} else {
    // Not a POST request
    header("Location: ../writing-services");
    exit();
}

// Close database connection
$conn->close();
?>
