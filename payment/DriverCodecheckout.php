<?php
session_start();
include('../connection/connection.php');
// if(isset($_SESSION['uniqueUserId'])){
if (!isset($_SESSION['username']) && !isset($_SESSION['uniqueUserId'])) {
    header('location:go-to-signUp.php');
}
$uniqueUserIdVariable = $_SESSION['uniqueUserId'];
// }
$checkSubscription = mysqli_query($conn, "SELECT userId,status FROM users WHERE UserUniqueId='$uniqueUserIdVariable'");
$retrieve = mysqli_fetch_assoc($checkSubscription);
$id = $retrieve['userId'];
$checkIfExpired = mysqli_query($conn, "SELECT * FROM subscription WHERE UserId=$id AND SubscriptionStatus=1");
if ($checkIfExpired->num_rows > 0) {
    echo '
    <script type="text/javascript">
    window.location.href="./userDashBoard.php";
    </script>
    ';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- <link rel="stylesheet" href="../style/indexStyle.css" /> -->
    <meta charset="UTF-8">
    <link rel="shortcut icon" type="image/x-icon" href="../media/logo2.png" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <title>Checkout</title>
    <link rel="stylesheet" type="text/css" href="../style/checkout.css" />

</head>

<body>
    <div class="body">
        <?php
        if (!isset($_GET['subscrption'])) {
            echo '<script type="text/javascript">
    window.location.href="subscribe.php";
    </script>';
        } else {
            if ($_GET['subscrption'] == 'basic') {
                $subscription = 'Iminsi 15';
                $type = 15;
                $amount = 2000;
                $discount = '0';
                $finalAmount = $amount;
                if (isset($_POST['coupons'])) {
                    $couponName = $_POST['couponName'];
                    $selectCoupon = mysqli_query($conn, "SELECT * FROM coupon WHERE couponName='$couponName' AND status=1");
                    if ($selectCoupon->num_rows > 0) {
                        $couponData = mysqli_fetch_assoc($selectCoupon);
                        $discount = $couponData['discount'];
                        $finalAmount = $amount - ($amount * $couponData['discount'] / 100);
                        $msg = 'coupon found';
                        $class = 'formMsgSuccess2';
                    } else {
                        $msg = 'coupon not found';
                        $class = 'formMsgFail2';
                    }
                }
                // echo $amount - 2000*20/100;
            } else if ($_GET['subscrption'] == 'standard') {
                $subscription = 'Iminsi 30';
                $type = 30;
                $amount = 3000;
                $discount = '0';
                $finalAmount = $amount;
                if (isset($_POST['coupons'])) {
                    $couponName = $_POST['couponName'];
                    $selectCoupon = mysqli_query($conn, "SELECT * FROM coupon WHERE couponName='$couponName' AND status=1");
                    if ($selectCoupon->num_rows > 0) {
                        $couponData = mysqli_fetch_assoc($selectCoupon);
                        $date = strtotime(date('Y-m-d'));
                        if ($couponData['ExpDate'] > $date and $couponData['status'] != 0) {
                            $discount = $couponData['discount'];
                            $finalAmount = $amount - ($amount * $couponData['discount'] / 100);
                            $msg = 'coupon found';
                            $class = 'formMsgSuccess2';
                        } else {
                            $msg = 'coupon expired';
                            $class = 'formMsgFail2';
                        }
                    } else {
                        $msg = 'coupon not found';
                        $class = 'formMsgFail2';
                    }
                }
            } else {
                echo '<script type="text/javascript">
        window.location.href="subscribe.php";
        </script>';
            }
        }
        ?>
        <div class="container21">

            <div class="bxx1">
                <div class="backAndLogo">
                    <div class="checkoutImage " onclick="history.back()">
                        <svg class="backBTn" id="i1" baseProfile="tiny" height="24px" id="Layer_1" version="1.2" viewBox="0 0 24 24"
                            width="24px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <path
                                d="M12,9.059V6.5c0-0.256-0.098-0.512-0.293-0.708C11.512,5.597,11.256,5.5,11,5.5s-0.512,0.097-0.707,0.292L4,12l6.293,6.207  C10.488,18.402,10.744,18.5,11,18.5s0.512-0.098,0.707-0.293S12,17.755,12,17.5v-2.489c2.75,0.068,5.755,0.566,8,3.989v-1  C20,13.367,16.5,9.557,12,9.059z" />
                        </svg>
                    </div>

                    <div class="checkoutImage">
                        <a href="../index.php">
                            <img src="../media/logo1.png" alt="" class="img1" />
                        </a>
                    </div>
                </div>
                <div class="SubscribeTitle">
                <h1><i class="fa-solid fa-bag-shopping"></i> Murakoze</h1>
                    
                </div>
                <p class="hhh3">
                    To finalize your subscription, kindly complete your payment using a
                    discount code if any.!
                </p>
                <div class="form">
                    <div class="<?php echo $class; ?>">
                        <?php echo $msg ?>
                    </div>
                    <form method="post">
                        <!-- color: #45bb00; -->
                        <!-- background-color: #45bb0038; -->
                        <!-- font-weight: bold; -->
                        <div class="form-group">

                            <span>Discount Code</span>
                            <input class="form-field" type="text" placeholder="C00-20-0FF" name="couponName" value="<?php if (isset($couponData['discount'])) {
                                echo $couponData['couponName'];
                            } ?>" required>

                            <button class="couponBtn" name="coupons">
                                Check
                            </button>

                        </div>

                    </form>
                    <!-- <div class="form-group">
        <input class="form-field" type="email" placeholder="Email">
        <span>@gmail.com</span>
    </div> -->
                    <?php
                    // if(isset($_POST['payNow'])){
                    include('pay.php');
                    // }
                    ?>
                </div>


                <img src="../media/fa.png" alt="" width="70%" class="pay">
            </div>
            <div class="bxx2">
                <div class="backAndLogo hide2">
                    <div class="checkoutImage" onclick="history.back()">
                        <svg class="backBTn" id="i2" baseProfile="tiny" height="24px" id="Layer_1" version="1.2" viewBox="0 0 24 24"
                            width="24px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink">
                            <path
                                d="M12,9.059V6.5c0-0.256-0.098-0.512-0.293-0.708C11.512,5.597,11.256,5.5,11,5.5s-0.512,0.097-0.707,0.292L4,12l6.293,6.207  C10.488,18.402,10.744,18.5,11,18.5s0.512-0.098,0.707-0.293S12,17.755,12,17.5v-2.489c2.75,0.068,5.755,0.566,8,3.989v-1  C20,13.367,16.5,9.557,12,9.059z" />
                        </svg>
                    </div>

                    <div class="checkoutImage">
                        <a href="../index.php">
                            <img src="../media/logo1.png" alt="" class="img2" />
                        </a>
                    </div>
                </div>
                <div class="bcx">
                    <!-- <h5 class="pay1">You,ve to pay.</h5> -->


                    <div class="con">
                        <h3 class="to1">Ifatabuguzi</h3>

                        <p>
                            <?php echo $subscription; ?>
                        </p>
                    </div>


                    <div class="con">
                        <h3 class="to1">Igiciro</h3>
                        <p>
                            <?php echo $amount . ' RWF'; ?>
                        </p>
                    </div>

                    <div class="con dis">
                        <h3 class="to1">Discount</h3>
                        <p>
                            <?php echo $discount . '%'; ?>
                        </p>
                    </div>

                    <div class="h11">
                        <h1>Total:</h1>
                        <h1>
                            <?php if (isset($finalAmount))
                                echo $finalAmount; ?> RWF
                        </h1>
                    </div>
                </div>
            </div>

        </div>
    </div>
    </div>
    <script src="../js/preventFormResubmition.js"></script>
</body>

</html>