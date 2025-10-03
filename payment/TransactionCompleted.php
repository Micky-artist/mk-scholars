<?php
/**
 * Flutterwave Payment Success Handler
 * 
 * This page handles successful payments from Flutterwave v4 API
 */

include('../dbconnection/connection.php');
include('./config.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../login");
    exit;
}

// Get parameters from Flutterwave callback
$courseId = $_GET['courseId'] ?? '';
$type = $_GET['type'] ?? '';
$userId = $_GET['userId'] ?? $_SESSION['userId'];
$tx_ref = $_GET['tx_ref'] ?? '';
$transaction_id = $_GET['transaction_id'] ?? '';

// If we have transaction_id, verify the payment
if (!empty($transaction_id)) {
    $secretKey = "54cadf5f-a20f-4af5-8825-36e0121da065";
    
    // Build the verify endpoint for v4
    $verifyUrl = "https://api.flutterwave.com/v4/transactions/{$transaction_id}/verify";
    
    $ch = curl_init($verifyUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer {$secretKey}",
            "Content-Type: application/json"
        ]
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        $result = json_decode($response, true);
        
        if (isset($result['status']) && $result['status'] === "success" && $result['data']['status'] === "successful") {
            $paymentData = $result['data'];
            
            // Determine context
            $contextType = $paymentData['meta']['contextType'] ?? null;
            $courseId = $paymentData['meta']['courseId'] ?? $courseId;
            $scholarshipId = $paymentData['meta']['scholarshipId'] ?? null;
            $couponId = $paymentData['meta']['couponId'] ?? null;
            $couponCode = $paymentData['meta']['couponCode'] ?? null;

            if ($contextType === 'course' && $courseId) {
                // Insert enrollment record
                $enrollmentQuery = "INSERT INTO CourseEnrollments (userId, courseId, enrollmentDate, paymentAmount, paymentCurrency, paymentStatus, transactionReference) 
                                   VALUES (?, ?, NOW(), ?, ?, 'completed', ?) 
                                   ON DUPLICATE KEY UPDATE 
                                   enrollmentDate = NOW(), 
                                   paymentAmount = VALUES(paymentAmount),
                                   paymentCurrency = VALUES(paymentCurrency),
                                   paymentStatus = 'completed',
                                   transactionReference = VALUES(transactionReference)";
                
                $stmt = $conn->prepare($enrollmentQuery);
                $stmt->bind_param('iisss', $userId, $courseId, $paymentData['amount'], $paymentData['currency'], $tx_ref);
                
                if ($stmt->execute()) {
                    $enrollmentSuccess = true;
                } else {
                    $enrollmentError = "Database error: " . $stmt->error;
                }
            } elseif ($contextType === 'scholarship' && $scholarshipId) {
                // Insert application request after successful payment
                $requestDate = date('Y-m-d');
                $requestTime = date('H:i:s');
                $status = 0; // unseen
                $comments = $_SESSION['pending_application_comments'] ?? '';
                unset($_SESSION['pending_application_comments']);
                $stmt = $conn->prepare("INSERT INTO ApplicationRequests (UserId, ApplicationId, RequestDate, RequestTime, Status, Comments) VALUES (?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param('iissss', $userId, $scholarshipId, $requestDate, $requestTime, $status, $comments);
                    if ($stmt->execute()) {
                        $enrollmentSuccess = true;
                    } else {
                        $enrollmentError = "Application save error: " . $stmt->error;
                    }
                } else {
                    $enrollmentError = "Application prepare failed: " . $conn->error;
                }
            }

            // Log coupon redemption if any
            if (isset($enrollmentSuccess) && $enrollmentSuccess && $couponId) {
                $stmt = $conn->prepare("INSERT INTO CouponRedemptions (coupon_id, user_id, transaction_reference, amount_charged, currency, status, created_at) VALUES (?, ?, ?, ?, ?, 'success', NOW())");
                if ($stmt) {
                    $amountCharged = $paymentData['amount'];
                    $currency = $paymentData['currency'];
                    $stmt->bind_param('iisss', $couponId, $userId, $tx_ref, $amountCharged, $currency);
                    $stmt->execute();
                }
            }
        } else {
            $paymentError = "Payment verification failed";
        }
    } else {
        $paymentError = "Failed to verify payment";
    }
}

