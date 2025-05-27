<?php
// TransactionCompleted.php
session_start();
include('../dbconnection/connection.php');

// 1) Must be GET + status=successful
if ($_SERVER['REQUEST_METHOD'] !== 'GET'
    || !isset($_GET['status'])
    || $_GET['status'] !== 'successful'
) {
    header('Location: ./failed');
    exit;
}

// 2) Must have logged‑in user
if (empty($_SESSION['userId'])) {
    header('Location: ./failed');
    exit;
}
$userId = (int) $_SESSION['userId'];

// 3) Validate subscription type
$allowedTypes = ['notes','englishcourse', 'moroccoadmissions','codingcourse'];
if (!isset($_GET['type']) || !in_array($_GET['type'], $allowedTypes, true)) {
    header('Location: ./failed');
    exit;
}
$subscriptionType = $_GET['type'];

// 4) Map to server‑side amount
$amountMap = [
    'notes'      => 4000,
    'englishcourse' => 15000,
    'moroccoadmissions' => 2600,
    'codingcourse' => 25000,
];
$finalAmount = $amountMap[$subscriptionType];

// 5) Verify user exists in normUsers
$stmt = $conn->prepare("SELECT NoUserId FROM normUsers WHERE NoUserId = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$stmt->close();

if ($userResult->num_rows === 0) {
    header('Location: ./failed');
    exit;
}

// 6) Check for existing active subscription
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT 1
    FROM subscription
    WHERE UserId = ?
      AND SubscriptionStatus = 1
      AND expirationDate > ?
    LIMIT 1
");
$stmt->bind_param('is', $userId, $today);
$stmt->execute();
$activeResult = $stmt->get_result();
$stmt->close();

// If already active, just go to success
if ($activeResult->num_rows > 0) {
    header('Location: ./success');
    exit;
}

// 7) Insert new subscription
$statusFlag       = 1;
$subscriptionCode = 'SUB_' . random_int(100, 999) . '_' . date('Ymd_His');
$subscriptionDate = $today;
$expirationDate   = date('Y-m-d', strtotime('+1 month'));

$stmt = $conn->prepare("INSERT INTO subscription (
      SubscriptionStatus,
      item,
      UserId,
      SubscriptionCode,
      subscriptionDate,
      expirationDate
    ) VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param(
    'isisss',
    $statusFlag,
    $subscriptionType,
    $userId,
    $subscriptionCode,
    $subscriptionDate,
    $expirationDate
);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: ./success');
    exit;
} else {
    $stmt->close();
    header('Location: ./failed');
    exit;
}
