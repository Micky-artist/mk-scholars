<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creative Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
            overflow: hidden;
        }

        .container {
            position: relative;
            width: 380px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            transform: translateY(50px);
            opacity: 0;
            animation: slideUp 1s forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 30px;
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: none;
            border-bottom: 2px solid #ddd;
            outline: none;
            font-size: 16px;
            transition: 0.3s;
            background: transparent;
        }

        .form-group label {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            pointer-events: none;
            transition: 0.3s;
        }

        .form-group input:focus ~ label,
        .form-group input:valid ~ label {
            top: -5px;
            font-size: 12px;
            color: #4ecdc4;
        }

        .form-group input:focus,
        .form-group input:valid {
            border-bottom-color: #4ecdc4;
        }

        .btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, #4ecdc4, #45b7af);
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
        }

        .extra-links {
            text-align: center;
            margin-top: 25px;
        }

        .extra-links a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .extra-links a:hover {
            color: #4ecdc4;
        }

        .floating-objects span {
            position: absolute;
            width: 30px;
            height: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 10s linear infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0);
            }
            25% {
                transform: translate(10px, 10px);
            }
            50% {
                transform: translate(-10px, 20px);
            }
            75% {
                transform: translate(-20px, -10px);
            }
        }

        .floating-objects span:nth-child(1) {
            top: 20%;
            left: 15%;
            animation-delay: 0s;
        }

        .floating-objects span:nth-child(2) {
            top: 60%;
            left: 75%;
            animation-delay: -2s;
        }

        .floating-objects span:nth-child(3) {
            top: 80%;
            left: 40%;
            animation-delay: -5s;
        }

        .social-login {
            margin-top: 30px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .social-btn:hover {
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <div class="floating-objects">
        <span></span>
        <span></span>
        <span></span>
    </div>

    <div class="container">
        <h2 style="text-align: center; margin-bottom: 40px; color: #333;">Welcome Back</h2>
        
        <form>
            <div class="form-group">
                <input type="text" required>
                <label>Username or Email</label>
            </div>

            <div class="form-group">
                <input type="password" required>
                <label>Password</label>
            </div>

            <button type="submit" class="btn">Sign In</button>

            <div class="extra-links">
                <a href="#">Forgot Password?</a>
                <br>
                <a href="#" style="margin-top: 10px; display: inline-block;">Create Account</a>
            </div>

            <div class="social-login">
                <button type="button" class="social-btn">📘</button>
                <button type="button" class="social-btn">📷</button>
                <button type="button" class="social-btn">🔍</button>
            </div>
        </form>
    </div>
</body>
</html>