// Get course information for display
$courseName = "Course";
$courseDescription = "";
if ($courseId) {
    $courseQuery = "SELECT courseName, courseDescription FROM Courses WHERE courseId = ?";
    $stmt = $conn->prepare($courseQuery);
    $stmt->bind_param("i", $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($course = $result->fetch_assoc()) {
        $courseName = $course['courseName'];
        $courseDescription = $course['courseDescription'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - <?= COMPANY_NAME ?></title>
    <link rel="shortcut icon" href="<?= COMPANY_LOGO ?>" type="image/x-icon">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .payment-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 600px;
            width: 90%;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #27ae60;
            margin-bottom: 1rem;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .success-message {
            color: #7f8c8d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .course-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin: 1.5rem 0;
            text-align: left;
        }
        
        .course-info h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .transaction-details {
            background: #ecf0f1;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            font-family: monospace;
            color: #6c757d;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            background: #229954;
            transform: translateY(-2px);
        }
        
        .btn.secondary {
            background: #3498db;
        }
        
        .btn.secondary:hover {
            background: #2980b9;
        }
        
        .error-message {
            color: #e74c3c;
            background: #fdf2f2;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            border-left: 4px solid #e74c3c;
        }
        
        .support-info {
            margin-top: 2rem;
            padding: 1rem;
            background: #ecf0f1;
            border-radius: 10px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <?php if (isset($enrollmentSuccess) && $enrollmentSuccess): ?>
            <div class="success-icon">✅</div>
            <h1>Payment Successful!</h1>
            <p class="success-message">
                Thank you for your payment! You have been successfully enrolled in the course.
            </p>
            
            <div class="course-info">
                <h3><?= htmlspecialchars($courseName) ?></h3>
                <p><?= htmlspecialchars($courseDescription) ?></p>
            </div>
            
            <?php if ($tx_ref): ?>
                <div class="transaction-details">
                    Transaction Reference: <?= htmlspecialchars($tx_ref) ?><br>
                    <?php if (isset($paymentData)): ?>
                        Amount: <?= number_format($paymentData['amount']) ?> <?= htmlspecialchars($paymentData['currency']) ?><br>
                        Payment Status: <?= htmlspecialchars($paymentData['status']) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            <div>
                <a href="../e-learning" class="btn">Go to E-Learning</a>
                <a href="../courses" class="btn secondary">Browse More Courses</a>
            </div>
            
        <?php elseif (isset($paymentError)): ?>
            <div class="success-icon">❌</div>
            <h1>Payment Verification Failed</h1>
            <div class="error-message">
                <?= htmlspecialchars($paymentError) ?>
            </div>
            <div>
                <a href="../subscription?course=<?= $courseId ?>" class="btn">Try Again</a>
                <a href="../courses" class="btn secondary">Browse Courses</a>
            </div>
            
        <?php elseif (isset($enrollmentError)): ?>
            <div class="success-icon">⚠️</div>
            <h1>Payment Successful - Enrollment Issue</h1>
            <p class="success-message">
                Your payment was successful, but there was an issue enrolling you in the course.
            </p>
            <div class="error-message">
                <?= htmlspecialchars($enrollmentError) ?>
            </div>
            <div>
                <a href="../e-learning" class="btn">Check E-Learning</a>
                <a href="../courses" class="btn secondary">Browse Courses</a>
            </div>
            
        <?php else: ?>
            <div class="success-icon">✅</div>
            <h1>Payment Processed</h1>
            <p class="success-message">
                Your payment has been processed. Please check your email for confirmation.
            </p>
            <div>
                <a href="../e-learning" class="btn">Go to E-Learning</a>
                <a href="../courses" class="btn secondary">Browse Courses</a>
            </div>
        <?php endif; ?>
        
        <div class="support-info">
            <p><strong>Need Help?</strong></p>
            <p>Contact us at <?= SUPPORT_PHONE ?></p>
            <p>Or email us at support@mkscholars.com</p>
        </div>
    </div>
</body>
</html>