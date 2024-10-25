<?php
if (isset($_POST['updateScholarship'])) {
    if (isset($_GET['edit']) && !empty($_GET['edit']) && isset($_GET['i']) && !empty($_GET['i'])) {
        $scholarshipId = $_GET['i'];
        $ScholarshipTitle = mysqli_real_escape_string($conn, $_POST['ScholarshipTitle']);
        $ScholarshipStatus = mysqli_real_escape_string($conn, $_POST['ScholarshipStatus']);
        $ScholarshipPrice = mysqli_real_escape_string($conn, $_POST['ScholarshipPrice']);
        $ScholarshipCountry = mysqli_real_escape_string($conn, $_POST['ScholarshipCountry']);
        $ScholarshipLink = mysqli_real_escape_string($conn, $_POST['ScholarshipLink']);
        $scholarshipYoutubeLink = mysqli_real_escape_string($conn, $_POST['scholarshipYoutubeLink']);
        $ScholarshipDescription = $_POST['ScholarshipDescription'];

        $uploadDate = date("Y-m-d");
        $uploadTime = date("h:i a");

        $tmp_name = $_FILES["ScholarshipImage"]["tmp_name"];
        $ScholarshipImage = basename($_FILES["ScholarshipImage"]["name"]);
        $uploads_dir = './uploads/posts';

       

        if (empty($ScholarshipStatus) || empty($ScholarshipCountry)) {
            echo '<script>alert("Some inputs are missing");</script>';
        } else {
            if($ScholarshipStatus==10){
                $ScholarshipStatus=0;
            }
            if (empty($ScholarshipImage)) {
                // Update without changing the image
                $sql1 = "UPDATE scholarships 
                         SET scholarshipTitle='$ScholarshipTitle', 
                             scholarshipDetails='$ScholarshipDescription', 
                             scholarshipUpdateDate='$uploadDate', 
                             scholarshipLink='$ScholarshipLink', 
                             scholarshipYoutubeLink='$scholarshipYoutubeLink', 
                             scholarshipStatus='$ScholarshipStatus', 
                             amount='$ScholarshipPrice', 
                             country='$ScholarshipCountry' 
                         WHERE scholarshipId=$scholarshipId";
            } else {
                // Delete the old image
                $selectCurrentImage = mysqli_query($conn, "SELECT scholarshipImage FROM scholarships WHERE scholarshipId=$scholarshipId");
                if ($selectCurrentImage->num_rows > 0) {
                    $currentImageName = mysqli_fetch_assoc($selectCurrentImage);
                    $imageName = $currentImageName['scholarshipImage'];
                    $filename = "./uploads/posts/" . $imageName;

                    if (file_exists($filename)) {
                        unlink($filename);
                    }
                }

                // Update with the new image
                $sql1 = "UPDATE scholarships 
                         SET scholarshipTitle='$ScholarshipTitle', 
                             scholarshipDetails='$ScholarshipDescription', 
                             scholarshipUpdateDate='$uploadDate', 
                             scholarshipLink='$ScholarshipLink', 
                             scholarshipYoutubeLink='$scholarshipYoutubeLink', 
                             scholarshipImage='$ScholarshipImage', 
                             scholarshipStatus='$ScholarshipStatus', 
                             amount='$ScholarshipPrice', 
                             country='$ScholarshipCountry' 
                         WHERE scholarshipId=$scholarshipId";
            }

            // Execute the query and handle the file upload
            $run_data = mysqli_query($conn, $sql1);
            if ($run_data) {
                if (!empty($ScholarshipImage)) {
                    move_uploaded_file($tmp_name, "$uploads_dir/$ScholarshipImage");
                }
                echo '<script>alert("Scholarship updated successfully"); window.location.href="scholarships";</script>';
            } else {
                echo '<script>alert("Scholarship not updated"); window.location.href="scholarships";</script>';
            }
        }
    } else {
        echo '<script>window.location.href="404";</script>';
    }
}
