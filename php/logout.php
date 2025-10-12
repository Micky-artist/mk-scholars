<title>Logging out...</title>
<?php
// Include session configuration
include("../config/session.php");

// Check if user is logged in
if(isset($_SESSION['username']) && isset($_SESSION['userId'])){
    // Log the logout
    error_log("User logout: " . $_SESSION['username'] . " (ID: " . $_SESSION['userId'] . ")");
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
    
    // Redirect to home page
    header("Location: ../index");
    exit();
} else {
    // User not logged in, redirect anyway
    header("Location: ../index");
    exit();
}
