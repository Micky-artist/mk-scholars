<?php
if(isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    $query = mysqli_query($conn, "SELECT * FROM applied_scholarships WHERE user_id = $user_id");
    
    if(mysqli_num_rows($query) > 0) {
        echo '<ul class="list-group">';
        while($row = mysqli_fetch_assoc($query)) {
            echo '<li class="list-group-item">';
            echo '<h5>'.$row['scholarship_name'].'</h5>';
            echo '<p class="mb-1">Applied Date: '.$row['applied_date'].'</p>';
            echo '<p class="mb-1">Status: '.$row['status'].'</p>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="alert alert-info">No scholarships applied</div>';
    }
} else {
    echo '<div class="alert alert-danger">Invalid request</div>';
}
?>