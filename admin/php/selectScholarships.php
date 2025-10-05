<?php
session_start();
include("../dbconnections/connection.php");

// Debug: Check if we're online and database connection
$isOnline = isOnline();
$dbConnected = $conn ? true : false;

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Pagination logic
$limit = 20; // Number of posts per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Offset for SQL query

// Search logic
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_condition = $search ? " AND (scholarshipTitle LIKE '%$search%' OR scholarshipDetails LIKE '%$search%')" : '';

// Filter logic
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filter_condition = '';
if ($filter === 'published') {
    $filter_condition = " WHERE scholarshipStatus = 1";
} elseif ($filter === 'unpublished') {
    $filter_condition = " WHERE scholarshipStatus = 0";
} else {
    $filter_condition = " WHERE 1"; // Show all
}

// Fetch total number of scholarships
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships $filter_condition $search_condition");
$totalRows = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalRows / $limit); // Total number of pages

$sql = "SELECT COUNT(*) as scholarship_count FROM scholarships";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$scholarship_count = $row['scholarship_count'];
// Fetch scholarships with pagination, search, and filter
$query = "SELECT * FROM scholarships $filter_condition $search_condition ORDER BY scholarshipUpdateDate DESC LIMIT $limit OFFSET $offset";
$selectScholarships = mysqli_query($conn, $query);

// Check for query errors
if (!$selectScholarships) {
    $error_message = "Database query failed: " . mysqli_error($conn);
    $selectScholarships = false;
}
?>

