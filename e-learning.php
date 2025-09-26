<?php
session_start();
include('./dbconnection/connection.php');
include('./php/validateSession.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning | MK Scholars</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #f3f4f6;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --glass-bg: rgba(255, 255, 255, 0.9);
            --glass-border: rgba(255, 255, 255, 0.3);
            --neumorphic-shadow: 5px 5px 10px #d1d5db, -5px -5px 10px #ffffff;
        }

        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --glass-bg: rgba(31, 41, 55, 0.9);
            --glass-border: rgba(255, 255, 255, 0.1);
            --neumorphic-shadow: 5px 5px 10px #0a0c10, -5px -5px 10px #283447;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        }

        .main-content {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .sidebar {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            box-shadow: var(--neumorphic-shadow);
            min-height: 100vh;
        }

        .neumorphic-icon {
            width: 60px;
            height: 60px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--neumorphic-shadow);
        }

        .nav-link {
            color: var(--text-primary);
            text-decoration: none;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: var(--glass-bg);
            color: var(--text-primary);
            transform: translateY(-2px);
            box-shadow: var(--neumorphic-shadow);
        }

        .sidebar-toggle {
            display: none;
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 1rem;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: -100%;
                width: 280px;
                height: 100vh;
                z-index: 1050;
                transition: left 0.3s ease;
            }

            .sidebar.show {
                left: 0;
            }

            .sidebar-toggle {
                display: block;
            }
        }

        .theme-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1100;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .card:hover {
            transform: translateY(-4px);
        }

        .card-title {
            color: var(--text-primary);
        }

        .card-text {
            color: var(--text-secondary);
        }

        .card-img-top {
            height: 150px;
            object-fit: cover;
            width: 100%;
        }

        @media (max-width: 768px) {
            .card-img-top {
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            .card-img-top {
                height: 100px;
            }
        }
    </style>
</head>

<body data-theme="light">
    <!-- Theme Toggle Button -->
    <button style="color: orange;" class="btn btn-secondary theme-toggle glass-panel">
        <i class="fas fa-moon"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include("./partials/dashboardNavigation.php"); ?>

            <!-- Main Content -->
            <main class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h3 class="mb-0">E-Learning Courses</h3>
                </div>

                <div class="row g-4">
            <!-- Study Deutsch Course Card -->
            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="position-relative">
                        <img src="./images/courses/deutsch-academy.jpg" class="card-img-top rounded-top" alt="Study Deutsch in MK Deutsch Academy">
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">Language</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-semibold">Study Deutsch in MK Deutsch Academy</h5>
                        <p class="card-text text-muted flex-grow-1">Master German language for academic & career success. A1 to B2 levels with certified instructors.</p>
                        <a href="deutsch-academy" class="btn btn-outline-success w-100 mt-2">Register Now</a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="position-relative">
                        <img src="https://mkscholars.com/images/courses/ucat.jpg" class="card-img-top rounded-top" alt="UCAT">
                        <span class="badge bg-info text-dark position-absolute top-0 start-0 m-2">Medical</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-semibold">UCAT Coaching</h5>
                        <p class="card-text text-muted flex-grow-1">Prepare for the UCAT with expert-led sessions, strategies, and full mock tests.</p>
                        <a href="ucat-course" class="btn btn-outline-info w-100 mt-2">View Course</a>
                    </div>
                </div>
            </div>
            
            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="position-relative">
                        <img src="./images/courses/alu.jpeg" class="card-img-top rounded-top" alt="ALU English Proficiency Program">
                        <span class="badge bg-primary position-absolute top-0 start-0 m-2">Language</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-semibold">ALU English Proficiency Program</h5>
                        <p class="card-text text-muted flex-grow-1">Boost your English for ALU English Proficency Test (EPT). 10 days online training in speaking, reading & writing.</p>
                        <a href="alu-english-program" class="btn btn-outline-primary w-100 mt-2">Register Now</a>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="position-relative">
                        <img src="./images/courses/codingcourse.jpeg" class="card-img-top rounded-top" alt="Coding Bootcamp">
                        <span class="badge bg-success position-absolute top-0 start-0 m-2">Tech</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-semibold">30-Day Coding Bootcamp</h5>
                        <p class="card-text text-muted flex-grow-1">Learn HTML, CSS, JS, React & backend. Evening sessions. Certificate on completion.</p>
                        <a href="coding-course" class="btn btn-outline-success w-100 mt-2">Register Now</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="position-relative">
                        <img src="./images/courses/englishcourse.jpeg" class="card-img-top rounded-top" alt="English Course">
                        <span class="badge bg-warning text-dark position-absolute top-0 start-0 m-2">Language</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-semibold">English Communication Course</h5>
                        <p class="card-text text-muted flex-grow-1">Boost your English speaking, writing & listening. 2-month program with certificate.</p>
                        <a href="english-course" class="btn btn-outline-warning w-100 mt-2">Join Now</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="position-relative">
                        <img src="./images/courses/morocco.jpeg" class="card-img-top rounded-top" alt="Morocco Admissions">
                        <span class="badge bg-primary position-absolute top-0 start-0 m-2">Admissions</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-semibold">Morocco Admissions</h5>
                        <p class="card-text text-muted flex-grow-1">Practice interviews, tourism knowledge, and logic tests.</p>
                        <a href="morocco-admissions" class="btn btn-outline-primary w-100 mt-2">View Course</a>
                    </div>
                </div>
            </div>




           

                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const body = document.body;
            const themeToggle = document.querySelector('.theme-toggle');
            const savedTheme = localStorage.getItem('theme') || 'light';
            body.setAttribute('data-theme', savedTheme);
            updateToggleIcon();

            themeToggle.addEventListener('click', () => {
                const currentTheme = body.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateToggleIcon();
            });

            function updateToggleIcon() {
                const icon = themeToggle.querySelector('i');
                icon.className = body.getAttribute('data-theme') === 'light' ? 'fas fa-moon' : 'fas fa-sun';
            }

            // Sidebar toggle functionality
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('show');
                });
            }

        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>