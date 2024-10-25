<?php
session_start();
include("../dbconnections/connection.php");
include("./validateAdminSession.php");


if ((isset($_GET['i']) && !empty(isset($_GET['i'])) && (isset($_GET['n']) && !empty(isset($_GET['n']))))) {
    $Id = $_GET['i'];
    if (isset($_GET['a']) && $_GET['a'] == "publishScholarship") {
        $publishScholarship = mysqli_query($conn, "UPDATE scholarships SET scholarshipStatus=1 WHERE scholarshipId=$Id");
        if ($publishScholarship) {
            echo ('
<script type="text/javascript">
                window.location.href="../scholarships"

</script>
');
        }
    } else if (isset($_GET['a']) && $_GET['a'] == "unPublishScholarship") {
        $unPublishScholarship = mysqli_query($conn, "UPDATE scholarships SET scholarshipStatus=0 WHERE scholarshipId=$Id");
        if ($unPublishScholarship) {
            echo ('
<script type="text/javascript">
                window.location.href="../scholarships"

</script>
');
        }
    } else if (isset($_GET['a']) && $_GET['a'] == "publishPost") {
        $publishPost = mysqli_query($conn, "UPDATE posts SET projStatus=1 WHERE postId=$Id");
        if ($publishPost) {
            echo ('
<script type="text/javascript">
                window.location.href="../scholarships"

</script>
');
        }
    } else if (isset($_GET['a']) && $_GET['a'] == "unPublishPost") {
        $unPublishPost = mysqli_query($conn, "UPDATE posts SET projStatus=0 WHERE postId=$Id");
        if ($unPublishPost) {
            echo ('
<script type="text/javascript">
                window.location.href="../scholarships"

</script>
');
        }
    } else if (isset($_GET['a']) && $_GET['a'] == "deleteScholarship") {

        $selectImageToDelete = mysqli_query($conn, "SELECT scholarshipId,scholarshipImage FROM scholarships WHERE scholarshipId=$Id");
        if ($selectImageToDelete->num_rows > 0) {
            $imageNameData = mysqli_fetch_assoc($selectImageToDelete);
            $imageName = $imageNameData['image1'];
            $filename = "../uploads/posts" . $imageName;

            if (file_exists($filename)) {
                unlink($filename);
                $deleteScholarship = mysqli_query($conn, "DELETE FROM scholarships WHERE scholarshipId=$Id");
                // echo 'File ' . $filename . ' has been deleted';
                echo ('
                <script type="text/javascript">
                alert("Scholarship has been successfuly deleted");
                window.location.href="../scholarships"
                </script>
                ');
            } else {
                // echo 'Could not delete ' . $filename . ', file does not exist';
                echo ('
                <script type="text/javascript">
                window.location.href="../404"
                </script>
                ');
            }
        }
    } else if (isset($_GET['a']) && $_GET['a'] == "deletePost") {
        $selectImageToDelete = mysqli_query($conn, "SELECT postId,projectImg1 FROM posts WHERE postId=$Id");
        if ($selectImageToDelete->num_rows > 0) {
            $imageNameData = mysqli_fetch_assoc($selectImageToDelete);
            $imageName = $imageNameData['projectImg1'];
            $filename = "../uploads/posts/" . $imageName;

            if (file_exists($filename)) {
                unlink($filename);
                $deletePost = mysqli_query($conn, "DELETE FROM posts WHERE postId=$Id");
                // echo 'File ' . $filename . ' has been deleted';
                echo ('
                <script type="text/javascript">
                alert("Post has been successfuly deleted");
                history.back()
                </script>
                ');
            } else {
                // echo 'Could not delete ' . $filename . ', file does not exist';
                echo ('
                <script type="text/javascript">
                window.location.href="404"
                </script>
                ');
            }
        }
    } else {
        echo ('
        <script type="text/javascript">
        window.location.href="404"
        </script>
        ');
    }
} else {
    echo ('
<script type="text/javascript">
window.location.href="404";
</script>
');
}
