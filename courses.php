<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK Scholars - Courses</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ==== Modern Design System ==== */
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #7c3aed;
            --accent: #f59e0b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --shadow-2xl: 0 25px 50px -12px rgb(0 0 0 / 0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding-top: 120px;
            line-height: 1.6;
            color: var(--gray-800);
        }


        /* ==== Main Container ==== */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem 2rem;
        }

        /* ==== Course Grid ==== */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
            margin: 1rem 0;
        }

        /* ==== Modern Course Cards ==== */
        .course-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid var(--gray-200);
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .course-image-container {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .course-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }

        .course-card:hover .course-image {
            transform: scale(1.05);
        }

        .course-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--success);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            box-shadow: var(--shadow-md);
        }

        .course-content {
            padding: 1.5rem;
        }

        .course-header {
            margin-bottom: 1rem;
        }

        .course-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
            line-height: 1.3;
        }

        .course-subtitle {
            color: var(--gray-600);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .course-pricing {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .price-tag {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: var(--white);
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        .price-tag.secondary {
            background: linear-gradient(135deg, var(--accent) 0%, #f97316 100%);
        }

        .course-description {
            color: var(--gray-600);
            line-height: 1.5;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .course-features {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.5rem;
            background: var(--gray-50);
            border-radius: 8px;
            font-size: 0.8rem;
            color: var(--gray-700);
            transition: all 0.2s ease;
        }

        .feature-item:hover {
            background: var(--gray-100);
            transform: translateY(-1px);
        }

        .feature-icon {
            color: var(--primary);
            font-size: 1rem;
        }

        .course-deadline {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: var(--gray-800);
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .course-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .enroll-button {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
            box-shadow: var(--shadow-sm);
        }

        .enroll-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1e40af 100%);
        }

        .enroll-button:active {
            transform: translateY(0);
        }

        /* ==== Responsive Design ==== */
        @media (max-width: 768px) {
            body {
                padding-top: 100px;
            }

            .main-container {
                padding: 0.5rem 1rem;
            }

            .courses-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                margin: 0.5rem 0;
            }

            .course-content {
                padding: 1.25rem;
            }

            .course-features {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            body {
                padding-top: 90px;
            }

            .main-container {
                padding: 0.5rem 0.75rem;
            }

            .courses-grid {
                gap: 0.75rem;
            }

            .course-content {
                padding: 1rem;
            }

            .course-pricing {
                flex-direction: column;
            }

            .course-image-container {
                height: 180px;
            }
        }

        /* ==== Animations ==== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .course-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .course-card:nth-child(1) { animation-delay: 0.1s; }
        .course-card:nth-child(2) { animation-delay: 0.2s; }
        .course-card:nth-child(3) { animation-delay: 0.3s; }
        .course-card:nth-child(4) { animation-delay: 0.4s; }

        /* ==== Loading States ==== */
        .course-card.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .course-card.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin: -20px 0 0 -20px;
            border: 3px solid var(--gray-200);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <!-- ==== Universal Navigation ==== -->
    <?php include("./partials/navigation.php") ?>


    <!-- ==== Courses Section ==== -->
    <div class="main-container">
        <div class="courses-grid">

            <!-- Study Deutsch Course Card -->
            <div class="course-card">
                <div class="course-image-container">
                    <img src="./images/courses/deutsch-academy.jpg" alt="Study Deutsch in MK Deutsch Academy" class="course-image">
                    <div class="course-badge">Open</div>
                </div>
                <div class="course-content">
                    <div class="course-header">
                        <h2 class="course-title">Study Deutsch in MK Deutsch Academy</h2>
                        <p class="course-subtitle">Master German Language for Academic & Career Success</p>
                        <div class="course-pricing">
                            <span class="price-tag">25,000 RWF - Complete Program</span>
                        </div>
                    </div>
                    
                    <p class="course-description">
                        Comprehensive German language program designed for students preparing for German universities and professional opportunities. Includes A1 to B2 levels with certified instructors.
                    </p>
                    
                    <div class="course-features">
                        <div class="feature-item">
                            <i class="fas fa-graduation-cap feature-icon"></i>
                            <span>Certified Instructors</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users feature-icon"></i>
                            <span>25 Seats Available</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-certificate feature-icon"></i>
                            <span>Official Certificate</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-clock feature-icon"></i>
                            <span>Flexible Schedule</span>
                        </div>
                    </div>
                    
                    <div class="course-deadline">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Classes Begin: August 18, 2025</span>
                    </div>
                    
                    <div class="course-actions">
                        <button onclick="window.location.href='./deutsch-academy'" class="enroll-button">
                            <i class="fas fa-arrow-right"></i>
                            Register Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- UCAT Course Card -->
            <div class="course-card">
                <div class="course-image-container">
                    <img src="./images/courses/ucat.jpg" alt="UCAT Online Coaching" class="course-image">
                    <div class="course-badge">Open</div>
                </div>
                <div class="course-content">
                    <div class="course-header">
                        <h2 class="course-title">UCAT Online Coaching</h2>
                        <p class="course-subtitle">For Future Medical Students</p>
                        <div class="course-pricing">
                            <span class="price-tag">7,500 RWF - Notes & Answers</span>
                            <span class="price-tag secondary">15,000 RWF - With Teacher</span>
                        </div>
                    </div>
                    
                    <p class="course-description">
                        Comprehensive online coaching for students preparing for the University Clinical Aptitude Test (UCAT). Includes expert guidance, practice tests, and strategies to improve scores.
                    </p>
                    
                    <div class="course-features">
                        <div class="feature-item">
                            <i class="fas fa-graduation-cap feature-icon"></i>
                            <span>Online Learning</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users feature-icon"></i>
                            <span>30 Seats Available</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-clock feature-icon"></i>
                            <span>Flexible Schedule</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-certificate feature-icon"></i>
                            <span>Certification</span>
                        </div>
                    </div>
                    
                    <div class="course-deadline">
                        <i class="fas fa-hourglass-half"></i>
                        <span>Registration Closes: September 28, 2025</span>
                    </div>
                    
                    <div class="course-actions">
                        <button onclick="window.location.href='./ucat'" class="enroll-button">
                            <i class="fas fa-arrow-right"></i>
                            Register Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- ALU English Course Card -->
            <div class="course-card">
                <div class="course-image-container">
                    <img src="./images/courses/alu.jpeg" alt="ALU English Proficiency Program" class="course-image">
                    <div class="course-badge">Open</div>
                </div>
                <div class="course-content">
                    <div class="course-header">
                        <h2 class="course-title">ALU English Proficiency Program</h2>
                        <p class="course-subtitle">Boost Your English Skills for Academic & Career Success</p>
                        <div class="course-pricing">
                            <span class="price-tag">15,000 RWF - 10 Days Practice</span>
                            <span class="price-tag secondary">15,000 RWF - Sample Questions</span>
                        </div>
                    </div>
                    
                    <p class="course-description">
                        A comprehensive 10-day practice program with sample questions and detailed explanations, specifically tailored to help you pass your English proficiency test for ALU admissions.
                    </p>
                    
                    <div class="course-features">
                        <div class="feature-item">
                            <i class="fas fa-chalkboard-teacher feature-icon"></i>
                            <span>Live Virtual Classes</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users feature-icon"></i>
                            <span>40 Seats Available</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-book feature-icon"></i>
                            <span>Practice Materials</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-trophy feature-icon"></i>
                            <span>Success Guaranteed</span>
                        </div>
                    </div>
                    
                    <div class="course-deadline">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Classes Begin: July 10, 2025</span>
                    </div>
                    
                    <div class="course-actions">
                        <button onclick="window.location.href='./alu-english-program'" class="enroll-button">
                            <i class="fas fa-arrow-right"></i>
                            Register Now
                        </button>
                    </div>
                </div>
            </div>


            <!-- Coding Bootcamp Course Card -->
            <div class="course-card">
                <div class="course-image-container">
                    <img src="./images/courses/codingcourse.jpeg" alt="Coding Bootcamp" class="course-image">
                    <div class="course-badge">Open</div>
                </div>
                <div class="course-content">
                    <div class="course-header">
                        <h2 class="course-title">Coding Bootcamp</h2>
                        <p class="course-subtitle">For Beginners & Tech Enthusiasts</p>
                        <div class="course-pricing">
                            <span class="price-tag">25,000 RWF - Complete Package</span>
                        </div>
                    </div>
                    
                    <p class="course-description">
                        Hands-on coding course designed to introduce students to programming fundamentals using HTML, CSS, JavaScript, React JS, MySQL, and Node.js. Perfect for beginners with flexible scheduling.
                    </p>
                    
                    <div class="course-features">
                        <div class="feature-item">
                            <i class="fas fa-laptop-code feature-icon"></i>
                            <span>Live Mentoring</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users feature-icon"></i>
                            <span>30 Seats Available</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-file-pdf feature-icon"></i>
                            <span>PDF Notes & Assignments</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-clock feature-icon"></i>
                            <span>Flexible Schedule</span>
                        </div>
                    </div>
                    
                    <div class="course-deadline">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Training Starts: July 10th, 2025</span>
                    </div>
                    
                    <div class="course-actions">
                        <button onclick="window.location.href='./coding-course'" class="enroll-button">
                            <i class="fas fa-arrow-right"></i>
                            Register Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- English Communication Course Card -->
            <div class="course-card">
                <div class="course-image-container">
                    <img src="./images/courses/englishcourse.jpeg" alt="English Communication Course" class="course-image">
                    <div class="course-badge">Open</div>
                </div>
                <div class="course-content">
                    <div class="course-header">
                        <h2 class="course-title">English Communication Course</h2>
                        <p class="course-subtitle">For All Levels – Learn to Speak & Write Confidently</p>
                        <div class="course-pricing">
                            <span class="price-tag">15,000 RWF - Complete Package</span>
                        </div>
                    </div>
                    
                    <p class="course-description">
                        Improve your English speaking, listening, reading, and writing through practical online sessions. Designed for students, professionals, and anyone eager to communicate fluently.
                    </p>
                    
                    <div class="course-features">
                        <div class="feature-item">
                            <i class="fas fa-chalkboard-teacher feature-icon"></i>
                            <span>Expert Instructors</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-users feature-icon"></i>
                            <span>20 Seats Available</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-book feature-icon"></i>
                            <span>Practice Materials</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-clock feature-icon"></i>
                            <span>Flexible Schedule</span>
                        </div>
                    </div>
                    
                    <div class="course-deadline">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Classes Begin: July 10, 2025</span>
                    </div>
                    
                    <div class="course-actions">
                        <button onclick="window.location.href='./english-course'" class="enroll-button">
                            <i class="fas fa-arrow-right"></i>
                            Register Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==== Enhanced JavaScript ==== -->
    <script>
        // Enhanced course card interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading states to buttons
            const enrollButtons = document.querySelectorAll('.enroll-button');
            
            enrollButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const card = this.closest('.course-card');
                    const originalText = this.innerHTML;
                    
                    // Add loading state
                    card.classList.add('loading');
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                    this.disabled = true;
                    
                    // Simulate loading delay (remove in production)
                    setTimeout(() => {
                        card.classList.remove('loading');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, 1000);
                });
            });
            
            // Add smooth scroll for better UX
            const smoothScroll = (target) => {
                const element = document.querySelector(target);
                if (element) {
                    element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            };
            
            // Add intersection observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Observe all course cards
            const courseCards = document.querySelectorAll('.course-card');
            courseCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>

</body>

</html>