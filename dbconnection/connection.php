<?php
date_default_timezone_set('Africa/Kigali');
// $conn=mysqli_connect('localhost','u947191862_icyezainterior','IcyezaInteriors123@','u947191862_icyezainterior');
$conn=mysqli_connect('localhost','root','','mkscholars');
if(!$conn){
    echo "not connected to db";
}


$class='';
$msg='';
$username='';
$email='';