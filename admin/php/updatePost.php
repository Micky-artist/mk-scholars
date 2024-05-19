<?php
if (isset($_POST['updatePost'])) {
    if ((isset($_GET['i']) && !empty(isset($_GET['i'])) && (isset($_GET['n']) && !empty(isset($_GET['n']))))) {
        $projectId = $_GET['i'];
        // if(isset($_POST['uploadPost'])){
        $postTitle = $_POST['postTitle'];
        $projStatus = $_POST['projStatus'];
        $projectCat = $_POST['projectCat'];
        $projectService = $_POST['projectService'];
        $projectDescription = $_POST['projectDescription'];
        $shortDescription = $_POST['shortDescription'];
        $uploadDate = date("Y-m-d");
        $uploadTime = date("h:i a");

        $tmp_name = $_FILES["projectImg"]["tmp_name"];
        $name = basename($_FILES["projectImg"]["name"]);
        $uploads_dir = './uploads/posts';

        if (empty($name)) {
            $sql1 = "UPDATE posts SET postTitle='$postTitle',shortDescription='$shortDescription',projectDescription='$projectDescription',
        projStatus=$projStatus,projectCat=$projectCat,projectService=$projectService,uploadDate='$uploadDate',uploadTime='$uploadTime' WHERE postId=$projectId";
        } else {
            $selectCurrentImage = mysqli_query($conn, "SELECT postId, projectImg1 FROM posts WHERE postId=$projectId");
            if ($selectCurrentImage->num_rows > 0) {
                $currentImageName = mysqli_fetch_assoc($selectCurrentImage);
                $imageName = $currentImageName['projectImg1'];
                $filename = "./uploads/posts/" . $imageName;

                if (file_exists($filename)) {
                    unlink($filename);
                }
            } else {
                echo "No image available";
            }
            $sql1 = "UPDATE posts SET postTitle='$postTitle',shortDescription='$shortDescription',projectDescription='$projectDescription',projectImg1='$name',
        projStatus=$projStatus,projectCat=$projectCat,projectService=$projectService,uploadDate='$uploadDate',uploadTime='$uploadTime' WHERE postId=$projectId";
        }

        $run_data = mysqli_query($conn, $sql1);
        if ($run_data) {
            move_uploaded_file($tmp_name, "$uploads_dir/$name");
            echo '<script> alert("Post updated successfully");
            window.location.href="projects";
            </script>';
        } else {
            echo '<script> alert("Post not updated");
            window.location.href="projects";
            </script>';
        }

    } else {
        echo ('
<script type="text/javascript">
window.location.href="404";
</script>
');
    }
}
