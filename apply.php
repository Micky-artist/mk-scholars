<?php
// Include session configuration for persistent sessions
include("./config/session.php");
include('./dbconnection/connection.php');
include('./php/validateSession.php');

// Initialize variables
$scholarshipId = isset($_GET['scholarshipId']) ? intval($_GET['scholarshipId']) : null;
$searchQuery = isset($_POST['searchQuery']) ? trim($_POST['searchQuery']) : '';
$scholarshipData = null;
$msg = '';
$class = '';

// Pagination settings
$records_per_page = 9; // Reduced for better layout
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $records_per_page;

// Initialize the base query for scholarship listing
$query = "SELECT s.*, c.CountryName FROM scholarships s 
          JOIN countries c ON s.country = c.countryId 
          WHERE s.scholarshipStatus != 0";
$params = [];
$types = "";
$resultHeading = "";

// Handle country filter
if (isset($_GET['i']) && !empty($_GET['i']) && is_numeric($_GET['i']) &&
    isset($_GET['Country_name']) && !empty($_GET['Country_name'])) {
    
    $countryId = (int)$_GET['i'];
    $query .= " AND s.country = ?";
    $params[] = $countryId;
    $types .= "i";
    $resultHeading = "<h5>Showing results of \"" . htmlspecialchars($_GET['Country_name'], ENT_QUOTES, 'UTF-8') . "\"</h5><br>";
}
// Handle search text
elseif (isset($_GET['search']) && isset($_GET['searchText']) && !empty($_GET['searchText'])) {
    $search = $_GET['searchText'];
    $query .= " AND s.scholarshipDetails LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
    $resultHeading = "<h5>Showing results of \"" . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . "\"</h5><br>";
}
// Handle key search
elseif (isset($_GET['key']) && !empty($_GET['key'])) {
    $key = $_GET['key'];
    $query .= " AND s.scholarshipDetails LIKE ?";
    $params[] = "%$key%";
    $types .= "s";
    $resultHeading = "<h5>Showing results of \"" . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . "\"</h5><br>";
}

// Add the ordering to all queries
$query .= " ORDER BY s.scholarshipId DESC";

// Query for counting total records (for pagination)
$count_query = str_replace("SELECT s.*, c.CountryName", "SELECT COUNT(*) as total", $query);
$count_stmt = $conn->prepare($count_query);

// Bind parameters if there are any
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}

$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_records = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_records / $records_per_page);
$count_stmt->close();

// Add LIMIT clause for pagination
$query .= " LIMIT ?, ?";
$params[] = $offset;
$params[] = $records_per_page;
$types .= "ii"; // Two integer parameters for LIMIT

// Prepare and execute the statement for scholarship listing
$stmt = $conn->prepare($query);

// Bind parameters if there are any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$selectScholarships = $stmt->get_result();
$stmt->close();

// Fetch specific scholarship data if scholarshipId is provided
if ($scholarshipId) {
    $query = "SELECT s.*, c.* 
              FROM scholarships s 
              JOIN countries c ON s.country = c.countryId 
              WHERE s.scholarshipId = ?";
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
    if (!$scholarshipData) {
        $msg = "No scholarship selected.";
        $class = "alert alert-danger";
    } else {
        $userId = $_SESSION['userId'];
        $applicationId = (int)$scholarshipData['scholarshipId'];
        $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';

        $amountDue = isset($scholarshipData['amount']) ? floatval($scholarshipData['amount']) : 0;

        if ($amountDue <= 0) {
            // Free application: insert immediately
            $requestDate = date('Y-m-d');
            $requestTime = date('H:i:s');
            $status = 0; // unseen
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
        } else {
            // Paid application: redirect to checkout for scholarship payment
            // Persist comments temporarily in session so we can save after successful payment
            $_SESSION['pending_application_comments'] = $comments;
            header("Location: ./payment/checkout.php?scholarshipId=" . urlencode($applicationId));
            exit;
        }
    }
}

