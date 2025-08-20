<?php
include("./popup.php");
?>

<!-- Top Bar with Date and Social Icons -->
<div class="top-bar">
	<div class="top-bar-content">
		<div class="top-date">
			<span class="top-time"><?php echo date("l, F jS, Y") ?></span>
		</div>
		<div class="social-icons">
			<a href="https://www.youtube.com/@mkscholars" target="_blank" class="social-link youtube">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
					<path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z"/>
				</svg>
			</a>
			<a href="https://chat.whatsapp.com/Ij6O5iYUVOt8pUXXJ1cCpD" target="_blank" class="social-link whatsapp">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
					<path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
				</svg>
			</a>
			<a href="https://www.facebook.com/profile.php?id=100069262368212&sk=following" target="_blank" class="social-link facebook">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
					<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
				</svg>
			</a>
			<a href="https://x.com/MkScholars" target="_blank" class="social-link twitter">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
					<path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
				</svg>
			</a>
		</div>
	</div>
</div>

<!-- Main Navigation -->
<header class="main-navigation">
	<nav class="nav-container">
		<!-- Logo on the left -->
		<div class="nav-logo">
			<a href="./" class="logo-link">
				<img src="./images/logo/logoRound.png" alt="MK Scholars" class="logo-image">
			</a>
		</div>

		<!-- Navigation menu in the center (desktop) -->
		<div class="nav-menu" id="nav-menu">
			<ul class="nav-list">
				<li class="nav-item">
					<a href="./" class="nav-link">Home</a>
				</li>
				<li class="nav-item">
					<a href="./applications" class="nav-link">All Applications</a>
				</li>
				<li class="nav-item">
					<a href="./courses" class="nav-link">Courses</a>
				</li>
				<li class="nav-item">
					<a href="./writing-services" class="nav-link">Writting Services</a>
				</li>
				<li class="nav-item">
					<a href="./driving-school" class="nav-link">Driving School</a>
				</li>
				<li class="nav-item dropdown">
					<a href="#" class="nav-link dropdown-toggle">More</a>
					<ul class="dropdown-menu">
						<li><a href="./about-us#contact" class="dropdown-link">Contact Us</a></li>
						<li><a href="./about-us" class="dropdown-link">About Us</a></li>
						<li><a href="./faq" class="dropdown-link">FAQ</a></li>
						<?php if (isset($_SESSION['username']) && isset($_SESSION['userId'])): ?>
							<li><a href="./php/logout.php" class="dropdown-link">Log Out: <?php echo $_SESSION['username']; ?></a></li>
						<?php else: ?>
							<li><a href="./login" class="dropdown-link">Login</a></li>
							<li><a href="./sign-up" class="dropdown-link">Sign Up</a></li>
						<?php endif; ?>
					</ul>
				</li>
			</ul>
		</div>

		<!-- Right side: Login/Dashboard button and hamburger menu -->
		<div class="nav-right">
			<!-- Login/Dashboard button -->
			<div class="nav-auth">
				<?php if (isset($_SESSION['username']) && isset($_SESSION['userId'])): ?>
					<a href="./dashboard" class="auth-button dashboard-btn">Dashboard</a>
				<?php else: ?>
					<a href="./login" class="auth-button login-btn">Login</a>
				<?php endif; ?>
			</div>

			<!-- Hamburger menu button on the right -->
			<button class="hamburger-menu" id="hamburger-menu" aria-label="Toggle navigation menu">
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
			</button>
		</div>
	</nav>
</header>

<!-- Mobile Navigation Overlay -->
<div class="mobile-nav-overlay" id="mobile-nav-overlay">
	<div class="mobile-nav-content">
		<div class="mobile-nav-header">
			<div class="mobile-logo">
				<img src="./images/logo/logoRound.png" alt="MK Scholars" class="mobile-logo-image">
			</div>
			<button class="mobile-close" id="mobile-close" aria-label="Close navigation menu">
				<span class="close-line"></span>
				<span class="close-line"></span>
			</button>
		</div>
		<nav class="mobile-nav-menu">
			<ul class="mobile-nav-list">
				<li class="mobile-nav-item">
					<a href="./" class="mobile-nav-link">Home</a>
				</li>
				<li class="mobile-nav-item">
					<a href="./applications" class="mobile-nav-link">All Applications</a>
				</li>
				<li class="mobile-nav-item">
					<a href="./courses" class="mobile-nav-link">Courses</a>
				</li>
				<li class="mobile-nav-item">
					<a href="./writing-services" class="mobile-nav-link">Writting Services</a>
				</li>
				<li class="mobile-nav-item">
					<a href="./driving-school" class="mobile-nav-link">Driving School</a>
				</li>
				<li class="mobile-nav-item">
					<a href="./about-us" class="mobile-nav-link">About Us</a>
				</li>
				<li class="mobile-nav-item">
					<a href="./faq" class="mobile-nav-link">FAQ</a>
				</li>
				<li class="mobile-nav-item">
					<a href="./about-us#contact" class="mobile-nav-link">Contact Us</a>
				</li>
				<?php if (isset($_SESSION['username']) && isset($_SESSION['userId'])): ?>
					<li class="mobile-nav-item">
						<a href="./dashboard" class="mobile-nav-link">Dashboard</a>
					</li>
					<li class="mobile-nav-item">
						<a href="./php/logout.php" class="mobile-nav-link">Log Out: <?php echo $_SESSION['username']; ?></a>
					</li>
				<?php else: ?>
					<li class="mobile-nav-item">
						<a href="./login" class="mobile-nav-link">Login</a>
					</li>
					<li class="mobile-nav-item">
						<a href="./sign-up" class="mobile-nav-link">Sign Up</a>
					</li>
				<?php endif; ?>
			</ul>
		</nav>
	</div>
