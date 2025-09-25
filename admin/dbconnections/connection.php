<?php
date_default_timezone_set('Africa/Kigali');

// Enhanced function to detect if we're online (production) or offline (local development)
if (!function_exists('isOnline')) {
function isOnline() {
    // Get server information
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
    $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
    $port = $_SERVER['SERVER_PORT'] ?? '80';
    $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    
    // List of local development indicators
    $localIndicators = [
        'localhost',
        '127.0.0.1',
        '::1',
        '0.0.0.0',
        'xampp',
        'wamp',
        'mamp',
        'laragon',
        'local',
        'dev',
        'development',
        'test',
        'staging.local',
        '.local',
        '.dev',
        '.test'
    ];
    
    // Check host and server name for local indicators
    foreach ($localIndicators as $indicator) {
        if (stripos($host, $indicator) !== false || 
            stripos($serverName, $indicator) !== false ||
            stripos($serverAddr, $indicator) !== false) {
            return false; // We're offline (local development)
        }
    }
    
    // Check for common local development ports
    $localPorts = ['3306', '8080', '8000', '3000', '5000', '9000', '8888', '8889'];
    if (in_array($port, $localPorts)) {
        return false; // Likely local development
    }
    
    // Check document root for local development paths
    $localPaths = ['xampp', 'wamp', 'mamp', 'laragon', 'htdocs', 'www', 'public_html'];
    foreach ($localPaths as $path) {
        if (stripos($documentRoot, $path) !== false) {
            return false; // Likely local development
        }
    }
    
    // Check for environment variables (if set)
    if (getenv('APP_ENV') && in_array(strtolower(getenv('APP_ENV')), ['local', 'development', 'dev', 'test'])) {
        return false;
    }
    
    // Check for .env file or config files that might indicate local development
    if (file_exists(__DIR__ . '/.env') || 
        file_exists(__DIR__ . '/../.env') ||
        file_exists(__DIR__ . '/../config/local.php')) {
        // Read .env file to check for local environment
        $envFile = file_exists(__DIR__ . '/.env') ? __DIR__ . '/.env' : __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            if (stripos($envContent, 'APP_ENV=local') !== false || 
                stripos($envContent, 'APP_ENV=development') !== false) {
                return false;
            }
        }
    }
    
    // Check if we're behind a common local development proxy
    $forwardedHost = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? '';
    if (!empty($forwardedHost)) {
        foreach ($localIndicators as $indicator) {
            if (stripos($forwardedHost, $indicator) !== false) {
                return false;
            }
        }
    }
    
    // Check for common production indicators
    $productionIndicators = [
        '.com',
        '.org',
        '.net',
        '.io',
        'www.',
        'https://'
    ];
    
    $isProduction = false;
    foreach ($productionIndicators as $indicator) {
        if (stripos($host, $indicator) !== false) {
            $isProduction = true;
            break;
        }
    }
    
    // If we have production indicators, we're likely online
    if ($isProduction) {
        return true;
    }
    
    // Final check: if we can't determine, default to online (production)
    // This is safer for production environments
    return true;
}
}

// Database configuration based on environment
$isOnline = isOnline();
$connectionType = $isOnline ? 'PRODUCTION' : 'LOCAL';

// Database configurations
$configs = [
    'production' => [
        'host' => 'localhost',
        'username' => 'u722035022_mkscholars',
        'password' => 'Mkscholars123@',
        'database' => 'u722035022_mkscholars',
        'port' => 3306,
        'charset' => 'utf8mb4'
    ],
    'local' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'mkscholars',
        'port' => 3306,
        'charset' => 'utf8mb4'
    ]
];

// Select configuration based on environment
$config = $isOnline ? $configs['production'] : $configs['local'];

// Create connection with enhanced error handling and fallback
$conn = null;
$connectionAttempts = 0;
$maxAttempts = 3;

while ($connectionAttempts < $maxAttempts && !$conn) {
    $connectionAttempts++;
    
    try {
        // Attempt connection
        $conn = mysqli_connect(
            $config['host'], 
            $config['username'], 
            $config['password'], 
            $config['database'],
            $config['port']
        );
        
        if ($conn) {
            // Set charset for proper UTF-8 support
            mysqli_set_charset($conn, $config['charset']);
            
            // Log successful connection
            error_log("Admin database connected successfully ({$connectionType}) to {$config['database']} on {$config['host']}:{$config['port']}");
            break;
        } else {
            $error = mysqli_connect_error();
            error_log("Admin database connection attempt {$connectionAttempts} failed ({$connectionType}): {$error}");
            
            // If production fails, try local as fallback
            if ($isOnline && $connectionAttempts == 1) {
                error_log("Production connection failed, trying local fallback...");
                $config = $configs['local'];
                $connectionType = 'LOCAL_FALLBACK';
            }
        }
    } catch (Exception $e) {
        error_log("Admin database connection exception (attempt {$connectionAttempts}, {$connectionType}): " . $e->getMessage());
        
        // If production fails, try local as fallback
        if ($isOnline && $connectionAttempts == 1) {
            error_log("Production connection exception, trying local fallback...");
            $config = $configs['local'];
            $connectionType = 'LOCAL_FALLBACK';
        }
    }
    
    // Wait before retry (only if not the last attempt)
    if ($connectionAttempts < $maxAttempts) {
        usleep(500000); // 0.5 second delay
    }
}

// Final connection status
if (!$conn) {
    error_log("All admin database connection attempts failed. Final status: {$connectionType}");
    $conn = null; // Set to null instead of crashing
}

// Debug information (remove in production)
if (isset($_GET['debug']) && $_GET['debug'] == 'db') {
    echo "<div style='background: #f0f0f0; padding: 10px; margin: 10px; border: 1px solid #ccc;'>";
    echo "<strong>Admin Database Connection Debug:</strong><br>";
    echo "Environment: " . ($connectionType) . "<br>";
    echo "Status: " . ($conn ? 'Connected' : 'Failed') . "<br>";
    echo "Server: " . ($_SERVER['HTTP_HOST'] ?? 'Unknown') . "<br>";
    echo "Port: " . ($_SERVER['SERVER_PORT'] ?? 'Unknown') . "<br>";
    echo "</div>";
}
?>