// Helper function to generate pagination links preserving existing GET parameters
function generatePaginationLink($page_num)
{
    $params = $_GET;
    $params['page'] = $page_num;
    return '?' . http_build_query($params);
}
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
            --primary-color: #0E77C2;
            --secondary-color: #083352;
            --accent-color: #4CAF50;
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --text-primary: #1f2937;
            --text-secondary: #4b5563;
            --border-color: #e5e7eb;
            --shadow-light: 0 2px 4px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-heavy: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        [data-theme="dark"] {
            --bg-primary: #111827;
            --bg-secondary: #1f2937;
            --text-primary: #f9fafb;
            --text-secondary: #9ca3af;
            --border-color: #374151;
        }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background 0.3s, color 0.3s;
        }
        
        .sidebar {
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            width: 250px;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0 !important;
            }
        }

        /* Application Form Styling */
        .application-form-container {
            background: var(--bg-secondary);
            border-radius: 20px;
            box-shadow: var(--shadow-heavy);
            margin-bottom: 2rem;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .form-header h2 {
            margin: 0;
            font-weight: 600;
        }

        .form-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }

        .form-content {
            padding: 2rem;
        }

        .scholarship-preview {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .scholarship-preview h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .fee-badge {
            background: var(--accent-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            display: inline-block;
        }

        /* Scholarship Grid Styling */
        .scholarships-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .scholarship-item {
            background: var(--bg-secondary);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            cursor: pointer;
            position: relative;
        }

        .scholarship-item:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-heavy);
        }

        .scholarship-item.selected {
            border: 3px solid var(--primary-color);
            box-shadow: 0 0 0 3px rgba(14, 119, 194, 0.1);
        }

        .scholarship-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .scholarship-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .scholarship-item:hover .scholarship-image img {
            transform: scale(1.05);
        }

        .scholarship-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .scholarship-item:hover .scholarship-overlay {
            opacity: 1;
        }

        .select-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .select-button:hover {
            background: var(--secondary-color);
            transform: scale(1.05);
        }

        .scholarship-content {
            padding: 1.5rem;
        }

        .scholarship-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            line-height: 1.4;
        }

        .scholarship-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .scholarship-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: var(--text-secondary);
        }

        .scholarship-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .scholarship-fee {
            background: var(--accent-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Sidebar Styling */
        .filters-sidebar {
            background: var(--bg-secondary);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--border-color);
        }

        .filter-section {
            margin-bottom: 2rem;
        }

        .filter-section h6 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }

        .filter-section input {
            border-radius: 10px;
            border: 1px solid var(--border-color);
            background: var(--bg-primary);
            color: var(--text-primary);
            padding: 0.75rem;
        }

        .search-btn {
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: background 0.3s ease;
        }

        .search-btn:hover {
            background: var(--secondary-color);
        }

        .filter-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .filter-list li {
            margin-bottom: 0.5rem;
        }

        .filter-list a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 0.5rem;
            border-radius: 8px;
            display: block;
            transition: all 0.3s ease;
        }

        .filter-list a:hover {
            background: var(--primary-color);
            color: white;
        }

        /* Pagination Styling */
        .pagination-container {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination li a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--bg-secondary);
            color: var(--text-secondary);
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .pagination li a:hover,
        .pagination li a.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Alert Styling */
        .alert {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .scholarships-grid {
                grid-template-columns: 1fr;
            }
            
            .form-content {
                padding: 1rem;
            }
            
            .form-header {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body data-theme="light">
    <!-- Theme Toggle Button -->
    <button style="color: orange;" class="btn btn-secondary theme-toggle glass-panel">
        <i class="fas fa-moon"></i>
    </button>

    <!-- Include your existing sidebar code here -->
    <?php 
    // Set current page for navigation highlighting
    $_GET['page'] = 'apply';
    include("./partials/universalNavigation.php"); 
    ?>

    <main class="col-md-9 col-lg-10 main-content p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <button class="btn btn-light d-md-none glass-panel sidebar-toggle" type="button">
                <i class="fas fa-bars"></i>
            </button>
            <h3 class="mb-0">Scholarship Application</h3>
            <div class="glass-panel px-3 py-2 notification-btn" style="cursor: pointer;">
                <i class="fas fa-bell text-muted"></i>
            </div>
        </div>

        <!-- Selected Scholarship Application Form - Displayed at the top -->
        <?php if ($scholarshipData): ?>
            <div class="application-form-container">
                <div class="form-header">
                    <h2><i class="fas fa-graduation-cap me-2"></i>Application Request</h2>
                    <p>Submit your application for professional assistance</p>
                </div>
                
                <div class="form-content">
                    <div class="scholarship-preview">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="https://admin.mkscholars.com/uploads/posts/<?php echo $scholarshipData['scholarshipImage']; ?>" 
                                     alt="Scholarship" class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                            </div>
                            <div class="col-md-7">
                                <h4><?= $scholarshipData['scholarshipTitle'] ?></h4>
                                <div class="d-flex gap-3 text-muted">
                                    <span><i class="fas fa-calendar me-1"></i><?= $scholarshipData['scholarshipUpdateDate'] ?></span>
                                    <span><i class="fas fa-globe me-1"></i><?= $scholarshipData['CountryName'] ?></span>
                                </div>
                            </div>
                            <div class="col-md-3 text-end">
                                <div class="fee-badge">
                                    $<?= $scholarshipData['amount'] ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="mb-4">
                            <label class="form-label fw-bold">Additional Comments <small class="text-muted">(max 200 words)</small></label>
                            <textarea class="form-control" rows="4" maxlength="200" id="comments" name="comments" 
                                      placeholder="Tell us about your background, goals, and any specific requirements..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="termsCheck">
                                <label class="form-check-label" for="termsCheck">
                                    I agree to <a href="./terms-and-conditions" target="_blank">terms & conditions</a>
                                </label>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-secondary" onclick="clearSelection()">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                                <button type="submit" name="submit_application" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                    <i class="fas fa-paper-plane me-2"></i>Submit Application
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Alert Messages -->
        <div class="<?php echo $class; ?>">
            <?php echo $msg; ?>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <!-- Search Results Heading -->
                    <?php if (!empty($resultHeading)): ?>
                        <div class="mb-3">
                            <?php echo $resultHeading; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Scholarship Grid -->
                    <div class="scholarships-grid">
                        <?php
                        $scholarships = [];
                        if ($selectScholarships->num_rows > 0) {
                            while ($row = mysqli_fetch_assoc($selectScholarships)) {
                                $scholarships[] = $row;
                            }
                        }

                        if (count($scholarships) > 0) {
                            foreach ($scholarships as $s) {
                                $isSelected = ($scholarshipId == $s['scholarshipId']);
                        ?>
                                <div class="scholarship-item <?php echo $isSelected ? 'selected' : ''; ?>" 
                                     onclick="selectScholarship(<?php echo $s['scholarshipId']; ?>)">
                                    <div class="scholarship-image">
                                        <img src="https://admin.mkscholars.com/uploads/posts/<?php echo htmlspecialchars($s['scholarshipImage'], ENT_QUOTES) ?>" 
                                             alt="<?php echo htmlspecialchars($s['scholarshipTitle'], ENT_QUOTES) ?>">
                                        <div class="scholarship-overlay">
                                            <button class="select-button">
                                                <i class="fas fa-hand-pointer me-2"></i>Select This Scholarship
                                            </button>
                                        </div>
                                    </div>
                                    <div class="scholarship-content">
                                        <h3 class="scholarship-title">
                                            <?php echo htmlspecialchars($s['scholarshipTitle'], ENT_QUOTES) ?>
                                        </h3>
                                        <p class="scholarship-description">
                                            <?php echo htmlspecialchars($s['scholarshipDetails'], ENT_QUOTES) ?>
                                        </p>
                                        <div class="scholarship-meta">
                                            <div class="scholarship-date">
                                                <i class="fas fa-calendar"></i>
                                                <span><?php echo $s['scholarshipUpdateDate'] ?></span>
                                            </div>
                                            <div class="scholarship-fee">
                                                $<?php echo $s['amount'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                        ?>
                            <div class="col-12 text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h4>No scholarships found</h4>
                                <p class="text-muted">Try adjusting your search criteria or filters</p>
                            </div>
                        <?php
                        }
                        ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="pagination-container">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li><a href="<?php echo generatePaginationLink($page - 1); ?>" aria-label="Previous page">
                                        <i class="fas fa-chevron-left"></i>
                                    </a></li>
                                <?php endif; ?>

                                <?php
                                $start_page = max(1, $page - 2);
                                $end_page = min($total_pages, $page + 2);

                                if ($start_page > 1) {
                                    echo '<li><a href="' . generatePaginationLink(1) . '">1</a></li>';
                                    if ($start_page > 2) {
                                        echo '<li><span class="px-3">...</span></li>';
                                    }
                                }

                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                    <li><a href="<?php echo generatePaginationLink($i); ?>" 
                                          class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a></li>
                                <?php endfor; ?>

                                <?php
                                if ($end_page < $total_pages) {
                                    if ($end_page < $total_pages - 1) {
                                        echo '<li><span class="px-3">...</span></li>';
                                    }
                                    echo '<li><a href="' . generatePaginationLink($total_pages) . '">' . $total_pages . '</a></li>';
                                }
                                ?>

                                <?php if ($page < $total_pages): ?>
                                    <li><a href="<?php echo generatePaginationLink($page + 1); ?>" aria-label="Next page">
                                        <i class="fas fa-chevron-right"></i>
                                    </a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Filters Sidebar -->
                <div class="col-md-3">
                    <div class="filters-sidebar">
                        <div class="filter-section">
                            <h6><i class="fas fa-search me-2"></i>Search</h6>
                            <form method="get" class="d-flex gap-2">
                                <input type="text" name="searchText" class="form-control" 
                                       placeholder="Search scholarships..." 
                                       value="<?php echo isset($_GET['searchText']) ? htmlspecialchars($_GET['searchText']) : ''; ?>">
                                <button class="search-btn" type="submit" name="search">
                                    <i class="fa fa-search"></i>
                                </button>
                            </form>
                        </div>

                        <div class="filter-section">
                            <h6><i class="fas fa-globe me-2"></i>Countries</h6>
                            <ul class="filter-list">
                                <li><a href="apply" class="fw-bold">Show All (Reset)</a></li>
                                <?php include("./php/selectCountriesLI.php") ?>
                            </ul>
                        </div>

                        <div class="filter-section">
                            <h6><i class="fas fa-tags me-2"></i>Tags</h6>
                            <ul class="filter-list">
                                <?php
                                $selectTags = mysqli_query($conn, "SELECT * FROM PostTags WHERE TagStatus = 1");
                                if ($selectTags->num_rows > 0) {
                                    while ($tagData = mysqli_fetch_assoc($selectTags)) {
                                ?>
                                        <li><a href="?key=<?php echo htmlspecialchars($tagData['TagValue'], ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo htmlspecialchars($tagData['TagName'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a></li>
                                <?php
                                    }
                                } else {
                                ?>
                                    <li>No tags available</li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Theme Toggle
        const themeToggle = document.querySelector('.theme-toggle');
        const body = document.body;
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
            const currentTheme = body.getAttribute('data-theme');
            themeToggle.innerHTML = currentTheme === 'light' ?
                '<i class="fas fa-moon"></i>' :
                '<i class="fas fa-sun"></i>';
        }

        // Scholarship Selection
        function selectScholarship(scholarshipId) {
            window.location.href = `?scholarshipId=${scholarshipId}`;
        }

        function clearSelection() {
            window.location.href = 'apply';
        }

        // Form Validation
        document.getElementById('termsCheck')?.addEventListener('change', function() {
            document.getElementById('submitBtn').disabled = !this.checked;
        });

        document.getElementById('comments')?.addEventListener('input', function() {
            const words = this.value.trim().split(/\s+/);
            if (words.length > 200) {
                this.value = words.slice(0, 200).join(' ');
            }
        });

        // Smooth scroll to form when scholarship is selected
        <?php if ($scholarshipData): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const formContainer = document.querySelector('.application-form-container');
            if (formContainer) {
                formContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
        <?php endif; ?>
    </script>
    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const sidebar = document.querySelector('.sidebar');
            const sidebarToggle = document.querySelector('.sidebar-toggle');

            if (sidebar && sidebarToggle) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                });
            }

            // Hide sidebar on mobile when clicking outside
            document.addEventListener('click', function (event) {
                if (
                    window.innerWidth < 768 &&
                    sidebar &&
                    !sidebar.contains(event.target) &&
                    !sidebarToggle.contains(event.target)
                ) {
                    sidebar.classList.remove('active');
                }
            });
        });
    </script>

</body>

</html>