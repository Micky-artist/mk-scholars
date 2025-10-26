<?php
include('../dbconnection/connection.php');
include('./config.php');

// Use shared session configuration to ensure consistency across the app
include('../config/session.php');

// Prevent caching to avoid form resubmission issues
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

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
  $subscriptionName = trim((string)$subscriptionName);

  // Primary lookup: match by coursePaymentCodeName OR pricingDescription
  $pricingQuery = "SELECT cp.*, c.courseName, c.courseShortDescription, curr.currencySymbol 
                 FROM CoursePricing cp 
                 JOIN Courses c ON cp.courseId = c.courseId 
                 LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                 WHERE cp.courseId = ? 
                   AND c.courseDisplayStatus = 1
                   AND (cp.coursePaymentCodeName = ? OR cp.pricingDescription = ?)";
  $stmt = $conn->prepare($pricingQuery);
  $stmt->bind_param('iss', $courseId, $subscriptionName, $subscriptionName);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 0) {
    // Fallback: pick the first available pricing for this course
    if ($stmt) { $stmt->close(); }
    $fallbackSql = "SELECT cp.*, c.courseName, c.courseShortDescription, curr.currencySymbol 
                    FROM CoursePricing cp 
                    JOIN Courses c ON cp.courseId = c.courseId 
                    LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                    WHERE cp.courseId = ? AND c.courseDisplayStatus = 1
                    ORDER BY cp.amount ASC
                    LIMIT 1";
    $stmt = $conn->prepare($fallbackSql);
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
      http_response_code(404);
      die('Course pricing not found or course not available.');
    }
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

// Debug: Check if Coupons table exists
$tableExists = false;
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'Coupons'");
if ($tableCheck && mysqli_num_rows($tableCheck) > 0) {
  $tableExists = true;
}

// handle coupon POST (validate against Coupons table)
$appliedCoupon = null;
$couponMsg = '';

// Check if this is a coupon application request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyCoupon'])) {
  if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die('Invalid CSRF token');
  }
  
  if (!$tableExists) {
    $couponMsg = 'Coupon system is not available. Please contact support.';
  } else {
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
    } else {
      $couponMsg = 'Please enter a coupon code.';
    }
  }
  
  // Redirect to prevent form resubmission - preserve original parameters
  $baseUrl = $_SERVER['PHP_SELF'];
  $params = [];
  
  // Preserve original GET parameters
  if (isset($_GET['course'])) {
    $params['course'] = $_GET['course'];
  }
  if (isset($_GET['scholarshipId'])) {
    $params['scholarshipId'] = $_GET['scholarshipId'];
  }
  if (isset($_GET['subscription'])) {
    $params['subscription'] = $_GET['subscription'];
  }
  if (isset($_GET['autopay'])) {
    $params['autopay'] = $_GET['autopay'];
  }
  
  // Add coupon message
  if ($couponMsg) {
    $params['coupon_msg'] = $couponMsg;
  }
  if ($appliedCoupon) {
    $params['coupon_applied'] = '1';
    $params['coupon_code'] = $appliedCoupon['code'];
  }
  
  $redirectUrl = $baseUrl . '?' . http_build_query($params);
  header("Location: " . $redirectUrl);
  exit;
}

// Check for coupon message from redirect
if (isset($_GET['coupon_msg'])) {
  $couponMsg = urldecode($_GET['coupon_msg']);
}

