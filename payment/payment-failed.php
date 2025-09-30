<?php
include('./config.php');
include('../partials/navigation.php');

$error = $_GET['error'] ?? 'unknown';
$tx_ref = $_GET['tx_ref'] ?? '';

$errorMessages = [
    'payment_failed' => 'Your payment was not successful. Please try again.',
    'database' => 'There was an error processing your enrollment. Please contact support.',
    'no_course' => 'Invalid course information. Please try again.',
    'unknown' => 'An unexpected error occurred. Please try again.'
];

$errorMessage = $errorMessages[$error] ?? $errorMessages['unknown'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - <?= COMPANY_NAME ?></title>
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
            max-width: 500px;
            width: 90%;
        }
        
        .error-icon {
            font-size: 4rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }
        
        h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }
        
        .error-message {
            color: #7f8c8d;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .transaction-ref {
            background: #f8f9fa;
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
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        
        .btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn.secondary {
            background: #95a5a6;
        }
        
        .btn.secondary:hover {
            background: #7f8c8d;
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
        <div class="error-icon">❌</div>
        <h1>Payment Failed</h1>
        <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        
        <?php if ($tx_ref): ?>
            <div class="transaction-ref">
                Transaction Reference: <?= htmlspecialchars($tx_ref) ?>
            </div>
        <?php endif; ?>
        
        <div>
            <a href="../subscription?course=<?= $_GET['courseId'] ?? '' ?>" class="btn">Try Again</a>
            <a href="../courses" class="btn secondary">Browse Courses</a>
        </div>
        
        <div class="support-info">
            <p><strong>Need Help?</strong></p>
            <p>Contact us at <?= SUPPORT_PHONE ?></p>
            <p>Or email us at support@mkscholars.com</p>
        </div>
    </div>
</body>
</html>
