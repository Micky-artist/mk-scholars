<?php
// Test session configuration
include("./config/session.php");

echo "<h1>Session Test</h1>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Status: " . session_status() . "</p>";
echo "<p>Session Name: " . session_name() . "</p>";

echo "<h2>Session Variables:</h2>";
if (isset($_SESSION['username'])) {
    echo "<p>Username: " . htmlspecialchars($_SESSION['username']) . "</p>";
} else {
    echo "<p>Username: Not set</p>";
}

if (isset($_SESSION['userId'])) {
    echo "<p>User ID: " . htmlspecialchars($_SESSION['userId']) . "</p>";
} else {
    echo "<p>User ID: Not set</p>";
}

echo "<h2>All Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Cookie Parameters:</h2>";
$params = session_get_cookie_params();
echo "<pre>";
print_r($params);
echo "</pre>";

echo "<p><a href='home'>Go to Home Page</a></p>";
echo "<p><a href='login'>Go to Login Page</a></p>";
?>
