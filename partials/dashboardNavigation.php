<nav class="col-md-3 col-lg-2 sidebar p-4">
                <div class="d-flex flex-column h-100">
                    <div class="text-center mb-5">
                        <div class="neumorphic-icon mx-auto mb-3">
                            <i class="fas fa-user text-primary"></i>
                        </div>
                        <h5 class="mb-1"><?php echo $_SESSION['username']; ?></h5>
                        <small class="text-muted"><?php echo $_SESSION['NoEmail']; ?></small>
                    </div>

                    <div class="glass-panel p-2 mb-1">
                        <a class="nav-link d-flex align-items-center" href="./dashboard">
                            <span>Dashboard</span>
                        </a>
                    </div>
                    
                    <div class="glass-panel p-2 mb-1">
                        <a class="nav-link d-flex align-items-center" href="./conversations">
                            <span>conversations</span>
                        </a>
                    </div>
                    
                    <div class="glass-panel p-2 mb-1">
                        <a class="nav-link d-flex align-items-center" href="./apply">
                            <span>Apply</span>
                        </a>
                    </div>
                    <!-- <div class="glass-panel p-2 mb-1">
                        <a class="nav-link d-flex align-items-center" href="./applications">
                            <i class="fas fa-comment-alt me-3 text-primary"></i>
                            <span>Applications</span>
                        </a>
                    </div> -->
                    <div class="glass-panel p-2 mb-1">
                        <a class="nav-link d-flex align-items-center" target="_blank" href="./home">
                            <span>Back home</span>
                        </a>
                    </div>
                    <a class="p-3 mt-auto" href="">
                    <button class=" glass-panel p-3 mt-auto" style="color: red; font-weight: bold;">Logout</button>
                    </a>

                </div>
            </nav>

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