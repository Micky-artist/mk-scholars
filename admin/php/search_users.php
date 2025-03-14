<?php
session_start();
include("../dbconnections/connection.php");

// Get the search query from the AJAX request
if (isset($_GET['query'])) {
    $query = mysqli_real_escape_string($conn, $_GET['query']); // Sanitize the input

    // Search for users by name or email
    $sql = "SELECT `NoUserId`, `NoUsername`, `NoEmail`, `NoPhone`, `NoPassword`, `NoStatus`, `NoCreationDate` 
            FROM `normUsers` 
            WHERE `NoUsername` LIKE '%$query%' OR `NoEmail` LIKE '%$query%'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($user = mysqli_fetch_assoc($result)) {
            // Generate HTML for each search result
            echo '
            <div class="list-group-item">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-user-circle fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">' . htmlspecialchars($user['NoUsername']) . '</h6>
                        <small class="text-muted">' . htmlspecialchars($user['NoEmail']) . '</small>
                    </div>
                    <div class="ms-auto">
                        <a href="?username=' . urlencode($user['NoUsername']) . '&userId=' . $user['NoUserId'] . '" class="btn btn-sm btn-primary">
                            <i class="fas fa-comment"></i> Chat
                        </a>
                    </div>
                </div>
            </div>';
        }
    } else {
        // No users found
        echo '<div class="list-group-item text-center">No users found.</div>';
    }
} else {
    // No query provided
    echo '<div class="list-group-item text-center">Invalid request.</div>';
}

// Close the database connection
mysqli_close($conn);
?>