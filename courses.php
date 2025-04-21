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



        /* ==== Scholarship Cards (Separate Section) ==== */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            display: flex;
            flex-direction: column;
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
            .navbar-nav {
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
    <?php include("./partials/coursesNav.php") ?>


    <!-- ==== Scholarship Cards ==== -->
    <div class="container">
        <div class="scholarship-card">
            <img src="./images/courses/ucat.jpg" alt="UCAT" class="card-image">
            <div class="card-content">
                <div class="card-header">
                    <h2 class="card-title">UCAT Online Coaching</h2>
                    <p class="card-subtitle">For Future Medical Students</p>
                    <div style="display: flex; flex-wrap: wrap;">
                        <div class="price-tag">4,500 RWF Prepared notes and answers</div>
                        <div class="price-tag" style="background: rgba(255, 193, 7, 0.15); color: #FFA000;">7,500 FRW Coaching with a teacher</div>

                    </div>
                </div>

                <p class="card-description">
                    Comprehensive online coaching for students preparing for the University Clinical Aptitude Test (UCAT). Includes expert guidance, practice tests, and strategies to improve scores. Morning and evening classes are available. <br>
                    <!-- <li>March Intake (Virtual Classes) – Course starts on March 24, 2025</li> <br> -->
                </p>

                <div class="card-details">
                    <div class="detail-item">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Online Studying</span>
                    </div>
                    <!-- <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <span>30 Or 15 Days Program</span>
                    </div> -->
                    <div class="detail-item">
                        <i class="fas fa-users"></i>
                        <span>3 Seats Available</span>
                    </div>
                </div>

                <div class="action-section">
                    <div class="deadline">
                        <i class="fas fa-hourglass-half"></i>
                        Registration Closes: April 24, 2025
                    </div>
                    <button onclick="window.location.href='./ucat'" class="apply-button">
                        <i class="fas fa-arrow-circle-right"></i>
                        Register now (Iyandikishe)
                    </button>
                </div>
            </div>
            <div class="status-tag">Open</div>
        </div>

        <div class="scholarship-card">
            <img src="./images/courses/lang.jpg" alt="Languages" class="card-image">
            <div class="card-content">
                <div class="card-header">
                    <h2 class="card-title">Language & Coding Classes – Virtual & In-Person</h2>
                    <p class="card-subtitle"> Improve Your Skills in English, French, German, or Coding!</p>
                    <div class="price-tag">
                        15,000 RWF - 25,000 RWF
                    </div>
                </div>

                <p class="card-description">
                    <li>April Intake (Virtual Classes) – Registration closes March 30, 2025</li> <br>
                    <li>June Intake (Virtual & In-Person) – Registration closes May 30, 2025 (Fees to be announced)</li><br>
                    Morning & Evening Classes Available – Choose the best time for you!
                </p>

                <div class="card-details">
                    <div class="detail-item">
                        <i class="fas fa-certificate"></i>
                        <span>Morning & Evening Classes Available</span>
                    </div>
                    <!-- <div class="detail-item">
                        <i class="fas fa-clock"></i>
                        <span>1 Year Duration</span>
                    </div> -->
                    <div class="detail-item">
                        <i class="fas fa-users"></i>
                        <span>30 Seats Available Per Intake</span>
                    </div>
                </div>

                <div class="action-section">
                    <!-- <div class="deadline">
                        <i class="fas fa-hourglass-half"></i>
                        Application Closes: April 1, 2024
                    </div> -->
                    <button onclick="window.location.href='./language-coding'" class="apply-button">
                        <i class="fas fa-arrow-circle-right"></i>
                        Register Now (Iyandikishe)
                    </button>
                </div>
            </div>
            <!-- <div class="status-tag" style="background: rgba(255, 87, 34, 0.15); color: #FF5722;"> -->
            <div class="status-tag">
                Open
            </div>
        </div>
    </div>

    <!-- ==== Navigation JavaScript ==== -->

</body>

</html>