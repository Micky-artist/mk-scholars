
<?php
if(isset($_POST['uploadScholarship'])){
$ScholarshipTitle=mysqli_real_escape_string($conn, $_POST['ScholarshipTitle']);
$ScholarshipStatus=mysqli_real_escape_string($conn, $_POST['ScholarshipStatus']);
$ScholarshipPrice=mysqli_real_escape_string($conn, $_POST['ScholarshipPrice']);
$ScholarshipCountry=mysqli_real_escape_string($conn, $_POST['ScholarshipCountry']);
$ScholarshipLink=mysqli_real_escape_string($conn, $_POST['ScholarshipLink']);
$scholarshipYoutubeLink=mysqli_real_escape_string($conn, $_POST['scholarshipYoutubeLink']);
$ScholarshipDescription=mysqli_real_escape_string($conn,$_POST['ScholarshipDescription']);

$uploadDate=date("Y-m-d");
$uploadTime=date("h:i a");

$tmp_name=$_FILES["ScholarshipImage"]["tmp_name"];
$ScholarshipImage=basename($_FILES["ScholarshipImage"]["name"]);
$uploads_dir = './uploads/posts';

$sql1="INSERT INTO scholarships(scholarshipTitle,scholarshipDetails,scholarshipUpdateDate,scholarshipLink,scholarshipYoutubeLink,scholarshipImage,scholarshipStatus,amount,country) 
VALUES ('$ScholarshipTitle','$ScholarshipDescription','$uploadDate','$ScholarshipLink','$scholarshipYoutubeLink','$ScholarshipImage',$ScholarshipStatus,'$ScholarshipPrice','$ScholarshipCountry')";
$run_data=mysqli_query($conn, $sql1);
 if($run_data){
   move_uploaded_file($tmp_name,"$uploads_dir/$ScholarshipImage");
   echo"<script> alert ('Scholarship has been inserted successfully');</script>";
 }
 else
 {
   echo"<script> alert('Scholarship has not been inserted successfully');</script>";
 }
}
 ?>
 