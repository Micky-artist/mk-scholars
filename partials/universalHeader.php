<?php
// Universal Header Component
// This file provides consistent header styling and functionality across all user pages
?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="page-title"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
            <p class="page-subtitle"><?php echo $pageSubtitle ?? 'Welcome to your dashboard'; ?></p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Notification Button -->
            <div class="glass-panel px-3 py-2 notification-btn" style="cursor: pointer;">
                <i class="fas fa-bell text-muted"></i>
            </div>
            <!-- Mobile Sidebar Toggle -->
            <button class="btn btn-light d-md-none sidebar-toggle" type="button">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</div>

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

<!-- Theme Toggle Button -->
<button class="btn btn-secondary theme-toggle" onclick="toggleTheme()">
    <i class="fas fa-moon"></i>
</button>

<script>
// Universal JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Theme toggle functionality
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.body.setAttribute('data-theme', savedTheme);
    updateThemeIcon();

    // Mobile sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 768 && 
            !sidebar.contains(e.target) && 
            !sidebarToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    });

    // Notification functionality
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationBox = document.querySelector('.notification-box');
    
    if (notificationBtn && notificationBox) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationBox.style.display = notificationBox.style.display === 'block' ? 'none' : 'block';
        });

        // Close notifications when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationBtn.contains(e.target)) {
                notificationBox.style.display = 'none';
            }
        });
    }
});

function toggleTheme() {
    const currentTheme = document.body.getAttribute('data-theme');
    const newTheme = currentTheme === 'light' ? 'dark' : 'light';
    document.body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateThemeIcon();
}

function updateThemeIcon() {
    const currentTheme = document.body.getAttribute('data-theme');
    const icon = currentTheme === 'light' ? 'fas fa-moon' : 'fas fa-sun';
    const themeToggle = document.querySelector('.theme-toggle i');
    if (themeToggle) {
        themeToggle.className = icon;
    }
}
</script>
