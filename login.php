<!DOCTYPE html>
<html lang="en">

<?php
include("./partials/head.php");
include("./php/login.php");
?>


<body>
	<div class="main-page-wrapper">


		<div class="theme-login-page">
			<div class="logo"><a href="index"><img src="images/logo/logoRound.png" width="100" height="100" alt=""></a></div>
			<div class="form-wrapper">
				<br>
				<br>
				<div>
					<p class="<?php echo $class ?>">
						<?php echo $msg ?>
					</p>
				</div>
				<!-- <a href="#" class="facebook-button">LOGIN WITH FACEBOOK</a> -->
				<!-- <p><span>Or</span></p> -->
				<form method="post">
					<input type="text" name="username" placeholder="Username or email">
					<input type="password" name="password" placeholder="Password">
					<ul class="clearfix">
						<li class="float-left">
							<input type="checkbox" id="remember">
							<label for="remember">Remember Me</label>
						</li>
						<li class="float-right"><button class="theme-button tran3s" name="login">LOG IN</button></li>
					</ul>
					<p><a href="#" class="p-color">Forgot your username or pasword?</a></p>
					<p>Don’t have an account? <a href="sign-up" class="p-color">Sign Up</a></p>
					<p><a href="./home">Back Home</a></p>
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