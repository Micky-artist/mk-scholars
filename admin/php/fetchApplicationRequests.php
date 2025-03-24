<?php


function getScholarshipApplications($conn)
{
    $query = "SELECT s.*, ar.*, c.*, u.NoUserId, u.NoUsername, u.NoEmail, u.NoPhone, u.NoCreationDate
        FROM ApplicationRequests ar JOIN normUsers u ON ar.UserId = u.NoUserId JOIN scholarships s ON ar.ApplicationId = S.scholarshipId
        JOIN countries c ON s.country=c.countryId ORDER BY ar.ApplicationId DESC";

    $result = $conn->query($query);
    if (!$result) {
        die("Error fetching applications: " . $conn->error);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}


function updateApplicationStatus($conn, $applicationId, $newStatus)
{
    $query = "UPDATE ApplicationRequests 
        SET Status = ?
        WHERE RequestId = ?
    ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ii", $newStatus, $applicationId);
    if (!$stmt->execute()) {
        die("Error updating status 1: " . $stmt->error);
    }

    $stmt->close();
    return true;
}

/**
 * Filters applications based on search term, status, and date
 */
function filterApplications($conn, $searchTerm = "", $status = "", $date = "")
{
    $query = "SELECT s.*, ar.*, c.*, u.NoUserId, u.NoUsername, u.NoEmail, u.NoPhone, u.NoCreationDate
        FROM ApplicationRequests ar JOIN normUsers u ON ar.UserId = u.NoUserId JOIN scholarships s ON ar.ApplicationId = S.scholarshipId
        JOIN countries c ON s.country=c.countryId WHERE 1";

    // Add filters dynamically
    $params = [];
    $types = "";

    if (!empty($searchTerm)) {
        $query .= " AND (u.NoUsername LIKE ? OR u.NoEmail LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $types .= "ss";
    }

    if (!empty($status)) {
        $query .= " AND ar.Status = ?";
        $params[] = $status;
        $types .= "i";
    }

    if (!empty($date)) {
        $query .= " AND DATE(s.scholarshipUpdateDate) = ?";
        $params[] = $date;
        $types .= "s";
    }

    $query .= " ORDER BY s.scholarshipUpdateDate DESC";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Counts total number of applications
 */
function countTotalApplications($conn)
{
    $query = "SELECT COUNT(*) AS total 
        FROM ApplicationRequests";

    $result = $conn->query($query);
    if (!$result) {
        die("Error counting applications: " . $conn->error);
    }

    $row = $result->fetch_assoc();
    return $row['total'];
}

/**
 * Gets paginated applications
 */
function getPaginatedApplications($conn, $page = 1, $perPage = 10)
{
    $offset = ($page - 1) * $perPage;

    $query = "SELECT s.*, ar.*, c.*, u.NoUserId, u.NoUsername, u.NoEmail, u.NoPhone, u.NoCreationDate
        FROM ApplicationRequests ar JOIN normUsers u ON ar.UserId = u.NoUserId JOIN scholarships s ON ar.ApplicationId = S.scholarshipId
        JOIN countries c ON s.country=c.countryId ORDER BY ar.ApplicationId DESC LIMIT ?, ? ";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ii", $offset, $perPage);
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Gets status text based on status code
 */
function getStatusText($status)
{
    $texts = ['Unseen', 'Seen', 'In Progress', 'Reviewing', 'Completed'];
    return isset($texts[$status]) ? $texts[$status] : 'Unknown';
}

/**
 * Gets status color based on status code
 */
function getStatusColor($status)
{
    $colors = ['secondary', 'primary', 'warning', 'info', 'success'];
    return isset($colors[$status]) ? $colors[$status] : 'secondary';
}

// Set current page for pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

// Get total applications for pagination calculation
$totalApplications = countTotalApplications($conn);
$totalPages = ceil($totalApplications / $perPage);

// Check if we're filtering
if (isset($_GET['search']) || isset($_GET['status']) || isset($_GET['date'])) {
    $searchTerm = isset($_GET['search']) ? $_GET['search'] : "";
    $status = isset($_GET['status']) ? $_GET['status'] : "";
    $date = isset($_GET['date']) ? $_GET['date'] : "";

    $applications = filterApplications($conn, $searchTerm, $status, $date);
} else {
    // Default: get paginated applications
    $applications = getPaginatedApplications($conn, $page, $perPage);
}

// Close the connection when done (at the end of the file)
$conn->close();
?>

<style>
    /* .card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        } */
    .status-dropdown {
        cursor: pointer;
    }

    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.75em;
    }

    .admin-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 15px 0;
        margin-bottom: 20px;
    }

    .pagination {
        margin-top: 30px;
    }
