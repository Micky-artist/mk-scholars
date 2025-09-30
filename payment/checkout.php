<?php
include('../dbconnection/connection.php');
include('./config.php');

session_start();

if (!isset($_SESSION['userId'])) {
  header("Location: ../login");
  exit;
}
// --- 0) SECURITY HEADERS & HTTPS REDIRECT ---
// Only redirect to HTTPS in production/online; support reverse proxy headers
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
  || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
if (function_exists('isOnline') && isOnline() && !$isHttps) {
  header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit;
}
header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
header("Content-Security-Policy: default-src 'self' https://checkout.flutterwave.com; script-src 'self' https://checkout.flutterwave.com; form-action 'self' https://checkout.flutterwave.com; frame-ancestors 'none';");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

// --- 3) DATABASE COURSE PRICING LOOKUP ---
$courseId = $_GET['course'] ?? null;
$subscriptionName = $_GET['subscription'] ?? null;

// Validate required parameters
if (!$courseId || !$subscriptionName) {
  http_response_code(400);
  die('Missing course or subscription parameter.');
}

// Fetch course pricing from database
$pricingQuery = "SELECT cp.*, c.courseName, c.courseDescription, curr.currencySymbol 
                 FROM CoursePricing cp 
                 JOIN Courses c ON cp.courseId = c.courseId 
                 LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                 WHERE cp.courseId = ? AND cp.coursePaymentCodeName = ? AND c.courseDisplayStatus = 1";

$stmt = $conn->prepare($pricingQuery);
$stmt->bind_param('is', $courseId, $subscriptionName);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  http_response_code(404);
  die('Course pricing not found or course not available.');
}

$pricingData = $result->fetch_assoc();
$stmt->close();

// Set payment details from database
$finalAmount = $pricingData['amount'];
$currency = $pricingData['currency'];
$currencySymbol = $pricingData['currencySymbol'] ?: $currency;
$courseName = $pricingData['courseName'];
$courseDescription = $pricingData['pricingDescription'] ?: $pricingData['courseDescription'];

// --- 4) FETCH USER SECURELY (PREPARED) ---
include('../dbconnection/connection.php');
$stmt = $conn->prepare("SELECT NoUsername, NoEmail, NoPhone
    FROM normUsers
    WHERE NoUserId = ?");
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
  // if ($code === 'SAVE10') {
  //   $finalAmount = intval($finalAmount * 0.9);
  //   $couponMsg = 'Coupon applied: 10% off';
  // } else {
  //   $couponMsg = 'Invalid or expired coupon.';
  // }
}

// --- 6) FLUTTERWAVE VARIABLES ---
$customer_email = htmlspecialchars($user['NoEmail'], ENT_QUOTES, 'UTF-8');
$customer_name  = htmlspecialchars($user['NoUsername'], ENT_QUOTES, 'UTF-8');
$transaction_id = 'TX-' . time() . '-' . $courseId;
$userId = isset($_SESSION['userId']) ? (int)$_SESSION['userId'] : 0;

// --- 7) FLUTTERWAVE V4 API PAYMENT CREATION ---
$secretKey = "54cadf5f-a20f-4af5-8825-36e0121da065";
$baseUrl = "https://api.flutterwave.com"; // v4 base (live)

// Payment request payload for v4 API
$payload = [
    "tx_ref"       => $transaction_id,
    "amount"       => $finalAmount,
    "currency"     => $currency,
    "redirect_url" => SUCCESS_REDIRECT_URL . "?courseId=" . urlencode($courseId) . "&type=" . urlencode($subscriptionName) . "&userId=" . urlencode($userId) . "&tx_ref=" . urlencode($transaction_id),
    "customer"     => [
        "name"  => $customer_name,
        "email" => $customer_email
    ],
    "customizations" => [
        "title"       => COMPANY_NAME . " - " . $courseName,
        "description" => "Payment for " . $courseName . " - " . $courseDescription,
        "logo"        => COMPANY_LOGO
    ],
    "meta" => [
        "courseId" => $courseId,
        "courseName" => $courseName,
        "pricingDescription" => $courseDescription,
        "userId" => $userId
    ]
];

// Initialize cURL request to create the hosted payment using v4 API
$ch = curl_init($baseUrl . "/v4/payments");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$secretKey}",
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode($payload)
]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

// Check if payment creation was successful
if (isset($result['status']) && $result['status'] === "success") {
    // Redirect user to Flutterwave checkout page
    $checkout_url = $result['data']['link'];
    header("Location: " . $checkout_url);
    exit;
} else {
    // Handle error - show error page or fallback
    $errorMessage = "Error creating payment: " . ($result['message'] ?? 'Unknown error');
    if (isset($result['data']['message'])) {
        $errorMessage .= " - " . $result['data']['message'];
    }
    // Debug information (remove in production)
    $errorMessage .= "\n\nDebug Info:\n" . print_r($result, true);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Checkout – <?= htmlspecialchars($subscriptionName) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="./checkout.css">
  <link rel="shortcut icon" href="https://mkscholars.com/images/logo/logoRound.png" type="image/x-icon">
  
</head>

<body>
  <div class="checkout-wrapper">
    <?php if (isset($errorMessage)): ?>
      <!-- Error Display -->
      <div class="panel error">
        <header>
          <button class="back" onclick="history.back()">←</button>
          <h1>Payment Error</h1>
        </header>
        <div class="error-content">
          <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
          <p>Please try again or contact support if the problem persists.</p>
          <p>If you are facing issues please call <?= SUPPORT_PHONE ?></p>
          <button class="btn retry" onclick="window.location.reload()">Try Again</button>
          <button class="btn back" onclick="history.back()">Go Back</button>
        </div>
      </div>
    <?php else: ?>
      <!-- Loading/Redirect Display -->
      <div class="panel loading">
        <header>
          <h1>Redirecting to Payment...</h1>
        </header>
        <div class="loading-content">
          <div class="spinner"></div>
          <p class="lead">
            Course: <strong><?= htmlspecialchars($courseName) ?></strong><br>
            Package: <strong><?= htmlspecialchars($courseDescription) ?></strong><br>
            Amount: <strong><?= number_format($finalAmount) ?> <?= htmlspecialchars($currencySymbol) ?></strong>
          </p>
          <p>Please wait while we redirect you to the secure payment page...</p>
          <p>If you are not redirected automatically, <a href="#" onclick="window.location.reload()">click here</a>.</p>
        </div>
      </div>
    <?php endif; ?>
  </div>

  <style>
    .spinner {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #3498db;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 2s linear infinite;
      margin: 20px auto;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .error-content {
      text-align: center;
      padding: 2rem;
    }
    
    .error-message {
      color: #e74c3c;
      font-weight: bold;
      margin-bottom: 1rem;
    }
    
    .loading-content {
      text-align: center;
      padding: 2rem;
    }
    
    .btn {
      display: inline-block;
      padding: 10px 20px;
      margin: 10px;
      background: #3498db;
      color: white;
      text-decoration: none;
      border-radius: 5px;
      border: none;
      cursor: pointer;
    }
    
    .btn:hover {
      background: #2980b9;
    }
    
    .btn.back {
      background: #95a5a6;
    }
    
    .btn.back:hover {
      background: #7f8c8d;
    }
  </style>
</body>

</html>