<?php
date_default_timezone_set('Africa/Kigali');

// Default configuration for local development
$db_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'mkscholars'
];

// Check if we're running in Docker environment (via environment variable)
if (getenv('DOCKER_ENV') === 'true') {
    $db_config = [
        'host' => getenv('DB_HOST') ?: 'db',
        'username' => getenv('DB_USERNAME') ?: 'mkscholars',
        'password' => getenv('DB_PASSWORD') ?: 'secret',
        'database' => getenv('DB_DATABASE') ?: 'mkscholars'
    ];
}

// Create database connection
$conn = mysqli_connect(
    $db_config['host'],
    $db_config['username'],
    $db_config['password'],
    $db_config['database']
);

// Check connection
if (!$conn) {
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection error. Please try again later.");
}

// Set charset to ensure proper encoding
mysqli_set_charset($conn, 'utf8mb4');

// Initialize other variables
$class = '';
$msg = '';
$username = '';
$email = '';