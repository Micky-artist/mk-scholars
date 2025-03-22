<?php
session_start();
include("./dbconnections/connection.php");

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
                                        <i class="fas fa-comment"></i> Text<?= $user['NoUserId'] ?>
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
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Previous</a>
                        </li>
                    <?php endif; ?>

                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
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
