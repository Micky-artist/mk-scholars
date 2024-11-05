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

			<div class="theme-inner-banner" style="background: url(images/home/s3.jpg) no-repeat center;background-size:cover;">
				<div class="opacity">
					<div class="container">
						<h3>Contact Us</h3>
						<ul>
							<li><a href="home">Home</a></li>
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
						<div class="col-lg-7 col-md-7 hidden-sm hidden-xs"><img src="images/home/grad2.jpg" alt=""></div>
						<div class="col-lg-5 col-md-5 col-xs-12">
							<div class="address-banner">
								<div class="opacity">
									<h2>OUR CONTACT</h2>
									<ul>
										<li><i class="fa fa-map-marker" aria-hidden="true"></i> Kigali - Rwanda <br> Kicukiro - Nyanza Near Canal Olympia</li>
										<li><i class="fa fa-phone" aria-hidden="true"></i> +250 798 611 161</li>
										<li><i class="fa fa-envelope-o" aria-hidden="true"></i> mkscholars250@gmail.com</li>
									</ul>
								</div> <!-- /.opacity -->
							</div>
						</div> <!-- /.col- -->
					</div> <!-- /.row -->
				</div> <!-- /.container -->
			</div> <!-- /.contact-address -->


			<!-- Contact Form -->
			<div class="contact-form-holder container">
				<!-- <div class="row">
					<form action="https://themazine.com/html/scholars-lms/inc/sendemail.php" class="form-validation" autocomplete="off">
						<div class="col-sm-6 col-xs-12">
							<div class="single-input-group">
								<input type="text" placeholder="First Name" name="Fname">
							</div> 
						</div> 
						<div class="col-sm-6 col-xs-12">
							<div class="single-input-group">
								<input type="text" placeholder="Last Name" name="Lname">
							</div> 
						</div> 
						<div class="col-sm-6 col-xs-12">
							<div class="single-input-group">
								<input type="email" placeholder="Email" name="email">
							</div>
						</div> 
						<div class="col-sm-6 col-xs-12">
							<div class="single-input-group">
								<input type="text" placeholder="Phone" name="phone">
							</div> 
						</div>
						<div class="col-xs-12">
							<textarea placeholder="Write Message" name="message"></textarea>
							<button class="tran3s">send</button>
						</div> 
					</form>
				</div>  -->
				<!-- /.row -->
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
			<!-- <div id="google-map-area">
				<div class="google-map" id="contact-google-map" data-map-lat="40.925372" data-map-lng="-74.276544" data-icon-path="images/logo/map.png" data-map-title="Find Map" data-map-zoom="12"></div>
	   		 </div> -->
			


			
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

</html>