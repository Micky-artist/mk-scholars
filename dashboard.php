<?php
session_start();
include('./dbconnection/connection.php');
include('./php/validateSession.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK Dashboard</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.3);
            --neumorphic-shadow: 5px 5px 10px #d1d5db, -5px -5px 10px #ffffff;
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
            transition: background 0.3s, color 0.3s;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(15px);
            border-right: 1px solid var(--glass-border);
            position: fixed;
            height: 100vh;
            z-index: 1000;
            width: 250px;
        }

        .chat-bubble {
            max-width: 75%;
            padding: 15px 20px;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .received {
            background: rgb(181, 181, 181);
            border: 1px solid var(--glass-border);
        }

        .sent {
            background: rgba(59, 130, 246, 0.9);
            color: white;
            margin-left: auto;
        }

        .app-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            transition: all 0.3s;
        }

        .neumorphic-icon {
            width: 40px;
            height: 40px;
            background: var(--glass-bg);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--neumorphic-shadow);
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

        .notification-box {
            position: fixed;
            top: 70px;
            right: 20px;
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            display: none;
            z-index: 1050;
        }

        .progress-glass {
            background: rgba(255, 255, 255, 0.1);
            height: 8px;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .notification-box {
                width: 90%;
                right: 5%;
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body data-theme="light">
    <!-- Theme Toggle Button -->
    <button style="color: orange;" class="btn btn-secondary theme-toggle glass-panel">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Notification Box -->
    <div class="glass-panel notification-box p-3">
        <h5>Notifications</h5>
        <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center">
                    <i class="fas fa-bell text-warning me-2"></i>
                    <div>
                        <small>New message received</small>
                        <div class="text-muted">2 minutes ago</div>
                    </div>
                </div>
            </a>
            <a href="#" class="list-group-item list-group-item-action">
                <div class="d-flex align-items-center">
                    <i class="fas fa-tasks text-success me-2"></i>
                    <div>
                        <small>Task completed</small>
                        <div class="text-muted">1 hour ago</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 sidebar p-4">
                <div class="d-flex flex-column h-100">
                    <div class="text-center mb-5">
                        <div class="neumorphic-icon mx-auto mb-3">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <h5 class="mb-1">Alice Smith</h5>
                        <small class="text-muted">Pro Member</small>
                    </div>

                    <div class="glass-panel p-3 mb-4">
                        <ul class="nav flex-column">
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex align-items-center" href="#">
                                    <i class="fas fa-comment-alt me-3 text-primary"></i>
                                    <span>Chat</span>
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex align-items-center" href="#">
                                    <i class="fas fa-cube me-3 text-success"></i>
                                    <span>Applications</span>
                                </a>
                            </li>
                            <li class="nav-item mb-2">
                                <a class="nav-link d-flex align-items-center" href="#">
                                    <i class="fas fa-chart-pie me-3 text-warning"></i>
                                    <span>Analytics</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link d-flex align-items-center" href="#">
                                    <i class="fas fa-cog me-3 text-secondary"></i>
                                    <span>Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="glass-panel p-3 mt-auto">
                        <h6 class="mb-3">Storage</h6>
                        <div class="progress-glass">
                            <div class="progress-bar bg-primary" style="width: 65%"></div>
                        </div>
                        <small class="text-muted">65% of 100GB used</small>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h3 class="mb-0">Dashboard</h3>
                    <div class="glass-panel px-3 py-2 notification-btn" style="cursor: pointer;">
                        <i class="fas fa-bell text-muted"></i>
                    </div>
                </div>

                <div class="row g-4">



                    <div class="col-lg-8">
                        <div class="glass-panel p-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h5 class="mb-0">Chat With Us</h5>
                                <span class="badge bg-primary rounded-pill">3 New</span>
                            </div>
                            <?php
                            $UserId = $_SESSION[''];
                            $CheckConvo = mysqli_query($conn,"SELECT * FROM Conversation WHERE UserId = '$'");
                            ?>
                            <div>
                                <form action="" method="post">
                                    <input type="hidden" name="" value="">
                                    <button class="glass-panel p-3">Start New Conversation</button>
                                </form>
                            </div>

                            <div class="chat-container" style="height: 400px; overflow-y: auto;">
                                <div class="chat-bubble received mb-3">Hey team! Let's discuss the new project updates.</div>
                                <div class="chat-bubble sent mb-3">I've finished the UI components for review</div>
                            </div>
                            <div class="input-group mt-4">
                                <input type="text" class="form-control bg-transparent" placeholder="Type message...">
                                <button class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="glass-panel p-4 h-100">
                            <h5 class="mb-4">Recent Apps</h5>
                            <div class="app-card p-3 mb-3 rounded-3">
                                <div class="d-flex align-items-center">
                                    <div class="neumorphic-icon me-3">
                                        <i class="fas fa-photo-video text-info"></i>
                                    </div>
                                    <div class="w-100">
                                        <h6 class="mb-0">Media Manager</h6>
                                        <small class="text-muted">Updated 2h ago</small>
                                        <div class="progress-glass mt-2">
                                            <div class="progress-bar bg-success" style="width: 75%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="app-card p-3 mb-3 rounded-3">
                                <div class="d-flex align-items-center">
                                    <div class="neumorphic-icon me-3">
                                        <i class="fas fa-cloud-upload-alt text-danger"></i>
                                    </div>
                                    <div class="w-100">
                                        <h6 class="mb-0">Cloud Storage</h6>
                                        <small class="text-muted">Syncing files</small>
                                        <div class="progress-glass mt-2">
                                            <div class="progress-bar bg-info" style="width: 45%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4 g-4">
                    <div class="col-md-6">
                        <div class="glass-panel p-4">
                            <h5><i class="fas fa-chart-line me-2"></i>Performance</h5>
                            <canvas id="performanceChart" style="height: 200px;"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-panel p-4">
                            <h5><i class="fas fa-tasks me-2"></i>Active Projects</h5>
                            <div class="progress-glass mt-3">
                                <div class="progress-bar bg-warning" style="width: 30%"></div>
                            </div>
                            <small class="text-muted">Project Alpha - 30% complete</small>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Theme Toggle
        const themeToggle = document.querySelector('.theme-toggle');
        const body = document.body;
        const savedTheme = localStorage.getItem('theme') || 'light';
        body.setAttribute('data-theme', savedTheme);
        updateToggleIcon();

        themeToggle.addEventListener('click', () => {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateToggleIcon();
        });

        function updateToggleIcon() {
            const currentTheme = body.getAttribute('data-theme');
            themeToggle.innerHTML = currentTheme === 'light' ?
                '<i class="fas fa-moon"></i>' :
                '<i class="fas fa-sun"></i>';
        }

        // Notifications
        const notificationBtn = document.querySelector('.notification-btn');
        const notificationBox = document.querySelector('.notification-box');

        notificationBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationBox.style.display = notificationBox.style.display === 'block' ? 'none' : 'block';
        });

        // Close notifications when clicking outside
        document.addEventListener('click', (e) => {
            if (!notificationBtn.contains(e.target)) {
                notificationBox.style.display = 'none';
            }
        });

        // Mobile Sidebar Toggle
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const mainContent = document.querySelector('.main-content');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>

</html>