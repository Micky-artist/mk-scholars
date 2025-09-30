<?php
/**
 * Flutterwave Payment Verification
 * 
 * This script verifies the payment status with Flutterwave API
 * and handles the payment completion logic.
 */

include('../dbconnection/connection.php');
include('./config.php');

session_start();

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: ../login");
    exit;
}

// Get transaction ID from URL (Flutterwave v4 uses transaction_id)
$transaction_id = $_GET['transaction_id'] ?? '';
$tx_ref = $_GET['tx_ref'] ?? '';

if (empty($transaction_id)) {
    die('No transaction ID supplied.');
}

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

if ($httpCode !== 200) {
    die('Failed to verify payment');
}

$result = json_decode($response, true);

if (!$result || $result['status'] !== 'success') {
    die('Payment verification failed');
}

$paymentData = $result['data'];

// Check if payment was successful
if ($paymentData['status'] === 'successful') {
    // Payment successful - process enrollment
    $courseId = $paymentData['meta']['courseId'] ?? null;
    $userId = $paymentData['meta']['userId'] ?? $_SESSION['userId'];
    $amount = $paymentData['amount'];
    $currency = $paymentData['currency'];
    
    if ($courseId) {
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
        $stmt->bind_param('iisss', $userId, $courseId, $amount, $currency, $tx_ref);
        
        if ($stmt->execute()) {
            // Redirect to success page
            header("Location: " . SUCCESS_REDIRECT_URL . "?status=success&courseId=" . $courseId . "&tx_ref=" . $tx_ref);
            exit;
        } else {
            // Database error
            error_log("Enrollment failed for user $userId, course $courseId: " . $stmt->error);
            header("Location: " . FAILURE_REDIRECT_URL . "?error=database&tx_ref=" . $tx_ref);
            exit;
        }
    } else {
        // No course ID in metadata
        header("Location: " . FAILURE_REDIRECT_URL . "?error=no_course&tx_ref=" . $tx_ref);
        exit;
    }
} else {
    // Payment failed
    header("Location: " . FAILURE_REDIRECT_URL . "?error=payment_failed&tx_ref=" . $tx_ref);
    exit;
}
?>
