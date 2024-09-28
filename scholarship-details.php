<?php

include("./dbconnection/connection.php");
include("./php/selectScholarshipDetails.php")

?>

<!DOCTYPE html>
<html lang="en">
	
<?php include("./partials/head.php") ?>


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

			<div class="theme-inner-banner" style="background: url(images/home/banner-5.jpg) no-repeat center;background-size:cover;">
				<div class="opacity">
					<div class="container">
						<h3><?php echo $scholarshipData['scholarshipTitle'] ?></h3>
						<ul>
							<li><a href="Home">Home</a></li>
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
							<div class="title">
								<span><?php echo $scholarshipData['scholarshipUpdateDate'] ?></span>
								<h4><?php echo $scholarshipData['scholarshipTitle'] ?></h4>
								<!-- <ul>
									<li>by admin</li>
									<li>|</li>
									<li>Business</li>
								</ul> -->
							</div> <!-- /.title -->
							<img src="https://admin.mkscholars.com/uploads/posts/<?php echo $getScholarships['scholarshipTitle'] ?><" alt=""> <br> <br>
							<p><?php echo $scholarshipData['scholarshipDetails'] ?></p> 
							<br>
							<div>
								<a style="font-size: 16px; background-color: #4183E6; padding: 10px; color: white;" class="btn btn-primary" target="_blank" href="<?php echo $scholarshipData['scholarshipLink'] ?>">Open Scholarship Link</a>
								<a style="font-size: 16px; background-color: red; padding: 10px; color: white;" class="btn btn-primary" target="_blank" href="<?php echo $scholarshipData['scholarshipYoutubeLink'] ?>">Youtube Guide Video</a>
							</div>
						</div> <!-- /.theme-large-sidebar -->
						

						<div class="col-md-3 col-sm-6 col-xs-12 theme-sidebar">
							<form action="#" class="sidebar-search">
								<input type="text" placeholder="Search...">
								<button class="s-color-bg tran3s"><i class="fa fa-search" aria-hidden="true"></i></button>
							</form>
							<div class="sidebar-recent-post">
								<h5>Recently Uploaded</h5>
								<!-- TO BE USED IN FUTURE -->
								<!-- <ul>
									<li class="clearfix">
										<img src="images/blog/7.jpg" alt="" class="float-left">
										<div class="post float-left">
											<a href="#" class="tran3s">How to Increase Your Values</a>
											<span>February 27, 2017</span>
										</div>
									</li>
									<li class="clearfix">
										<img src="images/blog/8.jpg" alt="" class="float-left">
										<div class="post float-left">
											<a href="#" class="tran3s">How to Increase Your Values</a>
											<span>February 27, 2017</span>
										</div> 
									</li>
									<li class="clearfix">
										<img src="images/blog/9.jpg" alt="" class="float-left">
										<div class="post float-left">
											<a href="#" class="tran3s">How to Increase Your Values</a>
											<span>February 27, 2017</span>
										</div>
									</li>
								</ul> -->
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