</div>

<style>
/* Top Bar Styles */
.top-bar {
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	color: white;
	padding: 8px 0;
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	z-index: 1000;
}

.top-bar-content {
	max-width: 1200px;
	margin: 0 auto;
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 0 20px;
}

.top-time {
	font-size: 14px;
	font-weight: 500;
}

.social-icons {
	display: flex;
	gap: 15px;
}

.social-link {
	color: white;
	transition: transform 0.3s ease, opacity 0.3s ease;
	display: flex;
	align-items: center;
	justify-content: center;
	width: 32px;
	height: 32px;
	border-radius: 50%;
	background: rgba(255, 255, 255, 0.1);
}

.social-link:hover {
	transform: translateY(-2px);
	opacity: 0.8;
}

.social-link.youtube:hover { background: rgba(255, 0, 0, 0.2); }
.social-link.whatsapp:hover { background: rgba(37, 211, 102, 0.2); }
.social-link.facebook:hover { background: rgba(66, 103, 178, 0.2); }
.social-link.twitter:hover { background: rgba(29, 161, 242, 0.2); }

/* Main Navigation Styles */
.main-navigation {
	background: white;
	box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
	position: fixed;
	top: 48px; /* Below top bar */
	left: 0;
	right: 0;
	z-index: 999;
}

.nav-container {
	max-width: 1200px;
	margin: 0 auto;
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 15px 20px;
}

/* Logo Styles */
.nav-logo {
	flex-shrink: 0;
}

.logo-link {
	display: block;
	text-decoration: none;
}

.logo-image {
	width: 60px;
	height: 60px;
	transition: transform 0.3s ease;
}

.logo-image:hover {
	transform: scale(1.05);
}

/* Navigation Menu Styles */
.nav-menu {
	display: flex;
	align-items: center;
}

.nav-list {
	display: flex;
	list-style: none;
	margin: 0;
	padding: 0;
	gap: 30px;
}

.nav-item {
	position: relative;
}

.nav-link {
	color: #333;
	text-decoration: none;
	font-weight: 500;
		font-size: 16px;
	padding: 10px 0;
	transition: color 0.3s ease;
	position: relative;
}

.nav-link:hover {
	color: #667eea;
}

.nav-link::after {
	content: '';
	position: absolute;
	bottom: 0;
	left: 0;
	width: 0;
	height: 2px;
	background: #667eea;
	transition: width 0.3s ease;
}

.nav-link:hover::after {
	width: 100%;
}

/* Dropdown Styles */
.dropdown {
	position: relative;
}

.dropdown-menu {
	position: absolute;
	top: 100%;
	left: 0;
	background: white;
	box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
	border-radius: 8px;
	padding: 15px 0;
	min-width: 200px;
	opacity: 0;
	visibility: hidden;
	transform: translateY(-10px);
	transition: all 0.3s ease;
	list-style: none;
	margin: 0;
}

.dropdown:hover .dropdown-menu {
	opacity: 1;
	visibility: visible;
	transform: translateY(0);
}

.dropdown-link {
	display: block;
	padding: 10px 20px;
	color: #333;
	text-decoration: none;
	transition: background 0.3s ease;
}

.dropdown-link:hover {
	background: #f8f9fa;
	color: #667eea;
}

/* Right Side Styles */
.nav-right {
	display: flex;
	align-items: center;
	gap: 20px;
}

.nav-auth {
	display: flex;
	align-items: center;
}

.auth-button {
	padding: 10px 20px;
	border-radius: 25px;
	text-decoration: none;
	font-weight: 500;
	transition: all 0.3s ease;
	border: 2px solid transparent;
}

