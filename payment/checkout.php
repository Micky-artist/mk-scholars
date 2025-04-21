<?php
include('../dbconnection/connection.php');

session_start();

if (!isset($_SESSION['userId'])) {
  header("Location: ../login");
  exit;
}
// --- 0) SECURITY HEADERS & HTTPS REDIRECT ---
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
  header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit;
}
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Content-Security-Policy: default-src 'self' https://checkout.flutterwave.com; script-src 'self' https://checkout.flutterwave.com; frame-ancestors 'none';");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// --- 3) WHITELIST & SERVER-SIDE PLAN LOOKUP ---
$plans = [
  'notes' => 4500,
  '30days' => 15000,
  'instructor'  => 7500,
];
if (!isset($_GET['subscription']) || !isset($plans[$_GET['subscription']])) {
  http_response_code(400);
  die('Invalid subscription.');
}
$subscriptionName = $_GET['subscription'];
$finalAmount      = $plans[$subscriptionName];

// --- 4) FETCH USER SECURELY (PREPARED) ---
include('../dbconnection/connection.php');
$stmt = $conn->prepare("
    SELECT NoUsername, NoEmail, NoPhone
    FROM normUsers
    WHERE NoUserId = ?
");
$stmt->bind_param('i', $_SESSION['userId']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  http_response_code(404);
  die('User not found.');
}
$user = $result->fetch_assoc();
$stmt->close();

// --- 5) CSRF TOKEN for coupon form ---
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// handle coupon POST
$couponMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyCoupon'])) {
  if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die('Invalid CSRF token');
  }
  // -- validate coupon against DB or list --
  $code = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['couponName']);
  // dummy example: 10% off code = SAVE10
  if ($code === 'SAVE10') {
    $finalAmount = intval($finalAmount * 0.9);
    $couponMsg = 'Coupon applied: 10% off';
  } else {
    $couponMsg = 'Invalid or expired coupon.';
  }
}

// --- 6) FLUTTERWAVE VARIABLES ---
$customer_email = htmlspecialchars($user['NoEmail'], ENT_QUOTES, 'UTF-8');
$customer_name  = htmlspecialchars($user['NoUsername'], ENT_QUOTES, 'UTF-8');
$transaction_id = bin2hex(random_bytes(8)) . '_' . time();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Checkout – <?= htmlspecialchars($subscriptionName) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="./checkout.css">
  <link rel="shortcut icon" href="https://mkscholars.com/images/logo/logoRound.png" type="image/x-icon">
  <script>
    // block right-click & copy
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('copy', e => e.preventDefault());
  </script>
</head>

<body>
  <div class="checkout‑wrapper">
    <div class="panel purchase">
      <header>
        <button class="back" onclick="history.back()">←</button>
        <h1>Murakoze</h1>
      </header>
      <p class="lead">
        Subscription: <strong><?= htmlspecialchars($subscriptionName) ?></strong><br>
        Amount: <strong><?= number_format($finalAmount) ?> RWF</strong>
      </p>

      <!-- <form method="post" action="" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label>
          Discount Code
          <input type="text" name="couponName" placeholder="C00-20-0FF" maxlength="20" required>
        </label>
        <button type="submit" name="applyCoupon">Check</button>
      </form> -->
      <?php if ($couponMsg): ?>
        <p class="coupon-msg"><?= htmlspecialchars($couponMsg) ?></p>
      <?php endif; ?>
    </div>

    <div class="panel summary">
      <h2>Order Summary</h2>
      <ul>
        <li><strong>User:</strong> <?= $customer_name ?></li>
        <li><strong>Email:</strong> <?= $customer_email ?></li>
        <li><strong>Plan:</strong> <?= htmlspecialchars($subscriptionName) ?></li>
        <li><strong>Total:</strong> <?= number_format($finalAmount) ?> RWF</li>
      </ul>
      <?php echo $userId = $_SESSION['userId'] ?>
      <form class="pay" method="POST"
        action="https://checkout.flutterwave.com/v3/hosted/pay">
        <input type="hidden" name="public_key"
          value="FLWPUBK-fd9a72fe52fbf0bd373323b44d7e2097-X">
        <input type="hidden" name="customer[email]" value="<?= $customer_email ?>">
        <input type="hidden" name="customer[name]" value="<?= $customer_name ?>">
        <input type="hidden" name="tx_ref" value="<?= htmlspecialchars($transaction_id) ?>">
        <input type="hidden" name="amount" value="<?= htmlspecialchars($finalAmount) ?>">
        <input type="hidden" name="subType" value="<?= htmlspecialchars($subscriptionName) ?>">
        <input type="hidden" name="currency" value="RWF">
        <input type="hidden" name="redirect_url" value="https://mkscholars.com/payment/TransactionCompleted?type=<?= urlencode($subscriptionName) ?>&userId=<?= urlencode($userId) ?>">
        <!-- <input type="hidden" name="redirect_url" value="https://mkscholars.com/payment/TransactionCompleted?status=successful&userId=<?= urlencode($userId) ?>&type=<?= urlencode($subscriptionName) ?>"> -->
        <button type="submit" class="btn pay-now">Pay Now</button>
      </form>
    </div>
  </div>
</body>

</html>