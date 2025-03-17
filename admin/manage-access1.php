<?php
session_start();
// Database connection
$host = 'localhost';
$db = 'mkscholars';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check super admin status (implement proper authentication)
$_SESSION['is_super_admin'] = true;

// Fetch all admins and their rights
$admins = [];
$sql = "SELECT * FROM AdminRights";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $admins[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['is_super_admin']) {
    $adminId = $_POST['adminId'];
    $rights = [];
    foreach ($_POST as $key => $value) {
        if ($key !== 'adminId') {
            $rights[$key] = isset($_POST[$key]) ? 1 : 0;
        }
    }

    $sql = "UPDATE AdminRights SET " . 
    implode(', ', array_map(fn($k) => "$k = {$rights[$key]}", array_keys($rights))) . 
    " WHERE AdminId = $adminId";

    if ($conn->query($sql)) {
        $_SESSION['flash'] = 'Rights updated successfully!';
    } else {
        $_SESSION['flash'] = "Error: " . $conn->error;
    }
    header("Location: ".$_SERVER['REQUEST_URI']);
    exit;
}
?>
<!-- <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Rights Manager</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"> -->
  <style>
    :root {
      --primary: #6c5dd3;
      --secondary: #a0d7e7;
      --light: #f9f9ff;
    }

    body {
      background: linear-gradient(135deg, #f9f9ff 0%, #e6f1ff 100%);
      min-height: 100vh;
      font-family: 'Inter', sans-serif;
    }

    .admin-card {
      background: white;
      border-radius: 15px;
      border: none;
      box-shadow: 0 4px 24px rgba(0,0,0,0.06);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .admin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 32px rgba(108,93,211,0.15);
    }

    .permission-pill {
      background: rgba(108,93,211,0.1);
      color: var(--primary);
      border-radius: 8px;
      padding: 4px 12px;
      font-size: 0.85rem;
    }

    .modal-float {
      animation: floatIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes floatIn {
      from { transform: translateY(20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .permission-toggle {
      position: relative;
      width: 48px;
      height: 24px;
      border-radius: 12px;
      background: #e0e0e0;
      transition: all 0.3s;
    }

    .permission-toggle:after {
      content: '';
      position: absolute;
      width: 20px;
      height: 20px;
      background: white;
      border-radius: 50%;
      top: 2px;
      left: 2px;
      transition: all 0.3s;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    input:checked + .permission-toggle {
      background: var(--primary);
    }

    input:checked + .permission-toggle:after {
      transform: translateX(24px);
    }

    .section-header {
      border-left: 4px solid var(--primary);
      padding-left: 1rem;
      margin: 2rem 0 1rem;
    }
  </style>
<!-- </head>
<body> -->
  <div class="container py-5">
    <div class="text-center mb-5">
      <h1 class="display-5 fw-bold mb-3" style="color: var(--primary)">Access Control Panel</h1>
      <p class="text-muted">Manage administrator permissions with precision</p>
    </div>

    <?php if(isset($_SESSION['flash'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="row g-4">
      <?php foreach ($admins as $admin): ?>
        <div class="col-md-6 col-xl-4">
          <div class="admin-card p-4" 
               data-bs-toggle="modal" 
               data-bs-target="#rightsModal" 
               data-admin-id="<?= $admin['AdminId'] ?>">
            <div class="d-flex align-items-center mb-3">
              <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                   style="width: 40px; height: 40px">
                <i class="fas fa-user-shield"></i>
              </div>
              <div class="ms-3">
                <h5 class="mb-0">Admin #<?= $admin['AdminId'] ?></h5>
                <small class="text-muted">Last modified: Today</small>
              </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
              <?php foreach ($admin as $key => $value): ?>
                <?php if ($value == 1 && !in_array($key, ['RightId','AdminId'])): ?>
                  <span class="permission-pill"><?= $key ?></span>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Rights Modal -->
  <div class="modal fade" id="rightsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 modal-float">
        <div class="modal-header">
          <h3 class="modal-title">Edit Permissions</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form method="POST">
          <input type="hidden" name="adminId" id="modalAdminId">
          <div class="modal-body">
            <div class="row g-4">
              <!-- Application Management -->
              <div class="col-md-6">
                <h5 class="section-header">Application Controls</h5>
                <?php $appRights = ['ViewApplications', 'DeleteApplication', 'EditApplication', 'PublishApplication']; ?>
                <?php foreach ($appRights as $right): ?>
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <label><?= $right ?></label>
                    <label class="d-flex align-items-center">
                      <input type="checkbox" name="<?= $right ?>" class="d-none">
                      <div class="permission-toggle"></div>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>

              <!-- User Management -->
              <div class="col-md-6">
                <h5 class="section-header">User Controls</h5>
                <?php $userRights = ['ViewUsers', 'ManageUsers', 'AddAdmin']; ?>
                <?php foreach ($userRights as $right): ?>
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <label><?= $right ?></label>
                    <label class="d-flex align-items-center">
                      <input type="checkbox" name="<?= $right ?>" class="d-none">
                      <div class="permission-toggle"></div>
                    </label>
                  </div>
                <?php endforeach; ?>
              </div>

              <!-- Add other sections similarly -->

            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const modal = document.getElementById('rightsModal');
      modal.addEventListener('show.bs.modal', e => {
        const adminId = e.relatedTarget.closest('.admin-card').dataset.adminId;
        document.getElementById('modalAdminId').value = adminId;

        const admins = <?= json_encode($admins) ?>;
        const admin = admins.find(a => a.AdminId == adminId);

        // Update all toggles
        modal.querySelectorAll('input[type="checkbox"]').forEach(input => {
          const rightName = input.name;
          input.checked = admin[rightName] === 1;
        });
      });

      // Add confirmation dialog
      document.querySelector('form').addEventListener('submit', (e) => {
        if (!confirm('Are you sure you want to update these permissions?')) {
          e.preventDefault();
        }
      });
    });
  </script>
<!-- </body>
</html> -->