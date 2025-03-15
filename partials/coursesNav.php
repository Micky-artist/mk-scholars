<nav class="navbar">
    <a href="./home" class="navbar-brand">
        <img src="images/logo/logoRound.png" width="90" height="90" alt="Logo">
        <span class="brand-text">MK Scholars</span>
    </a>

    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="navbar-nav">
        <a href="./home" class="nav-link">Home</a>
        <a href="./applications" class="nav-link">Applications</a>
        <a href="./courses" class="nav-link">Courses</a>
        <?php
        if ($pageName == "SignIn") {
        ?>
            <a href="./sign-up" style="text-decoration: none;" class="nav-button">
                <i class="fas fa-sign-in-alt"></i>
                Sign Up
            </a>
        <?php
        } else {
        ?>
            <a href="./login" style="text-decoration: none;" class="nav-button">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </a>

        <?php
        }
        ?>

    </div>
</nav>

<style>
    /* ==== Base Styles ==== */
    html,
    * {
        padding: 0;
        margin: 0;
    }

    :root {
        --primary: #2196F3;
        --secondary: #1976D2;
        --accent: #FF5722;
    }

    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background: linear-gradient(135deg, #f0f4ff 0%, #f8f9ff 100%);
        width: 100%;
    }

    /* ==== Reusable Navigation Bar ==== */
    .navbar {
        /* width: 80%; */
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.4);
        padding: .2rem 2rem;
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

    .logo-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
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

    /* ==== Responsive Navigation ==== */
    @media (max-width: 768px) {
        .navbar {
            padding: 1rem;
        }

        .navbar-nav {
            display: none;
        }

        .navbar-nav {
            position: fixed;
            top: 70px;
            right: -100%;
            width: 70%;
            height: calc(100vh - 70px);
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(25px);
            flex-direction: column;
            gap: 2rem;
            padding: 2rem;
            transition: right 0.3s ease;
        }

        .navbar-nav.active {
            display: flex;
            right: 0;
        }

        .nav-link {
            font-size: 1.1rem;
        }

        .menu-toggle {
            display: block;
        }

        .brand-text {
            display: none;
        }
    }

    /* ==== Responsive Scholarship Cards ==== */

    @media (max-width: 480px) {
        .navbar-nav {
            display: none;
        }
    }
</style>

<script>
    const menuToggle = document.querySelector('.menu-toggle');
    const navbarNav = document.querySelector('.navbar-nav');

    menuToggle.addEventListener('click', () => {

        navbarNav.classList.toggle('active');
    });

    document.addEventListener('click', (e) => {
        if (!navbarNav.contains(e.target) && !menuToggle.contains(e.target)) {
            navbarNav.classList.remove('active');
        }
    });

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>