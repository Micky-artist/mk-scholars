<?php

if (!hasPermission('ManageRights')) {
  header("Location: ./index");
  exit;
}


// Note: Activate/Deactivate actions are now handled in manage-access.php BEFORE HTML output
// This prevents "headers already sent" errors


// Check super admin status (implement proper authentication)
$_SESSION['is_super_admin'] = true;

// Note: Form submission is now handled in manage-access.php BEFORE HTML output
// This prevents "headers already sent" errors
// Check if show deactivated users toggle is set
$showDeactivated = isset($_GET['showDeactivated']) && $_GET['showDeactivated'] == '1';

// Fetch all admins and their rights
$admins = [];
$sql = "SELECT u.userId, u.username, u.email, u.status, ar.* 
        FROM users u 
        LEFT JOIN AdminRights ar ON u.userId = ar.AdminId";
        
// Filter out inactive users unless toggle is on
if (!$showDeactivated) {
    $sql .= " WHERE u.status = 1";
}

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $admins[] = $row;
  }
}
?>

<!-- <!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Access Control Panel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->
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
      box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .admin-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 32px rgba(108, 93, 211, 0.15);
    }

    .permission-pill {
      background: rgba(108, 93, 211, 0.1);
      color: var(--primary);
      border-radius: 8px;
      padding: 4px 12px;
      font-size: 0.85rem;
      cursor: pointer;
    }

    .permission-pill:hover {
      background: rgba(108, 93, 211, 0.2);
    }

    .modal-float {
      animation: floatIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes floatIn {
      from {
        transform: translateY(20px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
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
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    input:checked+.permission-toggle {
      background: var(--primary);
    }

    input:checked+.permission-toggle:after {
      transform: translateX(24px);
    }

    .section-header {
      border-left: 4px solid var(--primary);
      padding-left: 1rem;
      margin: 2rem 0 1rem;
    }

    .action-menu {
      position: relative;
      display: inline-block;
    }

    .action-menu-btn {
      background: transparent;
      border: none;
      color: #6c757d;
      font-size: 1.2rem;
      cursor: pointer;
      padding: 0.25rem 0.5rem;
      border-radius: 4px;
      transition: all 0.2s;
    }

    .action-menu-btn:hover {
      background: rgba(108, 93, 211, 0.1);
      color: var(--primary);
    }

    .action-dropdown {
      position: absolute;
      top: 100%;
      right: 0;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      min-width: 180px;
      z-index: 1000;
      display: none;
      margin-top: 0.5rem;
      overflow: hidden;
    }

    .action-dropdown.show {
      display: block;
      animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .action-dropdown-item {
      display: block;
      width: 100%;
      padding: 0.75rem 1rem;
      text-align: left;
      border: none;
      background: white;
      color: #333;
      text-decoration: none;
      transition: all 0.2s;
      cursor: pointer;
      font-size: 0.9rem;
    }

    .action-dropdown-item:hover {
      background: rgba(108, 93, 211, 0.1);
      color: var(--primary);
    }

    .action-dropdown-item:first-child {
      border-top-left-radius: 8px;
      border-top-right-radius: 8px;
    }

    .action-dropdown-item:last-child {
      border-bottom-left-radius: 8px;
      border-bottom-right-radius: 8px;
    }

    .action-dropdown-item i {
      margin-right: 0.5rem;
      width: 16px;
    }

    .action-dropdown-item.danger {
      color: #dc3545;
    }

    .action-dropdown-item.danger:hover {
      background: rgba(220, 53, 69, 0.1);
      color: #dc3545;
    }

    .action-dropdown-item.success {
      color: #198754;
    }

    .action-dropdown-item.success:hover {
      background: rgba(25, 135, 84, 0.1);
      color: #198754;
    }
  </style>
<!-- </head>

<body> -->
  <div class="container py-5">
    <div class="text-center mb-5">
      <h1 class="display-5 fw-bold mb-3" style="color: var(--primary)">Access Control Panel</h1>
      <p class="text-muted">Manage administrator permissions with precision</p>
    </div>
    
    <!-- Toggle for showing deactivated users -->
    <div class="mb-4 d-flex justify-content-end">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="showDeactivatedToggle" 
               <?= $showDeactivated ? 'checked' : '' ?> 
               onchange="toggleDeactivatedUsers()">
        <label class="form-check-label" for="showDeactivatedToggle">
          Show Deactivated Users
        </label>
      </div>
    </div>

    <?php if (isset($_SESSION['flash'])): ?>
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['flash'] ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <div class="row g-4">
      <?php foreach ($admins as $admin): ?>
        <div class="col-md-6 col-xl-4">
          <div class="admin-card p-4" data-admin-id="<?= $admin['userId'] ?>">
            <div class="d-flex align-items-center mb-3">
              <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px">
                <i class="fas fa-user-shield"></i>
              </div>
              <div class="ms-3 flex-grow-1">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h5 class="mb-0">
                      <?= $admin['username'] ?>
                      <?php if ($admin['status'] != 1): ?>
                        <span class="badge bg-secondary ms-2">Deactivated</span>
                      <?php endif; ?>
                    </h5>
                    <small class="text-muted">Last modified: Today</small>
                  </div>
                  <div class="action-menu">
                    <button class="action-menu-btn" onclick="toggleActionMenu(event, <?= $admin['userId'] ?>)" title="More options">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <div class="action-dropdown" id="actionMenu<?= $admin['userId'] ?>">
                      <a href="#" class="action-dropdown-item" onclick="event.preventDefault(); viewAdminDetails(<?= $admin['userId'] ?>); closeActionMenu(<?= $admin['userId'] ?>);">
                        <i class="fas fa-eye"></i> Details
                      </a>
                      <a href="#" class="action-dropdown-item" onclick="event.preventDefault(); resetAdminPassword(<?= $admin['userId'] ?>, '<?= htmlspecialchars($admin['username'], ENT_QUOTES) ?>'); closeActionMenu(<?= $admin['userId'] ?>);">
                        <i class="fas fa-key"></i> Reset Password
                      </a>
                      <?php if ($admin['status'] == 1): ?>
                        <a href="?Deactivate=<?= $admin['userId'] ?>" class="action-dropdown-item danger" onclick="closeActionMenu(<?= $admin['userId'] ?>);">
                          <i class="fas fa-ban"></i> Deactivate
                        </a>
                      <?php else: ?>
                        <a href="?Activate=<?= $admin['userId'] ?>" class="action-dropdown-item success" onclick="closeActionMenu(<?= $admin['userId'] ?>);">
                          <i class="fas fa-check-circle"></i> Activate
                        </a>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="d-flex flex-wrap gap-2 mb-3">
              <?php foreach ($admin as $key => $value): ?>
                <?php if ($value == 1 && !in_array($key, ['RightId', 'AdminId', 'userId', 'username', 'email', 'status'])): ?>
                  <span class="permission-pill remove-right" data-right="<?= $key ?>" data-admin-id="<?= $admin['userId'] ?>">
                    <?= $key ?>
                  </span>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
            <div class="text-center">
              <button class="btn btn-sm btn-primary" onclick="openPermissionsModal(<?= $admin['userId'] ?>)" title="Edit Permissions">
                <i class="fas fa-edit"></i> Edit Permissions
              </button>
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
        <form method="POST" id="rightsForm">
          <input type="hidden" name="adminId" id="modalAdminId">
          <div class="modal-body" id="modalBody">
            <!-- Modal content will be loaded dynamically -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Admin Details Modal -->
  <div class="modal fade" id="adminDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 modal-float">
        <div class="modal-header">
          <h3 class="modal-title">Admin Details</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="adminDetailsBody">
          <!-- Admin details will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Reset Password Modal -->
  <div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 modal-float">
        <div class="modal-header">
          <h3 class="modal-title">Reset Password</h3>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="resetPasswordForm">
          <input type="hidden" name="adminId" id="resetPasswordAdminId">
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Admin Username</label>
              <input type="text" class="form-control" id="resetPasswordUsername" readonly>
            </div>
            <div class="mb-3">
              <label class="form-label">New Password</label>
              <input type="password" class="form-control" name="newPassword" id="resetPasswordNew" required minlength="6" placeholder="Enter new password (min 6 characters)">
            </div>
            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input type="password" class="form-control" name="confirmPassword" id="resetPasswordConfirm" required minlength="6" placeholder="Confirm new password">
            </div>
            <div id="resetPasswordError" class="alert alert-danger d-none"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-warning">Reset Password</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const modal = document.getElementById('rightsModal');
      const modalBody = document.getElementById('modalBody');

      // Function to load admin rights content
      async function loadAdminRights(adminId) {
        try {
          const response = await fetch(`./php/get_admin_rights.php?adminId=${adminId}`);
          if (!response.ok) {
            throw new Error('Failed to load admin rights');
          }
          const data = await response.text();
          modalBody.innerHTML = data;
          
          // Re-initialize toggle switches after content is loaded
          const toggles = modalBody.querySelectorAll('input[type="checkbox"]');
          toggles.forEach(toggle => {
            toggle.addEventListener('change', function() {
              this.setAttribute('value', this.checked ? '1' : '');
            });
            
            // Initialize toggle visual state
            const toggleDiv = this.nextElementSibling;
            if (toggleDiv && toggleDiv.classList.contains('permission-toggle')) {
              if (this.checked) {
                toggleDiv.style.background = 'var(--primary)';
              }
            }
          });
          
          // Manually initialize course access checkboxes after content is loaded
          function initCourseAccessCheckboxes() {
            const courseCheckboxes = modalBody.querySelectorAll('.course-access-checkbox');
            console.log('Initializing course checkboxes:', courseCheckboxes.length);
            
            // Get adminId from the modal
            const modalAdminIdInput = document.getElementById('modalAdminId');
            const adminId = modalAdminIdInput ? parseInt(modalAdminIdInput.value) : 0;
            console.log('Admin ID for course access:', adminId);
            
            if (!adminId) {
              console.error('Admin ID not found!');
              return;
            }
            
            if (courseCheckboxes.length === 0) {
              console.log('No course checkboxes found in modal');
              return;
            }
            
            courseCheckboxes.forEach(function(checkbox) {
              const courseId = checkbox.dataset.courseId;
              const label = checkbox.closest('.course-access-label');
              const toggle = checkbox.nextElementSibling;
              
              console.log('Setting up checkbox for course:', courseId, 'Label:', label, 'Toggle:', toggle);
              
              // Set initial visual state
              if (toggle && toggle.classList.contains('permission-toggle')) {
                if (checkbox.checked) {
                  toggle.style.background = 'var(--primary)';
                } else {
                  toggle.style.background = '#e0e0e0';
                }
                toggle.style.cursor = 'pointer';
                toggle.style.pointerEvents = 'auto';
              }
              
              // Create click handler function
              const handleClick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Click detected for course:', courseId);
                
                // Toggle the checkbox
                const wasChecked = checkbox.checked;
                checkbox.checked = !wasChecked;
                
                // Update visual state immediately
                if (toggle) {
                  if (checkbox.checked) {
                    toggle.style.background = 'var(--primary)';
                  } else {
                    toggle.style.background = '#e0e0e0';
                  }
                }
                
                // Trigger change event to handle AJAX
                const changeEvent = new Event('change', { bubbles: true, cancelable: true });
                checkbox.dispatchEvent(changeEvent);
              };
              
              // Attach click handler to toggle div
              if (toggle) {
                toggle.addEventListener('click', handleClick);
                toggle.style.cursor = 'pointer';
              }
              
              // Attach click handler to label
              if (label) {
                label.addEventListener('click', function(e) {
                  // Only handle if clicking on label itself, not on toggle (to avoid double trigger)
                  if (e.target === label || e.target === checkbox) {
                    handleClick(e);
                  }
                });
              }
              
              // Handle checkbox change event
              const handleCheckboxChange = function() {
                const isGranted = this.checked;
                console.log('Checkbox changed for course:', courseId, 'Granted:', isGranted);
                
                // Update toggle visual state
                const currentToggle = this.nextElementSibling;
                if (currentToggle && currentToggle.classList.contains('permission-toggle')) {
                  if (isGranted) {
                    currentToggle.classList.add('active');
                    currentToggle.style.background = 'var(--primary)';
                  } else {
                    currentToggle.classList.remove('active');
                    currentToggle.style.background = '#e0e0e0';
                  }
                }
                
                // Update via AJAX
                const formData = new URLSearchParams();
                formData.append('adminId', adminId);
                formData.append('courseId', courseId);
                formData.append('grant', isGranted ? '1' : '0');
                
                console.log('Sending AJAX request:', { adminId, courseId, grant: isGranted ? '1' : '0' });
                
                fetch('./php/manage_course_access.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                  },
                  body: formData.toString()
                })
                .then(response => {
                  console.log('Response status:', response.status, response.statusText);
                  if (!response.ok) {
                    return response.text().then(text => {
                      console.error('Response text:', text);
                      throw new Error('Network response was not ok: ' + response.status);
                    });
                  }
                  return response.json();
                })
                .then(data => {
                  console.log('Response data:', data);
                  if (!data.success) {
                    // Revert checkbox if failed
                    this.checked = !isGranted;
                    const currentToggle = this.nextElementSibling;
                    if (currentToggle && currentToggle.classList.contains('permission-toggle')) {
                      if (!isGranted) {
                        currentToggle.classList.add('active');
                        currentToggle.style.background = 'var(--primary)';
                      } else {
                        currentToggle.classList.remove('active');
                        currentToggle.style.background = '#e0e0e0';
                      }
                    }
                    alert('Error: ' + (data.message || 'Failed to update course access'));
                  } else {
                    console.log('✓ Course access updated successfully:', data.message);
                  }
                })
                .catch(error => {
                  console.error('Error updating course access:', error);
                  this.checked = !isGranted;
                  const currentToggle = this.nextElementSibling;
                  if (currentToggle && currentToggle.classList.contains('permission-toggle')) {
                    if (!isGranted) {
                      currentToggle.classList.add('active');
                      currentToggle.style.background = 'var(--primary)';
                    } else {
                      currentToggle.classList.remove('active');
                      currentToggle.style.background = '#e0e0e0';
                    }
                  }
                  alert('Error updating course access: ' + error.message);
                });
              };
              
              // Remove old listener and add new one
              const newCheckbox = checkbox.cloneNode(true);
              checkbox.parentNode.replaceChild(newCheckbox, checkbox);
              newCheckbox.addEventListener('change', handleCheckboxChange);
            });
            
            console.log('✓ Course access checkboxes initialized');
          }
          
          // Initialize after a short delay to ensure DOM is ready
          setTimeout(initCourseAccessCheckboxes, 300);
        } catch (error) {
          console.error('Error loading admin rights:', error);
          modalBody.innerHTML = '<div class="alert alert-danger">Error loading admin rights. Please try again.</div>';
        }
      }
      
      // Function to open permissions modal
      window.openPermissionsModal = async function(adminId) {
        document.getElementById('modalAdminId').value = adminId;
        await loadAdminRights(adminId);
        const modal = new bootstrap.Modal(document.getElementById('rightsModal'));
        modal.show();
      };
      
      // Load modal content when modal is shown (for backward compatibility)
      modal.addEventListener('show.bs.modal', async (e) => {
        const adminId = document.getElementById('modalAdminId').value || (e.relatedTarget?.closest('.admin-card')?.dataset?.adminId);
        if (adminId) {
          document.getElementById('modalAdminId').value = adminId;
          await loadAdminRights(adminId);
        }

      });

      // Handle click on permission pills
      document.addEventListener('click', async (e) => {
        if (e.target.classList.contains('remove-right')) {
          const right = e.target.getAttribute('data-right');
          const adminId = e.target.getAttribute('data-admin-id');

          // Show confirmation alert
          const result = await Swal.fire({
            title: 'Remove Right?',
            text: `Are you sure you want to remove the "${right}" right?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
          });

          if (result.isConfirmed) {
            try {
            // Send AJAX request to remove the right
            const response = await fetch(`./php/remove_right.php?adminId=${adminId}&right=${right}`);
            const data = await response.json();

            if (data.success) {
                Swal.fire({
                  title: 'Success!',
                  text: `The "${right}" right has been removed.`,
                  icon: 'success',
                  confirmButtonText: 'OK'
                }).then(() => {
                  // Reload the page to refresh the UI
                  location.reload();
                });
            } else {
                Swal.fire('Error!', data.message || 'Failed to remove the right.', 'error');
              }
            } catch (error) {
              console.error('Error removing right:', error);
              Swal.fire('Error!', 'An error occurred while removing the right.', 'error');
            }
          }
        }
      });

      // Add custom confirmation dialog
      document.getElementById('rightsForm').addEventListener('submit', (e) => {
        e.preventDefault();
        Swal.fire({
          title: 'Confirm Changes',
          text: 'Are you sure you want to update these permissions?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, save changes',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            e.target.submit();
          }
        });
      });
    });
    
    // Toggle function for showing deactivated users
    function toggleDeactivatedUsers() {
      const toggle = document.getElementById('showDeactivatedToggle');
      const showDeactivated = toggle.checked ? '1' : '0';
      const url = new URL(window.location.href);
      url.searchParams.set('showDeactivated', showDeactivated);
      window.location.href = url.toString();
    }

    // Toggle action menu dropdown
    function toggleActionMenu(event, adminId) {
      event.stopPropagation();
      const menu = document.getElementById('actionMenu' + adminId);
      const isShowing = menu.classList.contains('show');
      
      // Close all other menus first
      document.querySelectorAll('.action-dropdown.show').forEach(dropdown => {
        dropdown.classList.remove('show');
      });
      
      // Toggle current menu
      if (!isShowing) {
        menu.classList.add('show');
      }
    }

    // Close action menu
    function closeActionMenu(adminId) {
      const menu = document.getElementById('actionMenu' + adminId);
      if (menu) {
        menu.classList.remove('show');
      }
    }

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
      if (!event.target.closest('.action-menu')) {
        document.querySelectorAll('.action-dropdown.show').forEach(dropdown => {
          dropdown.classList.remove('show');
        });
      }
    });
    
    // View admin details
    async function viewAdminDetails(adminId) {
      try {
        const response = await fetch(`./php/get_admin_details.php?adminId=${adminId}`);
        if (!response.ok) {
          throw new Error('Failed to load admin details');
        }
        const data = await response.text();
        document.getElementById('adminDetailsBody').innerHTML = data;
        const modal = new bootstrap.Modal(document.getElementById('adminDetailsModal'));
        modal.show();
      } catch (error) {
        console.error('Error loading admin details:', error);
        Swal.fire('Error!', 'Failed to load admin details. Please try again.', 'error');
      }
    }
    
    // Reset admin password
    function resetAdminPassword(adminId, username) {
      document.getElementById('resetPasswordAdminId').value = adminId;
      document.getElementById('resetPasswordUsername').value = username;
      document.getElementById('resetPasswordNew').value = '';
      document.getElementById('resetPasswordConfirm').value = '';
      document.getElementById('resetPasswordError').classList.add('d-none');
      const modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
      modal.show();
    }
    
    // Handle reset password form submission
    document.getElementById('resetPasswordForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const adminId = document.getElementById('resetPasswordAdminId').value;
      const newPassword = document.getElementById('resetPasswordNew').value;
      const confirmPassword = document.getElementById('resetPasswordConfirm').value;
      const errorDiv = document.getElementById('resetPasswordError');
      
      // Validate passwords match
      if (newPassword !== confirmPassword) {
        errorDiv.textContent = 'Passwords do not match!';
        errorDiv.classList.remove('d-none');
        return;
      }
      
      // Validate password length
      if (newPassword.length < 6) {
        errorDiv.textContent = 'Password must be at least 6 characters long!';
        errorDiv.classList.remove('d-none');
        return;
      }
      
      // Show confirmation
      const result = await Swal.fire({
        title: 'Reset Password?',
        text: 'Are you sure you want to reset this admin\'s password?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset it',
        cancelButtonText: 'Cancel'
      });
      
      if (result.isConfirmed) {
        try {
          const formData = new FormData();
          formData.append('adminId', adminId);
          formData.append('newPassword', newPassword);
          
          const response = await fetch('./php/reset_admin_password.php', {
            method: 'POST',
            body: formData
          });
          
          const data = await response.json();
          
          if (data.success) {
            Swal.fire({
              title: 'Success!',
              text: 'Password has been reset successfully.',
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(() => {
              bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal')).hide();
            });
          } else {
            errorDiv.textContent = data.message || 'Failed to reset password';
            errorDiv.classList.remove('d-none');
          }
        } catch (error) {
          console.error('Error resetting password:', error);
          errorDiv.textContent = 'An error occurred while resetting the password.';
          errorDiv.classList.remove('d-none');
        }
      }
    });
  </script>
<!-- </body>

</html> -->