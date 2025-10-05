<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

if (!hasPermission('ViewApplications')) {
  header("Location: ./index");
  exit;
}

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20; // Subscriptions per page
$offset = ($page - 1) * $limit;

// Get subscription data with pagination
$subscriptions = [];
$totalCount = 0;
$activeCount = 0;
$inactiveCount = 0;
$totalPages = 0;

if ($conn) {
    // First, get total count for pagination
    $countQuery = "SELECT COUNT(*) as total FROM subscription s";
    $countResult = mysqli_query($conn, $countQuery);
    if ($countResult) {
        $totalCount = mysqli_fetch_assoc($countResult)['total'];
        $totalPages = ceil($totalCount / $limit);
    }
    
    // Get active/inactive counts for stats
    $statsQuery = "
        SELECT 
            SubscriptionStatus,
            COUNT(*) as count
        FROM subscription 
        GROUP BY SubscriptionStatus
    ";
    $statsResult = mysqli_query($conn, $statsQuery);
    if ($statsResult) {
        while ($stat = mysqli_fetch_assoc($statsResult)) {
            if ((int)$stat['SubscriptionStatus'] === 1) {
                $activeCount = (int)$stat['count'];
            } else {
                $inactiveCount = (int)$stat['count'];
            }
        }
    }
    
    // Get paginated subscription data
    $query = "
        SELECT 
            s.SubId,
            s.Item,
            s.SubscriptionStatus,
            s.SubscriptionCode,
            s.subscriptionDate,
            s.expirationDate,
            u.NoUsername AS subscriberName,
            a.username AS adminName
        FROM subscription s
        LEFT JOIN normUsers u ON s.UserId = u.NoUserId
        LEFT JOIN users a ON s.adminId = a.userId
        ORDER BY s.subscriptionDate DESC
        LIMIT $limit OFFSET $offset
    ";
    
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        $subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscriptions Management | MK Scholars Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Admin Theme CSS -->
    <link href="./assets/libs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="./dist/css/style.min.css" rel="stylesheet">
    
    <style>
        /* Light Theme Overrides */
        .page-wrapper {
            background: #f8f9fa !important;
        }
        
        .page-breadcrumb {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
            color: #6c757d;
        }
        
        .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
        }
        
        .breadcrumb-item a:hover {
            color: #0056b3;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.primary {
            background: #e3f2fd;
            color: #1976d2;
        }

        .stat-icon.success {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .stat-icon.warning {
            background: #fff3e0;
            color: #f57c00;
        }

        .stat-icon.info {
            background: #e0f2f1;
            color: #00695c;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }

        .content-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            color: #212529;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .add-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .add-btn:hover {
            background: #0056b3;
            color: white;
            transform: translateY(-1px);
        }

        .subscriptions-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .subscriptions-table th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #e9ecef;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .subscriptions-table td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .subscriptions-table tbody tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.active {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.inactive {
            background: #fff3cd;
            color: #856404;
        }

        .action-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .action-btn.stop {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .action-btn.stop:hover {
            background: #dc3545;
            color: white;
        }

        .action-btn.grant {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .action-btn.grant:hover {
            background: #28a745;
            color: white;
        }

        .empty-state, .error-state {
            text-align: center;
            padding: 3rem 2rem;
            color: #6c757d;
        }

        .empty-state i, .error-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3, .error-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .empty-state p, .error-state p {
            font-size: 1rem;
            margin-bottom: 0;
        }

        .error-state {
            color: #dc3545;
        }

        .error-state i {
            color: #dc3545;
        }

        .error-state h3 {
            color: #dc3545;
        }

        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 2rem 0;
            gap: 0.5rem;
        }

        .pagination .page-item {
            list-style: none;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            margin: 0 0.25rem;
            color: #007bff;
            text-decoration: none;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            min-width: 40px;
            height: 40px;
        }

        .pagination .page-link:hover {
            color: #0056b3;
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .pagination .page-item.active .page-link {
            color: white;
            background-color: #007bff;
            border-color: #007bff;
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #dee2e6;
        }

        .pagination .page-link i {
            font-size: 0.875rem;
        }

        .pagination-info {
            text-align: center;
            margin-top: 1rem;
            color: #6c757d;
            font-size: 0.875rem;
        }

        .pagination-info strong {
            color: #495057;
        }

        /* Pagination responsive */
        @media (max-width: 768px) {
            .pagination {
                flex-wrap: wrap;
                gap: 0.25rem;
            }
            
            .pagination .page-link {
                padding: 0.375rem 0.5rem;
                margin: 0 0.125rem;
                min-width: 35px;
                height: 35px;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .content-header {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }
            
            .subscriptions-table th,
            .subscriptions-table td {
                padding: 0.75rem;
            }
        }
    </style>
</head>
<body>
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
    data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        
        <!-- Header -->
        <?php include("./partials/header.php"); ?>
        
        <!-- Sidebar -->
        <?php include("./partials/navbar.php"); ?>
        
        <!-- Main Content -->
        <div class="page-wrapper">
            <div class="container-fluid">
                <!-- Breadcrumb -->
                <div class="page-breadcrumb">
        <div class="row">
                        <div class="col-12 d-flex no-block align-items-center">
                            <h4 class="page-title">Subscriptions Management</h4>
                            <div class="ms-auto text-end">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="./home">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Subscriptions</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon primary">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="stat-value"><?php echo $totalCount; ?></div>
                        <div class="stat-label">Total Subscriptions</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $activeCount; ?></div>
                        <div class="stat-label">Active Subscriptions</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon warning">
                            <i class="fas fa-pause-circle"></i>
                        </div>
                        <div class="stat-value"><?php echo $inactiveCount; ?></div>
                        <div class="stat-label">Inactive Subscriptions</div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon info">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-value"><?php echo $totalCount > 0 ? round(($activeCount / $totalCount) * 100) : 0; ?>%</div>
                        <div class="stat-label">Active Rate</div>
        </div>
      </div>

                <!-- Content Card -->
                <div class="content-card">
                    <div class="content-header">
                        <h2 class="content-title">
                            <i class="fas fa-table"></i>
                            All Subscriptions
                        </h2>
                        <a href="add_subscription" class="add-btn">
                            <i class="fas fa-plus"></i>
                            Add New Subscription
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <?php if (!$conn): ?>
                            <div class="error-state">
                                <i class="fas fa-exclamation-triangle"></i>
                                <h3>Database Connection Error</h3>
                                <p>Unable to connect to the database. Please check your database configuration.</p>
                            </div>
                        <?php elseif (empty($subscriptions)): ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>No Subscriptions Found</h3>
                                <p>There are currently no subscriptions in the database. Create your first subscription to get started.</p>
                            </div>
                        <?php else: ?>
                            <table class="subscriptions-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Item</th>
                                        <th>Status</th>
                                        <th>Code</th>
                                        <th>Start Date</th>
                                        <th>Expiry Date</th>
                                        <th>Subscriber</th>
                                        <th>Admin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subscriptions as $subscription): ?>
                                        <tr>
                                            <td>
                                                <span class="fw-bold text-primary">#<?php echo (int)$subscription['SubId']; ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-medium"><?php echo htmlspecialchars($subscription['Item']); ?></span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo (int)$subscription['SubscriptionStatus'] === 1 ? 'active' : 'inactive'; ?>">
                                                    <?php echo (int)$subscription['SubscriptionStatus'] === 1 ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <code class="text-muted"><?php echo htmlspecialchars($subscription['SubscriptionCode']); ?></code>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($subscription['subscriptionDate'])); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($subscription['expirationDate'])); ?></td>
                                            <td><?php echo htmlspecialchars($subscription['subscriberName'] ?? '—'); ?></td>
                                            <td><?php echo htmlspecialchars($subscription['adminName'] ?? '—'); ?></td>
                                            <td>
                                                <?php if ((int)$subscription['SubscriptionStatus'] === 1): ?>
                                                    <a href="./php/stop_subscription.php?subId=<?php echo (int)$subscription['SubId']; ?>" 
                                                       class="action-btn stop">
                                                        <i class="fas fa-stop"></i>
                                                        Stop
                                                    </a>
                                                <?php else: ?>
                                                    <a href="./php/grant_subscription.php?subId=<?php echo (int)$subscription['SubId']; ?>" 
                                                       class="action-btn grant">
                                                        <i class="fas fa-play"></i>
                                                        Grant
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Pagination Controls -->
                    <?php if ($totalPages > 1): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <nav aria-label="Subscriptions pagination">
                                    <ul class="pagination justify-content-center">
                                        <!-- Previous Button -->
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="page-item disabled">
                                                <span class="page-link" aria-label="Previous">
                                                    <i class="fas fa-chevron-left"></i>
                                                </span>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <!-- Page Numbers -->
      <?php
                                        $startPage = max(1, $page - 2);
                                        $endPage = min($totalPages, $page + 2);
                                        
                                        // Show first page if not in range
                                        if ($startPage > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=1">1</a>
                                            </li>
                                            <?php if ($startPage > 2): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <!-- Page range -->
                                        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <!-- Show last page if not in range -->
                                        <?php if ($endPage < $totalPages): ?>
                                            <?php if ($endPage < $totalPages - 1): ?>
                                                <li class="page-item disabled">
                                                    <span class="page-link">...</span>
                                                </li>
                                            <?php endif; ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $totalPages; ?>"><?php echo $totalPages; ?></a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <!-- Next Button -->
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        <?php else: ?>
                                            <li class="page-item disabled">
                                                <span class="page-link" aria-label="Next">
                                                    <i class="fas fa-chevron-right"></i>
                                                </span>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                    
                                    <!-- Pagination Info -->
                                    <div class="pagination-info">
                                        <strong>Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalCount); ?></strong> 
                                        of <strong><?php echo $totalCount; ?></strong> subscriptions
                                        <?php if ($totalPages > 1): ?>
                                            (Page <?php echo $page; ?> of <?php echo $totalPages; ?>)
                                        <?php endif; ?>
                                    </div>
                                </nav>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Footer -->
            <?php include("./partials/footer.php"); ?>
        </div>
    </div>

    <!-- Admin Theme JS -->
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
  <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
  <script src="./dist/js/waves.js"></script>
  <script src="./dist/js/sidebarmenu.js"></script>
  <script src="./dist/js/custom.min.js"></script>
    
    <script>
        // Add smooth animations to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.subscriptions-table tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });

        // Add hover effects to stat cards
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-4px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Pagination enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading state to pagination links
            const paginationLinks = document.querySelectorAll('.pagination .page-link');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Add loading state
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    this.style.pointerEvents = 'none';
                    
                    // Re-enable after a short delay (in case of slow navigation)
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.pointerEvents = 'auto';
                    }, 2000);
                });
            });

            // Add smooth transitions to pagination
            const pagination = document.querySelector('.pagination');
            if (pagination) {
                pagination.style.transition = 'all 0.3s ease';
            }

            // Add keyboard navigation for pagination
            document.addEventListener('keydown', function(e) {
                const currentPage = <?php echo $page; ?>;
                const totalPages = <?php echo $totalPages; ?>;
                
                if (e.altKey) {
                    if (e.key === 'ArrowLeft' && currentPage > 1) {
                        e.preventDefault();
                        window.location.href = `?page=${currentPage - 1}`;
                    } else if (e.key === 'ArrowRight' && currentPage < totalPages) {
                        e.preventDefault();
                        window.location.href = `?page=${currentPage + 1}`;
                    }
                }
            });

            // Add tooltip for keyboard shortcuts
            const paginationInfo = document.querySelector('.pagination-info');
            if (paginationInfo && totalPages > 1) {
                paginationInfo.title = 'Keyboard shortcuts: Alt + ← (previous page), Alt + → (next page)';
                paginationInfo.style.cursor = 'help';
            }
        });

        // Performance optimization: Lazy load pagination on scroll
        let paginationLoaded = false;
        function loadPaginationOnScroll() {
            if (paginationLoaded) return;
            
            const pagination = document.querySelector('.pagination');
            if (pagination) {
                const rect = pagination.getBoundingClientRect();
                if (rect.top < window.innerHeight + 100) {
                    paginationLoaded = true;
                    pagination.style.opacity = '0';
                    pagination.style.transform = 'translateY(20px)';
                    
                    requestAnimationFrame(() => {
                        pagination.style.transition = 'all 0.5s ease';
                        pagination.style.opacity = '1';
                        pagination.style.transform = 'translateY(0)';
                    });
                }
            }
        }

        // Throttled scroll listener for performance
        let scrollTimeout;
        window.addEventListener('scroll', function() {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            scrollTimeout = setTimeout(loadPaginationOnScroll, 100);
        });

        // Load pagination immediately if it's already visible
        document.addEventListener('DOMContentLoaded', loadPaginationOnScroll);
    </script>
</body>
</html>