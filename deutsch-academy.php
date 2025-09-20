<?php
// Error handling for database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try to include database connection safely
try {
    include_once("./dbconnection/connection.php");
} catch (Exception $e) {
    error_log("Database connection error in deutsch-academy.php: " . $e->getMessage());
}

// Start session
(session_status() == PHP_SESSION_NONE) ? session_start() : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Deutsch in MK Deutsch Academy - Learn German Language | MK Scholars</title>
    <meta name="description" content="Study Deutsch in MK Deutsch Academy. Learn German language from A1 to B2 levels. Physical and online classes available. Registration open for October 2025 intake.">
    <meta name="keywords" content="German language, learn German, Deutsch Academy, German classes, Rwanda, Kigali, A1, B2, language learning">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    
    <!-- Include head partial -->
    <?php include_once("./partials/head.php"); ?>
    
    <style>
        /* Clean, Professional Design - Matching Promotional Flyer */
        :root {
            --deep-blue: #1a365d;
            --bright-yellow: #FFD700;
            --orange-yellow: #FFA500;
            --red: #DC143C;
            --white: #ffffff;
            --light-gray: #f8f9fa;
            --dark-gray: #2d3748;
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

        /* Hero Section - New Color Scheme */
        .hero-section {
            background: linear-gradient(135deg, #2c5282 0%, #1a365d 50%, #2d3748 100%);
            color: var(--white);
            text-align: center;
            padding: 120px 0 80px;
            margin-top: 108px;
            position: relative;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(44, 82, 130, 0.95) 0%, rgba(26, 54, 93, 0.9) 50%, rgba(45, 55, 72, 0.95) 100%);
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .main-title {
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            letter-spacing: -1px;
            color: white;
        }

        .academy-name {
            font-size: 3rem;
            font-weight: 700;
            color: var(--bright-yellow);
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .kinyarwanda-text {
            font-size: 1.8rem;
            font-weight: 500;
            margin-bottom: 50px;
            opacity: 0.95;
        }

        /* Intake Banner - Bright Yellow-Orange */
        .intake-banner {
            background: linear-gradient(135deg, var(--bright-yellow) 0%, var(--orange-yellow) 100%);
            border: 2px solid rgba(220, 20, 60, 0.3);
            border-radius: 15px;
            padding: 30px;
            margin: 40px auto;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .intake-text {
            font-size: 1.2rem;
            color: var(--dark-gray);
            margin-bottom: 10px;
            font-weight: 600;
        }

        .intake-date {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--white);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        /* Level Badge */
        .level-badge {
            background: var(--red);
            color: var(--white);
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 1.4rem;
            font-weight: 600;
            display: inline-block;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(220, 20, 60, 0.4);
        }

        /* Registration Section */
        .registration-section {
            background: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .registration-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 30px;
        }

        .registration-subtitle {
            background: linear-gradient(135deg, var(--deep-blue) 0%, #2d3748 100%);
            color: var(--white);
            padding: 20px 40px;
            border-radius: 30px;
            font-size: 1.3rem;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 8px 25px rgba(26, 54, 93, 0.3);
        }

        .registration-link {
            text-decoration: none;
            cursor: pointer;
        }

        /* Section Headers - Clean Design */
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 20px;
            position: relative;
        }

        .section-subtitle {
            font-size: 1.4rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Schedule Section */
        .schedule-section {
            background: var(--light-gray);
            padding: 80px 0;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
        }

        .schedule-card {
            background: var(--white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .schedule-card-header {
            background: linear-gradient(135deg, var(--deep-blue) 0%, #2d3748 100%);
            color: var(--white);
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .schedule-list {
            list-style: none;
            padding: 0;
        }

        .schedule-item {
            background: var(--light-gray);
            padding: 15px 20px;
            margin: 10px 0;
            border-radius: 10px;
            border-left: 4px solid var(--deep-blue);
            font-weight: 500;
        }

        .weekend-info {
            background: var(--light-gray);
            padding: 25px;
            border-radius: 15px;
            margin: 20px 0;
            text-align: center;
        }

        .start-date-badge {
            background: linear-gradient(135deg, var(--bright-yellow) 0%, var(--orange-yellow) 100%);
            color: var(--dark-gray);
            padding: 12px 25px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-block;
            margin-top: 15px;
            font-size: 1.1rem;
        }

        /* Benefits Section */
        .benefits-section {
            background: var(--white);
            padding: 80px 0;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .benefit-card {
            background: var(--light-gray);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .benefit-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            color: var(--deep-blue);
        }

        .benefit-title {
            font-size: 1.6rem;
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 15px;
        }

        .benefit-description {
            color: #666;
            line-height: 1.6;
        }

        /* Tuition Section */
        .tuition-section {
            background: var(--deep-blue);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .tuition-title {
            font-size: 2.2rem;
            font-weight: 600;
            margin-bottom: 50px;
            color: white;
        }

        .tuition-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            max-width: 800px;
            margin: 0 auto;
        }

        .tuition-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tuition-type {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .tuition-price {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--bright-yellow);
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        /* Contact Section */
        .contact-section {
            background: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .contact-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark-gray);
            margin-bottom: 40px;
        }

        .whatsapp-button {
            display: inline-flex;
            align-items: center;
            background: #25D366;
            color: var(--white);
            padding: 20px 40px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
        }

        .whatsapp-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(37, 211, 102, 0.4);
            color: var(--white);
            text-decoration: none;
        }

        .whatsapp-icon {
            width: 30px;
            height: 30px;
            margin-right: 15px;
        }

        .contact-info {
            margin-top: 25px;
            color: #666;
            font-size: 1.4rem;
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
                padding: 100px 0 60px;
                margin-top: 88px;
            }

            .main-title {
                font-size: 2.8rem;
            }

            .academy-name {
                font-size: 2.2rem;
            }

            .kinyarwanda-text {
                font-size: 1.4rem;
            }

            .schedule-grid,
            .tuition-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .registration-title {
                font-size: 2rem;
            }

            .contact-title {
                font-size: 1.8rem;
            }

            .whatsapp-button {
                font-size: 1.2rem;
                padding: 15px 30px;
            }

            .contact-info {
                font-size: 1.2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .section-subtitle {
                font-size: 1.2rem;
            }
        }

        @media (max-width: 480px) {
            .hero-section {
                padding: 80px 0 40px;
                margin-top: 68px;
            }

            .main-title {
                font-size: 2.2rem;
            }

            .academy-name {
                font-size: 1.8rem;
            }

            .kinyarwanda-text {
                font-size: 1.1rem;
            }

            .intake-banner {
                padding: 20px;
                margin: 30px auto;
            }

            .intake-date {
                font-size: 2rem;
            }

            .section-title {
                font-size: 1.6rem;
            }

            .benefits-grid {
                grid-template-columns: 1fr;
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
                <h1 class="main-title fade-in" STYLE>Study Deutsch in</h1>
                <div class="academy-name fade-in">MK Deutsch Academy</div>
                <div class="kinyarwanda-text fade-in">IGA URURIMI RW'IKIDAGE</div>
                
                <div class="intake-banner fade-in">
                    <div class="intake-text">OCTOBER INTAKE</div>
                    <div class="intake-date">CLASSES START OCTOBER 1ST</div>
                    <div style="font-size: 1.4rem; margin-top: 10px; font-weight: 600;">Registration open until September 30th</div>
                </div>
                
                <div class="level-badge fade-in pulse">LEVEL A1 - B2</div>
            </div>
        </div>
    </section>

    <!-- Registration Section -->
    <section class="registration-section">
        <div class="container">
            <h2 class="registration-title fade-in">Call For Registration</h2>
            <a href="https://wa.me/250798611161?text=Hello%20I%20would%20like%20to%20register%20for%20MK%20Deutsch%20Academy" class="registration-subtitle fade-in registration-link" target="_blank">Registration is Open Right Now</a>
        </div>
    </section>

    <!-- Program Types Section -->
    <section class="schedule-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Program Options</h2>
                <p class="section-subtitle">Choose the learning method that works best for you</p>
            </div>
            
            <div class="schedule-grid">
                <!-- Physical Classes -->
                <div class="schedule-card fade-in">
                    <div class="schedule-card-header">PHYSICAL CLASSES</div>
                    <ul class="schedule-list">
                        <li class="schedule-item">Location: Kigali City - Kiyovu</li>
                        <li class="schedule-item">Class starts: October 1st</li>
                        <li class="schedule-item">Registration: Until September 30th</li>
                        <li class="schedule-item">Early registration: 5% discount</li>
                        <li class="schedule-item">Books: Hard & soft copies provided</li>
                        <li class="schedule-item">Levels: A1 to B2 (6 weeks each)</li>
                    </ul>
                </div>

                <!-- Online Classes -->
                <div class="schedule-card fade-in">
                    <div class="schedule-card-header">ONLINE CLASSES</div>
                    <ul class="schedule-list">
                        <li class="schedule-item">Platform: MK Deutsch E-Learning</li>
                        <li class="schedule-item">Start: When student registers</li>
                        <li class="schedule-item">Registration: Always open</li>
                        <li class="schedule-item">Materials: Soft copies & audio/video</li>
                        <li class="schedule-item">Levels: A1 to B2 (6 weeks each)</li>
                        <li class="schedule-item">Access: 24/7 learning platform</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Why Choose MK Deutsch Academy?</h2>
                <p class="section-subtitle">Experience the best in German language education</p>
            </div>
            
            <div class="benefits-grid">
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Application Assistance</h3>
                    <p class="benefit-description">We assist students with Aupair, Freiwillige (Volunteer), and Ausbildung applications with special discounts for Germany applications.</p>
                </div>
                
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Goethe Exam Preparation</h3>
                    <p class="benefit-description">We prepare students for the GOETHE EXAM with sample questions and answers from previous exams.</p>
                </div>
                
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20 18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Complete Student Support</h3>
                    <p class="benefit-description">We assist students from day 1 to the final day with continuous follow-up for each student's progress.</p>
                </div>
                
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H16.5c-.8 0-1.54.5-1.85 1.26L12.5 14H10v8h2v-6h2.5l.5 6h3z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Comprehensive Learning Materials</h3>
                    <p class="benefit-description">Students receive books (hard & soft copies), videos, and audio materials to support their learning journey.</p>
                </div>
                
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 5h10v2h2V5c0-1.1-.9-2-2-2H7c-1.1 0-2 .9-2 2v2h2V5zm8 12v-5H9v5h6zm-8 2h10v2H7v-2z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Structured Learning Path</h3>
                    <p class="benefit-description">From A1 to B2 levels, each level takes 6 weeks to complete with comprehensive coverage of all studies.</p>
                </div>
                
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">Flexible Learning Options</h3>
                    <p class="benefit-description">Choose between physical classes in Kigali or online learning through our e-learning platform.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Tuition Section -->
    <section class="tuition-section">
        <div class="container">
            <h2 class="tuition-title fade-in">Tuition Fees (Per Level A1-B2)</h2>
            <div class="tuition-grid" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div class="tuition-card fade-in">
                    <div class="tuition-type">PHYSICAL CLASSES</div>
                    <div class="tuition-price">200K Rwf</div>
                    <div style="font-size: 1rem; margin-top: 15px; opacity: 0.9;">
                        <div>1st Installment: 120K Rwf (at start)</div>
                        <div>2nd Installment: 80K Rwf (after 3 weeks)</div>
                        <div style="margin-top: 10px; font-weight: 600; color: var(--bright-yellow);">Early registration: 5% discount</div>
                    </div>
                </div>
                <div class="tuition-card fade-in">
                    <div class="tuition-type">ONLINE CLASSES</div>
                    <div class="tuition-price">100K Rwf</div>
                    <div style="font-size: 1rem; margin-top: 15px; opacity: 0.9;">
                        <div>1st Installment: 60K Rwf (at start)</div>
                        <div>2nd Installment: 40K Rwf (after 3 weeks)</div>
                        <div style="margin-top: 10px; font-weight: 600; color: var(--bright-yellow);">Registration always open</div>
                    </div>
                </div>
                <div class="tuition-card fade-in">
                    <div class="tuition-type">INTERNATIONAL STUDENTS</div>
                    <div class="tuition-price">$100 USD</div>
                    <div style="font-size: 1rem; margin-top: 15px; opacity: 0.9;">
                        <div>1st Installment: $60 USD (at start)</div>
                        <div>2nd Installment: $40 USD (after 3 weeks)</div>
                        <div style="margin-top: 10px; font-weight: 600; color: var(--bright-yellow);">Same benefits as online classes</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Payment Methods Section -->
    <section class="benefits-section">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Payment Methods</h2>
                <p class="section-subtitle">Choose your preferred payment option</p>
            </div>
            
            <div class="benefits-grid">
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">For Rwandans</h3>
                    <p class="benefit-description">Mobile Money: 0798611161 (Paul SHYAKA)</p>
                </div>
                
                <div class="benefit-card fade-in">
                    <div class="benefit-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="benefit-title">For International Students</h3>
                    <p class="benefit-description">Flutterwave (M & S Innovation) or +250798611161 (Paul SHYAKA)</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <h2 class="contact-title fade-in">Contact Us</h2>
            <a href="https://wa.me/250798611161" class="whatsapp-button fade-in" target="_blank">
                <svg class="whatsapp-icon" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                </svg>
                +250 798 611 161
            </a>
            <p class="contact-info">
                Phone: 0798611161 (Paul)<br>
                Email: mkscholars250@gmail.com<br>
                Contact us for more information and registration
            </p>
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

            // WhatsApp contact tracking
            const whatsappBtn = document.querySelector('.whatsapp-button');
            if (whatsappBtn) {
                whatsappBtn.addEventListener('click', function() {
                    console.log('WhatsApp contact clicked');
                });
            }
        });
    </script>
</body>
</html>