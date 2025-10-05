<?php
if(isset($_SESSION['AdminName']) && isset($_SESSION['adminId'])){
    header("Location: index");
    exit;
}
