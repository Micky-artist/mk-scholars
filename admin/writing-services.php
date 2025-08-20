<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Include admin database connection
include_once("./dbconnections/connection.php");

// Handle status updates
if (isset($_POST['update_status']) && isset($_POST['request_id']) && isset($_POST['new_status'])) {
    $requestId = (int)$_POST['request_id'];
    $newStatus = $_POST['new_status'];
    
    $updateSQL = "UPDATE writing_services SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateSQL);
    mysqli_stmt_bind_param($stmt, 'si', $newStatus, $requestId);
    
    if (mysqli_stmt_execute($stmt)) {
        $successMessage = "Status updated successfully!";
    } else {
        $errorMessage = "Failed to update status.";
    }
    mysqli_stmt_close($stmt);
}

// Get writing service requests with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Get total count
$countSQL = "SELECT COUNT(*) as total FROM writing_services";
$countResult = mysqli_query($conn, $countSQL);
$totalRecords = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRecords / $limit);

// Get requests
$requestsSQL = "SELECT * FROM writing_services ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($conn, $requestsSQL);
mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
mysqli_stmt_execute($stmt);
$requestsResult = mysqli_stmt_get_result($stmt);

// Function to generate pagination links
function generatePaginationLink($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Writing Services - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .status-badge {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-in_progress { background-color: #d1ecf1; color: #0c5460; }
        .status-completed { background-color: #d4edda; color: #155724; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        
        .request-card {
            border-left: 4px solid #007bff;
            margin-bottom: 1rem;
        }
        
        .request-card.completed { border-left-color: #28a745; }
        .request-card.cancelled { border-left-color: #dc3545; }
        .request-card.in_progress { border-left-color: #ffc107; }
        
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white active" href="writing-services.php">
                                <i class="fas fa-pen-fancy"></i> Writing Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Writing Services Management</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <a href="dashboard.php" class="btn btn-sm btn-outline-secondary">Back to Dashboard</a>
                        </div>
                    </div>
                </div>

                <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $successMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $errorMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="stats-cards">
                    <?php
                    $statsSQL = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                        FROM writing_services";
                    $statsResult = mysqli_query($conn, $statsSQL);
                    $stats = mysqli_fetch_assoc($statsResult);
                    ?>
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['total']; ?></div>
                        <div class="stat-label">Total Requests</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['pending']; ?></div>
                        <div class="stat-label">Pending</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['in_progress']; ?></div>
                        <div class="stat-label">In Progress</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['completed']; ?></div>
                        <div class="stat-label">Completed</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $stats['cancelled']; ?></div>
                        <div class="stat-label">Cancelled</div>
                    </div>
                </div>

                <!-- Requests List -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Writing Service Requests</h5>
                            </div>
                            <div class="card-body">
                                <?php if (mysqli_num_rows($requestsResult) > 0): ?>
                                    <?php while ($request = mysqli_fetch_assoc($requestsResult)): ?>
                                        <div class="card request-card <?php echo $request['status']; ?> mb-3">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <h6 class="card-title">
                                                            Request #<?php echo $request['id']; ?> - 
                                                            <?php echo htmlspecialchars($request['full_name']); ?>
                                                        </h6>
                                                        <p class="card-text">
                                                            <strong>Service:</strong> <?php echo htmlspecialchars($request['service_type']); ?><br>
                                                            <strong>Description:</strong> <?php echo htmlspecialchars(substr($request['description'], 0, 100)) . (strlen($request['description']) > 100 ? '...' : ''); ?><br>
                                                            <strong>Urgency:</strong> <?php echo htmlspecialchars($request['urgency']); ?>
                                                            <?php if ($request['deadline']): ?>
                                                                <br><strong>Deadline:</strong> <?php echo $request['deadline']; ?>
                                                            <?php endif; ?>
                                                            <?php if ($request['word_count']): ?>
                                                                <br><strong>Word Count:</strong> <?php echo htmlspecialchars($request['word_count']); ?>
                                                            <?php endif; ?>
                                                        </p>
                                                        <small class="text-muted">
                                                            Submitted: <?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?>
                                                        </small>
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <span class="badge status-badge status-<?php echo $request['status']; ?>">
                                                            <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                                                        </span>
                                                        
                                                        <form method="POST" class="mt-2">
                                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                            <select name="new_status" class="form-select form-select-sm mb-2">
                                                                <option value="pending" <?php echo $request['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                                <option value="in_progress" <?php echo $request['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                                                <option value="completed" <?php echo $request['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                                <option value="cancelled" <?php echo $request['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                                            </select>
                                                            <button type="submit" name="update_status" class="btn btn-primary btn-sm">Update Status</button>
                                                        </form>
                                                        
                                                        <div class="mt-2">
                                                            <strong>Contact:</strong><br>
                                                            <small><?php echo htmlspecialchars($request['email']); ?><br>
                                                            <?php echo htmlspecialchars($request['phone']); ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <?php if ($request['additional_info']): ?>
                                                    <div class="mt-3">
                                                        <strong>Additional Information:</strong>
                                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($request['additional_info'])); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No writing service requests found</h5>
                                        <p class="text-muted">When users submit writing service requests, they will appear here.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination-wrapper">
                        <nav aria-label="Writing services pagination">
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo generatePaginationLink($page - 1); ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $page + 2);
                                
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo generatePaginationLink(1); ?>">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo generatePaginationLink($i); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo generatePaginationLink($totalPages); ?>"><?php echo $totalPages; ?></a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo generatePaginationLink($page + 1); ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
