<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Portal</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #0a192f;
            color: #fff;
        }

        /* Sticky Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.5rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(10, 25, 47, 0.9);
            backdrop-filter: blur(10px);
            z-index: 1000;
            transition: 0.3s all ease;
        }

        .navbar.sticky {
            background: rgba(10, 25, 47, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .nav-links {
            display: flex;
            gap: 2rem;
        }

        .nav-links a {
            color: #fff;
            text-decoration: none;
            position: relative;
            padding: 0.5rem 0;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #64ffda;
            transition: 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        /* Image Slider */
        .slider-container {
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .slider {
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .slide {
            position: absolute;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: 1s ease;
            background-size: cover;
            background-position: center;
            animation: zoom 20s linear infinite;
        }

        @keyframes zoom {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }

        .slide.active {
            opacity: 1;
        }

        /* Content Overlay */
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 2;
            background: rgba(10, 25, 47, 0.8);
            padding: 3rem;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            max-width: 800px;
            width: 90%;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #64ffda, #00b4d8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .cta-button {
            background: linear-gradient(45deg, #64ffda, #00b4d8);
            color: #0a192f;
            padding: 1rem 2.5rem;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s transform ease;
            margin-top: 2rem;
        }

        .cta-button:hover {
            transform: translateY(-3px);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .hero-content {
                padding: 2rem;
            }

            .menu-toggle {
                display: block;
                color: #fff;
                font-size: 1.5rem;
                cursor: pointer;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h2 class="logo">ScholarConnect</h2>
        <div class="nav-links">
            <a href="#">Home</a>
            <a href="#">Applications</a>
            <a href="#">Courses</a>
            <a href="#">Dashboard</a>
        </div>
        <i class='bx bx-menu menu-toggle'></i>
    </nav>

    <div class="slider-container">
        <div class="slider">
            <div class="slide active" style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS2dhrA2w0kJcS4h5Ww-yipVpz8kCDsLY0M9w&s')"></div>
            <div class="slide" style="background-image: url('https://marketplace.canva.com/EAEB97jvqIY/5/0/1600w/canva-blue-and-pink-classy-photo-cherry-blossom-inspirational-quotes-facebook-cover-vpnA8PdWGCs.jpg')"></div> 
            <div class="slide" style="background-image: url('https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/c547ba3d-624b-4a5d-892e-8458fd9952fa/d5zfrww-29cca157-7322-4da3-a20e-ee62518f8f7c.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7InBhdGgiOiJcL2ZcL2M1NDdiYTNkLTYyNGItNGE1ZC04OTJlLTg0NThmZDk5NTJmYVwvZDV6ZnJ3dy0yOWNjYTE1Ny03MzIyLTRkYTMtYTIwZS1lZTYyNTE4ZjhmN2MuanBnIn1dXSwiYXVkIjpbInVybjpzZXJ2aWNlOmZpbGUuZG93bmxvYWQiXX0.HXmOjvUsAqRSW6tI9C0n4ywXvKjP_e9jC-Psww8cGNQ')"></div>
            <div class="slide" style="background-image: url('https://marketplace.canva.com/EAEB97jvqIY/5/0/1600w/canva-blue-and-pink-classy-photo-cherry-blossom-inspirational-quotes-facebook-cover-vpnA8PdWGCs.jpg')"></div>
        </div>

        <div class="hero-content">
            <h1>Unlock Global Education Opportunities</h1>
            <p>Discover scholarships from 50+ countries with personalized matching</p>
            <button class="cta-button">Explore Scholarships</button>
        </div>
    </div>

    <script>
        // Image Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');

        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }

        setInterval(nextSlide, 4000);

        // Sticky Navigation
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('sticky');
            } else {
                navbar.classList.remove('sticky');
            }
        });

        // Mobile Menu Toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const navLinks = document.querySelector('.nav-links');

        menuToggle.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        });
    </script>
</body>
</html>