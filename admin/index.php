<?php
// Ensure session exists for admin landing
session_start();
header("Location: home");
exit;
?>