.login-btn {
	background: #667eea;
	color: white;
}

.login-btn:hover {
	background: #5a6fd8;
	transform: translateY(-2px);
}

.dashboard-btn {
	background: #28a745;
	color: white;
}

.dashboard-btn:hover {
	background: #218838;
	transform: translateY(-2px);
}

/* Hamburger Menu Styles */
.hamburger-menu {
	display: none;
	flex-direction: column;
	background: none;
	border: none;
	cursor: pointer;
	padding: 5px;
	width: 30px;
	height: 30px;
	justify-content: space-around;
	align-items: center;
}

.hamburger-line {
		width: 100%;
	height: 3px;
	background: #333;
	border-radius: 2px;
	transition: all 0.3s ease;
}

.hamburger-menu:hover .hamburger-line {
	background: #667eea;
}

/* Mobile Navigation Overlay */
.mobile-nav-overlay {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0, 0, 0, 0.8);
	z-index: 2000;
	opacity: 0;
	visibility: hidden;
	transition: all 0.3s ease;
}

.mobile-nav-overlay.active {
	opacity: 1;
	visibility: visible;
}

.mobile-nav-content {
	position: absolute;
	top: 0;
	right: 0;
	width: 300px;
	height: 100%;
	background: white;
	transform: translateX(100%);
	transition: transform 0.3s ease;
	overflow-y: auto;
}

.mobile-nav-overlay.active .mobile-nav-content {
	transform: translateX(0);
}

.mobile-nav-header {
		display: flex;
		justify-content: space-between;
		align-items: center;
	padding: 20px;
	border-bottom: 1px solid #eee;
}

.mobile-logo-image {
	width: 50px;
	height: 50px;
}

.mobile-close {
	background: none;
	border: none;
	cursor: pointer;
	width: 30px;
	height: 30px;
	position: relative;
}

.close-line {
	position: absolute;
	width: 100%;
	height: 3px;
	background: #333;
	border-radius: 2px;
	top: 50%;
	left: 0;
}

.close-line:first-child {
	transform: rotate(45deg);
}

.close-line:last-child {
	transform: rotate(-45deg);
}

.mobile-nav-menu {
	padding: 20px 0;
}

.mobile-nav-list {
	list-style: none;
	margin: 0;
	padding: 0;
}

.mobile-nav-item {
	border-bottom: 1px solid #f0f0f0;
}

.mobile-nav-link {
	display: block;
	padding: 15px 20px;
	color: #333;
	text-decoration: none;
	font-weight: 500;
	transition: background 0.3s ease;
}

.mobile-nav-link:hover {
	background: #f8f9fa;
	color: #667eea;
}

/* Responsive Design */
@media (max-width: 768px) {
	.top-bar-content {
		padding: 0 15px;
	}
	
	.top-time {
		font-size: 12px;
	}
	
	.social-icons {
		gap: 10px;
	}
	
	.social-link {
		width: 28px;
		height: 28px;
	}
	
	.nav-container {
		padding: 10px 15px;
	}
	
	.logo-image {
		width: 50px;
		height: 50px;
	}
	
	.nav-menu {
		display: none;
	}
	
	.hamburger-menu {
		display: flex;
	}
	
	.nav-auth {
		display: none;
	}
}

@media (max-width: 480px) {
	.top-bar {
		padding: 6px 0;
	}
	
	.main-navigation {
		top: 40px;
	}
	
	.nav-container {
		padding: 8px 15px;
	}
	
	.logo-image {
		width: 45px;
		height: 45px;
	}
	
	.mobile-nav-content {
		width: 100%;
	}
							}
						</style>

<script>
// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
	const hamburgerMenu = document.getElementById('hamburger-menu');
	const mobileNavOverlay = document.getElementById('mobile-nav-overlay');
	const mobileClose = document.getElementById('mobile-close');
	
	// Open mobile menu
	hamburgerMenu.addEventListener('click', function() {
		mobileNavOverlay.classList.add('active');
		document.body.style.overflow = 'hidden';
	});
	
	// Close mobile menu
	mobileClose.addEventListener('click', function() {
		mobileNavOverlay.classList.remove('active');
		document.body.style.overflow = '';
	});
	
	// Close mobile menu when clicking outside
	mobileNavOverlay.addEventListener('click', function(e) {
		if (e.target === mobileNavOverlay) {
			mobileNavOverlay.classList.remove('active');
			document.body.style.overflow = '';
		}
	});
	
	// Close mobile menu when pressing Escape key
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape' && mobileNavOverlay.classList.contains('active')) {
			mobileNavOverlay.classList.remove('active');
			document.body.style.overflow = '';
		}
	});
});
</script>