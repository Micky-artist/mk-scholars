<?php
session_start();
include('./dbconnection/connection.php');
include('./php/validateSession.php');

$userId = $_SESSION['userId'];

// Fetch subscriptions
$subscriptions = [];
$subQuery = "SELECT SubId, Item FROM subscription WHERE UserId = ?";
$stmt = $conn->prepare($subQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $subscriptions[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
  <meta charset="UTF-8" />
  <title>Settings | MK Scholars</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

  <style>
    :root {
      --bg-primary: #f3f4f6;
      --bg-secondary: #ffffff;
      --text-primary: #1f2937;
      --text-secondary: #4b5563;
      --glass-bg: rgba(255, 255, 255, 0.9);
      --glass-border: rgba(255, 255, 255, 0.3);
    }

    [data-theme="dark"] {
      --bg-primary: #111827;
      --bg-secondary: #1f2937;
      --text-primary: #f9fafb;
      --text-secondary: #9ca3af;
      --glass-bg: rgba(31, 41, 55, 0.9);
      --glass-border: rgba(255, 255, 255, 0.1);
    }

    body {
      background: var(--bg-primary);
      color: var(--text-primary);
      min-height: 100vh;
      transition: all 0.3s;
    }

    .sidebar {
      background: var(--glass-bg);
      border-right: 1px solid var(--glass-border);
      position: fixed;
      top: 0;
      left: 0;
      height: 100vh;
      z-index: 1000;
      width: 250px;
      overflow-y: auto;
      transition: transform 0.3s ease;
    }

    .main-content {
      margin-left: 250px;
      padding: 2rem;
      transition: margin-left 0.3s;
    }

    .glass-panel {
      background: var(--glass-bg);
      border: 1px solid var(--glass-border);
      border-radius: 12px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .theme-toggle {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1100;
      width: 50px;
      height: 50px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      .sidebar.active {
        transform: translateX(0);
      }

      .main-content {
        margin-left: 0 !important;
      }
    }
  </style>
</head>

<body>

<!-- Theme Toggle Button -->
<button class="btn btn-secondary theme-toggle glass-panel" style="color: orange;">
  <i class="fas fa-moon"></i>
</button>

<!-- Sidebar -->
<?php include('./partials/dashboardNavigation.php'); ?>

<!-- Main Settings Section -->
<main class="main-content">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
      <i class="fas fa-bars"></i>
    </button>
    <h3 class="mb-0">Account Settings</h3>
  </div>

  <!-- Username -->
  <div class="glass-panel">
    <h5>Update Username</h5>
    <form method="post" action="php/update_username.php">
      <div class="input-group">
        <input type="text" class="form-control" name="username" placeholder="Enter new username" required>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>

  <!-- Email -->
  <div class="glass-panel">
    <h5>Change Email</h5>
    <form method="post" action="php/update_email.php">
      <div class="input-group">
        <input type="email" class="form-control" name="email" placeholder="Enter new email" required>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
    </form>
  </div>

  <!-- Password -->
  <div class="glass-panel">
    <h5>Change Password</h5>
    <form method="post" action="php/update_password.php">
      <div class="mb-2">
        <input type="password" class="form-control" name="current_password" placeholder="Current password" required>
      </div>
      <div class="mb-2">
        <input type="password" class="form-control" name="new_password" placeholder="New password" required>
      </div>
      <button class="btn btn-primary" type="submit">Change Password</button>
    </form>
  </div>

  <!-- Subscriptions -->
  <div class="glass-panel">
    <h5>Select Subscription</h5>
    <form method="post" action="php/update_subscription.php">
      <select name="subscription" class="form-select mb-3" required>
        <?php foreach ($subscriptions as $sub): ?>
          <option value="<?= $sub['SubId'] ?>"><?= htmlspecialchars($sub['Item']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary" type="submit">Update Subscription</button>
    </form>
  </div>
</main>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const body = document.body;
    const toggle = document.querySelector('.theme-toggle');
    const icon = toggle.querySelector('i');
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const savedTheme = localStorage.getItem('theme') || 'light';

    body.setAttribute('data-theme', savedTheme);
    icon.className = savedTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';

    toggle.addEventListener('click', () => {
      const newTheme = body.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
      body.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      icon.className = newTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
    });

    if (sidebarToggle) {
      sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
      });

      document.addEventListener('click', function (e) {
        if (
          window.innerWidth < 768 &&
          sidebar &&
          !sidebar.contains(e.target) &&
          !sidebarToggle.contains(e.target)
        ) {
          sidebar.classList.remove('active');
        }
      });
    }
  });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
