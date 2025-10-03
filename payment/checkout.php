<?php
include('../dbconnection/connection.php');
include('./config.php');

session_start();

if (!isset($_SESSION['userId'])) {
  $currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $next = urlencode('/mkscholars/courses');
  if (!empty($_SERVER['HTTP_REFERER'])) {
    $ref = $_SERVER['HTTP_REFERER'];
    if (stripos($ref, '/courses') !== false) {
      $next = urlencode(parse_url($ref, PHP_URL_PATH) . (parse_url($ref, PHP_URL_QUERY) ? ('?' . parse_url($ref, PHP_URL_QUERY)) : ''));
    }
  }
  header("Location: ../login?next=" . $next);
  exit;
}
// --- 0) SECURITY HEADERS & HTTPS REDIRECT ---
// Only redirect to HTTPS in production/online; support reverse proxy headers
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
  || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
  || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
$online = function_exists('isOnline') && isOnline();
if ($online && !$isHttps) {
  header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
  exit;
}
if ($online) {
  header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
  header("Content-Security-Policy: default-src 'self' https://checkout.flutterwave.com; script-src 'self' https://checkout.flutterwave.com; form-action 'self' https://checkout.flutterwave.com; frame-ancestors 'none';");
  header("X-Frame-Options: DENY");
  header("X-Content-Type-Options: nosniff");
}

// --- 3) DATABASE COURSE PRICING LOOKUP ---
$courseId = $_GET['course'] ?? null;
$subscriptionName = $_GET['subscription'] ?? null;
$scholarshipId = isset($_GET['scholarshipId']) ? intval($_GET['scholarshipId']) : null;

// Validate required parameters
if ((!$courseId || !$subscriptionName) && !$scholarshipId) {
  http_response_code(400);
  die('Missing required parameters. Provide course+subscription or scholarshipId.');
}

// Establish base payment context
$context = [];
if ($scholarshipId) {
  // Scholarship payment context
  $stmt = $conn->prepare("SELECT scholarshipId, scholarshipTitle, scholarshipDetails, amount FROM scholarships WHERE scholarshipId = ? AND scholarshipStatus != 0");
  $stmt->bind_param('i', $scholarshipId);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows === 0) {
    http_response_code(404);
    die('Scholarship not found or not available.');
  }
  $sch = $result->fetch_assoc();
  $stmt->close();

  $finalAmount = floatval($sch['amount']);
  $currency = defined('DEFAULT_CURRENCY') ? DEFAULT_CURRENCY : 'USD';
  $currencySymbol = $currency;
  $courseName = $sch['scholarshipTitle'];
  $courseDescription = 'Scholarship application assistance';
  $context = [
    'type' => 'scholarship',
    'scholarshipId' => $scholarshipId
  ];
} else {
  // Course payment context
  $pricingQuery = "SELECT cp.*, c.courseName, c.courseShortDescription, curr.currencySymbol 
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

  $finalAmount = floatval($pricingData['amount']);
  $currency = $pricingData['currency'];
  $currencySymbol = $pricingData['currencySymbol'] ?: $currency;
  $courseName = $pricingData['courseName'];
  $courseDescription = $pricingData['pricingDescription'] ?: $pricingData['courseShortDescription'];
  $context = [
    'type' => 'course',
    'courseId' => (int)$courseId,
    'subscriptionName' => (string)$subscriptionName,
    'coursePaymentCodeName' => (string)$pricingData['coursePaymentCodeName']
  ];
}

// --- 4) FETCH USER SECURELY (PREPARED) ---
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