// Check for applied coupon from redirect
if (isset($_GET['coupon_applied']) && isset($_GET['coupon_code'])) {
  // Re-apply the coupon logic for display
  $code = strtoupper(trim($_GET['coupon_code']));
  if ($code !== '' && $tableExists) {
    $now = date('Y-m-d H:i:s');
    $userIdInt = (int)$_SESSION['userId'];
    $scopeType = $scholarshipId ? 'scholarship' : 'course_pricing';
    $scopeId = $scholarshipId ? $scholarshipId : ($context['courseId'] ?? 0);

    $couponSql = "SELECT * FROM Coupons 
                  WHERE code = ? AND status = 'active' 
                    AND (scope_type = 'global' 
                         OR (scope_type = ? AND (scope_id IS NULL OR scope_id = ?)))
                  LIMIT 1";
    $stmt = $conn->prepare($couponSql);
    $stmt->bind_param('ssi', $code, $scopeType, $scopeId);
    $stmt->execute();
    $couponRes = $stmt->get_result();
    if ($couponRes && $couponRes->num_rows > 0) {
      $coupon = $couponRes->fetch_assoc();
      $stmt->close();

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
      }
    }
  }
}
skip_coupon_apply:

// --- 6) FLUTTERWAVE VARIABLES ---
$customer_email = htmlspecialchars($user['NoEmail'], ENT_QUOTES, 'UTF-8');
$customer_name  = htmlspecialchars($user['NoUsername'], ENT_QUOTES, 'UTF-8');
$transaction_id = 'TX-' . time() . '-' . ($scholarshipId ? ('S' . $scholarshipId) : $courseId);
$userId = isset($_SESSION['userId']) ? (int)$_SESSION['userId'] : 0;

// Build MoMo USSD variables (use final amount, no decimals)
$ussdAmountInt = max(0, (int)round($finalAmount));
$ussdCode = "*182*8*1*021112*{$ussdAmountInt}#";
$telHref = "tel:*182*8*1*021112*{$ussdAmountInt}%23";

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

// Handle payment continuation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['continue_to_pay'])) {
  // Redirect to prevent form resubmission - preserve original parameters
  $baseUrl = $_SERVER['PHP_SELF'];
  $params = [];
  
  // Preserve original GET parameters
  if (isset($_GET['course'])) {
    $params['course'] = $_GET['course'];
  }
  if (isset($_GET['scholarshipId'])) {
    $params['scholarshipId'] = $_GET['scholarshipId'];
  }
  if (isset($_GET['subscription'])) {
    $params['subscription'] = $_GET['subscription'];
  }
  if (isset($_GET['coupon_msg'])) {
    $params['coupon_msg'] = $_GET['coupon_msg'];
  }
  if (isset($_GET['coupon_applied'])) {
    $params['coupon_applied'] = $_GET['coupon_applied'];
  }
  if (isset($_GET['coupon_code'])) {
    $params['coupon_code'] = $_GET['coupon_code'];
  }
  
  // Add autopay flag
  $params['autopay'] = '1';
  
  $redirectUrl = $baseUrl . '?' . http_build_query($params);
  header("Location: " . $redirectUrl);
    exit;
}

// Determine whether to proceed to payment or show coupon form first
$shouldCreatePayment = isset($_GET['autopay']);

