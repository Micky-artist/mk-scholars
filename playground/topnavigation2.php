<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Horizon</title>
    <link href="https://unpkg.com/css-doodle@0.38.3/css-doodle.min.js" rel="stylesheet">
    <style>
        :root {
            --primary: #7F5AF0;
            --secondary: #2CB67D;
            --accent: #FF8906;
            --bg: #0F0E17;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            cursor: none;
        }

        body {
            background: var(--bg);
            color: #FFFFFE;
            font-family: 'Space Grotesk', sans-serif;
            overflow-x: hidden;
        }

        .custom-cursor {
            position: fixed;
            width: 20px;
            height: 20px;
            border: 2px solid var(--accent);
            border-radius: 50%;
            pointer-events: none;
            transition: transform 0.3s, opacity 0.3s;
            z-index: 9999;
        }

        /* Quantum Particle Background */
        .quantum-bg {
            position: fixed;
            width: 100vw;
            height: 100vh;
            z-index: -1;
            opacity: 0.3;
        }

        /* Holographic Navigation */
        nav {
            position: fixed;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 14, 23, 0.8);
            padding: 1.5rem 3rem;
            border-radius: 50px;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(127, 90, 240, 0.3);
            box-shadow: 0 0 30px rgba(127, 90, 240, 0.2);
            display: flex;
            gap: 3rem;
            z-index: 1000;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        nav:hover {
            box-shadow: 0 0 50px rgba(127, 90, 240, 0.4);
            transform: translateX(-50%) scale(1.05);
        }

        .nav-link {
            color: #FFFFFE;
            text-decoration: none;
            position: relative;
            padding: 0.5rem 1rem;
            transition: 0.3s ease;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--secondary);
            transition: 0.3s ease;
        }

        .nav-link:hover::before {
            width: 100%;
        }

        /* Floating Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 4rem;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: float 6s ease-in-out infinite;
        }

        .gradient-orb {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--primary), transparent 60%);
            filter: blur(100px);
            opacity: 0.3;
            animation: orb-move 20s infinite alternate;
        }

        /* Neural Network Connection Animation */
        .neural-path {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            stroke-dasharray: 2000;
            stroke-dashoffset: 2000;
            animation: draw 20s linear infinite;
        }

        /* Holographic Card Grid */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 4rem;
        }

        .holo-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 2rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(127, 90, 240, 0.2);
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .holo-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .holo-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 200%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(127, 90, 240, 0.1),
                transparent
            );
            transition: 0.4s;
        }

        .holo-card:hover::before {
            left: 100%;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }

        @keyframes orb-move {
            0% { transform: translate(-30%, -30%); }
            100% { transform: translate(30%, 30%); }
        }

        @keyframes draw {
            to { stroke-dashoffset: 0; }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                padding: 1rem 2rem;
                gap: 1.5rem;
                top: 1rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .card-grid {
                grid-template-columns: 1fr;
                padding: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="custom-cursor"></div>

    <nav>
        <a href="#" class="nav-link">Home</a>
        <a href="#" class="nav-link">Applications</a>
        <a href="#" class="nav-link">Courses</a>
        <a href="#" class="nav-link">Dashboard</a>
    </nav>

    <section class="hero">
        <div class="gradient-orb"></div>
        <svg class="neural-path" viewBox="0 0 100 100">
            <path d="M10 10 Q 20 50, 30 30 T 60 70 T 90 10" stroke="rgba(127, 90, 240, 0.2)" fill="none"/>
        </svg>
        
        <div class="hero-content">
            <h1>Bridge to Your<br>Academic Future</h1>
            <div class="card-grid">
                <div class="holo-card">
                    <h3>Global Scholarships</h3>
                    <p>Discover opportunities across 50+ countries</p>
                </div>
                <div class="holo-card">
                    <h3>Smart Matching</h3>
                    <p>AI-powered scholarship recommendations</p>
                </div>
                <div class="holo-card">
                    <h3>Instant Applications</h3>
                    <p>One-click submission to multiple programs</p>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Custom Cursor
        const cursor = document.querySelector('.custom-cursor');
        document.addEventListener('mousemove', (e) => {
            cursor.style.left = `${e.clientX - 10}px`;
            cursor.style.top = `${e.clientY - 10}px`;
        });

        // Dynamic Background
        document.addEventListener('DOMContentLoaded', () => {
            const quantumBg = document.createElement('css-doodle');
            quantumBg.innerHTML = `
                :doodle {
                    @grid: 50x1 / 100vmax;
                    position: fixed;
                    z-index: -1;
                }
                @size: @rand(2, 8)px;
                background: hsl(@rand(240, 300), 70%, 70%);
                position: absolute;
                top: @rand(0, 100)%;
                left: @rand(0, 100)%;
                animation: float @rand(5, 15)s infinite linear;
                @keyframes float {
                    0% { transform: translateY(0); opacity: 0; }
                    50% { opacity: 1; }
                    100% { transform: translateY(-100vh); opacity: 0; }
                }
            `;
            document.body.appendChild(quantumBg);
        });

        // Card Hover Effect
        document.querySelectorAll('.holo-card').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--x', `${x}px`);
                card.style.setProperty('--y', `${y}px`);
            });
        });
    </script>
</body>
</html>