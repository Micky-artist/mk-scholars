<?php
session_start();
include("./dbconnections/connection.php");

// Test database connection
$connectionStatus = '';
$subscriptions = [];
$totalCount = 0;

if (!$conn) {
    $connectionStatus = 'error';
    $errorMessage = 'Database connection failed. Please check your database configuration.';
} else {
    $connectionStatus = 'success';
    
    // Test query to get subscriptions
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
    ";
    
    $result = mysqli_query($conn, $query);
    
    if ($result === false) {
        $connectionStatus = 'error';
        $errorMessage = 'Query failed: ' . mysqli_error($conn);
    } else {
        $subscriptions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $totalCount = count($subscriptions);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
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
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --primary-dark: #3730a3;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        .main-container {
            padding: 2rem 0;
            min-height: 100vh;
        }

        .page-header {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 1.1rem;
            margin-bottom: 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-200);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
        }

        .stat-icon.success {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning-color), #d97706);
            color: white;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--gray-600);
            font-size: 0.875rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        .content-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .content-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .add-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .add-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateY(-1px);
        }

        .table-container {
            padding: 0;
        }

        .subscriptions-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .subscriptions-table th {
            background: var(--gray-50);
            color: var(--gray-700);
            font-weight: 600;
            padding: 1rem 1.5rem;
            text-align: left;
            border-bottom: 2px solid var(--gray-200);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .subscriptions-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
        }

        .subscriptions-table tbody tr {
            transition: all 0.2s ease;
        }

        .subscriptions-table tbody tr:hover {
            background: var(--gray-50);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
        }

        .status-badge.inactive {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
        }

        .action-btn {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
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
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border-color: rgba(239, 68, 68, 0.2);
        }

        .action-btn.stop:hover {
            background: var(--danger-color);
            color: white;
        }

        .action-btn.grant {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .action-btn.grant:hover {
            background: var(--success-color);
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--gray-500);
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--gray-700);
        }

        .empty-state p {
            font-size: 1rem;
            margin-bottom: 0;
        }

        .error-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--danger-color);
        }

        .error-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .error-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .connection-status {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .connection-status.success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .connection-status.error {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem 0;
            }
            
            .page-header {
                padding: 1.5rem;
                margin-bottom: 1.5rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .subscriptions-table th,
            .subscriptions-table td {
                padding: 0.75rem 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Connection Status Indicator -->
    <div class="connection-status <?php echo $connectionStatus; ?>">
        <i class="fas fa-<?php echo $connectionStatus === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
        <span>Database <?php echo $connectionStatus === 'success' ? 'Connected' : 'Error'; ?></span>
    </div>

    <div class="container-fluid main-container">
        <div class="container">
            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-calendar-check"></i>
                    Subscriptions Management
                </h1>
                <p class="page-subtitle">Manage user subscriptions and access permissions</p>
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
                    <div class="stat-value">
                        <?php 
                        $activeCount = 0;
                        if ($connectionStatus === 'success') {
                            foreach ($subscriptions as $sub) {
                                if ((int)$sub['SubscriptionStatus'] === 1) $activeCount++;
                            }
                        }
                        echo $activeCount;
                        ?>
                    </div>
                    <div class="stat-label">Active Subscriptions</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-pause-circle"></i>
                    </div>
                    <div class="stat-value">
                        <?php echo $totalCount - $activeCount; ?>
                    </div>
                    <div class="stat-label">Inactive Subscriptions</div>
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
                
                <div class="table-container">
                    <?php if ($connectionStatus === 'error'): ?>
                        <div class="error-state">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h3>Database Connection Error</h3>
                            <p><?php echo htmlspecialchars($errorMessage); ?></p>
                        </div>
                    <?php elseif (empty($subscriptions)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No Subscriptions Found</h3>
                            <p>There are currently no subscriptions in the database.</p>
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
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide connection status after 5 seconds
        setTimeout(() => {
            const status = document.querySelector('.connection-status');
            if (status) {
                status.style.opacity = '0';
                status.style.transform = 'translateX(100%)';
                status.style.transition = 'all 0.3s ease';
                setTimeout(() => status.remove(), 300);
            }
        }, 5000);

        // Add smooth animations to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.subscriptions-table tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>