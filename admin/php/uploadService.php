
<?php
if(isset($_POST['uploadService'])){
$serviceName=$_POST['serviceName'];
$servicestatus=$_POST['servicestatus'];
$serviceDescription=$_POST['serviceDescription'];
$shortDescription=$_POST['shortDescription'];
$uploadDate=date("Y-m-d");
$uploadTime=date("h:i a");

$tmp_name=$_FILES["image"]["tmp_name"];
$name=basename($_FILES["image"]["name"]);
$uploads_dir = './uploads/services';

$sql1="INSERT INTO services(servicename,shortDescription,description,image1,servicestatus,uploadDate,uploadTime) 
VALUES ('$serviceName','$shortDescription','$serviceDescription','$name',$servicestatus,'$uploadDate','$uploadTime')";
$run_data=mysqli_query($conn, $sql1);
 if($run_data){
   move_uploaded_file($tmp_name,"$uploads_dir/$name");
   echo"<script> alert ('Service has inserted successfully');</script>";
 }
 else
 {
   echo"<script> alert('Service has not been inserted successfully');</script>";
 }
}
 ?>
 