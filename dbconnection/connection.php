<?php
/**
 * MK Scholars Database Connection
 * 
 * IMPORTANT: This file uses environment variables for configuration.
 * 
 * SETUP INSTRUCTIONS:
 * 1. Copy .env.example to .env (create .env file if it doesn't exist)
 * 2. Configure your environment variables in .env
 * 3. NEVER commit .env to version control (it's in .gitignore)
 * 
 * ENVIRONMENTS:
 * - Local: APP_ENV=local with your local database settings
 * - Production: APP_ENV=production with live database settings
 * 
 * REQUIRED ENV VARIABLES:
 * - DB_HOST: Database server hostname
 * - DB_PORT: Database server port (default: 3306)
 * - DB_NAME: Database name
 * - DB_USER: Database username
 * - DB_PASSWORD: Database password
 * - APP_ENV: Environment (local/production)
 * 
 * TROUBLESHOOTING:
 * - If connection fails, check .env file exists and has correct values
 * - Use ?debug=db parameter to see connection details
 * - Check error logs for detailed connection messages
 * 
 * @author MK Scholars Development Team
 * @version 2.0 - Environment-based configuration
 */

date_default_timezone_set('Africa/Kigali');

// Load environment variables
function loadEnv($file) {
    if (!file_exists($file)) {
        return false;
    }
    
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '#') === 0) {
            continue;
        }
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
    return true;
}

// Load .env file
loadEnv(__DIR__ . '/../.env');

// Database configuration from environment
$conn = null;
$connectionType = 'UNKNOWN';

try {
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '3306';
    $socket = getenv('DB_SOCKET') ?: '';
    $database = getenv('DB_NAME') ?: 'mkscholars';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASSWORD') ?: '';
    $charset = 'utf8mb4';
    
    $appEnv = getenv('APP_ENV') ?: 'local';
    $connectionType = strtoupper($appEnv);
    
    // Create connection (with socket if provided)
    if (!empty($socket)) {
        $conn = mysqli_connect($host, $username, $password, $database, $port, $socket);
    } else {
        $conn = mysqli_connect($host, $username, $password, $database, $port);
    }
    
    if ($conn) {
        mysqli_set_charset($conn, $charset);
        error_log("Database connected successfully ({$connectionType}) to {$database} on {$host}:{$port}");
    }
} catch (Exception $e) {
    error_log("Database connection exception ({$connectionType}): " . $e->getMessage());
    $conn = null;
}

// Initialize variables
$class = '';
$msg = '';
$username = '';
$email = '';

// Debug information (remove in production)
if (isset($_GET['debug']) && $_GET['debug'] == 'db') {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
    echo "<strong>Database Connection Debug:</strong><br>";
    echo "Environment: " . ($connectionType) . "<br>";
    echo "Host: {$host}<br>";
    echo "Database: {$database}<br>";
    echo "Status: " . ($conn ? 'Connected' : 'Failed') . "<br>";
    echo "Server: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "<br>";
    echo "Port: " . ($_SERVER['SERVER_PORT'] ?? 'Unknown') . "<br>";
    echo "</div>";
}
?>