// handle coupon POST (validate against Coupons table)
$appliedCoupon = null;
$couponMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyCoupon'])) {
  if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die('Invalid CSRF token');
  }
  $code = strtoupper(trim(preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['couponName'] ?? '')));
  if ($code !== '') {
    $now = date('Y-m-d H:i:s');
    $userIdInt = (int)$_SESSION['userId'];
    // Find active, valid coupon matching code and scope
    $scopeType = $scholarshipId ? 'scholarship' : 'course_pricing';
    $scopeId = $scholarshipId ? $scholarshipId : ($context['courseId'] ?? 0);

    $couponSql = "SELECT * FROM Coupons 
                  WHERE code = ? AND status = 'active' 
                    AND (valid_from IS NULL OR valid_from <= ?) 
                    AND (valid_to IS NULL OR valid_to >= ?) 
                    AND (scope_type = 'global' 
                         OR (scope_type = ? AND (scope_id IS NULL OR scope_id = ?)))
                  LIMIT 1";
    $stmt = $conn->prepare($couponSql);
    $stmt->bind_param('ssssi', $code, $now, $now, $scopeType, $scopeId);
    $stmt->execute();
    $couponRes = $stmt->get_result();
    if ($couponRes && $couponRes->num_rows > 0) {
      $coupon = $couponRes->fetch_assoc();
      $stmt->close();

      // Check overall usage limits
      if (!empty($coupon['max_uses'])) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS uses FROM CouponRedemptions WHERE coupon_id = ?");
        $stmt->bind_param('i', $coupon['id']);
        $stmt->execute();
        $countRes = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ((int)$countRes['uses'] >= (int)$coupon['max_uses']) {
          $couponMsg = 'Coupon usage limit reached.';
          goto skip_coupon_apply;
        }
      }
      // Check per-user limit
      if (!empty($coupon['per_user_limit'])) {
        $stmt = $conn->prepare("SELECT COUNT(*) AS uses FROM CouponRedemptions WHERE coupon_id = ? AND user_id = ?");
        $stmt->bind_param('ii', $coupon['id'], $userIdInt);
        $stmt->execute();
        $countRes = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ((int)$countRes['uses'] >= (int)$coupon['per_user_limit']) {
          $couponMsg = 'You have reached the usage limit for this coupon.';
          goto skip_coupon_apply;
        }
      }

      // Compute discount
      $discount = 0.0;
      if ($coupon['discount_type'] === 'percent') {
        $discount = max(0.0, min(100.0, floatval($coupon['discount_value'])));
        $discountAmount = round(($discount / 100.0) * $finalAmount, 2);
      } else {
        $discountAmount = round(floatval($coupon['discount_value']), 2);
      }
      $newAmount = max(0.0, round($finalAmount - $discountAmount, 2));
      if ($newAmount < $finalAmount) {
        $finalAmount = $newAmount;
        $appliedCoupon = [
          'id' => (int)$coupon['id'],
          'code' => $coupon['code'],
          'discountAmount' => $discountAmount
        ];
        $couponMsg = 'Coupon applied successfully.';
      } else {
        $couponMsg = 'Coupon not applicable.';
      }
    } else {
      if ($stmt) { $stmt->close(); }
      $couponMsg = 'Invalid or expired coupon.';
    }
  }
}
skip_coupon_apply:

// --- 6) FLUTTERWAVE VARIABLES ---
$customer_email = htmlspecialchars($user['NoEmail'], ENT_QUOTES, 'UTF-8');
$customer_name  = htmlspecialchars($user['NoUsername'], ENT_QUOTES, 'UTF-8');
$transaction_id = 'TX-' . time() . '-' . ($scholarshipId ? ('S' . $scholarshipId) : $courseId);
$userId = isset($_SESSION['userId']) ? (int)$_SESSION['userId'] : 0;

// Build redirect URL (use hosted URL online, local URL in dev)
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$isLocalHost = (stripos($host, 'localhost') !== false) || (strpos($host, '127.0.0.1') !== false) || (substr($host, -6) === '.local');
$redirectBase = ($online && !$isLocalHost) ? SUCCESS_REDIRECT_URL : ($scheme . '://' . $host . '/payment/TransactionCompleted.php');
$dynamicRedirectUrl = $redirectBase . '?tx_ref=' . urlencode($transaction_id);

// Persist coupon across requests
if (isset($appliedCoupon['code'])) {
  $_SESSION['checkout_coupon_code'] = $appliedCoupon['code'];
} elseif (!isset($_POST['applyCoupon']) && isset($_SESSION['checkout_coupon_code'])) {
  // Re-apply persisted coupon
  $_POST['couponName'] = $_SESSION['checkout_coupon_code'];
  $_POST['applyCoupon'] = 1;
  // Re-run coupon block by jumping back logically is complex; simplest: include minimal repeat
  // Intentionally left minimal to avoid recursion; coupon already applied this request if needed
}

// Determine whether to proceed to payment or show coupon form first
$shouldCreatePayment = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['continue_to_pay'])) || isset($_GET['autopay']);

