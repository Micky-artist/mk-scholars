<?php
session_start();
include("./dbconnections/connection.php");

// Pagination settings
$limit  = 20;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search
$search = isset($_GET['search'])
    ? mysqli_real_escape_string($conn, $_GET['search'])
    : '';
$search_condition = $search
    ? " AND (
        u.NoUsername     LIKE '%{$search}%'
     OR s.Item           LIKE '%{$search}%'
     OR s.SubscriptionCode LIKE '%{$search}%'
    )"
    : '';

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filter_condition = '';
if ($filter === 'active') {
    $filter_condition = " AND s.SubscriptionStatus = 1";
} elseif ($filter === 'inactive') {
    $filter_condition = " AND s.SubscriptionStatus = 0";
}

// Total rows
$totalQ = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total
       FROM subscription s
  LEFT JOIN normUsers u ON s.UserId = u.NoUserId
      WHERE 1
      {$filter_condition}
      {$search_condition}"
);
$totalRows  = mysqli_fetch_assoc($totalQ)['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch subscriptions
$sql = "
  SELECT
    s.SubId,
    s.Item,
    s.SubscriptionStatus,
    s.SubscriptionCode,
    s.subscriptionDate,
    s.expirationDate,
    u.NoUsername   AS subscriberName,
    a.username     AS adminName
  FROM subscription s
  LEFT JOIN normUsers u ON s.UserId = u.NoUserId
  LEFT JOIN users     a ON s.adminId = a.userId
  WHERE 1
    {$filter_condition}
    {$search_condition}
  ORDER BY s.subscriptionDate DESC
  LIMIT {$limit} OFFSET {$offset}
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
 <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --success-color: #4cc9f0;
            --danger-color: #f72585;
            --warning-color: #f8961e;
            --light-bg: #f8f9fa;
            --dark-bg: #212529;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f7fb;
            color: #333;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .subscription-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            background: white;
            overflow: hidden;
            height: 100%;
        }

        .subscription-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .subscription-card .card-body {
            padding: 1.5rem;
        }

        .subscription-card .card-title {
            font-weight: 600;
            color: var(--dark-bg);
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .badge-status {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.35rem 0.65rem;
            border-radius: 50px;
        }

        .badge-active {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
        }

        .badge-inactive {
            background-color: rgba(248, 150, 30, 0.1);
            color: var(--warning-color);
        }

        .subscription-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .subscription-meta strong {
            color: #495057;
            font-weight: 500;
        }

        .subscription-dates {
            background-color: rgba(67, 97, 238, 0.05);
            padding: 0.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .date-item {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }

        .date-item i {
            margin-right: 0.5rem;
            color: var(--primary-color);
            font-size: 0.9rem;
        }

        .btn-action {
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.4rem 0.9rem;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .btn-stop {
            background-color: rgba(247, 37, 133, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(247, 37, 133, 0.3);
        }

        .btn-stop:hover {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-grant {
            background-color: rgba(76, 201, 240, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(76, 201, 240, 0.3);
        }

        .btn-grant:hover {
            background-color: var(--success-color);
            color: white;
        }

        .search-box {
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #adb5bd;
        }

        .search-box input {
            padding-left: 40px;
            border-radius: 50px;
            border: 1px solid #e9ecef;
        }

        .filter-select {
            border-radius: 50px;
            border: 1px solid #e9ecef;
        }

        .empty-state {
            padding: 3rem 0;
            text-align: center;
        }

        .empty-state i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 1rem;
        }

        .empty-state h5 {
            color: #6c757d;
            font-weight: 500;
        }

        .pagination .page-item .page-link {
            border-radius: 50px !important;
            margin: 0 3px;
            border: none;
            color: #6c757d;
            min-width: 38px;
            text-align: center;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            color: white;
        }

        .pagination .page-item:hover .page-link {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-color);
        }

        .stats-card {
            background: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .stats-value {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .stats-label {
            font-size: 0.85rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="card mb-4">
            <div class="card-header text-white">
                
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Subscription Management
                    </h4>
                    <span class="badge bg-light text-dark fs-6">Total: <?= $totalRows ?></span>
                    <a href="add_subscription.php" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i> Add Subscription
                </a>
                </div>
                
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success'];
                                                        unset($_SESSION['success']); ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= $_SESSION['error'];
                                                    unset($_SESSION['error']); ?></div>
                <?php endif; ?>
                <!-- Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-value"><?= $totalRows ?></div>
                            <div class="stats-label">Total Subscriptions</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-value">
                                <?php
                                $activeQ = mysqli_query($conn, "SELECT COUNT(*) AS active FROM subscription WHERE SubscriptionStatus = 1");
                                echo mysqli_fetch_assoc($activeQ)['active'];
                                ?>
                            </div>
                            <div class="stats-label">Active Subscriptions</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <div class="stats-value">
                                <?php
                                $inactiveQ = mysqli_query($conn, "SELECT COUNT(*) AS inactive FROM subscription WHERE SubscriptionStatus = 0");
                                echo mysqli_fetch_assoc($inactiveQ)['inactive'];
                                ?>
                            </div>
                            <div class="stats-label">Inactive Subscriptions</div>
                        </div>
                    </div>
                </div>

                <!-- Search & Filter -->
                <form method="GET" class="row g-3 mb-4">
                    <div class="col-md-6 search-box">
                        <i class="fas fa-search"></i>
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search subscribers, items, or codes..."
                            value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="col-md-4">
                        <select name="filter" class="form-select filter-select">
                            <option value="all" <?= $filter === 'all'      ? 'selected' : '' ?>>All Subscriptions</option>
                            <option value="active" <?= $filter === 'active'   ? 'selected' : '' ?>>Active Only</option>
                            <option value="inactive" <?= $filter === 'inactive' ? 'selected' : '' ?>>Inactive Only</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 rounded-pill">
                            <i class="fas fa-filter me-1"></i> Apply
                        </button>
                    </div>
                </form>

                <!-- Subscription List -->
                <div class="row">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($sub = $result->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="subscription-card">
                                    <div class="card-body">
                                        <h5 class="card-title">
                                            <?= htmlspecialchars($sub['Item']) ?>
                                            <span class="badge badge-status <?= $sub['SubscriptionStatus'] ? 'badge-active' : 'badge-inactive' ?>">
                                                <?= $sub['SubscriptionStatus'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </h5>

                                        <div class="subscription-dates">
                                            <div class="date-item">
                                                <i class="fas fa-play-circle"></i>
                                                <small>Start: <?= date('M d, Y', strtotime($sub['subscriptionDate'])) ?></small>
                                            </div>
                                            <div class="date-item">
                                                <i class="fas fa-stop-circle"></i>
                                                <small>Expires: <?= date('M d, Y', strtotime($sub['expirationDate'])) ?></small>
                                            </div>
                                        </div>

                                        <div class="subscription-meta">
                                            <p class="mb-1"><strong><i class="fas fa-user me-1"></i>Subscriber:</strong> <?= htmlspecialchars($sub['subscriberName']) ?></p>
                                            <p class="mb-1"><strong><i class="fas fa-tag me-1"></i>Code:</strong> <?= htmlspecialchars($sub['SubscriptionCode']) ?></p>
                                            <p class="mb-3"><strong><i class="fas fa-user-shield me-1"></i>Admin:</strong> <?= htmlspecialchars($sub['adminName'] ?: '—') ?></p>
                                        </div>

                                        <div class="d-flex flex-wrap">
                                            <?php if ($sub['SubscriptionStatus']): ?>
                                                <a href="./php/stop_subscription.php?subId=<?= $sub['SubId'] ?>" class="btn btn-action btn-stop">
                                                    <i class="fas fa-stop-circle me-1"></i> Stop
                                                </a>
                                            <?php else: ?>
                                                <a href="./php/grant_subscription.php?subId=<?= $sub['SubId'] ?>" class="btn btn-action btn-grant">
                                                    <i class="fas fa-check-circle me-1"></i> Grant
                                                </a>
                                            <?php endif; ?>
                                            <a href="#" class="btn btn-outline-secondary btn-action">
                                                <i class="fas fa-ellipsis-h"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h5>No subscriptions found</h5>
                                <p class="text-muted">Try adjusting your search or filter criteria</p>
                                <a href="?" class="btn btn-primary rounded-pill mt-2">
                                    <i class="fas fa-sync-alt me-1"></i> Reset Filters
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Subscription pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>" aria-label="Previous">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            // Show limited pagination links
                            $start = max(1, $page - 2);
                            $end = min($totalPages, $page + 2);

                            if ($start > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=1&search=' . urlencode($search) . '&filter=' . $filter . '">1</a></li>';
                                if ($start > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            for ($i = $start; $i <= $end; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor;

                            if ($end < $totalPages) {
                                if ($end < $totalPages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '&search=' . urlencode($search) . '&filter=' . $filter . '">' . $totalPages . '</a></li>';
                            }
                            ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&filter=<?= $filter ?>" aria-label="Next">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactive elements
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to cards when they come into view
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });

            document.querySelectorAll('.subscription-card').forEach(card => {
                card.style.opacity = 0;
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(card);
            });
        });
    </script>
</body>

</html>