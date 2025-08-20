<?php
// Start session
session_start();

// Set content type to JSON
header('Content-Type: application/json');

// Include database connection
try {
    include_once("./dbconnection/connection.php");
} catch (Exception $e) {
    error_log("Database connection error in process-writing-service.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone number (basic validation)
function isValidPhone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    // Check if it's between 10-15 digits
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

try {
    // Get and validate form data
    $fullName = isset($_POST['full_name']) ? sanitizeInput($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $serviceType = isset($_POST['service_type']) ? sanitizeInput($_POST['service_type']) : '';
    $urgency = isset($_POST['urgency']) ? sanitizeInput($_POST['urgency']) : '';
    $deadline = isset($_POST['deadline']) ? sanitizeInput($_POST['deadline']) : '';
    $wordCount = isset($_POST['word_count']) ? sanitizeInput($_POST['word_count']) : '';
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    $additionalInfo = isset($_POST['additional_info']) ? sanitizeInput($_POST['additional_info']) : '';
    
    // Validation
    $errors = [];
    
    if (empty($fullName) || strlen($fullName) < 2) {
        $errors[] = 'Full name is required and must be at least 2 characters long';
    }
    
    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Valid email address is required';
    }
    
    if (empty($phone) || !isValidPhone($phone)) {
        $errors[] = 'Valid phone number is required';
    }
    
    if (empty($serviceType)) {
        $errors[] = 'Service type is required';
    }
    
    if (empty($urgency)) {
        $errors[] = 'Urgency level is required';
    }
    
    if (empty($description) || strlen($description) < 10) {
        $errors[] = 'Description is required and must be at least 10 characters long';
    }
    
    // If there are validation errors, return them
    if (!empty($errors)) {
        echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
        exit;
    }
    
    // Create writing_services table if it doesn't exist
    $createTableSQL = "CREATE TABLE IF NOT EXISTS writing_services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        service_type VARCHAR(100) NOT NULL,
        urgency VARCHAR(50) NOT NULL,
        deadline DATE,
        word_count VARCHAR(50),
        description TEXT NOT NULL,
        additional_info TEXT,
        status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $createTableSQL)) {
        error_log("Error creating writing_services table: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Database setup error']);
        exit;
    }
    
    // Prepare and execute the insert statement
    $insertSQL = "INSERT INTO writing_services (full_name, email, phone, service_type, urgency, deadline, word_count, description, additional_info) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $insertSQL);
    if (!$stmt) {
        error_log("Error preparing statement: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit;
    }
    
    // Bind parameters
    mysqli_stmt_bind_param($stmt, 'sssssssss', 
        $fullName, $email, $phone, $serviceType, $urgency, $deadline, $wordCount, $description, $additionalInfo
    );
    
    // Execute the statement
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Error executing statement: " . mysqli_stmt_error($stmt));
        echo json_encode(['success' => false, 'message' => 'Failed to save request']);
        exit;
    }
    
    // Get the inserted ID
    $requestId = mysqli_insert_id($conn);
    
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Send email notification (if email functionality is available)
    $emailSent = false;
    try {
        $emailSent = sendEmailNotification($fullName, $email, $serviceType, $requestId);
    } catch (Exception $e) {
        error_log("Email notification error: " . $e->getMessage());
        // Don't fail the request if email fails
    }
    
    // Log the successful submission
    error_log("Writing service request submitted successfully. ID: $requestId, Name: $fullName, Email: $email, Service: $serviceType");
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'Your writing service request has been submitted successfully!',
        'request_id' => $requestId,
        'email_sent' => $emailSent
    ]);
    
} catch (Exception $e) {
    error_log("Error in process-writing-service.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred. Please try again.']);
} finally {
    // Close database connection
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}

/**
 * Send email notification for new writing service request
 */
function sendEmailNotification($fullName, $email, $serviceType, $requestId) {
    // Check if mail function is available
    if (!function_exists('mail')) {
        return false;
    }
    
    // Email to admin
    $adminEmail = 'mkscholars250@gmail.com';
    $subject = "New Writing Service Request - #$requestId";
    
    $adminMessage = "
    New Writing Service Request Received
    
    Request ID: #$requestId
    Name: $fullName
    Email: $email
    Service Type: $serviceType
    Date: " . date('Y-m-d H:i:s') . "
    
    Please check the admin panel for full details.
    ";
    
    $adminHeaders = "From: noreply@mkscholars.com\r\n";
    $adminHeaders .= "Reply-To: $email\r\n";
    $adminHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $adminEmailSent = mail($adminEmail, $subject, $adminMessage, $adminHeaders);
    
    // Email to customer (confirmation)
    $customerSubject = "Writing Service Request Confirmation - MK Scholars";
    
    $customerMessage = "
    Dear $fullName,
    
    Thank you for submitting your writing service request to MK Scholars!
    
    Request Details:
    - Request ID: #$requestId
    - Service Type: $serviceType
    - Date Submitted: " . date('Y-m-d H:i:s') . "
    
    We have received your request and our team will review it shortly. 
    You will be contacted within 24 hours with further details and pricing information.
    
    If you have any questions, please don't hesitate to contact us:
    - Email: mkscholars250@gmail.com
    - Phone: +250798611161
    
    Best regards,
    MK Scholars Team
    ";
    
    $customerHeaders = "From: noreply@mkscholars.com\r\n";
    $customerHeaders .= "Reply-To: mkscholars250@gmail.com\r\n";
    $customerHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    $customerEmailSent = mail($email, $customerSubject, $customerMessage, $customerHeaders);
    
    return $adminEmailSent && $customerEmailSent;
}
?>
