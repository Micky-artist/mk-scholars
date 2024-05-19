<?php
if (isset($_POST['updateService'])) {
    if ((isset($_GET['i']) && !empty(isset($_GET['i'])) && (isset($_GET['n']) && !empty(isset($_GET['n']))))) {
        $serviceId = $_GET['i'];
        $serviceName = $_POST['serviceName'];
        $servicestatus = $_POST['servicestatus'];
        $serviceDescription = $_POST['serviceDescription'];
        $shortDescription=$_POST['shortDescription'];
        $uploadDate = date("Y-m-d");
        $uploadTime = date("h:i a");

        $tmp_name = $_FILES["image"]["tmp_name"];
        $name = basename($_FILES["image"]["name"]);
        $uploads_dir = './uploads/services';
        if(empty($name)){
        $sql1 = "UPDATE services SET servicename='$serviceName',shortDescription='$shortDescription',description='$serviceDescription',
        servicestatus=$servicestatus,uploadDate='$uploadDate',uploadTime='$uploadTime' WHERE serviceId=$serviceId";
        }else{
            $selectCurrentImage = mysqli_query($conn, "SELECT serviceId, image1 FROM services WHERE serviceId=$serviceId");
            if ($selectCurrentImage->num_rows > 0) {
                $currentImageName = mysqli_fetch_assoc($selectCurrentImage);
                $imageName = $currentImageName['image1'];
                $filename = "./uploads/services/" . $imageName;

                if (file_exists($filename)) {
                    unlink($filename);
                }
            } else {
                echo "No image available";
            }
        $sql1 = "UPDATE services SET servicename='$serviceName',shortDescription='$shortDescription',description='$serviceDescription',image1='$name',
        servicestatus=$servicestatus,uploadDate='$uploadDate',uploadTime='$uploadTime' WHERE serviceId=$serviceId";

        }
        $run_data = mysqli_query($conn, $sql1);
        if ($run_data) {
            move_uploaded_file($tmp_name, "$uploads_dir/$name");
            echo '<script> alert("Service updated successfully");
            window.location.href="services";
            </script>';
        } else {
            echo '<script> alert("Service not updated");
            window.location.href="services";
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
?>