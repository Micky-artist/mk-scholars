<?php
session_start();
include('../dbconnection/connection.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="shortcut icon" type="image/x-icon" href="https://mkscholars.com/images/logo/logoRound.png" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>subscribe</title>
</head>

<body>
  <div class="container">
    <div class="navigation">
      <div class="NavBtns">
      </div>
      <a href="../index">
        <div class="NavLogo">
          <img src="https://mkscholars.com/images/logo/logoRound.png" alt="" />
        </div>
      </a>
      <div class="NavBtns">
        <div><a class="linkHov" href="../index">HOME</a></div>
      </div>
      <div class="NavContLinks">
        <a href="../php/logout.php">Sign Out</a>
      </div>
    </div>
    <div class="bannedContainer">
      <div class="successTransaction"><i class="fa-sharp fa-solid fa-circle-check fa-bounce"></i></div>
      <div>
        <h1>Transaction Complete</h1>
      </div>
      <div>
        <p>Contact the administration for other information</p>
      </div>
      <div>
      <div><p>+250 798 611 161</p></div>
      </div>
      <div>
        <p>Or</p>
      </div>
      <div class="backtohomebann"><a href="../dashboard">Start Studying <i class="fa-solid fa-arrow-right"></i></a></div>
      <!-- <div class="backtohomebann"><a href="../index.php">Buy Now <i class="fa-solid fa-arrow-right"></i></a></div> -->
    </div>
    <div class="footer">
      <div>
        <p>
          Copyright &copy; <?php echo date('Y'); ?>
          <a href="../index">MK Scholars</a> All rights reserved.
        </p>
      </div>
      <div>Powered by <a href="">M&S Inovation Lab.</a></div>
      <div>Contact: +250 798 611 161</div>
      <div><a href="../terms-and-conditions">Privacy & Policy</a></div>
    </div>
  </div>
  <style>
    /* Reset & base */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: #f4f4f4;
      color: #333;
      line-height: 1.5;
    }
    .successTransaction {
      font-size: 4rem;
      color: #28a745;
      margin-bottom: 1rem;
      animation: pulse 1s infinite;
    }
    @keyframes pulse {
      0%, 100% { transform: scale(1); }
      50%      { transform: scale(1.2); }
    }
    /* Navigation */
    .navigation {
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      padding: 0.75rem 2rem;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .NavLogo img {
      height: 40px;
    }

    .NavBtns a.linkHov,
    .NavContLinks a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
      margin: 0 0.75rem;
      transition: color 0.3s;
    }

    .NavBtns a.linkHov:hover,
    .NavContLinks a:hover {
      color: #4bc2c5;
    }

    /* Error panel */
    .bannedContainer {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: calc(100vh - 160px);
      /* account for nav + footer */
      text-align: center;
      padding: 2rem;
    }

    .failedTransaction {
      font-size: 4rem;
      color: #e74c3c;
      margin-bottom: 1rem;
      animation: beat 1s infinite;
    }

    @keyframes beat {

      0%,
      100% {
        transform: scale(1);
      }

      50% {
        transform: scale(1.2);
      }
    }

    .bannedContainer h1 {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }

    .bannedContainer p {
      font-size: 1.1rem;
      margin: 0.25rem 0;
    }

    .backtohomebann a {
      display: inline-block;
      margin-top: 1rem;
      padding: 0.75rem 1.5rem;
      background: #4bc2c5;
      color: #fff;
      border-radius: 25px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .backtohomebann a:hover {
      background: #3aa9ac;
    }

    /* Footer */
    .footer {
      background: #fff;
      padding: 1rem 2rem;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      font-size: 0.9rem;
      color: #777;
      border-top: 1px solid #eee;
    }

    .footer a {
      color: #4bc2c5;
      text-decoration: none;
    }

    .footer a:hover {
      text-decoration: underline;
    }

    @media (max-width: 600px) {
      .footer {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
      }
    }
  </style>
</body>

</html>