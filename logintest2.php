<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth Pages</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f5;
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

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
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
    </style>
</head>
<body>
    <div class="auth-container" id="signup-container">
        <div class="form-header">
            <h1>Create Account</h1>
            <p>Join our community today</p>
        </div>

        <form class="auth-form" id="signup-form">
            <div class="input-group">
                <input type="text" id="name" placeholder=" ">
                <label for="name">Full Name</label>
            </div>

            <div class="input-group">
                <input type="email" id="email" placeholder=" ">
                <label for="email">Email Address</label>
            </div>

            <div class="input-group">
                <input type="password" id="password" placeholder=" ">
                <label for="password">Password</label>
            </div>
            <p class="password-rules">At least 8 characters with a number and special character</p>

            <button type="submit" class="submit-btn">Sign Up</button>
        </form>

        <div class="switch-form">
            Already have an account? <a href="#" onclick="showLogin()">Login</a><br>
            <a href="#" onclick="showForgotPassword()">Forgot Password?</a>
        </div>
    </div>

    <div class="auth-container hidden" id="forgot-container">
        <div class="form-header">
            <h1>Reset Password</h1>
            <p>We'll send you a reset link</p>
        </div>

        <form class="auth-form" id="forgot-form">
            <div class="input-group">
                <input type="email" id="forgot-email" placeholder=" ">
                <label for="forgot-email">Email Address</label>
            </div>

            <button type="submit" class="submit-btn">Send Reset Link</button>
        </form>

        <div class="switch-form">
            Remember your password? <a href="#" onclick="showLogin()">Login</a><br>
            Need an account? <a href="#" onclick="showSignup()">Sign Up</a>
        </div>
    </div>
    <div class="auth-container hidden" id="login-container">
        <div class="form-header">
            <h1>Login Account</h1>
            <p>Get started to premium sections</p>
        </div>

        <form class="auth-form" id="login-form">
            <div class="input-group">
                <input type="email" id="login-email" placeholder=" ">
                <label for="login-email">Email Address</label>
            </div>
            <div class="input-group">
                <input type="email" id="login-email" placeholder=" ">
                <label for="login-email">Email Address</label>
            </div>

            <button type="submit" class="submit-btn">Login</button>
        </form>

        <div class="switch-form">
            Remember your password? <a href="#" onclick="showLogin()">Login</a><br>
            Need an account? <a href="#" onclick="showSignup()">Sign Up</a>
        </div>
    </div>

    <script>
        function showSignup() {
            document.getElementById('signup-container').classList.remove('hidden');
            document.getElementById('forgot-container').classList.add('hidden');
            document.getElementById('login-container').classList.add('hidden');
        }
        
        function showForgotPassword() {
            document.getElementById('signup-container').classList.add('hidden');
            document.getElementById('login-container').classList.add('hidden');
            document.getElementById('forgot-container').classList.remove('hidden');
        }
        
        function showLogin() {
            document.getElementById('login-container').classList.remove('hidden');
            document.getElementById('signup-container').classList.add('hidden');
            document.getElementById('forgot-container').classList.add('hidden');
            // Add your login page logic here
            // alert('Add your login page link here');
        }
    </script>
</body>
</html>