<div class="container mt-4">
    <!-- Debug Information (remove in production) -->
    <?php if (isset($_GET['debug']) && $_GET['debug'] == '1'): ?>
    <div class="alert alert-info mb-3">
        <strong>Debug Information:</strong><br>
        Environment: <?php echo $isOnline ? 'Online (Production)' : 'Local (Development)'; ?><br>
        Database Connected: <?php echo $dbConnected ? 'Yes' : 'No'; ?><br>
        Total Scholarships: <?php echo $scholarship_count; ?><br>
        Query: <?php echo "SELECT * FROM scholarships $filter_condition $search_condition ORDER BY scholarshipUpdateDate DESC LIMIT $limit OFFSET $offset"; ?>
    </div>
    <?php endif; ?>
    
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">Applications(<?php echo $scholarship_count ?>)</h4>
        </div>
        <div class="card-body">
            <!-- Search Bar and Filter -->
            <div class="mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Search by title or description" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="filter" class="form-select">
                            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Show All</option>
                            <option value="published" <?= $filter === 'published' ? 'selected' : '' ?>>Published</option>
                            <option value="unpublished" <?= $filter === 'unpublished' ? 'selected' : '' ?>>Unpublished</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="?" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Scholarships List -->
            <div class="row">
                <?php
                // Check for database errors
                if (isset($error_message)) {
                    echo '<div class="col-12">
                            <div class="alert alert-danger">
                                <strong>Database Error:</strong> ' . htmlspecialchars($error_message) . '
                            </div>
                          </div>';
                } elseif ($selectScholarships && $selectScholarships->num_rows > 0) {
                    while ($getScholarships = mysqli_fetch_assoc($selectScholarships)) {
                ?>
                <div class="col-md-12 mb-4">
                    <div class="card card-horizontal">
                        <div class="row g-0">
                            <div class="col-md-3">
                                <?php 
                                $imagePath = 'uploads/posts/' . $getScholarships['scholarshipImage'];
                                $imageUrl = getImageUrl($imagePath);
                                
                                // Fallback for image URL
                                if (empty($imageUrl) || $imageUrl === './' . $imagePath) {
                                    $imageUrl = $isOnline ? 'https://mkscholars.com/' . $imagePath : './' . $imagePath;
                                }
                                ?>
                                <img src="<?= $imageUrl ?>" 
                                     class="img-fluid rounded-start" 
                                     alt="<?= htmlspecialchars($getScholarships['scholarshipTitle']) ?>"
                                     onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPk5vIEltYWdlPC90ZXh0Pjwvc3ZnPg=='">
                            </div>
                            <div class="col-md-9">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="card-title mb-0">
                                            <?= $getScholarships['scholarshipTitle'] ?>
                                            <span class="badge bg-<?= $getScholarships['scholarshipStatus'] ? 'success' : 'warning' ?> ms-2">
                                                <?= $getScholarships['scholarshipStatus'] ? 'Published' : 'Draft' ?>
                                            </span>
                                        </h5>
                                        <small class="text-muted">Last updated: <?= $getScholarships['scholarshipUpdateDate'] ?></small>
                                    </div>

                                    <div class="btn-group gap-2">
                                        <a href="edit-scholarship?edit=true&i=<?= $getScholarships['scholarshipId'] ?>&n=<?= urlencode($getScholarships['scholarshipTitle']) ?>" 
                                           class="btn btn-outline-primary btn-sm" 
                                           target="_blank">
                                           <i class="fas fa-edit"></i> Edit
                                        </a>

                                        <a href="https://www.mkscholars.com/scholarship-details-preview?scholarship-id=<?= $getScholarships['scholarshipId']?>&scholarship-title=<?= urlencode($getScholarships['scholarshipTitle'])?>" 
                                           class="btn btn-primary btn-sm" 
                                           target="_blank">
                                           <i class="fas fa-eye"></i> View
                                        </a>

                                        <?php if ($getScholarships['scholarshipStatus'] == 0) { ?>
                                            <a href="#" 
                                               class="btn btn-outline-success btn-sm publish-btn" 
                                               data-id="<?= $getScholarships['scholarshipId'] ?>" 
                                               data-title="<?= $getScholarships['scholarshipTitle'] ?>">
                                               <i class="fas fa-check"></i> Publish
                                            </a>
                                        <?php } else { ?>
                                            <a href="#" 
                                               class="btn btn-outline-warning btn-sm unpublish-btn" 
                                               data-id="<?= $getScholarships['scholarshipId'] ?>" 
                                               data-title="<?= $getScholarships['scholarshipTitle'] ?>">
                                               <i class="fas fa-times"></i> Unpublish
                                            </a>
                                        <?php } ?>

                                        <a href="#" 
                                           class="btn btn-outline-danger btn-sm delete-btn" 
                                           data-id="<?= $getScholarships['scholarshipId'] ?>" 
                                           data-title="<?= $getScholarships['scholarshipTitle'] ?>">
                                           <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<div class="col-12 text-center py-5">
                            <div class="alert alert-info">No scholarships found</div>
                          </div>';
                }
                ?>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mt-4">
                    <?php if ($page > 1) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->

<script>
// Function to handle delete action
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const scholarshipId = this.getAttribute('data-id');
        const scholarshipTitle = this.getAttribute('data-title');

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete "${scholarshipTitle}". This action cannot be undone!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `./php/actions?a=deleteScholarship&i=${scholarshipId}&n=${encodeURIComponent(scholarshipTitle)}`;
            }
        });
    });
});

// Function to handle publish action
document.querySelectorAll('.publish-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const scholarshipId = this.getAttribute('data-id');
        const scholarshipTitle = this.getAttribute('data-title');

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to publish "${scholarshipTitle}".`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, publish it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `./php/actions?a=publishScholarship&i=${scholarshipId}&n=${encodeURIComponent(scholarshipTitle)}`;
            }
        });
    });
});

// Function to handle unpublish action
document.querySelectorAll('.unpublish-btn').forEach(button => {
    button.addEventListener('click', function (e) {
        e.preventDefault();
        const scholarshipId = this.getAttribute('data-id');
        const scholarshipTitle = this.getAttribute('data-title');

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to unpublish "${scholarshipTitle}".`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, unpublish it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `./php/actions?a=unPublishScholarship&i=${scholarshipId}&n=${encodeURIComponent(scholarshipTitle)}`;
            }
        });
    });
});
</script>
<style>
    .card-horizontal {
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 0.75rem;
        overflow: hidden;
        transition: transform 0.2s;
        margin: 0 10px;
    }

    .card-horizontal .img-fluid {
        height: 200px;
        object-fit: cover;
        width: 100%;
    }
    
    .btn-group {
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
    }

    .pagination .page-link {
        color: #007bff;
    }

    .pagination .page-link:hover {
        color: #0056b3;
    }
</style>