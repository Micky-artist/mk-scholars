<?php
session_start();
include("./dbconnection/connection.php");

// Initialize message variables
$msg = '';
$class = '';

if (isset($_POST['reset_password']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

        // Validate required fields exist
        $required_fields = ['email', 'phone', 'newPassword', 'coNewPassword'];
        $missing_fields = false;
        
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                $missing_fields = true;
                break;
            }
        }
        
        if ($missing_fields) {
            $msg = "All fields are required.";
            $class = "alert alert-danger";
        } else {
            // Sanitize inputs
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $newPassword = $_POST['newPassword']; // Don't escape password before hashing
            $coNewPassword = $_POST['coNewPassword'];
            
            // Validate inputs
            $validationErrors = [];
            
            // Email validation
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validationErrors[] = "Please enter a valid email address.";
            }
            
            // Phone validation (basic pattern)
            if (!preg_match('/^[0-9+\-\s]{6,20}$/', $phone)) {
                $validationErrors[] = "Please enter a valid phone number.";
            }
            
            // Password validation
            if (strlen($newPassword) < 8) {
                $validationErrors[] = "Password must be at least 8 characters.";
            }
            
            if (!preg_match('/[A-Z]/', $newPassword)) {
                $validationErrors[] = "Password must contain at least one uppercase letter.";
            }
            
            if (!preg_match('/[a-z]/', $newPassword)) {
                $validationErrors[] = "Password must contain at least one lowercase letter.";
            }
            
            if (!preg_match('/[0-9]/', $newPassword)) {
                $validationErrors[] = "Password must contain at least one number.";
            }
            
            // Password confirmation
            if ($newPassword !== $coNewPassword) {
                $validationErrors[] = "Passwords do not match.";
            }
            
            // If validation errors exist, display them
            if (!empty($validationErrors)) {
                $msg = implode("<br>", $validationErrors);
                $class = "alert alert-danger";
            } else {
                // Check if email and phone match an existing user
                $stmt = $conn->prepare("SELECT NoEmail, NoPhone FROM normUsers WHERE NoEmail = ? AND NoPhone = ?");
                if (!$stmt) {
                    $msg = "System error. Please try again later.";
                    $class = "alert alert-danger";
                    error_log("Database error: " . $conn->error);
                } else {
                    $stmt->bind_param("ss", $email, $phone);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows === 0) {
                        $msg = "No account found with the provided email and phone number.";
                        $class = "alert alert-danger";
                    } else {
                        // Create secure password hash
                        $encPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]);
                        
                        // Update user password with prepared statement
                        $updateStmt = $conn->prepare("UPDATE normUsers SET NoPassword = ? WHERE NoEmail = ? AND NoPhone = ?");
                        
                        if (!$updateStmt) {
                            $msg = "System error. Please try again later.";
                            $class = "alert alert-danger";
                            error_log("Database error: " . $conn->error);
                        } else {
                            $updateStmt->bind_param("sss", $encPassword, $email, $phone);
                            
                            if ($updateStmt->execute()) {
                                // Generate a new CSRF token after successful password reset
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                $msg = "Your password has been reset successfully!";
                                $class = "alert alert-success";
                            } else {
                                $msg = "Password reset failed. Please try again.";
                                $class = "alert alert-danger";
                                error_log("Update error: " . $updateStmt->error);
                            }
                            
                            $updateStmt->close();
                        }
                    }
                    
                    $stmt->close();
                }
            }
        }
    
}

// Generate CSRF token for the form if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security headers
header("X-Frame-Options: DENY");
header("Content-Security-Policy: frame-ancestors 'none'");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
?>