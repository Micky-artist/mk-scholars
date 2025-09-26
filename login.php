<?php
session_start();
include("./dbconnection/connection.php");
$pageName = "SignIn";

// Redirect logged-in users to e-learning page
if (isset($_SESSION['userId']) && isset($_SESSION['username'])) {
    header("Location: ./e-learning");
    exit();
}

// Process login form if submitted
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
                        
                        // Clear any output buffer
                        if (ob_get_level()) {
                            ob_end_clean();
                        }
                        
                        // Debug: Log successful login
                        error_log("Login successful for user: " . $account['NoUsername'] . " (ID: " . $account['NoUserId'] . ")");
                        
                        // Set redirect header
                        $redirectUrl = "./e-learning";
                        
                        // Check if e-learning page exists and is accessible
                        if (file_exists("./e-learning.php")) {
                            // Additional debugging
                            error_log("Redirecting to: " . $redirectUrl);
                            
                            header("Location: " . $redirectUrl);
                            header("Cache-Control: no-cache, no-store, must-revalidate");
                            header("Pragma: no-cache");
                            header("Expires: 0");
                            exit();
                        } else {
                            // Fallback to dashboard if e-learning doesn't exist
                            $redirectUrl = "./dashboard";
                            error_log("E-learning page not found, redirecting to: " . $redirectUrl);
                            
                            header("Location: " . $redirectUrl);
                            header("Cache-Control: no-cache, no-store, must-revalidate");
                            header("Pragma: no-cache");
                            header("Expires: 0");
                            exit();
                        }
                    } else {
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

// Initialize variables if not set
if (!isset($msg)) {
    $msg = '';
    $class = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>MK Scholars - Login</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
	<link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">

</head>

<body>
    <!-- Universal Navigation -->
    <?php include("./partials/navigation.php"); ?>

    <!-- Display error message if login failed -->
    <?php if (!empty($msg)): ?>
        <div class="alert-container">
            <div class="<?php echo $class; ?>">
                <?php echo $msg; ?>
            </div>
        </div>
    <?php endif; ?>
<style>
	* {
		margin: 0;
		padding: 0;
		box-sizing: border-box;
		font-family: 'Inter', sans-serif;
	}

	body {
		display: flex;
		flex-direction: column;
		justify-content: center;
		background: #f5f5f5;
		width: 100%;
		padding-top: 120px; /* Account for fixed navigation */
	}

	.alert-container {
		position: fixed;
		top: 120px;
		left: 50%;
		transform: translateX(-50%);
		z-index: 1000;
		width: 90%;
		max-width: 500px;
	}

	.alert {
		padding: 1rem;
		margin-bottom: 1rem;
		border: 1px solid transparent;
		border-radius: 0.375rem;
		font-weight: 500;
	}

	.alert-danger {
		color: #721c24;
		background-color: #f8d7da;
		border-color: #f5c6cb;
	}

	@media (max-width: 768px) {
		body {
			padding-top: 100px; /* Reduced padding for mobile */
		}

		.alert-container {
			top: 100px;
		}
	}

	@media (max-width: 480px) {
		body {
			padding-top: 90px; /* Further reduced padding for small mobile */
		}

		.alert-container {
			top: 90px;
		}
	}

	.auth-container {
		background: white;
		padding: 40px;
		border-radius: 20px;
		box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
		width: 400px;
		transition: 0.3s ease;
	}

	.form-header {
		text-align: center;
		margin-bottom: 40px;
	}

	.form-header h1 {
		color: #2d3436;
		font-size: 28px;
		margin-bottom: 8px;
	}

	.form-header p {
		color: #636e72;
		font-size: 14px;
	}

	.auth-form {
		display: flex;
		flex-direction: column;
		gap: 25px;
	}

	.input-group {
		position: relative;
	}

	.input-group input {
		width: 100%;
		padding: 14px;
		border: 1px solid #e0e0e0;
		border-radius: 8px;
		font-size: 14px;
		transition: 0.3s ease;
	}

	.input-group input:focus {
		outline: none;
		border-color: #74b9ff;
		box-shadow: 0 0 0 3px rgba(116, 185, 255, 0.1);
	}

	.input-group label {
		position: absolute;
		left: 14px;
		top: 14px;
		color: #636e72;
		font-size: 14px;
		pointer-events: none;
		transition: 0.3s ease;
		background: white;
		padding: 0 5px;
	}

	.input-group input:focus+label,
	.input-group input:not(:placeholder-shown)+label {
		top: -10px;
		font-size: 12px;
		color: #74b9ff;
	}

	.submit-btn {
		background: #74b9ff;
		color: white;
		padding: 14px;
		border: none;
		border-radius: 8px;
		font-weight: 500;
		cursor: pointer;
		transition: 0.3s ease;
	}

	.submit-btn:hover {
		background: #4da8ff;
	}

	.switch-form {
		text-align: center;
		margin-top: 20px;
	}

	.switch-form a {
		color: #74b9ff;
		text-decoration: none;
		font-size: 14px;
		transition: 0.3s ease;
	}

	.switch-form a:hover {
		color: #4da8ff;
	}

	.hidden {
		display: none;
	}

	.password-rules {
		font-size: 12px;
		color: #636e72;
		margin-top: 5px;
	}

	.logo {
		/* background-color: #2d3436; */
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.container {
		max-width: 1200px;
		margin: 2rem auto;
		display: flex;
		flex-direction: column;
	}

	.alert {
		padding: 10px;
		margin: 5px 0;
		border-radius: 10px;
		font-size: 12px;
	}

	.alert-danger {
		border: .5px solid #c41f10;
		background-color: #fcd5d2;
	}

	.alert-success {
		border: .5px solid #325737;
		background-color: #cffad4;
	}
</style>


<body>
	<div class="container">
		<div class="auth-container" id="login-container">
			<div class="logo"><a href="index"><img src="images/logo/logoRound.png" width="100" height="100" alt=""></a></div>

			<div class="form-header">
				<h1>Login Account</h1>
				<p>Get started to premium sections</p>
			</div>
			<div class="<?php echo $class ?>">
				<?php echo $msg ?>
			</div>
			<form class="auth-form" method="post" id="login-form" action="">
				<div class="input-group">
					<input type="text" name="username" value="<?php echo $NoUserName; ?>" id="login-username" placeholder=" " required>
					<label for="login-username">Email or Phone</label>
				</div>
				<div class="input-group">
					<input type="password" name="password" id="login-password" placeholder=" " required>
					<label for="login-password">Password</label>
				</div>

				<button type="submit" name="login" class="submit-btn">Login</button>
			</form>

			<div class="switch-form">
				Need an account? <a href="./sign-up">Sign Up</a><br>
				<a href="./forgot-password">Forgot Password?</a>
			</div>
		</div>
	</div>

	<script>
		// Fallback redirect for successful login (in case PHP redirect fails)
		<?php if (isset($_SESSION['userId']) && isset($_SESSION['username']) && !isset($_POST['login'])): ?>
			// User is already logged in, redirect to e-learning
			window.location.href = './e-learning';
		<?php endif; ?>
	</script>
</body>

</html>