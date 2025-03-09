<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Activity Logs</h5>
        </div>
        <div class="card-body">
            <?php
            $selectLogs = mysqli_query($conn, "SELECT l.*, u.userId, u.username  FROM Logs l JOIN users u ON l.userId = u.userId ORDER BY logId DESC");
            if (mysqli_num_rows($selectLogs) > 0) {
                while ($logData = mysqli_fetch_assoc($selectLogs)) {
            ?>
                    <div class="log-item mb-3 p-3 border rounded">
                        <div class="d-flex flex-column">
                            <div class="mb-2">
                                <p class="mb-0 text-muted">
                                    <strong><?php echo $logData['username']; ?></strong> made changes at <?php echo $logData['logTime']; ?> on <?php echo $logData['logDate']; ?>
                                </p>
                            </div>
                            <div>
                                <p class="mb-0"><?php echo $logData['logMessage']; ?></p>
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
        </div>
    </div>
</div>