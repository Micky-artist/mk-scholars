
<?php
if(isset($_POST['uploadCountry'])){
$countryName=mysqli_real_escape_string($conn, $_POST['countryName']);
$countryStatus=mysqli_real_escape_string($conn, $_POST['countryStatus']);

$sql1="INSERT INTO countries(CountryName,CountryStatus) 
VALUES ('$countryName',$countryStatus)";
$run_data=mysqli_query($conn, $sql1);
 if($run_data){
   echo"<script> alert ('Country has been inserted successfully');</script>";
 }
 else
 {
   echo"<script> alert('Country has not been inserted successfully');</script>";
 }
}
 ?>
 