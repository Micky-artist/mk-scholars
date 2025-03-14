<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Dashboard</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --primary-green: #4CAF50;
            --hover-green: #45a049;
        }

        body {
            background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
            min-height: 100vh;
            font-family: 'Segoe UI', sans-serif;
        }

        .glass-panel {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            box-shadow: var(--glass-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .glass-panel:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
        }

        .scholarship-card {
            padding: 20px;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .scholarship-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }

        .scholarship-card h5 {
            color: var(--primary-green);
            margin-top: 15px;
        }

        .scholarship-card p {
            color: #555;
            font-size: 0.9rem;
        }

        .badge {
            background: var(--primary-green);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }

        .apply-btn {
            background: var(--primary-green);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            transition: background 0.3s;
        }

        .apply-btn:hover {
            background: var(--hover-green);
        }

        @media (max-width: 768px) {
            .scholarship-card img {
                height: 150px;
            }
        }
    </style>
</head>

<body>
    <main class="container-fluid p-4">
        <div class="container">
            <h2 class="mb-4 fw-bold text-success">Available Scholarships 🌱</h2>
            
            <!-- Search Bar -->
            <div class="glass-panel p-3 mb-4">
                <input type="text" class="form-control" placeholder="🔍 Search scholarships..." id="searchInput">
            </div>

            <!-- Scholarship Grid -->
            <div class="row" id="scholarshipGrid">
                <!-- Scholarship Card 1 -->
                <div class="col-md-6 col-lg-4 scholarship-item">
                    <div class="glass-panel scholarship-card">
                        <img src="https://source.unsplash.com/random/800x600?education" alt="Scholarship Image">
                        <h5 class="mt-3">STEM Scholarship Program</h5>
                        <p>This scholarship supports students pursuing degrees in Science, Technology, Engineering, and Mathematics.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge">USA</span>
                            <span class="text-success fw-bold">$5,000</span>
                        </div>
                        <button class="apply-btn w-100 mt-3">Apply Now</button>
                    </div>
                </div>

                <!-- Scholarship Card 2 -->
                <div class="col-md-6 col-lg-4 scholarship-item">
                    <div class="glass-panel scholarship-card">
                        <img src="https://source.unsplash.com/random/800x600?university" alt="Scholarship Image">
                        <h5 class="mt-3">Global Leadership Award</h5>
                        <p>Awarded to students demonstrating exceptional leadership skills and community involvement.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge">Canada</span>
                            <span class="text-success fw-bold">$7,500</span>
                        </div>
                        <button class="apply-btn w-100 mt-3">Apply Now</button>
                    </div>
                </div>

                <!-- Scholarship Card 3 -->
                <div class="col-md-6 col-lg-4 scholarship-item">
                    <div class="glass-panel scholarship-card">
                        <img src="https://source.unsplash.com/random/800x600?graduation" alt="Scholarship Image">
                        <h5 class="mt-3">Women in Tech Scholarship</h5>
                        <p>Encouraging women to pursue careers in technology and innovation.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge">Germany</span>
                            <span class="text-success fw-bold">$10,000</span>
                        </div>
                        <button class="apply-btn w-100 mt-3">Apply Now</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Real-time Search Functionality
        const searchInput = document.getElementById('searchInput');
        const scholarshipGrid = document.getElementById('scholarshipGrid');
        const scholarshipItems = document.querySelectorAll('.scholarship-item');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            scholarshipItems.forEach(item => {
                const title = item.querySelector('h5').textContent.toLowerCase();
                const description = item.querySelector('p').textContent.toLowerCase();
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Apply Button Interaction
        const applyButtons = document.querySelectorAll('.apply-btn');
        applyButtons.forEach(button => {
            button.addEventListener('click', function() {
                alert('Application submitted successfully!');
            });
        });
    </script>
</body>
</html>