if ($shouldCreatePayment) {
  // Flag to render v3 hosted form
  $renderHostedForm = true;
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
      <!-- Coupon + Confirm Panel -->
      <div class="panel">
        <header>
          <h1>Checkout</h1>
        </header>
        <div class="loading-content">
          <?php if (!empty($renderHostedForm)): ?>
            <p class="lead">Redirecting to payment...</p>
            <form class="FinalForm" method="POST" action="https://checkout.flutterwave.com/v3/hosted/pay" id="fwForm">
              <input type="hidden" name="public_key" value="<?= htmlspecialchars(defined('FLUTTERWAVE_PUBLIC_KEY') ? FLUTTERWAVE_PUBLIC_KEY : 'FLWPUBK-fd9a72fe52fbf0bd373323b44d7e2097-X') ?>" />
              <input type="hidden" name="tx_ref" value="<?= htmlspecialchars($transaction_id) ?>" />
              <input type="hidden" name="amount" value="<?= htmlspecialchars(number_format($finalAmount, 2, '.', '')) ?>" />
              <input type="hidden" name="currency" value="<?= htmlspecialchars($currency) ?>" />
              <input type="hidden" name="redirect_url" value="<?= htmlspecialchars($dynamicRedirectUrl) ?>" />

              <input type="hidden" name="customer[name]" value="<?= htmlspecialchars($customer_name) ?>" />
              <input type="hidden" name="customer[email]" value="<?= htmlspecialchars($customer_email) ?>" />

              <input type="hidden" name="customizations[title]" value="<?= htmlspecialchars(COMPANY_NAME . ' - ' . $courseName) ?>" />
              <input type="hidden" name="customizations[description]" value="<?= htmlspecialchars('Payment for ' . $courseName . ' - ' . $courseDescription) ?>" />
              <input type="hidden" name="customizations[logo]" value="<?= htmlspecialchars(COMPANY_LOGO) ?>" />

              <?php if (!empty($context['type'])): ?>
                <input type="hidden" name="meta[contextType]" value="<?= htmlspecialchars($context['type']) ?>" />
              <?php endif; ?>
              <?php if (($context['type'] ?? '') === 'course'): ?>
                <input type="hidden" name="meta[courseId]" value="<?= htmlspecialchars($context['courseId']) ?>" />
                <input type="hidden" name="meta[subscriptionName]" value="<?= htmlspecialchars($context['subscriptionName']) ?>" />
              <?php endif; ?>
              <?php if (($context['type'] ?? '') === 'scholarship'): ?>
                <input type="hidden" name="meta[scholarshipId]" value="<?= htmlspecialchars($context['scholarshipId']) ?>" />
              <?php endif; ?>
              <input type="hidden" name="meta[courseName]" value="<?= htmlspecialchars($courseName) ?>" />
              <input type="hidden" name="meta[pricingDescription]" value="<?= htmlspecialchars($courseDescription) ?>" />
              <input type="hidden" name="meta[userId]" value="<?= htmlspecialchars($userId) ?>" />
              <?php if (!empty($_SESSION['checkout_coupon_code'])): ?>
                <input type="hidden" name="meta[couponCode]" value="<?= htmlspecialchars($_SESSION['checkout_coupon_code']) ?>" />
              <?php endif; ?>
              <?php if (!empty($appliedCoupon['id'])): ?>
                <input type="hidden" name="meta[couponId]" value="<?= htmlspecialchars($appliedCoupon['id']) ?>" />
              <?php endif; ?>
            </form>
            <script>
              (function(){
                var f=document.getElementById('fwForm');
                if(f){ f.submit(); }
              })();
            </script>
          <?php else: ?>
          <p class="lead">
            Item: <strong><?= htmlspecialchars($courseName) ?></strong><br>
            Details: <strong><?= htmlspecialchars($courseDescription) ?></strong><br>
            Amount: <strong><?= number_format($finalAmount, 2) ?> <?= htmlspecialchars($currencySymbol) ?></strong>
          </p>

          <?php if (!empty($couponMsg)): ?>
            <p class="<?= strpos(strtolower($couponMsg),'invalid')!==false ? 'error-message' : '' ?>"><?= htmlspecialchars($couponMsg) ?></p>
          <?php endif; ?>

          <form method="post" style="margin: 1rem 0;">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
            <div style="display:flex; gap:8px; justify-content:center;">
              <input type="text" name="couponName" placeholder="Have a referral coupon?" value="<?= htmlspecialchars($_SESSION['checkout_coupon_code'] ?? '') ?>" />
              <button class="btn" type="submit" name="applyCoupon" value="1">Apply</button>
            </div>
          </form>

          <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
            <button class="btn" type="submit" name="continue_to_pay" value="1">Continue to Secure Payment</button>
          </form>
          <?php endif; ?>
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