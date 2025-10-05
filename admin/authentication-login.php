<?php
// Start session and handle login BEFORE any HTML output to avoid header issues in production
session_start();
include("./dbconnections/connection.php");
include("./php/validateSignInSignUp.php");
include("./php/login.php");
?>
<!DOCTYPE html>
<html dir="ltr">
<?php include("./partials/head.php"); ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - MK Scholars</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #343A40, #1E1E2F);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Poppins', sans-serif;
      color: #fff;
      /* width: 100%; */
    }

    .auth-box {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      border-radius: 20px;
      padding: 40px;
      width: 150%;
      max-width: 400px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
      border: 1px solid rgba(255, 255, 255, 0.1);
      text-align: center;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      color: #fff;
      font-weight: 600;
    }

    .input-group {
      margin-bottom: 20px;
      position: relative;
    }

    .input-group i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #777;
      font-size: 1.2rem;
    }

    .input-group input {
      width: 100%;
      padding: 12px 12px 12px 40px;
      border: none;
      border-radius: 10px;
      background: #fff;
      color: #333;
      font-size: 1rem;
      outline: none;
      transition: box-shadow 0.3s ease;
    }

    .input-group input:focus {
      box-shadow: 0 0 0 3px rgba(0, 176, 155, 0.3);
    }

    .input-group input::placeholder {
      color: #999;
    }

    .btn-login {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg, #00b09b, #96c93d);
      color: #fff;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .btn-login:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .footer-text {
      margin-top: 20px;
      font-size: 0.9rem;
      color: rgba(255, 255, 255, 0.7);
    }

    .footer-text a {
      color: #00b09b;
      text-decoration: none;
      font-weight: 600;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="auth-wrapper">
    <div class="auth-box">
      <div class="mb-3">
        <img src="./assets/images/favicon.png" width="150" height="150" alt="">
      </div>
      <div class="text-center">
        <h3>MK Scholars</h3>
      </div>
      <form class="form-horizontal mt-3" method="POST">
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="text" class="form-control" placeholder="Email" name="adminName" required>
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" class="form-control" placeholder="Password" name="password" required>
        </div>
        <button class="btn-login" name="submit">Login</button>
      </form>
    <?php if (!empty($msg)): ?>
      <div style="margin-top:12px;padding:10px;border-radius:8px;background:#ffecec;color:#c00;font-weight:600;">
        <?= htmlspecialchars($msg) ?>
      </div>
    <?php endif; ?>
    </div>
  </div>

  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    $(".preloader").fadeOut();
  </script>
</body>

</html>