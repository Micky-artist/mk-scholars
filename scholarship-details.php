<?php

include("./dbconnection/connection.php");
include("./php/selectScholarshipDetails.php")

?>

<!DOCTYPE html>
<html lang="en">
<title>Mk Scholars <?php echo $scholarshipData['scholarshipTitle'] ?></title>

<?php include("./partials/head.php") ?>

<head>
	<!-- <meta property="og:image:secure_url" content="https://admin.mkscholars.com/uploads/posts/<?php echo $$scholarshipData['scholarshipImage'] ?>"> -->
	<meta name="description" content="<?php echo $scholarshipData['scholarshipTitle'] ?>">
</head>


<body>
	<div class="main-page-wrapper">

		<?php
		include("./partials/navigation.php");
		?>

		<!-- 
			=============================================
				Theme Inner Banner
			============================================== 
			-->

		<div class="theme-inner-banner" style="background: url(https://admin.mkscholars.com/uploads/posts/<?php echo $scholarshipData['scholarshipImage'] ?>) no-repeat center;background-size:cover;">
			<div class="opacity">
				<div class="container">
					<h3><?php echo $scholarshipData['scholarshipTitle'] ?></h3>
					<ul>
						<li><a href="home">Home</a></li>
						<li>/</li>
						<li>More</li>
					</ul>
				</div> <!-- /.container -->
			</div> <!-- /.opacity -->
		</div> <!-- /.theme-inner-banner -->


		<!-- 
			=============================================
				Blog Details
			============================================== 
			-->
		<div class="theme-details-page blog-details">
			<div class="container">
				<div class="row">
					<div class="col-md-9 col-xs-12 theme-large-sidebar">
						<a style="font-size: 16px; background-color: green; padding: 10px; color: white;" class="btn btn-primary" target="_blank" href="https://chat.whatsapp.com/Jm0hfcLeRVm3pbnNPx82GD">Join What'sApp group</a>

						<div class="SocialMediaIcons">
							<a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#87CEEB" class="bi bi-facebook" viewBox="0 0 16 16">
									<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
								</svg></a>
							<a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#000" class="bi bi-twitter-x" viewBox="0 0 16 16">
									<path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
								</svg></a>
							<a href="https://www.youtube.com/@mkscholars"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="red" class="bi bi-youtube" viewBox="0 0 16 16">
									<path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z" />
								</svg></a>
						</div>
						<div class="title">
							<span><?php echo $scholarshipData['scholarshipUpdateDate'] ?></span>
							<h4><?php echo $scholarshipData['scholarshipTitle'] ?></h4>

							<!-- <ul>
									<li>by admin</li>
									<li>|</li>
									<li>Business</li>
								</ul> -->
						</div> <!-- /.title -->
						<img src="https://admin.mkscholars.com/uploads/posts/<?php echo $scholarshipData['scholarshipImage'] ?>" alt=""> <br>
						<p><?php echo $scholarshipData['scholarshipDetails'] ?></p>
						<br>
						<div>
							<a style="font-size: 16px; background-color: #4183E6; padding: 10px; color: white;" class="btn btn-primary" target="_blank" href="<?php echo $scholarshipData['scholarshipLink'] ?>">Open Scholarship Link</a>
							<a style="font-size: 16px; background-color: red; padding: 10px; color: white;" class="btn btn-primary" target="_blank" href="<?php echo $scholarshipData['scholarshipYoutubeLink'] ?>">Youtube Guide Video</a>
							<a style="font-size: 16px; background-color: green; padding: 10px; color: white;" class="btn btn-primary" target="_blank" href="https://chat.whatsapp.com/Jm0hfcLeRVm3pbnNPx82GD">Join What'sApp group</a>
						</div>
					</div> <!-- /.theme-large-sidebar -->


					<div class="col-md-3 col-sm-6 col-xs-12 theme-sidebar">
						<div>
							<?php
							$selectVideos=mysqli_query($conn,"SELECT * FROM youtubeVideos WHERE VideoStatus=1");
							if($selectVideos->num_rows>0){
								while($videoData=mysqli_fetch_assoc($selectVideos)){
									echo $videoData['videoLink'];
								}
							}
							?>
						</div>
						<form method="post" class="sidebar-search">
							<input type="text" name="searchValue" placeholder="Search...">
							<button name="search" class="s-color-bg tran3s"><i class="fa fa-search" aria-hidden="true"></i></button>
						</form>
						<div class="sidebar-recent-post">
							<h5>Recently Uploaded</h5>

							<!-- TO BE USED IN FUTURE -->
							<ul>
								<?php
								$PresentScholarship=$_GET['scholarship-id'];
								if(isset($_POST['search'])){
									$searchValue = $_POST['searchValue'];
									$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 AND scholarshipDetails LIKE '%$searchValue%' ORDER BY scholarshipId DESC LIMIT 7");
								}else{
									$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 AND scholarshipId != $PresentScholarship ORDER BY scholarshipId DESC LIMIT 7");
								}
								if ($selectScholarships->num_rows > 0) {
									while ($getScholarships = mysqli_fetch_assoc($selectScholarships)) {
								?>
										<li class="clearfix">
											<img src="https://admin.mkscholars.com/uploads/posts/<?php echo $getScholarships['scholarshipImage'] ?>" alt="" class="float-left">
											<div class="post float-left">
												<a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo $getScholarships['scholarshipTitle'] ?>" class="tran3s"><?php echo $getScholarships['scholarshipTitle'] ?></a>
												<span><?php echo $getScholarships['scholarshipUpdateDate'] ?></span>
											</div>
										</li>
								<?php
									}
								}else{
									?>
									<li class="clearfix">
										No Results found
									</li>
									<?php
								}
								?>
							</ul>
						</div>



					</div> <!-- /.theme-sidebar -->
				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</div> <!-- /.theme-details-page -->




		<!-- 
			=============================================
				Footer
			============================================== 
			-->
		<?php
		include("./partials/footer.php");
		?>



		<!-- Scroll Top Button -->
		<button class="scroll-top tran3s">
			<i class="fa fa-angle-up" aria-hidden="true"></i>
		</button>


		<!-- Js File_________________________________ -->

		<!-- j Query -->
		<script type="text/javascript" src="vendor/jquery.2.2.3.min.js"></script>
		<!-- Bootstrap Select JS -->
		<script type="text/javascript" src="vendor/bootstrap-select/dist/js/bootstrap-select.js"></script>
		<!-- Bootstrap JS -->
		<script type="text/javascript" src="vendor/bootstrap/bootstrap.min.js"></script>

		<!-- Vendor js _________ -->
		<!-- Mega menu  -->
		<script type="text/javascript" src="vendor/bootstrap-mega-menu/js/menu.js"></script>

		<!-- WOW js -->
		<script type="text/javascript" src="vendor/WOW-master/dist/wow.min.js"></script>
		<!-- owl.carousel -->
		<script type="text/javascript" src="vendor/owl-carousel/owl.carousel.min.js"></script>
		<!-- Feedback Star -->
		<script type="text/javascript" src="vendor/rateYo-master/src/jquery.rateyo.js"></script>

		<!-- Theme js -->
		<script type="text/javascript" src="js/theme.js"></script>

	</div> <!-- /.main-page-wrapper -->
</body>

</html>