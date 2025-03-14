<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Hub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ==== Base Styles ==== */
        html,*{
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
            padding: 1rem 2rem;
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
            .navbar-nav{
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

        /* ==== Scholarship Cards (Separate Section) ==== */
        .container {
            /* width: 100%; */
            max-width: 1200px;
            margin: 2rem auto;
            /* padding: 0 2rem; */
            display: flex;
            flex-direction: column;
            /* gap: 2rem; */
        }

        .scholarship-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            /* padding: 2rem; */
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.3);
            display: flex;
            flex-direction: column;
            /* gap: 1.5rem; */
            margin: 5px 0;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .scholarship-card:hover {
            transform: translateY(-5px);
        }

        .card-image {
            width: 100%;
            height: 250px;
            border-radius: 15px;
            object-fit: cover;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card-content {
            display: flex;
            flex-direction: column;
            /* gap: 1.2rem; */
        }

        .card-header {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .card-title {
            color: #1a1a1a;
            margin: 0;
            font-size: 1.8rem;
            font-weight: 700;
            line-height: 1.3;
        }

        .card-subtitle {
            color: #4a4a4a;
            margin: 0;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .price-tag {
            background: rgba(46, 204, 113, 0.15);
            color: #27ae60;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1.1rem;
            width: fit-content;
        }

        .card-description {
            color: #666;
            line-height: 1.6;
            font-size: 1rem;
            margin: 0.5rem 0;
        }

        .card-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: .5rem;
            /* margin: 1rem 0; */
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            color: #666;
            font-size: 0.95rem;
            padding: 0.8rem;
            background: rgba(0, 0, 0, 0.03);
            border-radius: 12px;
        }

        .action-section {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            margin-top: .5rem;
        }

        .apply-button {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 1.2rem 2rem;
            border-radius: 15px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            width: 100%;
            font-size: 1.1rem;
        }

        .apply-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.3);
        }

        .status-tag {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: rgba(76, 175, 80, 0.15);
            color: #4CAF50;
            padding: 0.6rem 1.5rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .deadline {
            color: #666;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            background: rgba(0, 0, 0, 0.03);
            border-radius: 12px;
        }

        /* ==== Responsive Scholarship Cards ==== */
        @media (min-width: 768px) {
            
            .scholarship-card {
                flex-direction: row;
                padding: 10px;
                gap: 2.5rem;
            }

            .card-image {
                width: 300px;
                height: auto;
                flex-shrink: 0;
            }

            .card-details {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 480px) {
            .navbar-nav{
                display: none;
            }
            .container {
                padding: 0 1rem;
            }

            .scholarship-card {
                padding: 1.5rem;
            }

            .card-title {
                font-size: 1.5rem;
            }

            .card-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- ==== Navigation Bar ==== -->
    <nav class="navbar">
        <a href="#" class="navbar-brand">
            <div class="logo-icon">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <span class="brand-text">Scholarship Hub</span>
        </a>

        <button class="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>

        <div class="navbar-nav">
            <a href="#" class="nav-link">Home</a>
            <a href="#" class="nav-link">Scholarships</a>
            <a href="#" class="nav-link">Resources</a>
            <button class="nav-button">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </button>
        </div>
    </nav>

    <!-- ==== Scholarship Cards ==== -->
    <div class="container">
        <div class="scholarship-card">
            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="STEM Scholarship" class="card-image">
            <div class="card-content">
                <div class="card-header">
                    <h2 class="card-title">STEM Innovation Scholarship</h2>
                    <p class="card-subtitle">For Future Technology Leaders</p>
                    <div class="price-tag">$15,000 Award</div>
                </div>
                
                <p class="card-description">
                    Comprehensive scholarship program for undergraduate students pursuing degrees in 
                    STEM fields. Includes mentorship opportunities, research grants, and 
                    internship placements with leading tech companies.
                </p>

                <div class="card-details">
                    <div class="detail-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Undergraduate</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <span>2 Year Program</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-users"></i>
                        <span>25 Seats Available</span>
                    </div>
                </div>

                <div class="action-section">
                    <div class="deadline">
                        <i class="fas fa-hourglass-half"></i>
                        Application Closes: March 15, 2024
                    </div>
                    <button class="apply-button">
                        <i class="fas fa-arrow-circle-right"></i>
                        Apply Now
                    </button>
                </div>
            </div>
            <div class="status-tag">Open</div>
        </div>

        <div class="scholarship-card">
            <img src="https://images.unsplash.com/photo-1536924940846-227afb31e2a5?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Arts Scholarship" class="card-image">
            <div class="card-content">
                <div class="card-header">
                    <h2 class="card-title">Creative Arts Fellowship</h2>
                    <p class="card-subtitle">Nurturing Artistic Talent</p>
                    <div class="price-tag" style="background: rgba(255, 193, 7, 0.15); color: #FFA000;">
                        $8,000 Grant
                    </div>
                </div>
                
                <p class="card-description">
                    Annual scholarship program for emerging artists, covering tuition fees, 
                    providing studio access, and offering exhibition opportunities. 
                    Includes professional portfolio development workshops.
                </p>

                <div class="card-details">
                    <div class="detail-item">
                        <i class="fas fa-certificate"></i>
                        <span>All Education Levels</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <span>1 Year Duration</span>
                    </div>
                    <div class="detail-item">
                        <i class="fas fa-users"></i>
                        <span>15 Seats Available</span>
                    </div>
                </div>

                <div class="action-section">
                    <div class="deadline">
                        <i class="fas fa-hourglass-half"></i>
                        Application Closes: April 1, 2024
                    </div>
                    <button class="apply-button">
                        <i class="fas fa-arrow-circle-right"></i>
                        Apply Now
                    </button>
                </div>
            </div>
            <div class="status-tag" style="background: rgba(255, 87, 34, 0.15); color: #FF5722;">
                Limited
            </div>
        </div>
    </div>

    <!-- ==== Navigation JavaScript ==== -->
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
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>