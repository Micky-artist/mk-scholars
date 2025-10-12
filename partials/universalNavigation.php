<?php
// Universal Navigation Component
// This file provides consistent navigation across all user pages

// Get current page from URL or parameter
$currentPage = $_GET['page'] ?? basename($_SERVER['PHP_SELF'], '.php');

// Function to check if a nav item is active
function isActive($page, $currentPage) {
    return $page === $currentPage ? 'active' : '';
}
?>

<nav class="col-md-3 col-lg-2 sidebar p-4">
    <div class="d-flex flex-column h-100">
        <div class="text-center mb-5">
            <div class="neumorphic-icon mx-auto mb-3">
                <i class="fas fa-user text-primary"></i>
            </div>
            <h5 class="mb-1"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></h5>
            <small class="text-muted"><?php echo htmlspecialchars($_SESSION['NoEmail'] ?? $_SESSION['userEmail'] ?? ''); ?></small>
            <a href="./profile.php" class="btn btn-sm btn-outline-primary">View Profile</a>
        </div>

        <div class="glass-panel p-2 mb-1 <?php echo isActive('dashboard', $currentPage); ?>">
            <a class="nav-link d-flex align-items-center" href="./dashboard.php">
                <i class="fas fa-tachometer-alt me-3 text-primary"></i>
                <span>Dashboard</span>
            </a>
        </div>
        <div class="glass-panel p-2 mb-1 <?php echo isActive('e-learning', $currentPage); ?>">
            <a class="nav-link d-flex align-items-center" href="./e-learning.php">
                <i class="fas fa-graduation-cap me-3 text-primary"></i>
                <span>E-Learning</span>
            </a>
        </div>
        <div class="glass-panel p-2 mb-1 <?php echo isActive('conversations', $currentPage); ?>">
            <a class="nav-link d-flex align-items-center" href="./conversations.php">
                <i class="fas fa-comments me-3 text-primary"></i>
                <span>Chat with us</span>
            </a>
        </div>
        <div class="glass-panel p-2 mb-1 <?php echo isActive('apply', $currentPage); ?>">
            <a class="nav-link d-flex align-items-center" href="./apply.php">
                <i class="fas fa-hand-holding-heart me-3 text-primary"></i>
                <span>Ask For Assistance</span>
            </a>
        </div>
        <div class="glass-panel p-2 mb-1 <?php echo isActive('profile', $currentPage); ?>">
            <a class="nav-link d-flex align-items-center" href="./profile.php">
                <i class="fas fa-user-circle me-3 text-primary"></i>
                <span>Profile</span>
            </a>
        </div>
        <div class="glass-panel p-2 mb-1">
            <a class="nav-link d-flex align-items-center" target="_blank" href="./home.php">
                <i class="fas fa-home me-3 text-primary"></i>
                <span>Back Home</span>
            </a>
        </div>
        <div class="glass-panel p-2 mb-1">
            <a class="p-3" href="./php/logout.php">
                <button style="background: none; border:none; color: red; font-weight: bold;" class="p-3 mt-auto">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </button>
            </a>
        </div>
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
        transition: background 0.3s, color 0.3s;
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 15px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .glass-panel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
    }

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

    .main-content {
        margin-left: 250px;
        transition: margin-left 0.3s;
        min-height: 100vh;
    }

    .neumorphic-icon {
        width: 50px;
        height: 50px;
        background: var(--glass-bg);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--neumorphic-shadow);
    }

    .nav-link {
        color: var(--text-primary);
        text-decoration: none;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .nav-link:hover {
        background: rgba(59, 130, 246, 0.1);
        color: var(--primary-color);
        transform: translateX(5px);
    }

    .nav-link i {
        width: 20px;
        text-align: center;
    }

    /* Active navigation item styles */
    .glass-panel.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(29, 78, 216, 0.1));
        border: 1px solid rgba(59, 130, 246, 0.3);
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.2);
    }

    .glass-panel.active .nav-link {
        color: var(--primary-color);
        font-weight: 600;
    }

    .glass-panel.active .nav-link i {
        color: var(--primary-color);
    }

    .glass-panel.active:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 25px rgba(59, 130, 246, 0.25);
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
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-primary);
        box-shadow: var(--neumorphic-shadow);
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

    /* Universal page header styles */
    .page-header {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-primary);
        margin: 0;
        background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin: 0.5rem 0 0 0;
        font-size: 1rem;
    }

    /* Universal content container */
    .content-container {
        padding: 2rem;
    }

    /* Universal glass panel styles */
    .glass-panel {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .glass-panel:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 40px rgba(0, 0, 0, 0.15);
    }

    /* Universal button styles */
    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
    }

    .btn-outline-primary {
        border: 1px solid var(--primary-color);
        color: var(--primary-color);
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-1px);
    }

    /* Universal form styles */
    .form-control {
        border: 1px solid var(--glass-border);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        background: var(--glass-bg);
        color: var(--text-primary);
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        background: var(--bg-secondary);
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    /* Universal table styles */
    .table {
        background: var(--bg-secondary);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }

    .table thead th {
        background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
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

    .table tbody tr:hover {
        background: rgba(59, 130, 246, 0.05);
    }

    /* Universal badge styles */
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.8rem;
    }

    .status-active {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-expired {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .status-pending {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    /* Universal loading animation */
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

    /* Universal slide-in animation */
    .slide-in {
        animation: slideIn 0.6s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Universal fade-in animation */
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Mobile sidebar toggle */
    .sidebar-toggle {
        display: none;
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        color: var(--text-primary);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .sidebar-toggle {
            display: inline-block;
        }
    }
</style>
