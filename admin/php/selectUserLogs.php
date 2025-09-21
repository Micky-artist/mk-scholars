<?php

// Handle Delete Log
if (isset($_POST['deleteLog'])) {
    $logId = $_POST['deleteLog'];
    $deleteQuery = mysqli_query($conn, "DELETE FROM Logs WHERE logId = $logId");
    if ($deleteQuery) {
        echo "<script>alert('Log deleted successfully!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Failed to delete log.');</script>";
    }
}

// Handle Toggle Read/Unread Status
if (isset($_POST['toggleReadStatus'])) {
    $logId = $_POST['toggleReadStatus'];
    $currentStatus = mysqli_fetch_assoc(mysqli_query($conn, "SELECT logStatus FROM Logs WHERE logId = $logId"))['logStatus'];
    $newStatus = ($currentStatus == 1) ? 0 : 1;
    $updateQuery = mysqli_query($conn, "UPDATE Logs SET logStatus = $newStatus WHERE logId = $logId");
    if ($updateQuery) {
        echo "<script>alert('Log status updated successfully!'); window.location.href = window.location.href;</script>";
    } else {
        echo "<script>alert('Failed to update log status.');</script>";
    }
}

// Handle Bulk Actions (Delete/Mark as Read/Mark as Unread)
if (isset($_POST['selectedLogs'])) {
    $selectedLogs = $_POST['selectedLogs'];
    if (isset($_POST['deleteSelected'])) {
        // Delete Selected Logs
        $deleteQuery = mysqli_query($conn, "DELETE FROM Logs WHERE logId IN (" . implode(',', $selectedLogs) . ")");
        if ($deleteQuery) {
            echo "<script>alert('Selected logs deleted successfully!'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Failed to delete selected logs.');</script>";
        }
    } elseif (isset($_POST['markAsRead'])) {
        // Mark Selected Logs as Read
        $updateQuery = mysqli_query($conn, "UPDATE Logs SET logStatus = 1 WHERE logId IN (" . implode(',', $selectedLogs) . ")");
        if ($updateQuery) {
            echo "<script>alert('Selected logs marked as read successfully!'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Failed to mark selected logs as read.');</script>";
        }
    } elseif (isset($_POST['markAsUnread'])) {
        // Mark Selected Logs as Unread
        $updateQuery = mysqli_query($conn, "UPDATE Logs SET logStatus = 0 WHERE logId IN (" . implode(',', $selectedLogs) . ")");
        if ($updateQuery) {
            echo "<script>alert('Selected logs marked as unread successfully!'); window.location.href = window.location.href;</script>";
        } else {
            echo "<script>alert('Failed to mark selected logs as unread.');</script>";
        }
    }
}
?>

<style>
.pagination .page-link {
    color: #007bff;
    border: 1px solid #dee2e6;
    padding: 0.375rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.25rem;
}

.pagination .page-item.active .page-link {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination .page-link:hover {
    color: #0056b3;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.log-item {
    transition: all 0.3s ease;
}

.log-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.bg-warning {
    background-color: #fff3cd !important;
    border-left: 4px solid #ffc107 !important;
}
</style>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Activity Logs</h5>
                <div class="text-light">
                    <?php if (isset($totalRecords)): ?>
                        <small>Total: <?php echo $totalRecords; ?> records | Page <?php echo $currentPage; ?> of <?php echo $totalPages; ?></small>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <!-- Bulk Action Buttons -->
                <form method="POST" action="?page=<?php echo $currentPage; ?>">
                    <div class="mb-3">
                        <?php if($_SESSION['adminId'] == 2){ ?> 
                        <button type="submit" name="deleteSelected" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i> Delete Selected
                        </button>
                        <?php } ?>
                        <button type="submit" name="markAsRead" class="btn btn-secondary btn-sm">
                            <i class="fas fa-check"></i> Mark Selected as Read
                        </button>
                        <button type="submit" name="markAsUnread" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times"></i> Mark Selected as Unread
                        </button>
                    </div>

                    <?php
                    // Pagination settings
                    $recordsPerPage = 20;
                    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $currentPage = max(1, $currentPage); // Ensure page is at least 1
                    $offset = ($currentPage - 1) * $recordsPerPage;
                    
                    // Get total number of records
                    $totalRecordsQuery = mysqli_query($conn, "SELECT COUNT(*) as total FROM Logs l JOIN users u ON l.userId = u.userId");
                    $totalRecords = mysqli_fetch_assoc($totalRecordsQuery)['total'];
                    $totalPages = ceil($totalRecords / $recordsPerPage);
                    
                    // Fetch logs with user details (paginated)
                    $selectLogs = mysqli_query($conn, "SELECT l.*, u.userId, u.username FROM Logs l JOIN users u ON l.userId = u.userId ORDER BY logId DESC LIMIT $recordsPerPage OFFSET $offset");
                    if (mysqli_num_rows($selectLogs) > 0) {
                        while ($logData = mysqli_fetch_assoc($selectLogs)) {
                            // Determine background color based on logStatus and selection
                            $bgColor = '';
                            if (isset($_POST['selectedLogs'])) {
                                if (in_array($logData['logId'], $_POST['selectedLogs'])) {
                                    $bgColor = 'bg-warning'; // Selected logs
                                }
                            }
                            if ($logData['logStatus'] == 0) {
                                $bgColor = 'bg-light'; 
                            } elseif ($logData['logStatus'] == 1) {
                                $bgColor = 'bg-white'; // Seen logs
                            }
                    ?>
                            <div class="log-item mb-3 p-3 border rounded <?php echo $bgColor; ?>">
                                <div class="d-flex flex-column">
                                    <!-- Checkbox for selecting the log -->
                                    <div class="form-check mb-2">
                                        <input type="checkbox" class="form-check-input log-checkbox" name="selectedLogs[]" id="log-<?php echo $logData['logId']; ?>" value="<?php echo $logData['logId']; ?>">
                                        <label class="form-check-label" for="log-<?php echo $logData['logId']; ?>">Select</label>
                                    </div>

                                    <!-- User and Timestamp -->
                                    <div class="mb-2">
                                        <p class="mb-0 text-muted">
                                            <strong><?php echo $logData['username']; ?></strong> made changes at <?php echo $logData['logTime']; ?> on <?php echo $logData['logDate']; ?>
                                        </p>
                                    </div>

                                    <!-- Log Description -->
                                    <div class="mb-2">
                                        <p class="mb-0"><?php echo $logData['logMessage']; ?></p>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2">
                                        <!-- Delete Button -->
                                        <?php if($_SESSION['adminId'] == 2){ ?> 
                                        <button type="submit" name="deleteLog" value="<?php echo $logData['logId']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this log?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        <?php } ?>
                                        <!-- Mark as Read/Unread Button -->
                                        <button type="submit" name="toggleReadStatus" value="<?php echo $logData['logId']; ?>" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-check"></i> Mark as <?php echo ($logData['logStatus'] == 1 ? 'Unread' : 'Read'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                    ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No logs available.</p>
                        </div>
                    <?php
                    }
                    ?>
                </form>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div class="text-muted">
                        Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $recordsPerPage, $totalRecords); ?> of <?php echo $totalRecords; ?> records
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm mb-0">
                            <!-- Previous Page -->
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
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
                            
                            <!-- Page Numbers -->
                            <?php
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($totalPages, $currentPage + 2);
                            
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
                            
                            <!-- Page numbers in range -->
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?php echo ($i == $currentPage) ? 'active' : ''; ?>">
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
                            
                            <!-- Next Page -->
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
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
