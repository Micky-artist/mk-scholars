<?php
// Start session and include dependencies BEFORE any output
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include("./dbconnection/connection.php");
// Include handler early to process POST and set $msg/$class/field values
include("./php/forgetPassword.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK Scholars - Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">

</head>
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
    <?php include("./partials/coursesNav.php") ?>
    <div class="container">
        <div class="auth-container" id="login-container">
            <div class="logo"><a href="index"><img src="images/logo/logoRound.png" width="100" height="100" alt=""></a></div>

            <div class="form-header">
                <h1>Reset Account Password</h1>
                <p>Can't log in you account. Worry no more!</p>
            </div>
            <?php if (!empty($msg)): ?>
            <div class="<?php echo $class ?>" id="global-alert">
                <?php echo $msg ?>
            </div>
            <?php endif; ?>
            <form class="auth-form" method="post" id="login-form">
                <input type="hidden" name="csrf_token" value="<?php echo isset($_SESSION['csrf_token']) ? htmlspecialchars($_SESSION['csrf_token']) : '' ?>">
                <div class="input-group">
                    <input type="email" name="email" id="login-email" value="<?php echo isset($email) ? htmlspecialchars($email) : '' ?>" placeholder=" ">
                    <label for="login-email">Email Address</label>
                    <?php if (!empty($errors['email'])): ?>
                    <div class="password-rules" style="color:#c41f10; margin-top:6px;">
                        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['email']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <input type="text" name="phone" id="login-phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : '' ?>" placeholder=" ">
                    <label for="login-phone">Phone Number</label>
                    <?php if (!empty($errors['phone'])): ?>
                    <div class="password-rules" style="color:#c41f10; margin-top:6px;">
                        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['phone']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <input type="password" name="newPassword" id="new-password" value="<?php echo isset($newPassword) ? htmlspecialchars($newPassword) : '' ?>" placeholder=" ">
                    <label for="new-password">New Password</label>
                    <?php if (!empty($errors['newPassword'])): ?>
                    <div class="password-rules" style="color:#c41f10; margin-top:6px;">
                        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['newPassword']); ?>
                    </div>
                    <?php else: ?>
                    <div class="password-rules">
                        At least 8 characters, including uppercase, lowercase, and a number.
                    </div>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <input type="password" name="coNewPassword" id="co-new-password" value="<?php echo isset($coNewPassword) ? htmlspecialchars($coNewPassword) : '' ?>" placeholder=" ">
                    <label for="co-new-password">Confirm New Password</label>
                    <?php if (!empty($errors['coNewPassword'])): ?>
                    <div class="password-rules" style="color:#c41f10; margin-top:6px;">
                        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($errors['coNewPassword']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <button type="submit" name="reset_password" class="submit-btn">Reset Password</button>
            </form>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                var alertBox = document.getElementById('global-alert');
                if (alertBox) {
                    setTimeout(function(){
                        alertBox.style.transition = 'opacity 0.4s ease';
                        alertBox.style.opacity = '0';
                        setTimeout(function(){ alertBox.style.display = 'none'; }, 400);
                    }, 6000);
                }
            });
            </script>

            <div class="switch-form">
                Need an account? <a href="./sign-up">Sign Up</a><br>
            </div>
            <div class="switch-form">
                <p>For further assistance call <br> <a href="tel:+250 798 611 161">+250 798 611 161</a></p>
            </div>
        </div>
    </div>

</body>

</html>