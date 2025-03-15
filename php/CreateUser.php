<?php
// Initialize message variables
$msg = '';
$class = '';

if (isset($_POST['signup']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token if implemented
        // Validate required fields exist
        $required_fields = ['NoUsername', 'NoEmail', 'NoPhone', 'NoPassword', 'NoCoPassword'];
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
            $NoUsername = trim($_POST['NoUsername']);
            $NoEmail = trim($_POST['NoEmail']);
            $NoPhone = trim($_POST['NoPhone']);
            $NoPassword = $_POST['NoPassword']; // Don't escape password before hashing
            $NoCoPassword = $_POST['NoCoPassword'];
            $aggree = isset($_POST['aggree']) ? 1 : 0;
            
            // Validate inputs
            $validationErrors = [];
            
            // Username validation
            if (strlen($NoUsername) < 3 || strlen($NoUsername) > 50) {
                $validationErrors[] = "Username must be between 3 and 50 characters.";
            }
            
            // Email validation
            if (!filter_var($NoEmail, FILTER_VALIDATE_EMAIL)) {
                $validationErrors[] = "Please enter a valid email address.";
            }
            
            // Phone validation (basic pattern)
            if (!preg_match('/^[0-9+\-\s]{6,20}$/', $NoPhone)) {
                $validationErrors[] = "Please enter a valid phone number.";
            }
            
            // Password validation
            if (strlen($NoPassword) < 8) {
                $validationErrors[] = "Password must be at least 8 characters.";
            }
            
            if (!preg_match('/[A-Z]/', $NoPassword)) {
                $validationErrors[] = "Password must contain at least one uppercase letter.";
            }
            
            if (!preg_match('/[a-z]/', $NoPassword)) {
                $validationErrors[] = "Password must contain at least one lowercase letter.";
            }
            
            if (!preg_match('/[0-9]/', $NoPassword)) {
                $validationErrors[] = "Password must contain at least one number.";
            }
            
            // Password confirmation
            if ($NoPassword !== $NoCoPassword) {
                $validationErrors[] = "Passwords do not match.";
            }
            
            // Terms agreement
            if (!$aggree) {
                $validationErrors[] = "You must agree to the terms and conditions and privacy policy.";
            }
            
            // If validation errors exist, display them
            if (!empty($validationErrors)) {
                $msg = implode("<br>", $validationErrors);
                $class = "alert alert-danger";
            } else {
                // Check if email or phone already exists using prepared statements
                $stmt = $conn->prepare("SELECT NoEmail, NoPhone FROM normUsers WHERE NoEmail = ? OR NoPhone = ?");
                if (!$stmt) {
                    $msg = "System error. Please try again later.";
                    $class = "alert alert-danger";
                    error_log("Database error: " . $conn->error);
                } else {
                    $stmt->bind_param("ss", $NoEmail, $NoPhone);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $existingUser = $result->fetch_assoc();
                        if ($existingUser['NoEmail'] === $NoEmail) {
                            $msg = "This email address is already registered.";
                        } else {
                            $msg = "This phone number is already registered.";
                        }
                        $class = "alert alert-danger";
                    } else {
                        // Create secure password hash
                        $encPassword = password_hash($NoPassword, PASSWORD_DEFAULT, ['cost' => 12]);
                        $creation_date = date('Y-m-d');
                        $status = 1;
                        
                        // Generate verification code if needed
                        $verificationCode = bin2hex(random_bytes(16));
                        
                        // Insert user with prepared statement
                        $insertStmt = $conn->prepare("INSERT INTO normUsers(NoUsername, NoEmail, NoPhone, NoPassword, NoStatus, NoCreationDate, VerificationCode) VALUES(?, ?, ?, ?, ?, ?, ?)");
                        
                        if (!$insertStmt) {
                            $msg = "System error. Please try again later.";
                            $class = "alert alert-danger";
                            error_log("Database error: " . $conn->error);
                        } else {
                            $insertStmt->bind_param("sssssss", $NoUsername, $NoEmail, $NoPhone, $encPassword, $status, $creation_date, $verificationCode);
                            
                            if ($insertStmt->execute()) {
                                // Generate a new CSRF token after successful registration
                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                
                                // Log successful registration
                                $userId = $insertStmt->insert_id;
                                $ipAddress = $_SERVER['REMOTE_ADDR'];
                                $userAgent = $_SERVER['HTTP_USER_AGENT'];
                                
                                // $logStmt = $conn->prepare("INSERT INTO registration_logs (user_id, ip_address, user_agent) VALUES (?, ?, ?)");
                                // if ($logStmt) {
                                //     $logStmt->bind_param("iss", $userId, $ipAddress, $userAgent);
                                //     $logStmt->execute();
                                //     $logStmt->close();
                                // }
                                
                                // If you want to automatically log them in:
                                // $_SESSION['username'] = $NoUsername;
                                // $_SESSION['userId'] = $userId;
                                // $_SESSION['status'] = $status;
                                
                                $msg = "Your account has been created successfully!";
                                $class = "alert alert-success";
                                
                                // Optional: Send verification email with verification code
                                // sendVerificationEmail($NoEmail, $NoUsername, $verificationCode);
                            } else {
                                $msg = "Account creation failed. Please try again.";
                                $class = "alert alert-danger";
                                error_log("Insert error: " . $insertStmt->error);
                            }
                            
                            $insertStmt->close();
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