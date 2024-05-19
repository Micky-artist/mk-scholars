
<?php
if(isset($_POST['uploadPost'])){
$postTitle=$_POST['postTitle'];
$projStatus=$_POST['projStatus'];
$projectCat=$_POST['projectCat'];
$projectService=$_POST['projectService'];
$projectDescription=$_POST['projectDescription'];
$shortDescription=$_POST['shortDescription'];
$uploadDate=date("Y-m-d");
$uploadTime=date("h:i a");

$tmp_name=$_FILES["projectImg"]["tmp_name"];
$name=basename($_FILES["projectImg"]["name"]);
$uploads_dir = './uploads/posts';

$sql1="INSERT INTO posts(postTitle,shortDescription,projectDescription,projectImg1,projStatus,projectCat,projectService,uploadDate,uploadTime) 
VALUES ('$postTitle','$shortDescription','$projectDescription','$name',$projStatus,$projectCat,$projectService,'$uploadDate','$uploadTime')";
$run_data=mysqli_query($conn, $sql1);
 if($run_data){
   move_uploaded_file($tmp_name,"$uploads_dir/$name");
   echo"<script> alert ('Post has been inserted successfully');</script>";
 }
 else
 {
   echo"<script> alert(' Post has not been inserted successfully');</script>";
 }
}
 ?>
 