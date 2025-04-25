<?php
session_start();
require_once './dbconnections/connection.php';
require_once './php/validateAdminSession.php';

// Ensure admin is logged in
if (!isset($_SESSION['adminId'])) {
    header('Location: login');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grant_subscription'])) {
    // Collect and validate inputs
    $userId            = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $subscriptionType  = filter_input(INPUT_POST, 'subscription_type', FILTER_SANITIZE_STRING);
    $duration          = filter_input(INPUT_POST, 'duration', FILTER_VALIDATE_INT);
    $allowedTypes      = ['notes','instructor','moroccoadmissions'];

    if (!$userId || !in_array($subscriptionType, $allowedTypes) || $duration <= 0) {
        $_SESSION['error'] = 'Invalid subscription parameters.';
        header('Location: add_subscription');
        exit;
    }

    // Generate subscription code
    try {
        $rand = bin2hex(random_bytes(4));
    } catch (Exception $e) {
        $rand = substr(md5(uniqid('', true)), 0, 8);
    }
    $subscriptionCode = 'ADM_' . strtoupper($rand) . '_' . date('Ymd');
    $subscriptionDate = date('Y-m-d');
    $expirationDate   = date('Y-m-d', strtotime("+{$duration} days"));

    // Insert into DB
    $stmt = $conn->prepare("
        INSERT INTO subscription 
          (SubscriptionStatus, item, UserId, adminId, SubscriptionCode, subscriptionDate, expirationDate)
        VALUES 
          (1, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        'siisss',
        $subscriptionType,
        $userId,
        $_SESSION['adminId'],
        $subscriptionCode,
        $subscriptionDate,
        $expirationDate
    );

    if ($stmt->execute()) {
        $_SESSION['success'] = 'Subscription granted successfully.';
        header('Location: subscriptions');
        exit;
    } else {
        $_SESSION['error'] = 'Database error: ' . $stmt->error;
        header('Location: add_subscription');
        exit;
    }
}

// Handle search
$searchQuery = '';
$users       = [];
if (!empty($_GET['search'])) {
    $searchQuery = trim($_GET['search']);
    $esc         = $conn->real_escape_string($searchQuery);
    $sql         = "
      SELECT NoUserId, NoUsername, NoEmail, NoPhone, NoCreationDate
        FROM normUsers
       WHERE NoUsername LIKE '%{$esc}%'
          OR NoEmail    LIKE '%{$esc}%'
          OR NoPhone    LIKE '%{$esc}%'
       LIMIT 20
    ";
    if ($res = $conn->query($sql)) {
        $users = $res->fetch_all(MYSQLI_ASSOC);
    }
}

// Grab and clear flash messages
$error   = $_SESSION['error']   ?? null; unset($_SESSION['error']);
$success = $_SESSION['success'] ?? null; unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Grant New Subscription</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary-color:   #4361ee;
      --secondary-color: #3f37c9;
      --success-color:   #4cc9f0;
      --light-bg:        #f5f7fb;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background-color: var(--light-bg);
    }
    .card {
      border: none;
      border-radius: .75rem;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .card-header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      border-radius: .75rem .75rem 0 0 !important;
    }
    .search-box {
      position: relative;
    }
    .search-box i {
      position: absolute;
      left: 15px;
      top: 50%;
      transform: translateY(-50%);
      color: #adb5bd;
    }
    .search-box input {
      padding-left: 40px;
      border-radius: 50px;
    }
    .user-card {
      border: 1px solid rgba(0,0,0,0.08);
      border-radius: .5rem;
      transition: transform .3s, border-color .3s, box-shadow .3s;
      cursor: pointer;
    }
    .user-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      border-color: var(--primary-color);
    }
    .user-card.selected {
      background-color: rgba(67,97,238,0.05);
      border-color: var(--primary-color);
    }
    .subscription-option {
      border: 1px solid #dee2e6;
      border-radius: .5rem;
      padding: 1rem;
      margin-bottom: 1rem;
      transition: border-color .2s, background-color .2s;
      cursor: pointer;
    }
    .subscription-option:hover {
      border-color: var(--primary-color);
    }
    .subscription-option.selected {
      background-color: rgba(67,97,238,0.05);
      border-color: var(--primary-color);
    }
    .btn-grant {
      background-color: var(--success-color);
      color: white;
      border-radius: 50px;
      padding: .5rem 1.5rem;
      font-weight: 500;
    }
    .btn-grant:hover {
      background-color: #3ab4d8;
      color: white;
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Grant New Subscription</h4>
        <a href="subscriptions" class="btn btn-sm btn-light">
          <i class="fas fa-arrow-left me-1"></i>Back to Subscriptions
        </a>
      </div>
      <div class="card-body">
        <?php if ($error): ?>
          <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
          <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="row">
          <!-- Search Column -->
          <div class="col-md-6">
            <h5 class="mb-3"><i class="fas fa-search me-2"></i>Search User</h5>
            <form method="GET" class="mb-4">
              <div class="search-box mb-3">
                <i class="fas fa-search"></i>
                <input
                  type="text"
                  name="search"
                  class="form-control"
                  placeholder="Username, email or phone"
                  value="<?= htmlspecialchars($searchQuery, ENT_QUOTES) ?>"
                >
              </div>
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-search me-1"></i>Search
              </button>
            </form>

            <?php if ($searchQuery !== ''): ?>
              <h6 class="mb-3">Results (<?= count($users) ?>)</h6>
              <div class="user-list">
                <?php if ($users): ?>
                  <?php foreach ($users as $user): ?>
                    <div
                      class="user-card p-3 mb-2"
                      onclick="selectUser(<?= (int)$user['NoUserId'] ?>, '<?= addslashes(htmlspecialchars($user['NoUsername'], ENT_QUOTES)) ?>')"
                      id="user-<?= (int)$user['NoUserId'] ?>"
                    >
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-1"><?= htmlspecialchars($user['NoUsername']) ?></h6>
                          <small class="text-muted"><?= htmlspecialchars($user['NoEmail']) ?></small>
                        </div>
                        <small class="text-muted">
                          Joined <?= date('M Y', strtotime($user['NoCreationDate'])) ?>
                        </small>
                      </div>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <div class="alert alert-info">No users found.</div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Subscription Column -->
          <div class="col-md-6">
            <h5 class="mb-3"><i class="fas fa-gift me-2"></i>Subscription Details</h5>
            <form method="POST" id="subscriptionForm">
              <input type="hidden" name="user_id" id="userIdInput">
              <input type="hidden" name="subscription_type" id="subscriptionTypeInput">

              <div class="mb-3">
                <label class="form-label">Selected User</label>
                <div class="form-control" id="selectedUserDisplay">None</div>
              </div>

              <div class="mb-3">
                <label class="form-label">Subscription Type</label>
                <div class="row g-2">
                  <div class="col-6">
                    <div class="subscription-option" onclick="selectSubscription('notes')" id="option-notes">
                      <h6>Notes Access</h6>
                      <small class="text-muted">Study materials</small>
                    </div>
                  </div>
                  
                  <div class="col-6">
                    <div class="subscription-option" onclick="selectSubscription('instructor')" id="option-instructor">
                      <h6>Instructor</h6>
                      <small class="text-muted">Study materials</small>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="subscription-option" onclick="selectSubscription('moroccoadmissions')" id="option-moroccoadmissions">
                      <h6>Morocco Admissions</h6>
                      <small class="text-muted">Study materials</small>
                    </div>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="duration" class="form-label">Duration (days)</label>
                <input
                  type="number"
                  class="form-control"
                  id="duration"
                  name="duration"
                  min="1"
                  value="30"
                  required
                >
              </div>

              <div class="d-grid">
                <button
                  type="submit"
                  name="grant_subscription"
                  class="btn btn-grant"
                  id="grantButton"
                  disabled
                >
                  <i class="fas fa-check-circle me-1"></i>Grant Subscription
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let selectedUserId = null;
    let selectedSubscriptionType = null;

    function selectUser(userId, username) {
      document.querySelectorAll('.user-card').forEach(c => c.classList.remove('selected'));
      document.getElementById(`user-${userId}`).classList.add('selected');
      selectedUserId = userId;
      document.getElementById('userIdInput').value = userId;
      document.getElementById('selectedUserDisplay').textContent = username;
      checkFormCompletion();
    }

    function selectSubscription(type) {
      document.querySelectorAll('.subscription-option').forEach(o => o.classList.remove('selected'));
      document.getElementById(`option-${type}`).classList.add('selected');
      selectedSubscriptionType = type;
      document.getElementById('subscriptionTypeInput').value = type;
      // default durations
      document.getElementById('duration').value = (type === '15days' ? 15 : 30);
      checkFormCompletion();
    }

    function checkFormCompletion() {
      const btn = document.getElementById('grantButton');
      btn.disabled = !(selectedUserId && selectedSubscriptionType);
    }
  </script>
</body>
</html>
