<?php
/**
 * Flutterwave Payment Configuration
 * 
 * This file contains the Flutterwave API credentials and configuration.
 * Update these values with your actual Flutterwave credentials.
 */

// Flutterwave API Credentials
define('FLUTTERWAVE_SECRET_KEY', 'YOUR_SECRET_KEY'); // Replace with your actual secret key
define('FLUTTERWAVE_PUBLIC_KEY', 'FLWPUBK-fd9a72fe52fbf0bd373323b44d7e2097-X'); // Replace with your actual public key

// Flutterwave API URLs (v4)
define('FLUTTERWAVE_API_URL', 'https://api.flutterwave.com/v4/payments');
define('FLUTTERWAVE_VERIFY_URL', 'https://api.flutterwave.com/v4/transactions');

// Default currency (can be overridden by database)
define('DEFAULT_CURRENCY', 'RWF');

// Redirect URLs
define('SUCCESS_REDIRECT_URL', 'https://mkscholars.com/payment/TransactionCompleted.php');
define('FAILURE_REDIRECT_URL', 'https://mkscholars.com/payment/payment-failed.php');

// Company Information
define('COMPANY_NAME', 'MK Scholars');
define('COMPANY_LOGO', 'https://mkscholars.com/images/logo/logoRound.png');
define('SUPPORT_PHONE', '+250 798 611 161');

// Payment Settings
define('PAYMENT_TIMEOUT', 30); // seconds
define('MAX_RETRY_ATTEMPTS', 3);
?>
