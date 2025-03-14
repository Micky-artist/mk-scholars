<?php
session_start();
include('../dbconnection/connection.php');
include('../php/validateSession.php');

// Fetch scholarships from the database
$query = "SELECT scholarshipId, scholarshipTitle, scholarshipDetails, scholarshipUpdateDate, 
          scholarshipImage, amount, country FROM scholarships WHERE scholarshipStatus = 'active'";
$result = mysqli_query($conn, $query);
$scholarships = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

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
    <!-- Include your existing sidebar code here -->

    <main class="col-md-9 col-lg-10 main-content p-4">
        <div class="container">
            <h2 class="mb-4 fw-bold text-success">Available Scholarships 🌱</h2>
            
            <!-- Search Bar -->
            <div class="glass-panel p-3 mb-4">
                <input type="text" class="form-control" placeholder="🔍 Search scholarships..." id="searchInput">
            </div>

            <!-- Scholarship Grid -->
            <div class="row" id="scholarshipGrid">
                <?php foreach ($scholarships as $scholarship): ?>
                    <div class="col-md-6 col-lg-4 scholarship-item">
                        <div class="glass-panel scholarship-card" 
                             data-id="<?= $scholarship['scholarshipId'] ?>">
                            <img src="<?= $scholarship['scholarshipImage'] ?>" 
                                 alt="<?= $scholarship['scholarshipTitle'] ?>">
                            <h5 class="mt-3"><?= $scholarship['scholarshipTitle'] ?></h5>
                            <p><?= substr($scholarship['scholarshipDetails'], 0, 100) ?>...</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge"><?= $scholarship['country'] ?></span>
                                <span class="text-success fw-bold">$<?= $scholarship['amount'] ?></span>
                            </div>
                            <button class="apply-btn w-100 mt-3" 
                                    onclick="applyForScholarship(<?= $scholarship['scholarshipId'] ?>)">
                                Apply Now
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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

        // Apply for Scholarship Function
        function applyForScholarship(scholarshipId) {
            if (confirm('Are you sure you want to apply for this scholarship?')) {
                fetch('apply_scholarship.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ scholarshipId: scholarshipId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Application submitted successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>