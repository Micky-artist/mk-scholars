<?php
session_start();
include('../connection/connection.php');
include('./php/getUserIp.php');

$checkSubscription=mysqli_query($conn,"SELECT status FROM users WHERE UserUniqueId='$uniqueUserIdVariable'");
            $retrieve=mysqli_fetch_assoc($checkSubscription);
            if($retrieve['status']==1){
                header("location:./userDashBoard.php");
            }else if($retrieve['status']==2){
                header("location:./account-banned.php");
            }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/userPannel.css" />
    <link rel="stylesheet" href="../style/indexStyle.css" />
  <link rel="shortcut icon" type="image/x-icon" href="../media/logo2.png" />
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
    <a href="../index.php">
      <div class="NavLogo">
        <img src="../media/logo1.png" alt="" />
      </div>
    </a>
    <div class="NavBtns">
      <div><a class="linkHov" href="../index.php">HOME</a></div>
    </div>
    <div class="NavContLinks">
                    <a href="./php/sessionDestroy.php">Sign Out</a>
                </div>
  </div>
    <div class="bannedContainer">
        <div class="successTransaction"><i class="fa-sharp fa-solid fa-circle-check fa-bounce"></i></div>
        <div><h1>Transaction Complete</h1></div>
        <div><p>Contact the administration for other information</p></div>
        <div><p>Phone: (+250) 786 585 008 || (+250) 783 876 662</p></div>
        <div><p>Or</p></div>
        <div class="backtohomebann"><a href="./userDashBoard.php">Start Studying <i class="fa-solid fa-arrow-right"></i></a></div>
        <!-- <div class="backtohomebann"><a href="../index.php">Buy Now <i class="fa-solid fa-arrow-right"></i></a></div> -->
    </div>
    <div class="footer">
        <div>
          <p>
            Copyright &copy; 2022 - 2023
            <a href="./index.php">Rwanda Driver Code.</a> All rights reserved.
          </p>
        </div>
        <div>Powered by <a href="">M&S Inovation Lab.</a></div>
        <div>Contact: (+250) 786 585 008 || (+250) 783 876 662</div>
        <div><a href="./pages/privacy-policy.html">Privacy & Policy</a></div>
      </div>
    </div>
</body>
</html>