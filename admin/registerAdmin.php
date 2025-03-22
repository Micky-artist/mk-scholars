<?php


// Initialize variables for form data and messages
$username = $email = $password = $confirmPassword = '';
$alertType = $alertMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    // Validate form data
    if (empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
        $alertType = 'error';
        $alertMessage = 'All fields are required';
    } elseif ($password !== $confirmPassword) {
        $alertType = 'error';
        $alertMessage = 'Passwords do not match';
    } else {
        // Check if the username or email already exists
        $checkSql = "SELECT * FROM users WHERE username = ? OR email = ?";
        $checkStmt = $conn->prepare($checkSql);
        if ($checkStmt) {
            $checkStmt->bind_param('ss', $username, $email);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();

            if ($checkResult->num_rows > 0) {
                $alertType = 'error';
                $alertMessage = 'Username or email already exists';
            } else {
                // Hash the password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // Insert into the database
                $insertSql = "INSERT INTO users (username, email, password, status) VALUES (?, ?, ?, ?)";
                $insertStmt = $conn->prepare($insertSql);
                if ($insertStmt) {
                    $status = 1; // Default status for new users
                    $insertStmt->bind_param('sssi', $username, $email, $hashedPassword, $status);
                    if ($insertStmt->execute()) {
                        $alertType = 'success';
                        $alertMessage = 'Account created successfully!';
                    } else {
                        $alertType = 'error';
                        $alertMessage = 'Error: ' . $insertStmt->error;
                    }
                    $insertStmt->close();
                } else {
                    $alertType = 'error';
                    $alertMessage = 'Error: ' . $conn->error;
                }
            }
            $checkStmt->close();
        } else {
            $alertType = 'error';
            $alertMessage = 'Error: ' . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Administrator</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Global Styles */
        :root {
            --primary: #4F46E5; /* Indigo */
            --secondary: #818CF8; /* Lighter Indigo */
            --background: #F8FAFC; /* Light Gray */
            --surface: #FFFFFF; /* White */
            --border: #E2E8F0; /* Light Border */
            --text: #1E293B; /* Dark Gray */
            --success: #22C55E; /* Green */
            --error: #EF4444; /* Red */
        }

        body {
            background: var(--background);
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Form Container */
        .form-container {
            background: var(--surface);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            margin: 1rem;
        }

        /* Logo */
        .logo {
            width: 120px;
            margin: 0 auto 2rem;
            display: block;
        }

        /* Form Title */
        .form-title {
            text-align: center;
            color: var(--text);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        /* Input Fields */
        .input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.5rem;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 1rem;
            color: var(--text);
            transition: all 0.3s ease;
        }

        .input-group input:focus,
        .input-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .input-group input::placeholder {
            color: #94A3B8; /* Light Gray */
        }

        .input-group select {
            appearance: none;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234F46E5'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3C/svg%3E") 
                no-repeat right 1rem center/1.2em;
        }

        /* Icons */
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.2rem;
        }

        /* Submit Button */
        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background: #4338CA; /* Darker Indigo */
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.2);
        }

        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            border: 2px solid rgba(34, 197, 94, 0.2);
            color: var(--success);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 2px solid rgba(239, 68, 68, 0.2);
            color: var(--error);
        }

        .alert-icon {
            width: 24px;
            height: 24px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <!-- Form Title -->
        <h1 class="form-title">Add an Administrator</h1>

        <!-- Alert Message -->
        <?php if (!empty($alertMessage)): ?>
            <div class="alert <?= $alertType === 'success' ? 'alert-success' : 'alert-error' ?>">
                <svg class="alert-icon" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                </svg>
                <span><?= $alertMessage ?></span>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form method="POST">
            <!-- Username -->
            <div class="input-group">
                <i class="input-icon fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($username) ?>" required>
            </div>

            <!-- Email -->
            <div class="input-group">
                <i class="input-icon fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" value="<?= htmlspecialchars($email) ?>" required>
            </div>

            <!-- Password -->
            <div class="input-group">
                <i class="input-icon fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" value="<?= htmlspecialchars($password) ?>" required>
            </div>

            <!-- Confirm Password -->
            <div class="input-group">
                <i class="input-icon fas fa-lock"></i>
                <input type="password" name="confirmPassword" placeholder="Confirm Password" value="<?= htmlspecialchars($confirmPassword) ?>" required>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="submit-btn">Create Account</button>
        </form>
    </div>
</body>
</html>