</style>

<!-- Admin Header -->
<header class="admin-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="m-0">Scholarship Applications</h3>
            </div>
            <div class="col-auto">
                <span class="badge bg-info"><?= $totalApplications ?> Total Applications</span>
            </div>
        </div>
    </div>
</header>

<div class="container-fluid">
    <!-- Search and Filter Section -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form id="filterForm" method="GET" action="">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Search by name or email..."
                            id="searchInput" name="search" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter" name="status">
                            <option value="">All Statuses</option>
                            <option value="0" <?= (isset($_GET['status']) && $_GET['status'] === '0') ? 'selected' : '' ?>>Unseen</option>
                            <option value="1" <?= (isset($_GET['status']) && $_GET['status'] === '1') ? 'selected' : '' ?>>Seen</option>
                            <option value="2" <?= (isset($_GET['status']) && $_GET['status'] === '2') ? 'selected' : '' ?>>In Progress</option>
                            <option value="3" <?= (isset($_GET['status']) && $_GET['status'] === '3') ? 'selected' : '' ?>>Reviewing</option>
                            <option value="4" <?= (isset($_GET['status']) && $_GET['status'] === '4') ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="dateFilter" name="date"
                            value="<?= isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '' ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                    <div class="col-md-1">
                        <a href="./application-requests" class="btn btn-secondary w-100">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Application Requests -->
    <div class="row row-cols-1 row-cols-md-2 g-4" id="applicationsContainer">
        <!-- Single Application Card -->
        <?php if (empty($applications)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    No scholarship applications found matching your criteria.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($applications as $app): ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-<?= getStatusColor($app['Status']) ?>">
                                <?= getStatusText($app['Status']) ?>
                            </span>
                            <small class="text-muted"><?= date('M d, Y', strtotime($app['scholarshipUpdateDate'])) ?></small>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($app['scholarshipTitle']) ?></h5>
                            <div class="d-flex gap-2 mb-3">
                                <i class="bi bi-person-circle"></i>
                                <div>
                                    <p class="mb-0"><?= htmlspecialchars($app['NoUsername']) ?></p>
                                    <small class="text-muted"><?= htmlspecialchars($app['NoEmail']) ?></small>
                                </div>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-6">
                                    <select class="form-select status-dropdown" data-app-id="<?= $app['RequestId'] ?>">
                                        <option value="0" <?= $app['Status'] == 0 ? 'selected' : '' ?>>Unseen</option>
                                        <option value="1" <?= $app['Status'] == 1 ? 'selected' : '' ?>>Seen</option>
                                        <option value="2" <?= $app['Status'] == 2 ? 'selected' : '' ?>>In Progress</option>
                                        <option value="3" <?= $app['Status'] == 3 ? 'selected' : '' ?>>Reviewing</option>
                                        <option value="4" <?= $app['Status'] == 4 ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal"
                                        data-bs-target="#userModal-<?= $app['scholarshipId'] ?>">
                                        <i class="bi bi-person-lines-fill"></i> View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Details Modal -->
                <div class="modal fade" id="userModal-<?= $app['scholarshipId'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Scholarship Application Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-header">
                                <h5 class="modal-title"><?= htmlspecialchars($app['scholarshipTitle']) ?></h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>User Information</h6>
                                        <ul class="list-group mb-3">
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Username:</span>
                                                <strong><?= htmlspecialchars($app['NoUsername']) ?></strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Email:</span>
                                                <strong><?= htmlspecialchars($app['NoEmail']) ?></strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Phone:</span>
                                                <strong><?= htmlspecialchars($app['NoPhone']) ?></strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Registered:</span>
                                                <strong><?= date('M d, Y', strtotime($app['NoCreationDate'])) ?></strong>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Scholarship Information</h6>
                                        <ul class="list-group mb-3">
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Amount:</span>
                                                <strong>RWF <?= $app['amount'] ?></strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Country:</span>
                                                <strong><?= htmlspecialchars($app['CountryName']) ?></strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Status:</span>
                                                <strong>
                                                    <span class="badge bg-<?= getStatusColor($app['Status']) ?>">
                                                        <?= getStatusText($app['Status']) ?>
                                                    </span>
                                                </strong>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>Submitted:</span>
                                                <strong><?= date('M d, Y', strtotime($app['scholarshipUpdateDate'])) ?></strong>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (!isset($_GET['search']) && !isset($_GET['status']) && !isset($_GET['date']) && $totalPages > 1): ?>
        <nav aria-label="Page navigation" class="my-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<!-- Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle status dropdown changes
        document.querySelectorAll('.status-dropdown').forEach(function(dropdown) {
            dropdown.addEventListener('change', function() {
                updateApplicationStatus(this.dataset.appId, this.value);
            });
        });

        // Handle status update from modal
        document.querySelectorAll('.status-update').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                updateApplicationStatus(this.dataset.appId, this.dataset.status);

                // Close the modal
                var modalEl = this.closest('.modal');
                var modal = bootstrap.Modal.getInstance(modalEl);
                modal.hide();
            });
        });

        // Function to update application status via AJAX
        function updateApplicationStatus(applicationId, newStatus) {
            // Create FormData
            var formData = new FormData();
            formData.append('applicationId', applicationId);
            formData.append('newStatus', newStatus);

            // Send AJAX request
            fetch('./php/update_status.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI to reflect new status
                        updateStatusUI(applicationId, newStatus);
                        showToast('Status updated successfully!', 'success');
                    } else {
                        showToast('Error updating status 2: ' + data.message, 'danger');
                    }
                })
                .catch(error => {
                    showToast('Error updating status: 3' + error, 'danger');
                });
        }

        // Function to update the UI after status change
        function updateStatusUI(applicationId, newStatus) {
            // Update dropdown
            document.querySelectorAll(`.status-dropdown[data-app-id="${applicationId}"]`).forEach(function(dropdown) {
                dropdown.value = newStatus;
            });

            // Update badge
            const card = document.querySelector(`.status-dropdown[data-app-id="${applicationId}"]`).closest('.card');
            const badge = card.querySelector('.badge');

            // Get status text and color
            const statusText = getStatusText(newStatus);
            const statusColor = getStatusColor(newStatus);

            // Update badge class and text
            badge.className = `badge bg-${statusColor}`;
            badge.textContent = statusText;

            // Also update in modal if open
            const modal = document.getElementById(`userModal-${applicationId}`);
            if (modal) {
                const modalBadge = modal.querySelector('.badge');
                if (modalBadge) {
                    modalBadge.className = `badge bg-${statusColor}`;
                    modalBadge.textContent = statusText;
                }
            }
        }

        // Helper function to get status text
        function getStatusText(status) {
            const texts = ['Unseen', 'Seen', 'In Progress', 'Reviewing', 'Completed'];
            return texts[status] || 'Unknown';
        }

        // Helper function to get status color
        function getStatusColor(status) {
            const colors = ['secondary', 'primary', 'warning', 'info', 'success'];
            return colors[status] || 'secondary';
        }

        // Function to show toast notifications
        function showToast(message, type = 'info') {
            // Check if toast container exists, create if not
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }

            // Create toast element
            const toastEl = document.createElement('div');
            toastEl.className = `toast align-items-center text-white bg-${type} border-0`;
            toastEl.setAttribute('role', 'alert');
            toastEl.setAttribute('aria-live', 'assertive');
            toastEl.setAttribute('aria-atomic', 'true');

            // Create toast content
            toastEl.innerHTML = `<div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>`;

            // Add to container
            toastContainer.appendChild(toastEl);

            // Initialize and show toast
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            // Remove after hidden
            toastEl.addEventListener('hidden.bs.toast', function() {
                toastEl.remove();
            });
        }

        // Clear filter button
        // const clearFilterBtn = document.createElement('button');
        // clearFilterBtn.className = 'btn btn-outline-secondary ms-2';
        // clearFilterBtn.textContent = 'Clear Filters';
        // clearFilterBtn.addEventListener('click', function() {
        //     window.location.href = window.location.pathname;
        // });

        // Add clear button if filters are active
        // const filterForm = document.getElementById('filterForm');
        // const filterActive = window.location.search.includes('search=') || 
        //                    window.location.search.includes('status=') || 
        //                    window.location.search.includes('date=');

        // if (filterActive) {
        //     filterForm.querySelector('button[type="submit"]').parentNode.appendChild(clearFilterBtn);
        // }
    });
</script>