<?php
session_start();
include("./dbconnection/connection.php");

// Fetch courses from database only
$courses = [];

if ($conn) {
    $coursesQuery = "SELECT c.*, cp.amount, cp.currency, cp.pricingDescription, curr.currencySymbol 
                     FROM Courses c 
                     LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
                     LEFT JOIN Currencies curr ON cp.currency = curr.currencyCode 
                     WHERE c.courseDisplayStatus = 1 
                     ORDER BY c.courseCreatedDate DESC";
    
    $coursesResult = mysqli_query($conn, $coursesQuery);
    
    if ($coursesResult && mysqli_num_rows($coursesResult) > 0) {
        while ($course = mysqli_fetch_assoc($coursesResult)) {
            $courses[] = $course;
        }
    }
}

// Helper function to get image URL
function getImageUrl($path = '') {
    if (isOnline()) {
        return 'https://admin.mkscholars.com/' . ltrim($path, './');
    } else {
        return './' . ltrim($path, './');
    }
}

// Helper function to format price
function formatPrice($amount, $currencySymbol, $currency) {
    if ($amount && $amount > 0) {
        $symbol = $currencySymbol ?: $currency;
        return $symbol . ' ' . number_format($amount, 0);
    }
    return 'Free';
}

// Helper function to get status text
function getStatusText($status) {
    switch($status) {
        case 1: return 'Open';
        case 2: return 'Closed';
        default: return 'Inactive';
    }
}

