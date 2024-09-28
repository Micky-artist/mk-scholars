<!DOCTYPE html>
<html lang="en">
	
<?php include("./partials/head.php") ?>


	<body>
		<div class="main-page-wrapper">

			<!-- ===================================================
				Loading Transition
			==================================================== -->
			<div id="loader-wrapper">
				<div id="loader"></div>
			</div>

			
			<!-- ===================================================
				Login Page
			==================================================== -->
			<div class="theme-login-page">
				<div class="logo"><a href="index.html"><img src="images/logo/logo2.png" alt=""></a></div>
				<div class="form-wrapper">
					<a href="#" class="facebook-button">LOGIN WITH FACEBOOK</a>
					<p><span>Or</span></p>
					<form action="#">
						<input type="text" placeholder="Username or email">
						<input type="password" placeholder="Password">
						<ul class="clearfix">
							<li class="float-left">
								<input type="checkbox" id="remember">
								<label for="remember">Remember Me</label>
							</li>
							<li class="float-right"><button class="theme-button tran3s">LOG IN</button></li>
						</ul>
						<p><a href="#" class="p-color">Forgot your username or pasword?</a></p>
						<p>Don’t have an account? <a href="sign-up.html" class="p-color">Sign Up</a></p>
					</form>
				</div> <!-- /.form-wrapper -->
			</div>
	        


		<!-- Js File_________________________________ -->

		<!-- j Query -->
		<script type="text/javascript" src="vendor/jquery.2.2.3.min.js"></script>
		<!-- Bootstrap Select JS -->
		<script type="text/javascript" src="vendor/bootstrap-select/dist/js/bootstrap-select.js"></script>
		<!-- Feedback Star -->
		<script type="text/javascript" src="vendor/rateYo-master/src/jquery.rateyo.js"></script>

		<!-- Theme js -->
		<script type="text/javascript" src="js/theme.js"></script>

		</div> <!-- /.main-page-wrapper -->
	</body>

</html>