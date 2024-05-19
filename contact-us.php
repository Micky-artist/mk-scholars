<!DOCTYPE html>
<html lang="en">
	
<!-- Mirrored from themazine.com/html/scholars-lms/contact-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 10 May 2024 11:39:10 GMT -->
<head>
		<meta charset="UTF-8">
		<!-- For IE -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge">

		<!-- For Resposive Device -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Scholars - Education, University &amp; LMS HTML Template</title>

		<!-- Favicon -->
		<link rel="icon" type="image/png" sizes="56x56" href="images/fav-icon/icon.png">


		<!-- Main style sheet -->
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<!-- responsive style sheet -->
		<link rel="stylesheet" type="text/css" href="css/responsive.css">


		<!-- Fix Internet Explorer ______________________________________-->

		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
			<script src="vendor/html5shiv.js"></script>
			<script src="vendor/respond.js"></script>
		<![endif]-->
			
	</head>

	<body>
		<div class="main-page-wrapper">

			<!-- ===================================================
				Loading Transition
			==================================================== -->
			<div id="loader-wrapper">
				<div id="loader"></div>
			</div>



			<!-- 
			=============================================
				Theme Header
			============================================== 
			-->
			<?php
			include("./partials/navigation.php");
			?>
			
			<!-- 
			=============================================
				Theme Inner Banner
			============================================== 
			-->

			<div class="theme-inner-banner" style="background: url(images/home/banner-7.jpg) no-repeat center;background-size:cover;">
				<div class="opacity">
					<div class="container">
						<h3>Contact Us</h3>
						<ul>
							<li><a href="index.html">Home</a></li>
							<li>/</li>
							<li>Contact Us</li>
						</ul>
					</div> <!-- /.container -->
				</div> <!-- /.opacity -->
			</div> <!-- /.theme-inner-banner -->


			<!--
			=============================================
				Contact Us Page
			==============================================
			-->
			<div class="contact-address">
				<div class="container">
					<div class="row">
						<div class="col-lg-7 col-md-7 hidden-sm hidden-xs"><img src="images/inner-page/3.jpg" alt=""></div>
						<div class="col-lg-5 col-md-5 col-xs-12">
							<div class="address-banner">
								<div class="opacity">
									<h2>OUR CONTACT</h2>
									<ul>
										<li><i class="fa fa-map-marker" aria-hidden="true"></i> 1010, Mountain view <br> North Pole, LA, CA</li>
										<li><i class="fa fa-phone" aria-hidden="true"></i> +1 202 245 3062</li>
										<li><i class="fa fa-envelope-o" aria-hidden="true"></i> info@fintech.com</li>
									</ul>
								</div> <!-- /.opacity -->
							</div>
						</div> <!-- /.col- -->
					</div> <!-- /.row -->
				</div> <!-- /.container -->
			</div> <!-- /.contact-address -->


			<!-- Contact Form -->
			<div class="contact-form-holder container">
				<div class="row">
					<form action="https://themazine.com/html/scholars-lms/inc/sendemail.php" class="form-validation" autocomplete="off">
						<div class="col-sm-6 col-xs-12">
							<div class="single-input-group">
								<input type="text" placeholder="First Name" name="Fname">
							</div> <!-- /.single-input-group -->
						</div> <!-- /.col- -->
						<div class="col-sm-6 col-xs-12">
							<div class="single-input-group">
								<input type="text" placeholder="Last Name" name="Lname">
							</div> <!-- /.single-input-group -->
						</div> <!-- /.col- -->
						<div class="col-sm-6 col-xs-12">
							<div class="single-input-group">
								<input type="email" placeholder="Email" name="email">
							</div> <!-- /.single-input-group -->
						</div> <!-- /.col- -->
						<div class="col-sm-6 col-xs-12">
							<div class="single-input-group">
								<input type="text" placeholder="Phone" name="phone">
							</div> <!-- /.single-input-group -->
						</div> <!-- /.col- -->
						<div class="col-xs-12">
							<textarea placeholder="Write Message" name="message"></textarea>
							<button class="tran3s">send</button>
						</div> <!-- /.col- -->
					</form>
				</div> <!-- /.row -->
				<!--Contact Form Validation Markup -->
				<!-- Contact alert -->
				<div class="alert-wrapper" id="alert-success">
					<div id="success">
						<button class="closeAlert"><i class="fa fa-times" aria-hidden="true"></i></button>
						<div class="wrapper">
			               	<p>Your message was sent successfully.</p>
			             </div>
			        </div>
			    </div> <!-- End of .alert_wrapper -->
			    <div class="alert-wrapper" id="alert-error">
			        <div id="error">
			           	<button class="closeAlert"><i class="fa fa-times" aria-hidden="true"></i></button>
			           	<div class="wrapper">
			               	<p>Sorry!Something Went Wrong.</p>
			            </div>
			        </div>
			    </div> <!-- End of .alert_wrapper -->
			</div> <!-- /.contact-form-holder -->

			<!-- Google Map _______________________ -->
			<div id="google-map-area">
				<div class="google-map" id="contact-google-map" data-map-lat="40.925372" data-map-lng="-74.276544" data-icon-path="images/logo/map.png" data-map-title="Find Map" data-map-zoom="12"></div>
	   		 </div>
			


			
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

		<!-- Validation -->
		<script type="text/javascript" src="vendor/contact-form/validate.js"></script>
		<script type="text/javascript" src="vendor/contact-form/jquery.form.js"></script>
		<!-- Google map js -->
		<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBZ8VrXgGZ3QSC-0XubNhuB2uKKCwqVaD0&amp;callback=googleMap" type="text/javascript"></script> <!-- Gmap Helper -->
		<script src="vendor/gmaps.min.js"></script>
		<!-- Feedback Star -->
		<script type="text/javascript" src="vendor/rateYo-master/src/jquery.rateyo.js"></script>

		<!-- Theme js -->
		<script type="text/javascript" src="js/theme.js"></script>
		<script type="text/javascript" src="js/map-script.js"></script>

		</div> <!-- /.main-page-wrapper -->
	</body>

<!-- Mirrored from themazine.com/html/scholars-lms/contact-us.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 10 May 2024 11:39:20 GMT -->
</html>