<?php
session_start();

// Database connection
$host = 'localhost';
$db = 'mkscholars';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is a super admin
if (!isset($_SESSION['is_super_admin']) || !$_SESSION['is_super_admin']) {
    die("Unauthorized access.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminId = $_POST['adminId'];
    $rights = [];

    // Collect all rights from the form
    foreach ($_POST as $key => $value) {
        if ($key !== 'adminId') {
            $rights[$key] = isset($_POST[$key]) ? 1 : 0;
        }
    }

    // Check if the admin already has rights
    $checkSql = "SELECT * FROM AdminRights WHERE AdminId = $adminId";
    $checkResult = $conn->query($checkSql);

    if ($checkResult->num_rows > 0) {
        // Update existing rights
        $updateSql = "UPDATE AdminRights SET " . 
            implode(', ', array_map(function($k) use ($rights) {
                return "$k = {$rights[$k]}";
            }, array_keys($rights))) . 
            " WHERE AdminId = $adminId"; // Added space before WHERE

        if ($conn->query($updateSql)) {
            $_SESSION['flash'] = 'Rights updated successfully!';
        } else {
            $_SESSION['flash'] = "Error: " . $conn->error;
        }
    } else {
        // Insert new rights
        $columns = implode(', ', array_keys($rights));
        $values = implode(', ', array_values($rights));
        $insertSql = "INSERT INTO AdminRights (AdminId, $columns) VALUES ($adminId, $values)";
        if ($conn->query($insertSql)) {
            $_SESSION['flash'] = 'Rights created successfully!';
        } else {
            $_SESSION['flash'] = "Error: " . $conn->error;
        }
    }

    // Redirect back to the previous page
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    die("Invalid request method.");
}
?>