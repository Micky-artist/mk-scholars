<?php

if((isset($_GET['scholarship-id']) && $_GET['scholarship-id'] != NULL ) && (isset($_GET['scholarship-title']) && $_GET['scholarship-title'] != NULL )){
    $scholarshipId=$_GET['scholarship-id'];
    $scholarshipTitle=$_GET['scholarship-title'];

    $selectScholarshipDetails=mysqli_query($conn,"SELECT * FROM scholarships WHERE scholarshipId=$scholarshipId AND scholarshipStatus != 0 ");
    if($selectScholarshipDetails->num_rows>0){
        $scholarshipData = mysqli_fetch_assoc($selectScholarshipDetails);
    }else{
        echo '<script type="text/javascript">
	window.location.href="home";
</script>';
    }
}else{
    echo '<script type="text/javascript">
	window.location.href="home";
</script>';
}

?>