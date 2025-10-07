<?php
(session_status() == PHP_SESSION_NONE) ? session_start() : '';

// Try to include database connection safely
try {
    include_once("./dbconnection/connection.php");
} catch (Exception $e) {
    error_log("Database connection error in head.php: " . $e->getMessage());
    // Continue without database connection
}
?>
<head>
	<meta charset="UTF-8">
	<!-- For IE -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge">

	<!-- For Resposive Device -->
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>MK Scholars</title>

	<!-- Favicon -->
	<link rel="icon" type="image/png" sizes="56x56" href="">
	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	
	<link rel="stylesheet" type="text/css" href="css/responsive.css">
	<link rel="shortcut icon" href="./images/logo/logoRound.png" type="image/x-icon">
	<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-2301802228767379"
	crossorigin="anonymous"></script>
	
	<!-- Font Awesome for contact button -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>