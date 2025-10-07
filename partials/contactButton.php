<?php
// Floating Contact Us Button - Include this in all normal pages
?>
<style>
/* Floating Contact Button */
.floating-contact-btn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #00b09b, #96c93d);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 20px rgba(0, 176, 155, 0.4);
    cursor: pointer;
    z-index: 1000;
    transition: all 0.3s ease;
    text-decoration: none;
    color: white;
    font-size: 24px;
}

.floating-contact-btn:hover {
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 6px 25px rgba(0, 176, 155, 0.6);
    color: white;
    text-decoration: none;
}

.floating-contact-btn:active {
    transform: translateY(-1px) scale(1.05);
}

/* Pulse animation for attention */
@keyframes pulse {
    0% {
        box-shadow: 0 4px 20px rgba(0, 176, 155, 0.4);
    }
    50% {
        box-shadow: 0 4px 20px rgba(0, 176, 155, 0.8), 0 0 0 10px rgba(0, 176, 155, 0.1);
    }
    100% {
        box-shadow: 0 4px 20px rgba(0, 176, 155, 0.4);
    }
}

.floating-contact-btn.pulse {
    animation: pulse 2s infinite;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .floating-contact-btn {
        bottom: 15px;
        right: 15px;
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
}

/* Hide on admin pages */
.admin-page .floating-contact-btn {
    display: none;
}
</style>

<a href="./conversations.php" class="floating-contact-btn" title="Contact Us - Get Help">
    <i class="fas fa-comments"></i>
</a>

<script>
// Add pulse animation on page load for first-time visitors
document.addEventListener('DOMContentLoaded', function() {
    const contactBtn = document.querySelector('.floating-contact-btn');
    if (contactBtn) {
        // Check if user has visited before (using localStorage)
        const hasVisited = localStorage.getItem('mk_scholars_visited');
        if (!hasVisited) {
            contactBtn.classList.add('pulse');
            // Remove pulse after 10 seconds
            setTimeout(() => {
                contactBtn.classList.remove('pulse');
            }, 10000);
            // Mark as visited
            localStorage.setItem('mk_scholars_visited', 'true');
        }
    }
});
</script>
