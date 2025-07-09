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

    <!-- Main Content -->
    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                <i class="fas fa-bars"></i>
            </button>
            <h3 class="mb-0">My Profile</h3>
        </div>

        <!-- Profile Details -->
        <div class="glass-panel">
            <h5 class="mb-3">Profile Information</h5>
            <form id="profileForm">
                <input type="hidden" name="userId" value="<?php echo htmlspecialchars($userId); ?>">
                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Username</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="NoUsername" id="NoUsername" value="<?php echo htmlspecialchars($userName); ?>" disabled>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-6">
                        <input type="email" class="form-control" name="NoEmail" id="NoEmail" value="<?php echo htmlspecialchars($userEmail); ?>" disabled>
                    </div>
                </div>
                <div class="mb-3 row">
                    <label class="col-sm-2 col-form-label">Phone</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="NoPhone" id="NoPhone" value="<?php echo htmlspecialchars($userPhone); ?>" disabled>
                    </div>
                </div>
                <div class="mb-3 row">
                    <div class="col-sm-8">
                        <button type="button" id="editBtn" class="btn btn-outline-primary">Edit</button>
                        <button type="submit" id="saveBtn" class="btn btn-success d-none">Save</button>
                        <button type="button" id="cancelBtn" class="btn btn-secondary d-none">Cancel</button>
                    </div>
                </div>
                <div id="profileMsg"></div>
            </form>
        </div>

        <!-- Subscriptions -->
        <div class="glass-panel">
            <h5 class="mb-3">My Subscriptions</h5>
            <table class="table table-bordered" id="subscriptionsTable">
                <thead>
                    <tr>
                        <th>Subscription</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <div id="subsMsg" class="text-muted"></div>
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
                $.post('php/update_profile.php', $(this).serialize(), function(resp) {
                    if (resp.success) {
                        $('#profileMsg').html('<div class="alert alert-success">Profile updated!</div>');
                        $('#NoUsername, #NoEmail, #NoPhone').prop('disabled', true);
                        $('#saveBtn, #cancelBtn').addClass('d-none');
                        $('#editBtn').removeClass('d-none');
                    } else {
                        $('#profileMsg').html('<div class="alert alert-danger">' + (resp.error || 'Update failed.') + '</div>');
                    }
                }, 'json');
            });

            // Load subscriptions
            function loadSubscriptions() {
                $.get('php/list_user_subscriptions.php', { userId: <?php echo (int)$userId; ?> }, function(data) {
                    let rows = '';
                    if (data.length === 0) {
                        $('#subsMsg').text('No subscriptions found.');
                    } else {
                        $('#subsMsg').text('');
                        data.forEach(function(sub) {
                            rows += `<tr><td>${sub.Item}</td><td>${sub.SubscriptionStatus}</td><td>${sub.subscriptionDate}</td><td>${sub.expirationDate}</td></tr>`;
                        });
                    }
                    $('#subscriptionsTable tbody').html(rows);
                }, 'json');
            }
            loadSubscriptions();
        });
    </script>
</body>
</html>
