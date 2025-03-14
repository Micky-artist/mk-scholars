<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --deep-ocean: #153350;
            --crystal-blue: #4ECDC4;
            --sunrise-coral: #FF6B6B;
            --moonlight: rgba(255, 255, 255, 0.9);
            --wave-gradient: linear-gradient(135deg, #153350 0%, #1a446b 100%);
        }

        body {
            background: var(--wave-gradient);
            color: var(--moonlight);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        .neo-glass {
            background: rgba(21, 51, 80, 0.25);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .neo-glass:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }

        .scholar-card {
            position: relative;
            overflow: hidden;
            min-height: 400px;
        }

        .card-image {
            height: 220px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .card-image::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(21, 51, 80, 0) 0%, #153350 90%);
        }

        .floating-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--crystal-blue);
            padding: 6px 15px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .animated-search {
            background: rgba(21, 51, 80, 0.3);
            border: 2px solid var(--crystal-blue);
            padding: 1rem 2rem;
            border-radius: 30px;
            transition: all 0.3s;
        }

        .animated-search:focus {
            background: rgba(21, 51, 80, 0.5);
            border-color: var(--sunrise-coral);
        }

        .bubble-filter {
            background: rgba(78, 205, 196, 0.1);
            border: 1px solid var(--crystal-blue);
            border-radius: 15px;
            padding: 8px 20px;
            margin: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .bubble-filter.active {
            background: var(--crystal-blue);
            color: var(--deep-ocean);
        }

        .apply-pulse {
            background: var(--sunrise-coral);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .apply-pulse:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(255, 107, 107, 0.4);
        }

        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .shape {
            position: absolute;
            background: rgba(78, 205, 196, 0.1);
            border-radius: 50%;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        @media (max-width: 768px) {
            .card-image {
                height: 180px;
            }
            
            .scholar-card {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <main class="container-fluid py-5">
        <div class="container">
            <!-- Hero Section -->
            <div class="neo-glass p-5 mb-5 text-center position-relative overflow-hidden">
                <div class="floating-shapes">
                    <div class="shape" style="width: 120px; height: 120px; top: 20%; left: 10%"></div>
                    <div class="shape" style="width: 80px; height: 80px; top: 60%; right: 15%"></div>
                </div>
                <h1 class="display-4 fw-bold mb-3">Discover Your Future 🌊</h1>
                <p class="lead mb-4">Explore 500+ international scholarships and funding opportunities</p>
                <div class="animated-search">
                    <input type="text" class="form-control bg-transparent border-0 text-white" 
                           placeholder="🔍 Search by field, country, or university...">
                </div>
            </div>

            <!-- Filter Section -->
            <div class="d-flex flex-wrap mb-4">
                <div class="bubble-filter active">All</div>
                <div class="bubble-filter">STEM</div>
                <div class="bubble-filter">Arts</div>
                <div class="bubble-filter">Business</div>
                <div class="bubble-filter">Full Funding</div>
            </div>

            <!-- Scholarship Grid -->
            <div class="row g-4">
                <!-- Scholarship Card 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="neo-glass scholar-card">
                        <div class="card-image" 
                             style="background-image: url('https://source.unsplash.com/random/800x600?university')">
                            <div class="floating-tag">Full Scholarship</div>
                        </div>
                        <div class="p-4">
                            <h4 class="mb-3">Global Tech Innovators</h4>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <i class="fas fa-globe me-2"></i>USA
                                    <i class="fas fa-coins ms-3 me-2"></i>$45,000
                                </div>
                                <div class="text-crystal-blue">
                                    <i class="fas fa-clock"></i> 15d left
                                </div>
                            </div>
                            <button class="apply-pulse w-100">
                                Apply Now <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Scholarship Card 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="neo-glass scholar-card">
                        <div class="card-image" 
                             style="background-image: url('https://source.unsplash.com/random/800x600?graduation')">
                            <div class="floating-tag">Partial Funding</div>
                        </div>
                        <div class="p-4">
                            <h4 class="mb-3">Women in Engineering</h4>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <i class="fas fa-globe me-2"></i>Germany
                                    <i class="fas fa-coins ms-3 me-2"></i>$25,000
                                </div>
                                <div class="text-crystal-blue">
                                    <i class="fas fa-clock"></i> 30d left
                                </div>
                            </div>
                            <button class="apply-pulse w-100">
                                Apply Now <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Scholarship Card 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="neo-glass scholar-card">
                        <div class="card-image" 
                             style="background-image: url('https://source.unsplash.com/random/800x600?campus')">
                            <div class="floating-tag">Research Grant</div>
                        </div>
                        <div class="p-4">
                            <h4 class="mb-3">Sustainable Futures</h4>
                            <div class="d-flex justify-content-between mb-3">
                                <div>
                                    <i class="fas fa-globe me-2"></i>Canada
                                    <i class="fas fa-coins ms-3 me-2"></i>$60,000
                                </div>
                                <div class="text-crystal-blue">
                                    <i class="fas fa-clock"></i> 7d left
                                </div>
                            </div>
                            <button class="apply-pulse w-100">
                                Apply Now <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Interactive Filters
        document.querySelectorAll('.bubble-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                document.querySelector('.bubble-filter.active').classList.remove('active');
                this.classList.add('active');
            });
        });

        // Animated Apply Buttons
        document.querySelectorAll('.apply-pulse').forEach(button => {
            button.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 100);
            });
        });
    </script>
</body>
</html>