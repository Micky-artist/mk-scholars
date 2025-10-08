<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['userId'];
$userName = $_SESSION['userName'] ?? $_SESSION['username'] ?? '';
$userEmail = $_SESSION['userEmail'] ?? $_SESSION['NoEmail'] ?? '';
$userPhone = $_SESSION['userPhone'] ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <title>Profile | MK Scholars</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Universal Navigation -->
            <?php 
            try {
                // Set current page for navigation highlighting
                $_GET['page'] = 'profile';
                if (file_exists("./partials/universalNavigation.php")) {
                    include("./partials/universalNavigation.php"); 
                } else {
                    echo "<!-- Navigation file not found -->";
                }
            } catch (Exception $e) {
                echo "<!-- Navigation error: " . $e->getMessage() . " -->";
            }
            ?>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content">
                <div class="content-container">
                    <!-- Page Header -->
                    <div class="page-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="page-title">Profile</h1>
                                <p class="page-subtitle">Manage your account settings and view your subscriptions</p>
                            </div>
                            <button class="btn btn-light d-md-none sidebar-toggle" type="button">
                                <i class="fas fa-bars"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Profile Information Card -->
                    <div class="glass-panel slide-in">
                        <div class="row align-items-center">
                            <div class="col-md-3 text-center mb-4 mb-md-0">
                                <div class="profile-avatar mx-auto mb-3">
                                    <i class="fas fa-user fa-3x text-primary"></i>
                                </div>
                                <h3 class="profile-name"><?php echo htmlspecialchars($userName); ?></h3>
                                <p class="profile-email"><?php echo htmlspecialchars($userEmail); ?></p>
                            </div>
                            <div class="col-md-9">
                                <div class="profile-stats">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <span class="stat-number" id="subscriptionCount">0</span>
                                                <span class="stat-label">Total Subscriptions</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <span class="stat-number" id="activeCount">0</span>
                                                <span class="stat-label">Active</span>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="stat-item">
                                                <span class="stat-number" id="expiredCount">0</span>
                                                <span class="stat-label">Expired</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile Card -->
                    <div class="glass-panel slide-in">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-edit fa-2x text-primary me-3"></i>
                                <h5 class="mb-0">Edit Profile</h5>
                            </div>
                        </div>
                        
                        <form id="profileForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="userName" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="userName" name="userName" value="<?php echo htmlspecialchars($userName); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="userEmail" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="userEmail" name="userEmail" value="<?php echo htmlspecialchars($userEmail); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="userPhone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="userPhone" name="userPhone" value="<?php echo htmlspecialchars($userPhone); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="currentPassword" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" name="currentPassword" placeholder="Enter current password to change">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="newPassword" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" name="newPassword" placeholder="Enter new password">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm new password">
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" id="saveBtn">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- My Subscriptions Card -->
                    <div class="glass-panel slide-in">
                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-credit-card fa-2x text-primary me-3"></i>
                                <h5 class="mb-0">My Subscriptions</h5>
                            </div>
                            <button class="btn btn-outline-primary" onclick="loadSubscriptions()">
                                <i class="fas fa-sync-alt me-2"></i>Refresh
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table" id="subscriptionsTable">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-book me-2"></i>Course Name</th>
                                        <th><i class="fas fa-info-circle me-2"></i>Status</th>
                                        <th><i class="fas fa-calendar-plus me-2"></i>Start Date</th>
                                        <th><i class="fas fa-calendar-times me-2"></i>End Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4" class="text-center py-4">
                                            <div class="loading"></div>
                                            <span class="ms-2">Loading subscriptions...</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="subsMsg" class="text-muted text-center mt-3"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Theme Toggle Button -->
    <button class="btn btn-secondary theme-toggle" onclick="toggleTheme()">
        <i class="fas fa-moon"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Prevent multiple initializations
            if (window.profilePageInitialized) {
                return;
            }
            window.profilePageInitialized = true;
            // Theme toggle functionality
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.body.setAttribute('data-theme', savedTheme);
            updateThemeIcon();

            // Mobile sidebar toggle
            $('.sidebar-toggle').click(function() {
                $('.sidebar').toggleClass('active');
            });

            // Close sidebar when clicking outside on mobile
            $(document).click(function(e) {
                if (window.innerWidth < 768 && !$('.sidebar').is(e.target) && $('.sidebar').has(e.target).length === 0 && !$('.sidebar-toggle').is(e.target)) {
                    $('.sidebar').removeClass('active');
                }
            });

            // Profile form submission
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                
                const saveBtn = $('#saveBtn');
                const originalText = saveBtn.html();
                
                saveBtn.html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');
                saveBtn.prop('disabled', true);
                
                const formData = {
                    userName: $('#userName').val().trim(),
                    userEmail: $('#userEmail').val().trim(),
                    userPhone: $('#userPhone').val().trim(),
                    currentPassword: $('#currentPassword').val(),
                    newPassword: $('#newPassword').val(),
                    confirmPassword: $('#confirmPassword').val()
                };
                
                // Basic validation
                if (!formData.userName || !formData.userEmail) {
                    showAlert('Name and email are required fields', 'error');
                    return;
                }
                
                if (formData.newPassword && formData.newPassword !== formData.confirmPassword) {
                    showAlert('New passwords do not match', 'error');
                    return;
                }
                
                $.ajax({
                    url: 'php/update_profile.php',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Profile update response:', response);
                        if (response && response.success) {
                            showAlert('Profile updated successfully!', 'success');
                            // Clear password fields
                            $('#currentPassword, #newPassword, #confirmPassword').val('');
                        } else {
                            showAlert(response.message || 'Failed to update profile', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Profile update error:', status, error, xhr.responseText);
                        showAlert('An error occurred. Please try again.', 'error');
                    },
                    complete: function() {
                        saveBtn.html(originalText);
                        saveBtn.prop('disabled', false);
                    }
                });
            });

            // Load subscriptions on page load with error handling and delay
            setTimeout(function() {
                try {
                    loadSubscriptions();
                } catch (e) {
                    console.error('Error loading subscriptions on page load:', e);
                }
            }, 500); // 500ms delay to ensure page is fully loaded
        });

        function toggleTheme() {
            try {
                const currentTheme = document.body.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                document.body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon();
            } catch (e) {
                console.error('Error toggling theme:', e);
            }
        }

        function updateThemeIcon() {
            try {
                const currentTheme = document.body.getAttribute('data-theme');
                const icon = currentTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
                const themeToggle = document.querySelector('.theme-toggle i');
                if (themeToggle) {
                    themeToggle.className = icon;
                }
            } catch (e) {
                console.error('Error updating theme icon:', e);
            }
        }

        // Prevent multiple simultaneous requests
        let isLoadingSubscriptions = false;

        function loadSubscriptions() {
            if (isLoadingSubscriptions) {
                console.log('Subscription request already in progress, skipping...');
                return;
            }
            
            isLoadingSubscriptions = true;
            console.log('Loading subscriptions for userId:', <?php echo (int)$userId; ?>);
            const tbody = $('#subscriptionsTable tbody');
            if (tbody.length === 0) {
                console.error('Subscription table not found');
                isLoadingSubscriptions = false;
                return;
            }
            tbody.html('<tr><td colspan="4" class="text-center py-4"><div class="loading"></div><span class="ms-2">Loading subscriptions...</span></td></tr>');
            
            $.ajax({
                url: 'php/list_user_subscriptions.php',
                method: 'GET',
                data: { userId: <?php echo (int)$userId; ?> },
                dataType: 'json',
                timeout: 10000, // 10 second timeout
                beforeSend: function() {
                    console.log('AJAX request starting...');
                },
                success: function(data) {
                    console.log('AJAX success, data received:', data);
                    try {
                        let rows = '';
                        let activeCount = 0;
                        let expiredCount = 0;
                        
                        if (!data || data.length === 0) {
                            $('#subsMsg').html('<i class="fas fa-info-circle me-2"></i>No subscriptions found.');
                            tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-3"></i><br>No subscriptions found</td></tr>');
                        } else {
                            $('#subsMsg').text('');
                            data.forEach(function(sub) {
                                const status = (sub.SubscriptionStatus || '').toLowerCase();
                                let statusClass = 'status-pending';
                                
                                if (status.includes('active') || status.includes('valid')) {
                                    statusClass = 'status-active';
                                    activeCount++;
                                } else if (status.includes('expired')) {
                                    statusClass = 'status-expired';
                                    expiredCount++;
                                }
                                
                                rows += `
                                    <tr>
                                        <td><strong>${sub.Item || 'N/A'}</strong></td>
                                        <td><span class="badge ${statusClass}">${sub.SubscriptionStatus || 'Unknown'}</span></td>
                                        <td>${sub.subscriptionDate || 'N/A'}</td>
                                        <td>${sub.expirationDate || 'N/A'}</td>
                                    </tr>`;
                            });
                            tbody.html(rows);
                        }
                        
                        // Update stats
                        $('#subscriptionCount').text(data ? data.length : 0);
                        $('#activeCount').text(activeCount);
                        $('#expiredCount').text(expiredCount);
                    } catch (e) {
                        console.error('Error processing subscription data:', e);
                        tbody.html('<tr><td colspan="4" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Error processing data</td></tr>');
                        $('#subsMsg').html('<i class="fas fa-exclamation-triangle me-2"></i>Error processing subscription data');
                    } finally {
                        isLoadingSubscriptions = false;
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error Details:');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Status Code:', xhr.status);
                    
                    let errorMsg = 'Failed to load subscriptions. ';
                    if (xhr.status === 0) {
                        errorMsg += 'Network error - please check your connection.';
                    } else if (xhr.status === 404) {
                        errorMsg += 'File not found.';
                    } else if (xhr.status === 500) {
                        errorMsg += 'Server error.';
                    } else {
                        errorMsg += 'Error: ' + error;
                    }
                    
                    tbody.html('<tr><td colspan="4" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>' + errorMsg + '</td></tr>');
                    $('#subsMsg').html('<i class="fas fa-exclamation-triangle me-2"></i>' + errorMsg);
                    isLoadingSubscriptions = false;
                }
            });
        }

        function showAlert(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const alert = $(`
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `);
            
            $('.content-container').prepend(alert);
            
            setTimeout(() => {
                alert.alert('close');
            }, 5000);
        }
    </script>

    <style>
        /* CSS Variables - must be defined first */
        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.3);
            --neumorphic-shadow: 5px 5px 10px #d1d5db, -5px -5px 10px #ffffff;
            --primary-color: #3b82f6;
        }

        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(31, 41, 55, 0.9);
            --glass-border: rgba(255, 255, 255, 0.1);
            --neumorphic-shadow: 5px 5px 10px #0a0c10, -5px -5px 10px #283447;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Universal glass panel styles */
        .glass-panel {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }


        /* Page header styles */
        .page-header {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .page-subtitle {
            color: var(--text-secondary);
            margin: 0.5rem 0 0 0;
            font-size: 1rem;
        }

        /* Content container */
        .content-container {
            padding: 2rem;
        }

        /* Main content */
        .main-content {
            margin-left: 250px;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-right: 1px solid var(--glass-border);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            width: 250px;
            overflow-y: auto;
        }

        /* Theme toggle */
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
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
            box-shadow: var(--neumorphic-shadow);
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        /* Additional profile-specific styles */
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.2);
        }

        .profile-name {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0.5rem 0 0.25rem 0;
        }

        .profile-email {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin: 0;
        }

        .profile-stats {
            margin-top: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            border: 1px solid var(--glass-border);
        }

        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid var(--glass-border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            background: var(--glass-bg);
            color: var(--text-primary);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background: var(--bg-secondary);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }


        .btn-outline-primary {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }


        .table {
            background: var(--bg-secondary);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table thead th {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--glass-border);
            vertical-align: middle;
        }


        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .status-active {
            background: #10b981;
            color: white;
        }

        .status-expired {
            background: #ef4444;
            color: white;
        }

        .status-pending {
            background: #f59e0b;
            color: white;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid var(--glass-border);
            border-radius: 50%;
            border-top-color: var(--primary-color);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }


        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-stats .row {
                margin: 0 -0.5rem;
            }
            
            .profile-stats .col-4 {
                padding: 0 0.5rem;
                margin-bottom: 1rem;
            }
            
            .stat-item {
                padding: 0.75rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
        }
    </style>
</body>
</html>