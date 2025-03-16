<?php
session_start();
include('./dbconnection/connection.php');
include('./php/validateSession.php');

// Initialize variables
$scholarshipId = isset($_GET['scholarshipId']) ? intval($_GET['scholarshipId']) : null;
$searchQuery = isset($_POST['searchQuery']) ? trim($_POST['searchQuery']) : '';
$scholarshipData = null;
$msg = '';
$class = '';

// Fetch scholarship data if scholarshipId is provided or searched
if ($scholarshipId || $searchQuery) {
    $query = "SELECT * FROM scholarships WHERE scholarshipId = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $scholarshipId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $scholarshipData = $result->fetch_assoc();
        } else {
            $msg = "No scholarship found.";
            $class = "alert alert-danger";
        }
        $stmt->close();
    } else {
        $msg = "Database error. Please try again later.";
        $class = "alert alert-danger";
        error_log("Database error: " . $conn->error);
    }
}

// Handle form submission
if (isset($_POST['submit_application']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    //     $msg = "Invalid CSRF token.";
    //     $class = "alert alert-danger";
    // } else {
        if (!$scholarshipData) {
            $msg = "No scholarship selected.";
            $class = "alert alert-danger";
        } else {
            $userId = $_SESSION['userId']; // Assuming userId is stored in the session
            $applicationId = $scholarshipData['scholarshipId'];
            $requestDate = date('Y-m-d');
            $requestTime = date('H:i:s');
            $status = 0; // Default status is 0 (unseen)
            $comments = isset($_POST['comments']) ? trim($_POST['comments']) : ''; // Capture comments

            // Insert into ApplicationRequests table
            $insertStmt = $conn->prepare("INSERT INTO ApplicationRequests (UserId, ApplicationId, RequestDate, RequestTime, Status, Comments) VALUES (?, ?, ?, ?, ?, ?)");
            if (!$insertStmt) {
                $msg = "System error. Please try again later.";
                $class = "alert alert-danger";
                error_log("Database error: " . $conn->error);
            } else {
                $insertStmt->bind_param("iissss", $userId, $applicationId, $requestDate, $requestTime, $status, $comments);
                if ($insertStmt->execute()) {
                    $msg = "Application submitted successfully!";
                    $class = "alert alert-success";
                } else {
                    $msg = "Submission failed. Please try again.";
                    $class = "alert alert-danger";
                    error_log("Insert error: " . $insertStmt->error);
                }
                $insertStmt->close();
            }
        }
    // }
}

// Generate CSRF token for the form if not exists
// if (!isset($_SESSION['csrf_token'])) {
//     $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scholarship Portal</title>
    <link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --grass-green: #4CAF50;
            --fresh-green: #81C784;
            --leaf-green: #689F38;
            --progress-bar: #FFD54F;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa, #e8f5e9);
            min-height: 100vh;
        }

        .application-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            overflow: hidden;
        }

        .app-image-container {
            height: 300px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .search-container {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-results {
            position: absolute;
            width: 100%;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
        }

        .search-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.2s;
        }

        .search-item:hover {
            background: #f8f9fa;
        }

        .alert {
            padding: 10px;
            margin: 5px 0;
            border-radius: 10px;
            font-size: 12px;
        }

        .alert-danger {
            border: .5px solid #c41f10;
            background-color: #fcd5d2;
        }

        .alert-success {
            border: .5px solid #325737;
            background-color: #cffad4;
        }

        @media (max-width: 768px) {
            .app-image-container {
                height: 200px;
            }

            .application-card {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <!-- Include your existing sidebar code here -->
    <?php include("./partials/dashboardNavigation.php"); ?>

    <main class="col-md-9 col-lg-10 main-content p-4">
        <div class="container">
            <div class="search-container">
                <input type="text" class="form-control form-control-lg" placeholder="Type to search for scholarships..." id="searchInput">
                <div class="search-results" id="searchResults"></div>
            </div>

            <div class="<?php echo $class; ?>">
                <?php echo $msg; ?>
            </div>

            <?php if ($scholarshipData): ?>
                <div class="application-card">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <div class="app-image-container" style="background-image: url('<?= $scholarshipData['scholarshipImage'] ?>')">
                                <div class="status-badge text-success">Active</div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h3 class="fw-bold mb-1"><?= $scholarshipData['scholarshipTitle'] ?></h3>
                                        <div class="d-flex gap-2 text-muted">
                                            <span><i class="fas fa-calendar me-1"></i><?= $scholarshipData['scholarshipUpdateDate'] ?></span>
                                            <span><i class="fas fa-globe me-1"></i><?= $scholarshipData['country'] ?></span>
                                        </div>
                                    </div>
                                    <button class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </button>
                                </div>

                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar bg-success" style="width: 65%"></div>
                                </div>

                                <div class="bg-light p-3 rounded mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">Application Assistance Fee</h6>
                                            <small class="text-muted">Professional support fee</small>
                                        </div>
                                        <span class="h5 text-success mb-0">$<?= $scholarshipData['amount'] ?></span>
                                    </div>
                                </div>

                                <form method="post">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Additional Comments <small class="text-muted">(max 200 words)</small></label>
                                        <textarea class="form-control" rows="3" maxlength="200" id="comments" name="comments"></textarea>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="termsCheck">
                                            <label class="form-check-label" for="termsCheck">I agree to terms & conditions</label>
                                        </div>
                                        <button type="submit" name="submit_application" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                            <i class="fas fa-paper-plane me-2"></i>Submit Application
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Real-time Search Implementation
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');
        let debounceTimer;

        searchInput.addEventListener('input', function (e) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                if (this.value.length > 2) {
                    fetchScholarships(this.value);
                }
            }, 300);
        });

        async function fetchScholarships(searchTerm) {
            try {
                const response = await fetch(`./php/search_scholarships.php?q=${encodeURIComponent(searchTerm)}`);
                const results = await response.json();
                displayResults(results);
            } catch (error) {
                console.error('Search error:', error);
            }
        }

        function displayResults(results) {
            searchResults.innerHTML = '';
            if (results.length > 0) {
                results.forEach(scholarship => {
                    const item = document.createElement('div');
                    item.className = 'search-item';
                    item.innerHTML = `
                        <h6>${scholarship.scholarshipTitle}</h6>
                        <small class="text-muted">${scholarship.country} • $${scholarship.amount}</small>
                    `;
                    item.addEventListener('click', () => {
                        window.location.href = `?scholarshipId=${scholarship.scholarshipId}`;
                    });
                    searchResults.appendChild(item);
                });
                searchResults.style.display = 'block';
            } else {
                searchResults.style.display = 'none';
            }
        }

        // Form Validation
        document.getElementById('termsCheck').addEventListener('change', function () {
            document.getElementById('submitBtn').disabled = !this.checked;
        });

        document.getElementById('comments').addEventListener('input', function () {
            const words = this.value.trim().split(/\s+/);
            if (words.length > 200) {
                this.value = words.slice(0, 200).join(' ');
            }
        });
    </script>
</body>
</html>