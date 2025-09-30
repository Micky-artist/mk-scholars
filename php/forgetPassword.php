<?php
// Ensure session is started and DB is included by parent, but keep safe fallbacks
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($conn)) {
    // Resolve path correctly when included from root page
    include(__DIR__ . '/../dbconnection/connection.php');
}

// Initialize message variables
$msg = isset($msg) ? $msg : '';
$class = isset($class) ? $class : '';
$email = isset($email) ? $email : '';
$phone = isset($phone) ? $phone : '';
$newPassword = isset($newPassword) ? $newPassword : '';
$coNewPassword = isset($coNewPassword) ? $coNewPassword : '';
// Field-specific error map for inline rendering
$errors = isset($errors) && is_array($errors) ? $errors : [];

if (isset($_POST['reset_password']) && $_SERVER['REQUEST_METHOD'] === 'POST') {

        // Ensure DB connection exists
        if (!$conn) {
            $msg = "Unable to connect to the database. Please try again later.";
            $class = "alert alert-danger";
        } else {

        // CSRF validation
        if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
            $msg = "Invalid session. Please refresh the page and try again.";
            $class = "alert alert-danger";
        } else {

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
            foreach ($required_fields as $field) {
                if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                    $errors[$field] = ucfirst($field) . " is required.";
                }
            }
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
                $errors['email'] = "Enter a valid email address.";
            }
            
            // Phone validation (basic pattern)
            if (!preg_match('/^[0-9+\-\s]{6,20}$/', $phone)) {
                $validationErrors[] = "Please enter a valid phone number.";
                $errors['phone'] = "Enter a valid phone number.";
            }
            
            // Password validation
            if (strlen($newPassword) < 8) {
                $validationErrors[] = "Password must be at least 8 characters.";
                $errors['newPassword'] = "At least 8 characters required.";
            }
            
            if (!preg_match('/[A-Z]/', $newPassword)) {
                $validationErrors[] = "Password must contain at least one uppercase letter.";
                $errors['newPassword'] = isset($errors['newPassword']) ? $errors['newPassword'] : "Must include at least one uppercase letter.";
            }
            
            if (!preg_match('/[a-z]/', $newPassword)) {
                $validationErrors[] = "Password must contain at least one lowercase letter.";
                $errors['newPassword'] = isset($errors['newPassword']) ? $errors['newPassword'] : "Must include at least one lowercase letter.";
            }
            
            if (!preg_match('/[0-9]/', $newPassword)) {
                $validationErrors[] = "Password must contain at least one number.";
                $errors['newPassword'] = isset($errors['newPassword']) ? $errors['newPassword'] : "Must include at least one number.";
            }
            
            // Password confirmation
            if ($newPassword !== $coNewPassword) {
                $validationErrors[] = "Passwords do not match.";
                $errors['coNewPassword'] = "Passwords do not match.";
            }
            
            // If validation errors exist, display them
            if (!empty($validationErrors)) {
                $msg = implode("<br>", $validationErrors);
                $class = "alert alert-danger";
            } else {
                // Look up user by email OR phone, then update by unique user id
                $stmt = $conn->prepare("SELECT NoUserId, NoEmail, NoPhone FROM normUsers WHERE NoEmail = ? OR NoPhone = ? LIMIT 1");
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
                        $errors['email'] = "Email not found for provided phone.";
                        $errors['phone'] = "Phone not found for provided email.";
                    } else {
                        $user = $result->fetch_assoc();
                        $userId = (int)$user['NoUserId'];
                        // Create secure password hash
                        $encPassword = password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]);
                        
                        // Update user password with prepared statement using primary key
                        $updateStmt = $conn->prepare("UPDATE normUsers SET NoPassword = ? WHERE NoUserId = ?");
                        
                        if (!$updateStmt) {
                            $msg = "System error. Please try again later.";
                            $class = "alert alert-danger";
                            error_log("Database error: " . $conn->error);
                        } else {
                            $updateStmt->bind_param("si", $encPassword, $userId);
                            
                            if ($updateStmt->execute()) {
                                // Generate a new CSRF token after successful password reset
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                $msg = "Your password has been reset successfully!";
                                $class = "alert alert-success";
                                // Clear sensitive fields
                                $newPassword = '';
                                $coNewPassword = '';
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
        }
    
}

// Generate CSRF token for the form if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Security headers
if (!headers_sent()) {
    header("X-Frame-Options: DENY");
    header("Content-Security-Policy: frame-ancestors 'none'");
    header("X-XSS-Protection: 1; mode=block");
    header("X-Content-Type-Options: nosniff");
}
?>