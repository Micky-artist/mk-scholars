<?php
session_start();
if (!isset($_SESSION['userId'])) {
    header('Location: login.php');
    exit;
}
$userId = $_SESSION['userId'];
$userName = $_SESSION['userName'] ?? '';
$userEmail = $_SESSION['userEmail'] ?? '';
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

    <style>
        :root {
            --primary-color: #0E77C2;
            --secondary-color: #083352;
            --accent-color: #4CAF50;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --border-color: #e5e7eb;
            --shadow-light: 0 2px 4px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-heavy: 0 10px 15px rgba(0, 0, 0, 0.1);
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            --gradient-success: linear-gradient(135deg, #28a745, #20c997);
            --gradient-warning: linear-gradient(135deg, #ffc107, #fd7e14);
        }

        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --border-color: #374151;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: all 0.3s;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            width: 250px;
            overflow-y: auto;
            transition: transform 0.3s ease;
            box-shadow: var(--shadow-medium);
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: margin-left 0.3s;
        }

        .glass-panel {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .glass-panel:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-heavy);
        }

        .glass-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-primary);
            color: white;
            border: none;
            box-shadow: var(--shadow-heavy);
            transition: all 0.3s ease;
        }

        .theme-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 25px rgba(14, 119, 194, 0.3);
        }

        /* Profile Header */
        .profile-header {
            background: var(--gradient-primary);
            color: white;
            border-radius: 20px;
            padding: 3rem 2rem;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 3rem;
            border: 4px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .profile-name {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .profile-email {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 1rem;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-top: 1.5rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            display: block;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Form Styling */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1rem;
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(14, 119, 194, 0.25);
            background: var(--bg-secondary);
        }

        .form-control:disabled {
            background: var(--bg-primary);
            opacity: 0.7;
        }

        /* Button Styling */
        .btn {
            border-radius: 12px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-outline-primary {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-success {
            background: var(--gradient-success);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }

        .btn-secondary {
            background: var(--text-secondary);
            color: white;
        }

        .btn-secondary:hover {
            background: var(--text-primary);
            transform: translateY(-2px);
        }

        /* Table Styling */
        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .table thead th {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: var(--bg-primary);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: var(--gradient-success);
            color: white;
        }

        .status-expired {
            background: var(--gradient-warning);
            color: white;
        }

        .status-pending {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        /* Alert Styling */
        .alert {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            margin-top: 1rem;
            font-weight: 500;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
                padding: 1rem;
            }

            .profile-header {
                padding: 2rem 1rem;
            }

            .profile-name {
                font-size: 2rem;
            }

            .profile-stats {
                flex-direction: column;
                gap: 1rem;
            }

            .glass-panel {
                padding: 1.5rem;
            }
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
    </style>
</head>

<body>
    <!-- Theme Toggle Button -->
    <button class="theme-toggle">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Sidebar -->
    <?php include('./partials/dashboardNavigation.php'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                <i class="fas fa-bars"></i>
            </button>
            <h3 class="mb-0">My Profile</h3>
        </div>

        <!-- Profile Header -->
        <div class="profile-header fade-in">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1 class="profile-name"><?php echo htmlspecialchars($userName); ?></h1>
            <p class="profile-email"><?php echo htmlspecialchars($userEmail); ?></p>
            <div class="profile-stats">
                <div class="stat-item">
                    <span class="stat-number" id="subscriptionCount">0</span>
                    <span class="stat-label">Subscriptions</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="activeCount">0</span>
                    <span class="stat-label">Active</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number" id="expiredCount">0</span>
                    <span class="stat-label">Expired</span>
                </div>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="glass-panel slide-in">
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-user-edit fa-2x text-primary me-3"></i>
                <h5 class="mb-0">Profile Information</h5>
            </div>
            <form id="profileForm">
                <input type="hidden" name="userId" value="<?php echo htmlspecialchars($userId); ?>">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user text-primary"></i>
                                Username
                            </label>
                            <input type="text" class="form-control" name="NoUsername" id="NoUsername" 
                                   value="<?php echo htmlspecialchars($userName); ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope text-primary"></i>
                                Email Address
                            </label>
                            <input type="email" class="form-control" name="NoEmail" id="NoEmail" 
                                   value="<?php echo htmlspecialchars($userEmail); ?>" disabled>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone text-primary"></i>
                                Phone Number
                            </label>
                            <input type="text" class="form-control" name="NoPhone" id="NoPhone" 
                                   value="<?php echo htmlspecialchars($userPhone); ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar text-primary"></i>
                                Member Since
                            </label>
                            <input type="text" class="form-control" value="<?php echo date('F Y'); ?>" disabled>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mt-4">
                    <button type="button" id="editBtn" class="btn btn-outline-primary">
                        <i class="fas fa-edit me-2"></i>Edit Profile
                    </button>
                    <button type="submit" id="saveBtn" class="btn btn-success d-none">
                        <i class="fas fa-save me-2"></i>Save Changes
                    </button>
                    <button type="button" id="cancelBtn" class="btn btn-secondary d-none">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                </div>
                <div id="profileMsg"></div>
            </form>
        </div>

        <!-- Subscriptions -->
        <div class="glass-panel slide-in">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <i class="fas fa-credit-card fa-2x text-primary me-3"></i>
                    <h5 class="mb-0">My Subscriptions</h5>
                </div>
                <button class="btn btn-outline-primary btn-sm" onclick="loadSubscriptions()">
                    <i class="fas fa-sync-alt me-2"></i>Refresh
                </button>
            </div>
            
            <div class="table-responsive">
                <table class="table" id="subscriptionsTable">
                    <thead>
                        <tr>
                            <th><i class="fas fa-tag me-2"></i>Subscription</th>
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
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            // Theme Toggle
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

            // Edit button
            $('#editBtn').on('click', function() {
                $('#NoUsername, #NoEmail, #NoPhone').prop('disabled', false);
                $('#saveBtn, #cancelBtn').removeClass('d-none');
                $('#editBtn').addClass('d-none');
                $('#profileMsg').html('');
            });

            // Cancel button
            $('#cancelBtn').on('click', function() {
                $('#NoUsername, #NoEmail, #NoPhone').prop('disabled', true);
                $('#saveBtn, #cancelBtn').addClass('d-none');
                $('#editBtn').removeClass('d-none');
                $('#profileMsg').html('');
            });

            // Save profile
            $('#profileForm').on('submit', function(e) {
                e.preventDefault();
                const saveBtn = $('#saveBtn');
                const originalText = saveBtn.html();
                
                saveBtn.html('<span class="loading"></span> Saving...');
                saveBtn.prop('disabled', true);
                
                $.post('php/update_profile.php', $(this).serialize(), function(resp) {
                    if (resp.success) {
                        $('#profileMsg').html('<div class="alert alert-success fade-in"><i class="fas fa-check-circle me-2"></i>Profile updated successfully!</div>');
                        $('#NoUsername, #NoEmail, #NoPhone').prop('disabled', true);
                        $('#saveBtn, #cancelBtn').addClass('d-none');
                        $('#editBtn').removeClass('d-none');
                    } else {
                        $('#profileMsg').html('<div class="alert alert-danger fade-in"><i class="fas fa-exclamation-circle me-2"></i>' + (resp.error || 'Update failed.') + '</div>');
                    }
                }, 'json').always(function() {
                    saveBtn.html(originalText);
                    saveBtn.prop('disabled', false);
                });
            });

            // Load subscriptions
            function loadSubscriptions() {
                const tbody = $('#subscriptionsTable tbody');
                tbody.html('<tr><td colspan="4" class="text-center py-4"><div class="loading"></div><span class="ms-2">Loading subscriptions...</span></td></tr>');
                
                $.ajax({
                    url: 'php/list_user_subscriptions.php',
                    method: 'GET',
                    data: { userId: <?php echo (int)$userId; ?> },
                    dataType: 'json',
                    success: function(data) {
                        let rows = '';
                        let activeCount = 0;
                        let expiredCount = 0;
                        
                        if (data.length === 0) {
                            $('#subsMsg').html('<i class="fas fa-info-circle me-2"></i>No subscriptions found.');
                            tbody.html('<tr><td colspan="4" class="text-center py-4 text-muted"><i class="fas fa-inbox fa-2x mb-3"></i><br>No subscriptions found</td></tr>');
                        } else {
                            $('#subsMsg').text('');
                            data.forEach(function(sub) {
                                
                                const status = sub.SubscriptionStatus.toLowerCase();
                                let statusClass = 'status-pending';
                                
                                if (status.includes('active') || status.includes('valid')) {
                                    statusClass = 'status-active';
                                    activeCount++;
                                } else if (status.includes('expired') || status.includes('inactive')) {
                                    statusClass = 'status-expired';
                                    expiredCount++;
                                }
                                
                                rows += `
                                    <tr class="fade-in">
                                        <td><strong>${sub.Item || 'N/A'}</strong></td>
                                        <td><span class="status-badge ${statusClass}">${sub.SubscriptionStatus || 'Unknown'}</span></td>
                                        <td>${sub.subscriptionDate || 'N/A'}</td>
                                        <td>${sub.expirationDate || 'N/A'}</td>
                                    </tr>`;
                            });
                            tbody.html(rows);
                        }
                        
                        // Update stats
                        $('#subscriptionCount').text(data.length);
                        $('#activeCount').text(activeCount);
                        $('#expiredCount').text(expiredCount);
                    },
                    error: function(xhr, status, error) {
                        tbody.html('<tr><td colspan="4" class="text-center py-4 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Failed to load subscriptions. Please try again.</td></tr>');
                        $('#subsMsg').html('<i class="fas fa-exclamation-triangle me-2"></i>Error loading subscriptions');
                    }
                });
            }
            
            // Initial load
            loadSubscriptions();
        });
    </script>
</body>
</html>
