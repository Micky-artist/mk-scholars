<?php
// Ensure session is started
session_start();

// Check if user is already logged in
if (isset($_SESSION['adminId'])) {
    // Redirect to dashboard if already logged in
    header("Location: ./index");
    exit;
}

// Initialize variables
$msg = '';
$class = '';

if (isset($_POST['submit'])) {
    // Validate that required fields exist and are not empty
    if (empty($_POST['adminName']) || empty($_POST['password'])) {
        $msg = 'All fields are required';
        $class = 'formMsgFail';
    } else {
        // Use prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT userId, username, password, status FROM users WHERE email = ? AND status = 1 LIMIT 1");
        $stmt->bind_param("s", $_POST['adminName']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $account = $result->fetch_assoc();
            
            // Use constant-time comparison for password verification
            if (password_verify($_POST['password'], $account['password'])) {
                // Set session variables
                $_SESSION['AdminName'] = $account['username'];
                $_SESSION['adminId'] = $account['userId'];
                $_SESSION['accountstatus'] = $account['status'];
                
                // Add CSRF token for additional security
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                
                // Log successful login
                $userId = $account['userId'];
                $logMessage = "Admin login successful";
                $logDate = date("Y-m-d");
                $logTime = date("H:i:s");
                
                $logStmt = $conn->prepare("INSERT INTO Logs (userId, logMessage, logDate, logTime, logStatus) VALUES (?, ?, ?, ?, 0)");
                $logStmt->bind_param("isss", $userId, $logMessage, $logDate, $logTime);
                $logStmt->execute();
                $logStmt->close();
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Redirect to dashboard
                header("Location: ./index");
                exit;
            } else {
                // Failed login - incorrect password
                $msg = 'Incorrect password';
                $class = 'formMsgFail';
                
                // Optional: Log failed login attempt
                // Don't include the actual password in logs
                $logMessage = "Failed login attempt for email: " . htmlspecialchars($_POST['adminName']);
                $logDate = date("Y-m-d");
                $logTime = date("H:i:s");
                
                $logStmt = $conn->prepare("INSERT INTO Logs (userId, logMessage, logDate, logTime, logStatus) VALUES (0, ?, ?, ?, 0)");
                $logStmt->bind_param("sss", $logMessage, $logDate, $logTime);
                $logStmt->execute();
                $logStmt->close();
            }
        } else {
            // Failed login - user not found or not active
            $msg = 'Login Failed';
            $class = 'formMsgFail';
            
            // Optional: Log failed login attempt
            $logMessage = "Failed login attempt for non-existent or inactive email: " . htmlspecialchars($_POST['adminName']);
            $logDate = date("Y-m-d");
            $logTime = date("H:i:s");
            
            $logStmt = $conn->prepare("INSERT INTO Logs (userId, logMessage, logDate, logTime, logStatus) VALUES (0, ?, ?, ?, 0)");
            $logStmt->bind_param("sss", $logMessage, $logDate, $logTime);
            $logStmt->execute();
            $logStmt->close();
        }
        
        // Close the main statement
        $stmt->close();
    }
}
?>