// Helper function to get status class
function getStatusClass($status) {
    switch($status) {
        case 1: return 'bg-success';
        case 2: return 'bg-warning';
        default: return 'bg-secondary';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses | MK Scholars</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #4bc2c5;
            --secondary: #ff7a7a;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --white: #ffffff;
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
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            color: var(--gray-800);
            line-height: 1.6;
            padding-top: 140px; /* account for fixed top bar + nav */
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
            padding: 0;
            margin: 0;
            box-shadow: var(--shadow-md);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            border: 1px solid var(--gray-200);
            display: flex;
            min-height: 300px;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .course-left-panel {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-width: 200px;
            width: 35%;
            position: relative;
            overflow: hidden;
        }

        .course-image-container {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 300px;
        }

        .course-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .course-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.8) 0%, rgba(30, 64, 175, 0.8) 100%);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2rem 1.5rem;
        }

        .course-brand {
            margin-bottom: 2rem;
        }

        .course-left-panel .course-brand {
            margin-bottom: 2rem;
        }

        .course-left-panel .course-contact {
            font-size: 0.875rem;
            font-weight: 500;
            padding: 0 1.5rem 2rem 1.5rem;
        }

        .course-brand-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            line-height: 1.1;
        }

        .course-brand-subtitle {
            font-size: 0.75rem;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 0.25rem;
        }

        .course-brand-name {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .course-contact {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .course-right-panel {
            background: white;
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .course-header {
            margin-bottom: 1.5rem;
        }

        .course-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .course-subtitle {
            font-size: 1rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .course-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: var(--success);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: var(--shadow-md);
        }

        .course-pricing-tags {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .price-tag {
            background: #dcfce7;
            color: #166534;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid #bbf7d0;
        }

        .price-tag.secondary {
            background: #fed7aa;
            color: #c2410c;
            border-color: #fdba74;
        }

        .course-description {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .course-features {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .feature-box {
            background: var(--gray-50);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-700);
            flex: 1;
        }

        .feature-icon {
            color: var(--primary);
            font-size: 1rem;
        }

        .course-deadline {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-bottom: 1.5rem;
        }

        .course-actions {
            margin-top: auto;
        }

        .course-header {
            margin-bottom: 1rem;
        }

        .course-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            line-height: 1.3;
        }

        .course-subtitle {
            font-size: 0.875rem;
            color: var(--gray-600);
            margin-bottom: 0.75rem;
        }

        .course-pricing {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .price-tag {
            background: var(--primary);
            color: var(--white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .course-description {
            font-size: 0.9rem;
            color: var(--gray-600);
            margin-bottom: 1rem;
            line-height: 1.5;
        }

        .course-deadline {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--gray-500);
            margin-bottom: 1rem;
        }

        .course-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .enroll-button {
            background: var(--primary);
            color: var(--white);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .enroll-button:hover {
            background: #3aa9ac;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .enroll-button:active {
            transform: translateY(0);
        }

        /* ==== Responsive Design ==== */
        @media (max-width: 768px) {
            body { padding-top: 110px; }
            .main-container {
                padding: 0.5rem 1rem;
            }

            .courses-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
                margin: 0.5rem 0;
            }

            .course-card {
                flex-direction: column;
                min-height: auto;
            }

            .course-left-panel {
                width: 100%;
                min-width: auto;
                padding: 0;
            }

            .course-image-container {
                min-height: 200px;
            }

            .course-image-overlay {
                padding: 1.5rem;
            }

            .course-left-panel .course-contact {
                padding: 0 1.5rem 1.5rem 1.5rem;
            }

            .course-right-panel {
                padding: 1.5rem;
            }

            .course-features {
                flex-direction: column;
                gap: 0.5rem;
            }

            .course-pricing-tags {
                gap: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            body { padding-top: 100px; }
            .courses-grid {
                gap: 0.75rem;
            }

            .course-left-panel {
                padding: 1rem;
            }

            .course-right-panel {
                padding: 1rem;
            }

            .course-brand-title {
                font-size: 1.5rem;
            }

            .course-title {
                font-size: 1.25rem;
            }

            .course-features {
                flex-direction: column;
            }
        }

        .course-card {
            animation: fadeInUp 0.6s ease-out;
        }

        .course-card:nth-child(1) { animation-delay: 0.1s; }
        .course-card:nth-child(2) { animation-delay: 0.2s; }
        .course-card:nth-child(3) { animation-delay: 0.3s; }
        .course-card:nth-child(4) { animation-delay: 0.4s; }

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
        <div class="course-count-section mb-4">
            <h3 class="mb-0">Available Courses</h3>
            <p class="text-muted"><?php echo count($courses); ?> course(s) available</p>
        </div>

        <div class="courses-grid">
            <!-- Dynamic Courses from Database Only -->
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="course-card">
                        <!-- Left Panel - Course Image -->
                        <div class="course-left-panel">
                            <?php if (!empty($course['coursePhoto'])): ?>
                                <div class="course-image-container">
                                    <img src="<?php echo getImageUrl($course['coursePhoto']); ?>" 
                                         alt="<?php echo htmlspecialchars($course['courseName']); ?>" 
                                         class="course-image">
                                    <div class="course-image-overlay">
                                        <div class="course-brand">
                                            <div class="course-brand-title"><?php echo strtoupper(substr($course['courseName'], 0, 5)); ?></div>
                                            <div class="course-brand-subtitle">COACHING WITH</div>
                                            <div class="course-brand-name">MK SCHOLARS</div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="course-image-container">
                                    <img src="<?php echo getImageUrl('images/courses/placeholder.jpg'); ?>" 
                                         alt="<?php echo htmlspecialchars($course['courseName']); ?>" 
                                         class="course-image">
                                    <div class="course-image-overlay">
                                        <div class="course-brand">
                                            <div class="course-brand-title"><?php echo strtoupper(substr($course['courseName'], 0, 5)); ?></div>
                                            <div class="course-brand-subtitle">COACHING WITH</div>
                                            <div class="course-brand-name">MK SCHOLARS</div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="course-contact">0798611161</div>
                        </div>
                        
                        <!-- Right Panel - Course Details -->
                        <div class="course-right-panel">
                            <div class="course-badge <?php echo getStatusClass($course['courseDisplayStatus']); ?>">
                                <?php echo getStatusText($course['courseDisplayStatus']); ?>
                            </div>
                            
                            <div class="course-header">
                                <h2 class="course-title"><?php echo htmlspecialchars($course['courseName']); ?></h2>
                                <p class="course-subtitle"><?php echo htmlspecialchars($course['courseDescription']); ?></p>
                            </div>
                            
                            <div class="course-pricing-tags">
                                <div class="price-tag">
                                    <?php echo formatPrice($course['amount'], $course['currencySymbol'], $course['currency']); ?> - Complete Package
                                </div>
                                <?php if ($course['pricingDescription']): ?>
                                    <div class="price-tag secondary">
                                        <?php echo htmlspecialchars($course['pricingDescription']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="course-description">
                                Comprehensive online coaching designed to help students achieve their academic goals. Includes expert guidance, practice materials, and personalized support.
                            </div>
                            
                            <div class="course-features">
                                <div class="feature-box">
                                    <i class="fas fa-graduation-cap feature-icon"></i>
                                    <span>Online Learning</span>
                                </div>
                                <div class="feature-box">
                                    <i class="fas fa-users feature-icon"></i>
                                    <span>30 Seats Available</span>
                                </div>
                            </div>
                            
                            <div class="course-deadline">
                                <i class="fas fa-hourglass-half"></i>
                                <span>Registration Ends: <?php echo date('F j, Y', strtotime($course['courseRegEndDate'])); ?></span>
                            </div>
                            
                            <div class="course-actions">
                                <?php
                                    $isLoggedIn = isset($_SESSION) && isset($_SESSION['userId']);
                                    $next = urlencode('/mkscholars/courses');
                                ?>
                                <button onclick="window.location.href='<?php echo $isLoggedIn ? ('./subscription?course=' . $course['courseId']) : ('./login?next=' . $next); ?>'" class="enroll-button">
                                    <i class="fas fa-arrow-right"></i>
                                    Register Now (Iyandikishe)
                                </button>
                                <button onclick="window.location.href='./request-help?course=<?php echo $course['courseId']; ?>'" class="enroll-button" style="background:#6b7280">
                                    <i class="fas fa-life-ring"></i>
                                    Ask for help
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No courses available</h4>
                        <p class="text-muted">Check back later for new courses!</p>
                    </div>
                </div>
            <?php endif; ?>
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
                    this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    this.disabled = true;
                    
                    // Simulate loading delay (remove in production)
                    setTimeout(() => {
                        // Remove loading state
                        card.classList.remove('loading');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }, 2000);
                });
            });

            // Intersection Observer for scroll animations
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