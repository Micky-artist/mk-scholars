<?php
/**
 * Session Configuration for MK Scholars
 * Handles persistent sessions for better user experience
 */

// Only configure if session hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    // Set session configuration
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS only
    ini_set('session.cookie_samesite', 'Lax');
    
    // Set session lifetime to 30 days (in seconds)
    $sessionLifetime = 30 * 24 * 60 * 60; // 30 days
    
    // Configure session cookie parameters
    session_set_cookie_params([
        'lifetime' => $sessionLifetime,
        'path' => '/',
        'domain' => '', // Empty for current domain
        'secure' => false, // Set to true for HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    // Set session name
    session_name('MKSCHOLARS_SESSION');
    
    // Start the session
    session_start();
    
    // Set session lifetime in PHP
    ini_set('session.gc_maxlifetime', $sessionLifetime);
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['last_regeneration'])) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    } else if (time() - $_SESSION['last_regeneration'] > 1800) { // Every 30 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
    
    // Check for session timeout (30 days)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
        // Session has expired
        session_unset();
        session_destroy();
        session_start();
    }
}
?>
