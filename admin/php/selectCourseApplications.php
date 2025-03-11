<?php
session_start();
include("../dbconnections/connection.php");
//mark applications as seen or notseen


// Pagination logic with validation
$limit = 20; // Number of applications per page
$page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT, ['options' => ['default' => 1, 'min_range' => 1]]) : 1;
$offset = ($page - 1) * $limit;

// Search logic with proper sanitization
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_param = "%$search%";

// Filter logic with validation
$valid_filters = ['all', 'English Course', 'French Course', 'German Course', 'Coding Course', 'In-person', 'Online'];
$filter = isset($_GET['filter']) && in_array($_GET['filter'], $valid_filters) ? $_GET['filter'] : 'all';

// Prepare base query with parameterized statements
$base_condition = "1=1";
$params = [];

// Add search condition if present
if (!empty($search)) {
    $base_condition .= " AND (FullNames LIKE ? OR Email LIKE ? OR Phone LIKE ? OR Comment LIKE ?)";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

// Add filter condition if not "all"
if ($filter !== 'all') {
    $base_condition .= " AND ApplicationContent LIKE ?";
    $params[] = "%$filter%";
}

// Fetch total number of applications using prepared statement
$total_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM applicationsSurvey WHERE $base_condition");
if ($params) {
    $total_stmt->bind_param(str_repeat('s', count($params)), ...$params);
}
$total_stmt->execute();
$totalResult = $total_stmt->get_result();
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch applications with pagination, search, and filter using prepared statement
$query = "SELECT * FROM applicationsSurvey WHERE $base_condition ORDER BY SubmitDate DESC, SubmitTime DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);

// Add limit and offset to params
$params[] = $limit;
$params[] = $offset;

// Create the correct type string for bind_param
$types = str_repeat('s', count($params) - 2) . 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$selectApplications = $stmt->get_result();
?>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet"> -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            /* transition: transform 0.2s; */
        }


        .card-header {
            background: linear-gradient(135deg, #4bc2c5, #6dd5fa);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .badge {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .btn-custom {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
        }

        .pagination .page-item.active .page-link {
            background-color: #4bc2c5;
            border-color: #4bc2c5;
        }

        .pagination .page-link {
            color: #4bc2c5;
        }

        .pagination .page-link:hover {
            color: #3aa9ac;
        }

        .search-filter-container {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .scholarship-badge {
            background-color: #ffc107;
            color: #212529;
            margin-right: 5px;
        }

        .application-items {
            list-style: none;
            padding-left: 0;
        }

        .application-items li {
            margin: 5px 0;
        }
    </style>
<!-- </head>
<body class="py-5"> -->
    <!-- <div class="container"> -->
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Applications (<?= $totalRows ?>)</h4>
            </div>
            <div class="card-body">
                <!-- Search Bar and Filter -->
                <div class="search-filter-container">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, email, phone, or comment" value="<?= htmlspecialchars($search) ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="filter" class="form-select">
                                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Courses</option>
                                <option value="English Course" <?= $filter === 'English Course' ? 'selected' : '' ?>>English Course</option>
                                <option value="French Course" <?= $filter === 'French Course' ? 'selected' : '' ?>>French Course</option>
                                <option value="German Course" <?= $filter === 'German Course' ? 'selected' : '' ?>>German Course</option>
                                <option value="Coding Course" <?= $filter === 'Coding Course' ? 'selected' : '' ?>>Coding Course</option>
                                <option value="In-person" <?= $filter === 'In-person' ? 'selected' : '' ?>>In-person</option>
                                <option value="Online" <?= $filter === 'Online' ? 'selected' : '' ?>>Online</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </form>
                </div>

                <!-- Applications List -->
                <div class="row">
                    <?php
                    if ($selectApplications->num_rows > 0) {
                        while ($getApplication = $selectApplications->fetch_assoc()) {
                            $applicationContent = json_decode($getApplication['ApplicationContent'], true);
                            $courses = isset($applicationContent['courses']) ? $applicationContent['courses'] : [];
                            $coursesString = is_array($courses) ? implode(", ", $courses) : (string)$courses;

                            $scholarships = isset($applicationContent['scholarships']) ? $applicationContent['scholarships'] : [];
                            $scholarshipsString = is_array($scholarships) ? implode(", ", $scholarships) : (string)$scholarships;
                    ?>
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h5 class="card-title">Names: <?= htmlspecialchars($getApplication['FullNames']) ?></h5>
                                        <p class="card-text">
                                            <strong>Email:</strong> <?= htmlspecialchars($getApplication['Email']) ?><br>
                                            <strong>Phone:</strong> <?= htmlspecialchars($getApplication['Phone']) ?><br>
                                            <strong>Courses:</strong>
                                            <?php
                                            $applicationContent = htmlspecialchars($getApplication['ApplicationContent']);
                                            if (strpos($applicationContent, ',') !== false) {
                                                $items = array_map('trim', explode(',', $applicationContent));
                                                echo '<ul class="application-items">';
                                                foreach ($items as $item) {
                                                    if (!empty($item)) {
                                                        echo '<li>' . $item . '</li>';
                                                    }
                                                }
                                                echo '</ul>';
                                            } else {
                                                echo '<ul class="application-items">';
                                                echo '<li>' . $applicationContent . '</li>';
                                                echo '</ul>';
                                            }
                                            ?>

                                            <?php if (!empty($scholarships) && is_array($scholarships)): ?>
                                                <br><strong>Scholarships:</strong>
                                                <div class="mt-2 mb-2">
                                                    <?php foreach ($scholarships as $scholarship): ?>
                                                        <span class="badge scholarship-badge"><?= htmlspecialchars($scholarship) ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <strong>Comment:</strong> <?= htmlspecialchars($getApplication['Comment']) ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Submitted on: <?= htmlspecialchars($getApplication['SubmitDate']) ?> at <?= htmlspecialchars($getApplication['SubmitTime']) ?></small>
                                            <!-- <span class="badge bg-<?= $getApplication['applicationStatus'] ? 'success' : 'warning' ?>">
                                                <?= $getApplication['applicationStatus'] ? 'Active' : 'Pending' ?>
                                            </span> -->
                                        </div>
                                        <form class="mt-3">
                                            <input type="hidden" name="AppId" value="<?= htmlspecialchars($getApplication['applicationId']) ?>">
                                            <button class="btn btn-sm btn-<?= $getApplication['applicationStatus'] ? 'warning' : 'success' ?> mark-status" name="<?= $getApplication['applicationStatus'] ? 'MarkasSeen' : 'MarkasUnseen' ?>" data-id="<?= $getApplication['applicationId'] ?>" data-status="<?= $getApplication['applicationStatus'] ? '0' : '1' ?>">
                                                <?= $getApplication['applicationStatus'] ? 'Mark as Unseen' : 'Mark as Seen' ?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo '<div class="col-12 text-center py-5">
                                <div class="alert alert-info">No applications found</div>
                              </div>';
                    }
                    ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-4">
                            <?php if ($page > 1) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode(htmlspecialchars($search)) ?>&filter=<?= urlencode(htmlspecialchars($filter)) ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);

                            if ($start > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode(htmlspecialchars($search)) . '&filter=' . urlencode(htmlspecialchars($filter)) . '">1</a></li>';
                                if ($start > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            for ($i = $start; $i <= $end; $i++) : ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode(htmlspecialchars($search)) ?>&filter=<?= urlencode(htmlspecialchars($filter)) ?>"><?= $i ?></a>
                                </li>
                            <?php endfor;

                            if ($end < $totalPages) {
                                if ($end < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&search=' . urlencode(htmlspecialchars($search)) . '&filter=' . urlencode(htmlspecialchars($filter)) . '">' . $totalPages . '</a></li>';
                            }
                            ?>

                            <?php if ($page < $totalPages) : ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode(htmlspecialchars($search)) ?>&filter=<?= urlencode(htmlspecialchars($filter)) ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    <!-- </div> -->

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
    <script>
        // Handle mark as seen/unseen
        document.querySelectorAll('.mark-status').forEach(button => {
            button.addEventListener('click', function () {
                const applicationId = this.getAttribute('data-id');
                const newStatus = this.getAttribute('data-status');

                fetch(`./php/actions?a=updateApplicationStatus&id=${applicationId}&status=${newStatus}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload(); // Reload the page to reflect the updated status
                        } else {
                            alert('Failed to update status');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
    </script>
<!-- </body>
</html> -->