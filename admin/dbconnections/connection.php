<?php
date_default_timezone_set('Africa/Kigali');
// $conn=mysqli_connect('localhost','u588339496_mkscholars','Mkscholars123@','u588339496_mkscholars');
$conn=mysqli_connect('localhost','root','','mkscholars');
if(!$conn){
    echo "not connected to db";
}


$class='';
$msg='';
$username='';
$email='';