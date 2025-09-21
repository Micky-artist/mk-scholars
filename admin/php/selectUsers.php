<?php
// session_start();
// include("./dbconnections/connection.php");
?>

<style>
/* Compact Pagination Styles */
.pagination-sm .page-link {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    margin: 0 1px;
    border-radius: 0.25rem;
}

.pagination-sm .page-item:first-child .page-link,
.pagination-sm .page-item:last-child .page-link {
    border-radius: 0.25rem;
}

.pagination {
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.pagination::-webkit-scrollbar {
    height: 4px;
}

.pagination::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.pagination::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.pagination::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        align-items: center !important;
        gap: 15px;
    }
    
    .pagination {
        justify-content: center;
    }
    
    .text-muted {
        text-align: center;
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .pagination-sm .page-link {
        padding: 0.2rem 0.4rem;
        font-size: 0.8rem;
    }
}
</style>

<?php

// Pagination
$per_page = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $per_page;

// Search functionality
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$search_condition = $search ? " WHERE (NoUsername LIKE '%$search%' OR NoEmail LIKE '%$search%')" : '';

// Get total users
$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM normUsers $search_condition");
$total_row = mysqli_fetch_assoc($total_query);
$total_pages = ceil($total_row['total'] / $per_page);

// Fetch users
$selectUsers = mysqli_query($conn, "SELECT * FROM normUsers $search_condition ORDER BY NoCreationDate DESC LIMIT $start, $per_page");
?>

<div class="container-fluid mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="card-title mb-0">User Management</h4>
        </div>
        <div class="card-body">
            <!-- Search Bar -->
            <div class="mb-4">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Search by username or email" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                    <div class="col-md-2">
                        <a href="?" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Users List -->
            <div class="row">
                <?php
                $counter = $start + 1;
                if ($selectUsers->num_rows > 0) {
                    while ($user = mysqli_fetch_assoc($selectUsers)) {
                ?>
                <div class="col-md-6 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="card-title mb-0"><?= $counter++ ?>. <?= htmlspecialchars($user['NoUsername']) ?></h5>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary view-scholarships" 
                                            data-userid="<?= $user['NoUserId'] ?>" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#scholarshipsModal">
                                        <i class="fas fa-list"></i> Scholarships
                                    </button>
                                    <button class="btn btn-sm btn-outline-success start-conversation" 
                                            data-userid="<?= $user['NoUserId'] ?>" data-usename="<?= $user['NoUsername'] ?>">
                                        <i class="fas fa-comment"></i> Text
                                    </button>
                                </div>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="fas fa-envelope me-2"></i><?= htmlspecialchars($user['NoEmail']) ?>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-phone me-2"></i><?= htmlspecialchars($user['NoPhone']) ?>
                                </li>
                                <li class="list-group-item">
                                    <i class="fas fa-calendar me-2"></i>Joined: <?= $user['NoCreationDate'] ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo '<div class="col-12 text-center py-5"><div class="alert alert-info">No users found</div></div>';
                }
                ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing <?= $start + 1 ?> to <?= min($start + $per_page, $total_row['total']) ?> of <?= $total_row['total'] ?> users
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        <!-- Previous Page -->
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link" aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </span>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Page Numbers (Compact) -->
                        <?php
                        $start_page = max(1, $page - 2);
                        $end_page = min($total_pages, $page + 2);
                        
                        // Show first page if not in range
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1&search=<?= urlencode($search) ?>">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Page numbers in range -->
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <!-- Show last page if not in range -->
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>"><?= $total_pages ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Next Page -->
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="page-item disabled">
                                <span class="page-link" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Scholarships Modal -->
<div class="modal fade" id="scholarshipsModal" tabindex="-1" aria-labelledby="scholarshipsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scholarshipsModalLabel">Applied Scholarships</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="scholarshipsContent">
                Loading...
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Scholarships Modal
    $('.view-scholarships').click(function() {
        const userId = $(this).data('userid');
        $('#scholarshipsContent').html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');
        
        $.ajax({
            url: `./php/get_scholarships.php?user_id=${userId}`,
            success: function(data) {
                $('#scholarshipsContent').html(data);
            },
            error: function() {
                $('#scholarshipsContent').html('<div class="alert alert-danger">Error loading scholarships</div>');
            }
        });
    });

    // Start Conversation
    $('.start-conversation').click(function() {
    const userId = $(this).data('userid');
    const usename = $(this).data('usename');
    Swal.fire({
        title: 'Start Conversation',
        text: 'Do you want to start a conversation with this user?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `./php/start_conversation.php?username=${usename}&user_id=${userId}`,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        window.location.href = `./chat-ground.php?username=${usename}&userId=${response.UserId}`;
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Failed to start conversation', 'error');
                }
            });
        }
    });
});
});
</script>
