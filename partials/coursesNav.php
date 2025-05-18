<!-- Navigation Bar -->
<nav class="navbar">
  <a href="./home" class="navbar-brand">
    <img src="images/logo/logoRound.png" width="90" height="90" alt="Logo">
    <span class="brand-text">MK Scholars</span>
  </a>

  <button class="menu-toggle" id="menu-toggle">
    <i class="fas fa-bars"></i>
  </button>

  <div class="navbar-nav" id="navbar-nav">
    <a href="./home" class="nav-link">Home</a>
    <a href="./applications" class="nav-link">Applications</a>
    <a href="./courses" class="nav-link">Courses</a>
    <a href="./dashboard" class="nav-link">Dashboard</a>

    <?php if (isset($_SESSION['username'])): ?>
      <a style="text-decoration: none;" class="nav-button">
        <?php echo $_SESSION['username']; ?>
      </a>
    <?php else: ?>
      <?php if (isset($pageName) && $pageName == "SignIn"): ?>
        <a href="./sign-up" class="nav-button">
          <i class="fas fa-sign-in-alt"></i> Sign Up
        </a>
      <?php else: ?>
        <a href="./login" class="nav-button">
          <i class="fas fa-sign-in-alt"></i> Login
        </a>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</nav>

<style>
  :root {
    --primary: #2196F3;
    --secondary: #1976D2;
    --accent: #FF5722;
  }

  body {
    margin: 0;
    padding: 0;
    font-family: 'Inter', system-ui, sans-serif;
    background: linear-gradient(135deg, #f0f4ff 0%, #f8f9ff 100%);
  }

  .navbar {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(15px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.4);
    padding: .5rem 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .navbar-brand {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    text-decoration: none;
  }

  .brand-text {
    font-size: 1.4rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }

  .navbar-nav {
    display: flex;
    gap: 2rem;
    align-items: center;
    transition: all 0.3s ease;
  }

  .nav-link {
    color: #4a4a4a;
    text-decoration: none;
    font-weight: 500;
    position: relative;
    padding: 0.5rem 0;
  }

  .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--primary);
    transition: width 0.3s ease;
  }

  .nav-link:hover::after {
    width: 100%;
  }

  .nav-button {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    border: none;
    padding: 0.8rem 1.8rem;
    border-radius: 30px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  .nav-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
  }

  .menu-toggle {
    display: none;
    background: none;
    border: none;
    color: var(--primary);
    font-size: 1.5rem;
    cursor: pointer;
  }
  @media (min-width: 769px) {
  .navbar-nav {
    position: static !important;
    flex-direction: row !important;
    height: auto !important;
    width: auto !important;
    background: transparent !important;
    box-shadow: none !important;
    padding: 0 !important;
    right: 0 !important;
  }
}


  /* Mobile Menu */
  @media (max-width: 768px) {
    .menu-toggle {
      display: block;
    }

    .brand-text {
      display: none;
    }

    .navbar-nav {
    position: fixed;
    top: 80px;
    right: -100%;
    flex-direction: column;
    width: 70%;
    height: 100vh;
    background: white;
    padding: 2rem;
    box-shadow: -2px 0 10px rgba(0,0,0,0.1);
    transition: right 0.3s ease;
  }

  .navbar-nav.active {
    right: 0;
  }
  }
</style>

<script>
  const menuToggle = document.getElementById('menu-toggle');
  const navbarNav = document.getElementById('navbar-nav');

  menuToggle.addEventListener('click', () => {
    navbarNav.classList.toggle('active');
  });

  document.addEventListener('click', (e) => {
    if (!navbarNav.contains(e.target) && !menuToggle.contains(e.target)) {
      navbarNav.classList.remove('active');
    }
  });
</script>