if ($shouldCreatePayment) {
  // Flag to render v3 hosted form
  $renderHostedForm = true;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secure Checkout – <?= htmlspecialchars($courseName) ?></title>
  <link rel="shortcut icon" href="https://mkscholars.com/images/logo/logoRound.png" type="image/x-icon">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary-color: #2563eb;
      --primary-light: #3b82f6;
      --primary-dark: #1d4ed8;
      --secondary-color: #64748b;
      --success-color: #10b981;
      --success-light: #34d399;
      --warning-color: #f59e0b;
      --danger-color: #ef4444;
      --light-bg: #f8fafc;
      --card-bg: #ffffff;
      --border-color: #e2e8f0;
      --text-primary: #1e293b;
      --text-secondary: #64748b;
      --text-muted: #94a3b8;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
      --radius-sm: 6px;
      --radius-md: 8px;
      --radius-lg: 12px;
      --radius-xl: 16px;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      line-height: 1.6;
    }

    .checkout-container {
      background: var(--card-bg);
      border-radius: var(--radius-xl);
      box-shadow: var(--shadow-lg);
      overflow: hidden;
      max-width: 480px;
      width: 100%;
      animation: slideUp 0.6s ease-out;
      border: 1px solid var(--border-color);
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .header {
      background: linear-gradient(135deg, var(--card-bg) 0%, var(--light-bg) 100%);
      color: var(--text-primary);
      padding: 32px 24px;
      text-align: center;
      position: relative;
      border-bottom: 1px solid var(--border-color);
    }

    .logo-section {
      margin-bottom: 16px;
    }

    .logo {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
      border-radius: var(--radius-lg);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 12px;
    }

    .header h1 {
      font-size: 1.75rem;
      font-weight: 700;
      margin-bottom: 8px;
      color: var(--text-primary);
    }

    .header p {
      color: var(--text-secondary);
      font-size: 1rem;
      font-weight: 400;
    }

    .back-btn {
      position: absolute;
      left: 20px;
      top: 50%;
      transform: translateY(-50%);
      background: var(--light-bg);
      border: 1px solid var(--border-color);
      color: var(--text-secondary);
      width: 40px;
      height: 40px;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.9rem;
    }

    .back-btn:hover {
      background: var(--primary-color);
      color: white;
      border-color: var(--primary-color);
      transform: translateY(-50%) scale(1.05);
    }

    .content {
      padding: 32px 24px;
    }

    .item-details {
      background: var(--light-bg);
      border-radius: var(--radius-lg);
      padding: 24px;
      margin-bottom: 24px;
      border-left: 4px solid var(--primary-color);
      border: 1px solid var(--border-color);
    }

    .item-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 8px;
    }

    .item-description {
      color: var(--text-secondary);
      font-size: 0.95rem;
      margin-bottom: 16px;
      line-height: 1.5;
    }

    .price-section {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 16px 0;
      border-top: 1px solid var(--border-color);
    }

    .price-label {
      font-size: 1rem;
      color: var(--text-secondary);
      font-weight: 500;
    }

    .price-amount {
      font-size: 1.75rem;
      font-weight: 700;
      color: var(--text-primary);
    }

    .discount-applied {
      background: linear-gradient(135deg, #dcfce7, #bbf7d0);
      color: #166534;
      padding: 8px 16px;
      border-radius: var(--radius-md);
      font-size: 0.875rem;
      font-weight: 600;
      margin-top: 12px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: 1px solid #bbf7d0;
    }

    .coupon-section {
      margin: 24px 0;
    }

    .coupon-label {
      font-size: 1rem;
      font-weight: 600;
      color: var(--text-primary);
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .coupon-form {
      display: flex;
      gap: 12px;
      margin-bottom: 16px;
    }

    .coupon-input {
      flex: 1;
      padding: 14px 16px;
      border: 2px solid var(--border-color);
      border-radius: var(--radius-md);
      font-size: 0.95rem;
      transition: all 0.3s ease;
      outline: none;
      background: var(--card-bg);
      color: var(--text-primary);
    }

    .coupon-input:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    .coupon-input::placeholder {
      color: var(--text-muted);
    }

    .coupon-btn {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
      color: white;
      border: none;
      padding: 14px 20px;
      border-radius: var(--radius-md);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      white-space: nowrap;
      font-size: 0.95rem;
    }

    .coupon-btn:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
    }

    .message {
      padding: 12px 16px;
      border-radius: var(--radius-md);
      margin: 12px 0;
      font-weight: 500;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .message.success {
      background: #dcfce7;
      color: #166534;
      border: 1px solid #bbf7d0;
    }

    .message.error {
      background: #fef2f2;
      color: #dc2626;
      border: 1px solid #fecaca;
    }

    .payment-btn {
      width: 100%;
      background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
      color: white;
      border: none;
      padding: 16px 24px;
      border-radius: var(--radius-md);
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
    }

    .payment-btn:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
      background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
    }

    .loading-section {
      text-align: center;
      padding: 40px 24px;
    }

    .spinner {
      width: 48px;
      height: 48px;
      border: 4px solid var(--border-color);
      border-top: 4px solid var(--primary-color);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    .loading-text {
      font-size: 1.1rem;
      color: var(--text-secondary);
      margin-bottom: 12px;
      font-weight: 500;
    }

    .loading-subtext {
      color: var(--text-muted);
      font-size: 0.9rem;
    }

    .error-section {
      text-align: center;
      padding: 40px 24px;
    }

    .error-icon {
      font-size: 3.5rem;
      color: var(--danger-color);
      margin-bottom: 20px;
    }

    .error-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--text-primary);
      margin-bottom: 12px;
    }
    
    .error-message {
      color: var(--danger-color);
      font-size: 1rem;
      margin-bottom: 16px;
      font-weight: 500;
    }

    .error-description {
      color: var(--text-secondary);
      margin-bottom: 24px;
      line-height: 1.6;
      font-size: 0.95rem;
    }

    .error-actions {
      display: flex;
      gap: 12px;
      justify-content: center;
      flex-wrap: wrap;
    }
    
    .btn {
      padding: 12px 20px;
      border: none;
      border-radius: var(--radius-md);
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      font-size: 0.95rem;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
      color: white;
    }

    .btn-secondary {
      background: var(--secondary-color);
      color: white;
    }
    
    .btn:hover {
      transform: translateY(-1px);
      box-shadow: var(--shadow-md);
    }

    .security-badge {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      margin-top: 20px;
      color: var(--text-muted);
      font-size: 0.85rem;
      padding: 12px;
      background: var(--light-bg);
      border-radius: var(--radius-md);
      border: 1px solid var(--border-color);
    }

    .security-badge i {
      color: var(--success-color);
    }

    .original-price {
      text-decoration: line-through;
      color: var(--text-muted);
      font-size: 1.2rem;
      margin-right: 8px;
    }

    .discount-amount {
      color: var(--success-color);
      font-weight: 600;
    }

    /* MoMo styles */
    .momo-box {
      background: var(--light-bg);
      border: 1px solid var(--border-color);
      border-radius: var(--radius-md);
      padding: 16px;
    }
    .momo-code {
      text-align: center;
    }
    .ussd-code {
      display: inline-block;
      font-family: 'Courier New', monospace;
      font-size: 1rem;
      font-weight: 700;
      color: var(--text-primary);
      background: #f8f9fa;
      padding: 8px 12px;
      border-radius: 8px;
      border: 2px dashed #ff6b35;
      margin-bottom: 6px;
      word-break: break-all;
    }
    .momo-note {
      color: var(--text-secondary);
      font-size: 0.85rem;
      display: block;
    }
    .momo-link {
      display: inline-flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #ff6b35, #e55a2b);
      color: white;
      padding: 10px 16px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
      margin-top: 6px;
      text-align: center;
    }
    .momo-link:hover { color: #fff; }
    .ussd-code-mobile {
      display: block;
      font-family: 'Courier New', monospace;
      font-size: 1.05rem;
      font-weight: 700;
      color: #fff;
      background: rgba(255, 255, 255, 0.2);
      padding: 6px 10px;
      border-radius: 6px;
      margin-bottom: 4px;
      word-break: break-all;
      border: 2px solid rgba(255, 255, 255, 0.3);
    }
    .momo-note-mobile {
      color: rgba(255, 255, 255, 0.95);
      font-size: 0.82rem;
      display: block;
    }

    @media (max-width: 480px) {
      .checkout-container {
        margin: 10px;
        border-radius: var(--radius-lg);
      }
      
      .content {
        padding: 24px 20px;
      }
      
      .coupon-form {
        flex-direction: column;
      }
      
      .error-actions {
        flex-direction: column;
      }

      .header {
        padding: 24px 20px;
      }

      .back-btn {
        left: 16px;
        width: 36px;
        height: 36px;
      }
    }
  </style>
</head>

<body>
  <div class="checkout-container">
    <?php if (isset($errorMessage)): ?>
      <!-- Error Display -->
      <div class="header">
        <button class="back-btn" onclick="goBack()" title="Go Back">
          <i class="fas fa-arrow-left"></i>
        </button>
        <div class="logo-section">
          <div class="logo">MK</div>
        </div>
        <h1><i class="fas fa-exclamation-triangle"></i> Payment Error</h1>
        <p>Something went wrong with your payment</p>
      </div>
      
      <div class="error-section">
        <div class="error-icon">
          <i class="fas fa-times-circle"></i>
        </div>
        <h2 class="error-title">Payment Failed</h2>
        <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <p class="error-description">
          Please try again or contact support if the problem persists.<br>
          If you need immediate assistance, please call <?= SUPPORT_PHONE ?>
        </p>
        <div class="error-actions">
          <button class="btn btn-primary" onclick="window.location.reload()">
            <i class="fas fa-redo"></i> Try Again
          </button>
          <button class="btn btn-secondary" onclick="history.back()">
            <i class="fas fa-arrow-left"></i> Go Back
          </button>
        </div>
      </div>
    <?php else: ?>
      <!-- Main Checkout Content -->
      <div class="header">
        <button class="back-btn" onclick="goBack()" title="Go Back">
          <i class="fas fa-arrow-left"></i>
        </button>
        <div class="logo-section">
          <div class="logo">MK</div>
        </div>
        <h1><i class="fas fa-credit-card"></i> Secure Checkout</h1>
        <p>Complete your payment securely</p>
      </div>
      
      <div class="content">
        <?php if (!empty($renderHostedForm)): ?>
          <!-- Payment Redirect -->
          <div class="loading-section">
            <div class="spinner"></div>
            <p class="loading-text">Redirecting to secure payment...</p>
            <p class="loading-subtext">Please wait while we redirect you to our payment processor.</p>
            
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
          </div>
        <?php else: ?>
          <!-- Item Details -->
          <div class="item-details">
            <h2 class="item-title"><?= htmlspecialchars($courseName) ?></h2>
            <p class="item-description"><?= htmlspecialchars($courseDescription) ?></p>
            <div class="price-section">
              <span class="price-label">Total Amount</span>
              <span class="price-amount"><?= number_format((float)$finalAmount, 0) ?> <?= htmlspecialchars($currencySymbol) ?></span>
            </div>
            
            <?php if (!empty($appliedCoupon)): ?>
              <div class="discount-applied">
                <i class="fas fa-ticket-alt"></i> 
                Coupon "<?= htmlspecialchars($appliedCoupon['code']) ?>" applied - 
                <?= number_format((float)$appliedCoupon['discountAmount'], 0) ?> <?= htmlspecialchars($currencySymbol) ?> off
              </div>
            <?php endif; ?>
          </div>

          <!-- Coupon Section -->
          <div class="coupon-section">
            <h3 class="coupon-label">
              <i class="fas fa-ticket-alt"></i> Have a Coupon?
            </h3>
            
            <?php if (!empty($couponMsg)): ?>
              <div class="message <?= strpos(strtolower($couponMsg),'invalid')!==false || strpos(strtolower($couponMsg),'error')!==false || strpos(strtolower($couponMsg),'not available')!==false ? 'error' : 'success' ?>">
                <i class="fas fa-<?= strpos(strtolower($couponMsg),'invalid')!==false || strpos(strtolower($couponMsg),'error')!==false || strpos(strtolower($couponMsg),'not available')!==false ? 'exclamation-circle' : 'check-circle' ?>"></i>
                <?= htmlspecialchars($couponMsg) ?>
              </div>
            <?php endif; ?>

            <form method="post" class="coupon-form">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
              <input type="text" 
                     name="couponName" 
                     class="coupon-input" 
                     placeholder="Enter coupon code" 
                     value="<?= htmlspecialchars($_SESSION['checkout_coupon_code'] ?? '') ?>" 
                     style="text-transform: uppercase;" />
              <button class="coupon-btn" type="submit" name="applyCoupon" value="1">
                <i class="fas fa-check"></i> Apply
              </button>
            </form>
          </div>

        <!-- MoMo USSD Payment Option (mobile-friendly) -->
        <div class="coupon-section" style="margin-top: 8px;">
          <h3 class="coupon-label">
            <i class="fas fa-mobile-alt"></i> Pay with MoMo (USSD)
          </h3>
          <div class="momo-box">
            <div id="momo-code" class="momo-code">
              <span class="ussd-code"><?= htmlspecialchars($ussdCode) ?></span>
              <small class="momo-note">Copy and dial this code on your phone</small>
            </div>
            <a id="momo-link" class="momo-link" href="<?= htmlspecialchars($telHref) ?>" style="display:none;">
              <span class="ussd-code-mobile"><?= htmlspecialchars($ussdCode) ?></span>
              <small class="momo-note-mobile">Tap to dial this code</small>
            </a>
          </div>
        </div>

          <!-- Payment Button -->
          <form method="post">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>" />
            <button class="payment-btn" type="submit" name="continue_to_pay" value="1">
              <i class="fas fa-lock"></i> Continue to Secure Payment
            </button>
          </form>

          <!-- Security Badge -->
          <div class="security-badge">
            <i class="fas fa-shield-alt"></i>
            <span>Your payment is secured with 256-bit SSL encryption</span>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <script>
    // Smart back button function to prevent form resubmission
    function goBack() {
      // Check if there's a referrer and it's from the same domain
      if (document.referrer && document.referrer.includes(window.location.hostname)) {
        // Use history.back() if we have a valid referrer
        history.back();
      } else {
        // Fallback to a safe redirect
        const referrer = document.referrer;
        if (referrer) {
          window.location.href = referrer;
        } else {
          // Default fallback - redirect to courses page
          window.location.href = '../courses.php';
        }
      }
    }

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }

    // Auto-clear only temporary URL parameters after a short delay to clean up the URL
    setTimeout(function() {
      if (window.history.replaceState && (window.location.search.includes('coupon_msg') || window.location.search.includes('coupon_applied'))) {
        // Preserve essential parameters (course, scholarshipId, subscription, autopay)
        const url = new URL(window.location);
        const essentialParams = ['course', 'scholarshipId', 'subscription', 'autopay'];
        const newParams = new URLSearchParams();
        
        // Keep essential parameters
        essentialParams.forEach(param => {
          if (url.searchParams.has(param)) {
            newParams.set(param, url.searchParams.get(param));
          }
        });
        
        // Build clean URL with only essential parameters
        const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + 
                        (newParams.toString() ? '?' + newParams.toString() : '');
        window.history.replaceState({}, document.title, cleanUrl);
      }
    }, 3000);

    // Mobile vs desktop behavior for MoMo section
    function isMobileDevice() {
      return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
             || (window.innerWidth <= 768);
    }
    function setupMomoUI() {
      var momoCode = document.getElementById('momo-code');
      var momoLink = document.getElementById('momo-link');
      if (!momoCode || !momoLink) return;
      if (isMobileDevice()) {
        momoCode.style.display = 'none';
        momoLink.style.display = 'inline-flex';
      } else {
        momoCode.style.display = 'block';
        momoLink.style.display = 'none';
        // Copy code on click for desktop
        momoCode.onclick = function() {
          var code = momoCode.querySelector('.ussd-code');
          if (!code) return;
          var text = code.textContent || code.innerText;
          navigator.clipboard.writeText(text).then(function(){
            alert('USSD code copied. Dial it on your phone.');
          });
        }
      }
    }
    document.addEventListener('DOMContentLoaded', setupMomoUI);
    window.addEventListener('resize', setupMomoUI);
  </script>

</body>
</html>