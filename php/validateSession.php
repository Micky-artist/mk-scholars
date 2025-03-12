<?php
if (!isset($_SESSION['username']) && !isset($_SESSION['userId'])) {
    header("location: ./login");
    exit;

}
?>