<?php
// Error handling for database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try to include database connection safely
try {
    include_once("./dbconnection/connection.php");
} catch (Exception $e) {
    error_log("Database connection error in driving-school.php: " . $e->getMessage());
}

// Start session
(session_status() == PHP_SESSION_NONE) ? session_start() : '';

// Language detection and switching
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'en';
if (!in_array($lang, ['en', 'kin'])) {
    $lang = 'en';
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK Driving School - Professional Driving Training | MK Scholars</title>
    <meta name="description" content="MK Driving School - Your trusted partner in passing the driving theory exam (Provisoire) the first time. Professional driving training in Rwanda.">
    <meta name="keywords" content="driving school, driving test, theory exam, provisional license, Rwanda, Kigali, driving lessons">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    
    <!-- Include head partial -->
    <?php include_once("./partials/head.php"); ?>
    
    <style>
        /* MK Driving School - Professional Design */
        :root {
            --primary-blue: #0E77C2;
            --dark-blue: #083352;
            --bright-orange: #FF6B35;
            --gold: #FFD700;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --dark-gray: #2d3748;
            --success-green: #10B981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark-gray);
            background-color: var(--white);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Hero Section - Driving Theme */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 50%, #1e40af 100%);
            color: var(--white);
            text-align: center;
            padding: 140px 0 100px;
            margin-top: 108px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="road" x="0" y="0" width="20" height="100" patternUnits="userSpaceOnUse"><rect width="20" height="100" fill="transparent"/><rect x="8" y="0" width="4" height="100" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23road)"/></svg>');
            opacity: 0.1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .driving-logo {
            width: 120px;
            height: 120px;
            margin: 0 auto 30px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .driving-logo img {
            width: 80px;
            height: 80px;
        }

        .main-title {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .subtitle {
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--gold);
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero-description {
            font-size: 1.3rem;
            margin-bottom: 40px;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.95;
        }

        /* Coming Soon Banner */
        .coming-soon-banner {
            background: linear-gradient(135deg, var(--bright-orange) 0%, #ff8c42 100%);
            border: 3px solid var(--gold);
            border-radius: 20px;
            padding: 30px;
            margin: 40px auto;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(255, 107, 53, 0.3);
        }

        .coming-soon-text {
            font-size: 1.4rem;
            color: var(--white);
            margin-bottom: 15px;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .launch-promise {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--white);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        /* Language Switcher */
        .language-switcher {
            margin: 30px 0;
        }

        .lang-buttons {
            display: inline-flex;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            padding: 5px;
            backdrop-filter: blur(10px);
        }

        .lang-btn {
            padding: 12px 25px;
            border: none;
            border-radius: 20px;
            background: transparent;
            color: var(--white);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .lang-btn.active,
        .lang-btn:hover {
            background: var(--white);
            color: var(--primary-blue);
            transform: translateY(-2px);
        }

        /* Features Section */
        .features-section {
            background: var(--light-gray);
            padding: 80px 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 20px;
        }

        .section-subtitle {
            font-size: 1.3rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 4px solid var(--primary-blue);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 4rem;
            margin-bottom: 25px;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 20px;
        }

        .feature-description {
            color: #666;
            line-height: 1.7;
            font-size: 1.1rem;
        }

        /* Process Section */
        .process-section {
            background: var(--white);
            padding: 80px 0;
        }

        .process-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .process-step {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            background: var(--light-gray);
            transition: transform 0.3s ease;
        }

        .process-step:hover {
            transform: translateY(-5px);
        }

        .step-number {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--dark-blue) 100%);
            color: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 auto 20px;
        }

        .step-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 15px;
        }

        .step-description {
            color: #666;
            line-height: 1.6;
        }

        /* Contact Section */
        .contact-section {
            background: var(--dark-blue);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .contact-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 50px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-item {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: transform 0.3s ease;
        }

        .contact-item:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.15);
        }

        .contact-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: var(--gold);
        }

        .contact-label {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .contact-value {
            font-size: 1.2rem;
            color: var(--white);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-value:hover {
            color: var(--gold);
        }

        /* Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-section {
                padding: 120px 0 80px;
                margin-top: 88px;
            }

            .main-title {
                font-size: 2.8rem;
            }

            .subtitle {
                font-size: 2rem;
            }

            .hero-description {
                font-size: 1.1rem;
            }

            .section-title {
                font-size: 2.2rem;
            }

            .contact-title {
                font-size: 2rem;
            }

            .features-grid,
            .process-grid,
            .contact-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .lang-buttons {
                flex-direction: column;
                gap: 10px;
            }

            .lang-btn {
                padding: 10px 20px;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 100px 0 60px;
                margin-top: 68px;
            }

            .main-title {
                font-size: 2.2rem;
            }

            .subtitle {
                font-size: 1.6rem;
            }

            .driving-logo {
                width: 100px;
                height: 100px;
            }

            .driving-logo img {
                width: 60px;
                height: 60px;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .contact-title {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include Navigation -->
    <?php include_once("./partials/navigation.php"); ?>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <div class="driving-logo fade-in">
                    <img src="./driving-school/3.png" alt="MK Driving School Logo">
                </div>
                
                <h1 class="main-title fade-in">
                    <?php echo $lang === 'kin' ? 'Ikaze kuri MK DRIVING SCHOOL!' : 'Welcome to MK DRIVING SCHOOL!'; ?>
                </h1>
                
                <div class="subtitle fade-in">🚗 Professional Driving Training 🚦</div>
                
                <p class="hero-description fade-in">
                    <?php 
                    if ($lang === 'kin') {
                        echo "Urubuga rugufasha kwitegura gukora ikizamini cy\'amategeko y\'umuhanda utavuye aho uri. Hamwe na MK DRIVING SCHOOL, ukeneye gukora rimwe gusa ugatsinda.";
                    } else {
                        echo "Your trusted partner in passing the driving theory exam (Provisoire) — the first time! Our app will provide you with real past exam questions and answers to help you study smarter and faster.";
                    }
                    ?>
                </p>
                
                <div class="coming-soon-banner fade-in">
                    <div class="coming-soon-text">
                        <?php echo $lang === 'kin' ? 'IRAZA VUBA - Komeza ukurikirane nawe utazacikanwa!' : 'COMING SOON — Stay tuned and be the first to join!'; ?>
                    </div>
                    <div class="launch-promise">
                        <?php echo $lang === 'kin' ? 'Iga! Witoze! Utsinde!' : 'Learn. Practice. ✅ Pass.'; ?>
                    </div>
                </div>
                
                <div class="language-switcher fade-in">
                    <div class="lang-buttons">
                        <a href="?lang=en" class="lang-btn <?php echo $lang === 'en' ? 'active' : ''; ?>">English</a>
                        <a href="?lang=kin" class="lang-btn <?php echo $lang === 'kin' ? 'active' : ''; ?>">Kinyarwanda</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">
                    <?php echo $lang === 'kin' ? 'Ibyiza tubaha' : 'Why Choose MK Driving School?'; ?>
                </h2>
                <p class="section-subtitle">
                    <?php echo $lang === 'kin' ? 'Ubuhamya bwo kwiga amategeko y\'umuhanda neza' : 'Experience the best in driving theory education'; ?>
                </p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">📚</div>
                    <h3 class="feature-title">
                        <?php echo $lang === 'kin' ? 'Ibibazo byukuri' : 'Real Past Exam Questions'; ?>
                    </h3>
                    <p class="feature-description">
                        <?php echo $lang === 'kin' ? 'Tuguhaye ibibazo byukuri byikizamini hamwe nibisubizo bigufasha kwiga neza kandi byihuse.' : 'Access authentic past exam questions with detailed answers to help you study effectively.'; ?>
                    </p>
                </div>
                
                <div class="feature-card fade-in">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">
                        <?php echo $lang === 'kin' ? 'Gutsinda rimwe gusa' : 'Pass First Time'; ?>
                    </h3>
                    <p class="feature-description">
                        <?php echo $lang === 'kin' ? 'Hamwe na MK DRIVING SCHOOL, ukeneye gukora rimwe gusa ugatsinda. Reka tubafashe gutunganya.' : 'With MK Driving School, you only need one chance to pass. Let us help you prepare perfectly.'; ?>
                    </p>
                </div>
                
                <div class="feature-card fade-in">
                    <div class="feature-icon">📱</div>
                    <h3 class="feature-title">
                        <?php echo $lang === 'kin' ? 'Porogaramu ngendanwa' : 'Mobile App'; ?>
                    </h3>
                    <p class="feature-description">
                        <?php echo $lang === 'kin' ? 'Kwiga ahantu hose, igihe icyo aricyo cyose. Porogaramu yacu ihagije ku ngendanwa zawe.' : 'Study anywhere, anytime. Our mobile app fits perfectly in your pocket.'; ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">
                    <?php echo $lang === 'kin' ? 'Uko dukora' : 'How It Works'; ?>
                </h2>
                <p class="section-subtitle">
                    <?php echo $lang === 'kin' ? 'Amahitamo yoroshye yo kwitegura' : 'Simple steps to success'; ?>
                </p>
            </div>
            
            <div class="process-grid">
                <div class="process-step fade-in">
                    <div class="step-number">1</div>
                    <h3 class="step-title">
                        <?php echo $lang === 'kin' ? 'Kwiyandikisha' : 'Download App'; ?>
                    </h3>
                    <p class="step-description">
                        <?php echo $lang === 'kin' ? 'Kuramo porogaramu yacu ku ngendanwa yawe.' : 'Download our app from your app store.'; ?>
                    </p>
                </div>
                
                <div class="process-step fade-in">
                    <div class="step-number">2</div>
                    <h3 class="step-title">
                        <?php echo $lang === 'kin' ? 'Kwiga' : 'Study'; ?>
                    </h3>
                    <p class="step-description">
                        <?php echo $lang === 'kin' ? 'Kwiga ibibazo byikizamini byukuri hamwe nibisubizo.' : 'Study real exam questions with detailed answers.'; ?>
                    </p>
                </div>
                
                <div class="process-step fade-in">
                    <div class="step-number">3</div>
                    <h3 class="step-title">
                        <?php echo $lang === 'kin' ? 'Gukora ikizamini' : 'Practice Tests'; ?>
                    </h3>
                    <p class="step-description">
                        <?php echo $lang === 'kin' ? 'Witoze ikizamini nkuko cyakozwe mbere.' : 'Take practice tests that simulate real exam conditions.'; ?>
                    </p>
                </div>
                
                <div class="process-step fade-in">
                    <div class="step-number">4</div>
                    <h3 class="step-title">
                        <?php echo $lang === 'kin' ? 'Gutsinda' : 'Pass'; ?>
                    </h3>
                    <p class="step-description">
                        <?php echo $lang === 'kin' ? 'Utsinde ikizamini cy\'amategeko y\'umuhanda rimwe gusa!' : 'Pass your driving theory exam on the first try!'; ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <h2 class="contact-title fade-in">
                <?php echo $lang === 'kin' ? 'Tugane' : 'Get In Touch'; ?>
            </h2>
            
            <div class="contact-grid">
                <div class="contact-item fade-in">
                    <div class="contact-icon">📞</div>
                    <div class="contact-label">
                        <?php echo $lang === 'kin' ? 'Telefone' : 'Phone'; ?>
                    </div>
                    <a href="tel:+250798611161" class="contact-value">+250 798 611 161</a>
                </div>
                
                <div class="contact-item fade-in">
                    <div class="contact-icon">📧</div>
                    <div class="contact-label">
                        <?php echo $lang === 'kin' ? 'Imeli' : 'Email'; ?>
                    </div>
                    <a href="mailto:mkscholars250@gmail.com" class="contact-value">mkscholars250@gmail.com</a>
                </div>
                
                <div class="contact-item fade-in">
                    <div class="contact-icon">📸</div>
                    <div class="contact-label">Instagram</div>
                    <a href="https://www.instagram.com/mkdrivingschool_/" target="_blank" class="contact-value">@mkdrivingschool_</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Include Footer -->
    <?php include_once("./partials/footer.php"); ?>

    <script>
        // Optimized performance script
        document.addEventListener('DOMContentLoaded', function() {
            // Intersection Observer for fade-in animations
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                            observer.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

                document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));
            } else {
                document.querySelectorAll('.fade-in').forEach(el => el.classList.add('visible'));
            }

            // Smooth scrolling for better UX
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
