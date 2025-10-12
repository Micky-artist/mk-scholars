<?php
// Include session configuration for persistent sessions
include("../config/session.php");

// Initialize variables
$msg = '';
$class = '';

// Check if the form was submitted
if (isset($_POST['login']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Implement rate limiting
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt'] = time();
    } else if (time() - $_SESSION['last_attempt'] > 3600) {
        // Reset after 1 hour
        $_SESSION['login_attempts'] = 0;
    }
    
    if ($_SESSION['login_attempts'] >= 5 && time() - $_SESSION['last_attempt'] < 900) {
        // Block for 15 minutes after 5 failed attempts
        $msg = 'Too many failed login attempts. Please try again later.';
        $class = 'alert alert-danger';
    } else {
        // Update attempt counter
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt'] = time();
        
        // Validate input exists
        if (empty($_POST['username']) || empty($_POST['password'])) {
            $msg = 'Please provide both username and password';
            $class = 'alert alert-danger';
        } else {
            // Sanitize inputs
            $NoUserName = trim($_POST['username']);
            $NoPassword = $_POST['password']; // Don't escape password before verification
            
            // Use prepared statements to prevent SQL injection
            $stmt = $conn->prepare("SELECT * FROM normUsers WHERE (NoEmail = ? OR NoPhone = ?) AND NoStatus = 1");
            
            if ($stmt) {
                $stmt->bind_param("ss", $NoUserName, $NoUserName);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $account = $result->fetch_assoc();
                    
                    // Verify password with constant-time comparison
                    if (password_verify($NoPassword, $account['NoPassword'])) {
                        // Reset failed login attempts
                        $_SESSION['login_attempts'] = 0;
                        
                        // Set session variables
                        $_SESSION['username'] = $account['NoUsername'];
                        $_SESSION['userId'] = $account['NoUserId'];
                        $_SESSION['status'] = $account['NoStatus'];
                        $_SESSION['last_activity'] = time();
                        
                        // Add CSRF token
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        
                        // Check for password rehashing needs (if using newer algorithm)
                        if (password_needs_rehash($account['NoPassword'], PASSWORD_DEFAULT)) {
                            $newHash = password_hash($NoPassword, PASSWORD_DEFAULT);
                            $updateStmt = $conn->prepare("UPDATE normUsers SET NoPassword = ? WHERE NoUserId = ?");
                            $updateStmt->bind_param("si", $newHash, $account['NoUserId']);
                            $updateStmt->execute();
                            $updateStmt->close();
                        }
                        
                        // Log successful login
                        // $ipAddress = $_SERVER['REMOTE_ADDR'];
                        // $userAgent = $_SERVER['HTTP_USER_AGENT'];
                        // $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, 'success')");
                        // $logStmt->bind_param("iss", $account['NoUserId'], $ipAddress, $userAgent);
                        // $logStmt->execute();
                        // $logStmt->close();
                        
                        // Redirect user
                        // Clear any output buffer
                        if (ob_get_level()) {
                            ob_end_clean();
                        }
                        
                        // Debug: Log successful login
                        error_log("Login successful for user: " . $account['NoUsername'] . " (ID: " . $account['NoUserId'] . ")");
                        
                        // Set redirect header
                        $redirectUrl = "./e-learning";
                        
                        // Check if e-learning page exists and is accessible
                        if (file_exists("../e-learning.php")) {
                            // Clear any output buffer before redirect
                            if (ob_get_level()) {
                                ob_end_clean();
                            }
                            
                            header("Location: " . $redirectUrl);
                            header("Cache-Control: no-cache, no-store, must-revalidate");
                            header("Pragma: no-cache");
                            header("Expires: 0");
                            exit();
                        } else {
                            // Fallback to dashboard if e-learning doesn't exist
                            $redirectUrl = "./dashboard";
                            
                            // Clear any output buffer before redirect
                            if (ob_get_level()) {
                                ob_end_clean();
                            }
                            
                            header("Location: " . $redirectUrl);
                            header("Cache-Control: no-cache, no-store, must-revalidate");
                            header("Pragma: no-cache");
                            header("Expires: 0");
                            exit();
                        }
                    } else {
                        // Log failed attempt
                        // $ipAddress = $_SERVER['REMOTE_ADDR'];
                        // $userAgent = $_SERVER['HTTP_USER_AGENT'];
                        // $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address, user_agent, status) VALUES (?, ?, ?, 'failed')");
                        // $logStmt->bind_param("iss", $account['NoUserId'], $ipAddress, $userAgent);
                        // $logStmt->execute();
                        // $logStmt->close();
                        
                        // Use generic error message for security
                        $msg = 'Invalid username or password';
                        $class = 'alert alert-danger';
                    }
                } else {
                    // Use generic error message to prevent username enumeration
                    $msg = 'Invalid username or password';
                    $class = 'alert alert-danger';
                }
                
                $stmt->close();
            } else {
                $msg = 'System error. Please try again later.';
                $class = 'alert alert-danger';
                // Log the actual error for administrators
                error_log("Database error in login: " . $conn->error);
            }
        }
    }
}

// Add a security header to prevent clickjacking
header("X-Frame-Options: DENY");
header("Content-Security-Policy: frame-ancestors 'none'");
?>