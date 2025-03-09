<?php

// Pagination logic
$limit = 20; // Number of posts per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $limit; // Offset for SQL query

// Search logic
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_condition = $search ? " WHERE (scholarshipTitle LIKE '%$search%' OR scholarshipDetails LIKE '%$search%')" : '';

// Fetch total number of scholarships
$totalQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM scholarships $search_condition");
$totalRows = mysqli_fetch_assoc($totalQuery)['total'];
$totalPages = ceil($totalRows / $limit); // Total number of pages

// Fetch scholarships with pagination and search
$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships $search_condition ORDER BY scholarshipUpdateDate DESC LIMIT $limit OFFSET $offset");
?>


<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">Applications</h4>
        </div>
        <div class="card-body">
            <!-- Search Bar -->
            <div class="mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Search by title or description" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                    <div class="col-md-2">
                        <a href="?" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Scholarships List -->
            <div class="row">
                <?php
                if ($selectScholarships->num_rows > 0) {
                    while ($getScholarships = mysqli_fetch_assoc($selectScholarships)) {
                ?>
                <div class="col-md-12 mb-4">
                    <div class="card card-horizontal">
                        <div class="row g-0">
                            <div class="col-md-3">
                                <img src="./uploads/posts/<?= $getScholarships['scholarshipImage'] ?>" 
                                     class="img-fluid rounded-start" 
                                     alt="<?= htmlspecialchars($getScholarships['scholarshipTitle']) ?>">
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
                                            <a href="./php/actions?a=publishScholarship&i=<?= $getScholarships['scholarshipId']?>&n=<?= urlencode($getScholarships['scholarshipTitle'])?>" 
                                               class="btn btn-outline-success btn-sm">
                                               <i class="fas fa-check"></i> Publish
                                            </a>
                                        <?php } else { ?>
                                            <a href="./php/actions?a=unPublishScholarship&i=<?= $getScholarships['scholarshipId']?>&n=<?= urlencode($getScholarships['scholarshipTitle'])?>" 
                                               class="btn btn-outline-warning btn-sm">
                                               <i class="fas fa-times"></i> Unpublish
                                            </a>
                                        <?php } ?>

                                        <a href="./php/actions?a=deleteScholarship&i=<?= $getScholarships['scholarshipId']?>&n=<?= urlencode($getScholarships['scholarshipTitle'])?>" 
                                           class="btn btn-outline-danger btn-sm">
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
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages) : ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>


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