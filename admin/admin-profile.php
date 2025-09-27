<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Update profile information
        $adminId = $_SESSION['adminId'];
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        
        $updateQuery = "UPDATE users SET username = '$username', email = '$email' WHERE userId = $adminId";
        
        if (mysqli_query($conn, $updateQuery)) {
            $_SESSION['AdminName'] = $username;
            $message = 'Profile updated successfully!';
            $messageType = 'success';
        } else {
            $message = 'Error updating profile: ' . mysqli_error($conn);
            $messageType = 'error';
        }
    }
    
    if (isset($_POST['change_password'])) {
        // Change password
        $adminId = $_SESSION['adminId'];
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];
        
        // Verify current password
        $checkQuery = "SELECT password FROM users WHERE userId = $adminId";
        $result = mysqli_query($conn, $checkQuery);
        $admin = mysqli_fetch_assoc($result);
        
        if (password_verify($currentPassword, $admin['password'])) {
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE userId = $adminId";
                
                if (mysqli_query($conn, $updateQuery)) {
                    $message = 'Password changed successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error changing password: ' . mysqli_error($conn);
                    $messageType = 'error';
                }
            } else {
                $message = 'New passwords do not match!';
                $messageType = 'error';
            }
        } else {
            $message = 'Current password is incorrect!';
            $messageType = 'error';
        }
    }
}

// Get current admin data
$adminId = $_SESSION['adminId'];
$adminQuery = "SELECT * FROM users WHERE userId = $adminId";
$adminResult = mysqli_query($conn, $adminQuery);

if (!$adminResult) {
    $message = 'Error retrieving admin data: ' . mysqli_error($conn);
    $messageType = 'error';
    $adminData = array('userId' => '', 'username' => '', 'email' => '', 'status' => 0);
} else {
    $adminData = mysqli_fetch_assoc($adminResult);
    if (!$adminData) {
        $message = 'Admin data not found';
        $messageType = 'error';
        $adminData = array('userId' => '', 'username' => '', 'email' => '', 'status' => 0);
    }
}
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include("./partials/head.php"); ?>

<style>
    .profile-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .profile-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        text-align: center;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        margin: 0 auto 1rem;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
        border-radius: 25px;
        padding: 0.75rem 2rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: #667eea;
        border-color: #667eea;
        transform: translateY(-2px);
    }

    .alert {
        border: none;
        border-radius: 10px;
        font-weight: 500;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        color: #721c24;
    }

    .section-title {
        color: #667eea;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 0;
        width: 50px;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .profile-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #6c757d;
    }

        .info-value {
            color: #495057;
        }

        .activity-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.2s ease;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-item:hover {
            background-color: #f8f9fa;
        }

        .activity-content {
            flex: 1;
            margin-right: 1rem;
        }

        .activity-message {
            font-weight: 500;
            color: #495057;
            margin-bottom: 0.25rem;
        }

        .activity-time {
            font-size: 0.875rem;
            color: #6c757d;
        }

        .activity-status {
            flex-shrink: 0;
        }
</style>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <?php include("./partials/header.php"); ?>
        <!-- ============================================================== -->
        <!-- End Topbar header -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <?php include("./partials/navbar.php"); ?>
        <!-- ============================================================== -->
        <!-- End Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Page wrapper  -->
        <!-- ============================================================== -->
        <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Admin Profile</h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="./home">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>


                <div class="row">
                    <!-- Profile Header Card -->
                    <div class="col-12">
                        <div class="card profile-card">
                            <div class="profile-header">
                                <div class="profile-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h3 class="mb-1"><?php echo htmlspecialchars($adminData['username']); ?></h3>
                                <p class="mb-0">Administrator</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Profile Information -->
                    <div class="col-lg-6">
                        <div class="card profile-card">
                            <div class="card-body">
                                <h5 class="section-title">Profile Information</h5>
                                
                                <div class="profile-info">
                                    <div class="info-item">
                                        <span class="info-label">Username:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($adminData['username']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Email:</span>
                                        <span class="info-value"><?php echo htmlspecialchars($adminData['email']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Status:</span>
                                        <span class="info-value">
                                            <?php if ($adminData['status'] == 1): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Update Profile Form -->
                    <div class="col-lg-6">
                        <div class="card profile-card">
                            <div class="card-body">
                                <h5 class="section-title">Update Profile</h5>
                                
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($adminData['username']); ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($adminData['email']); ?>" required>
                                    </div>
                                    
                                    <button type="submit" name="update_profile" class="btn btn-primary w-100">
                                        <i class="fas fa-save me-2"></i>Update Profile
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <!-- Change Password -->
                    <div class="col-lg-6">
                        <div class="card profile-card">
                            <div class="card-body">
                                <h5 class="section-title">Change Password</h5>
                                
                                <form method="POST" action="">
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="currentPassword" name="currentPassword" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" name="newPassword" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
                                    </div>
                                    
                                    <button type="submit" name="change_password" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-key me-2"></i>Change Password
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Logs -->
                    <div class="col-lg-6">
                        <div class="card profile-card">
                            <div class="card-body">
                                <h5 class="section-title">Recent Activity (Last 5 Days)</h5>
                                
                                <?php
                                // Get admin logs for the past 5 days
                                $adminId = $_SESSION['adminId'];
                                $logsQuery = "SELECT * FROM Logs WHERE userId = $adminId AND logDate >= DATE_SUB(CURDATE(), INTERVAL 5 DAY) ORDER BY logDate DESC, logTime DESC LIMIT 10";
                                $logsResult = mysqli_query($conn, $logsQuery);
                                
                                if ($logsResult && mysqli_num_rows($logsResult) > 0):
                                ?>
                                    <div class="activity-list">
                                        <?php while ($log = mysqli_fetch_assoc($logsResult)): ?>
                                            <div class="activity-item">
                                                <div class="activity-content">
                                                    <div class="activity-message"><?php echo htmlspecialchars($log['logMessage']); ?></div>
                                                    <div class="activity-time">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?php echo date('M d, Y H:i', strtotime($log['logDate'] . ' ' . $log['logTime'])); ?>
                                                    </div>
                                                </div>
                                                <div class="activity-status">
                                                    <?php if ($log['logStatus'] == 1): ?>
                                                        <span class="badge bg-success">Seen</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning">New</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-history fa-3x mb-3"></i>
                                        <p>No recent activity found</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <?php include("./partials/footer.php"); ?>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page wrapper  -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Wrapper -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- All Jquery -->
    <!-- ============================================================== -->
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap tether Core JavaScript -->
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
    <!--Wave Effects -->
    <script src="./dist/js/waves.js"></script>
    <!--Menu sidebar -->
    <script src="./dist/js/sidebarmenu.js"></script>
    <!--Custom JavaScript -->
    <script src="./dist/js/custom.min.js"></script>
    <!--This page JavaScript -->
    <script src="./dist/js/pages/dashboards/dashboard1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Password confirmation validation
        document.getElementById('confirmPassword').addEventListener('input', function() {
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>

</html>
