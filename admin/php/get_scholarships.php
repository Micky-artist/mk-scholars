<?php
// get_scholarships.php

// Include the database connection
include("../dbconnections/connection.php");

// Get the user ID from the query string
$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
// $userId= 131;
if ($userId > 0) {
    // Prepare the query to fetch scholarships applied by the user
    $query = "SELECT a.*, s.*, c.* 
        FROM ApplicationRequests a 
        JOIN scholarships s ON s.scholarshipId = a.ApplicationId JOIN countries c ON s.country =c.countryId
        WHERE a.UserId = $userId 
        ORDER BY a.RequestId DESC";

    // Execute the query
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        // Start building the HTML content
        $html = '<div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Scholarship Title</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Country</th>
                            </tr>
                        </thead>
                        <tbody>';

        $counter = 1;
        while ($row = mysqli_fetch_assoc($result)) {
            $html .= '<tr>
                        <td>' . $counter++ . '</td>
                        <td>' . htmlspecialchars($row['scholarshipTitle']) . '</td>
                        <td>' . htmlspecialchars($row['RequestDate'] . ' ' . $row['RequestTime']) . '</td>
                        <td>' . htmlspecialchars($row['Status']) . '</td>
                        <td>' . htmlspecialchars($row['amount']) . '</td>
                        <td>' . htmlspecialchars($row['country']) . '</td>
                      </tr>';
        }

        $html .= '</tbody></table></div>';
    } else {
        $html = '<div class="alert alert-info">No scholarships applied by this user.</div>';
    }
} else {
    $html = '<div class="alert alert-danger">Invalid user ID.</div>';
}

// Output the HTML content
echo $html;

// Close the database connection
mysqli_close($conn);
?>