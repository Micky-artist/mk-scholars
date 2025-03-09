<!DOCTYPE html>
<html lang="en">


<?php
include("./partials/head.php");
include("./php/CreateUser.php");
?>


<body>
	<div class="main-page-wrapper">


		<div class="theme-login-page sign-up-page">
			<div class="logo"><a href="index"><img src="images/logo/logoRound.png" width="100" height="100" alt=""></a></div>
			<div class="form-wrapper">
				<br>
				<div>
					<p class="<?php echo $class ?>">
						<?php echo $msg ?>
					</p>
				</div>
				<!-- <a href="#" class="facebook-button">SIGN UP WITH FACEBOOK</a> -->
				<!-- <p><span>Or</span></p> -->

				<form method="post">
					<input type="text" name="NoUsername" placeholder="Username" value="<?php echo $NoUsername ?>" required>
					<input type="email" name="NoEmail" placeholder="Email" value="<?php echo $NoEmail ?>" required>
					<input type="number" name="NoPhone" placeholder="Phone" value="<?php echo $NoPhone ?>" required>
					<input type="password" name="NoPassword" placeholder="Password" value="<?php echo $NoPassword ?>" required>
					<input type="password" name="NoCoPassword" placeholder="Comfirm Password" value="<?php echo $NoCoPassword ?>" required>
					<ul class="clearfix">
						<li class="float-left">
							<input type="checkbox" name="aggree" id="remember">
							<label for="remember">By signing up you confirm that you agree with <a href="./terms-and-conditions" target="_blank">Terms and Conditions</a> and <a href="./privacy-policy" target="_blank">Privacy Policy</a></label>
						</li>
					</ul>
					<button class="theme-button tran3s" name="signup">SIGN UP</button>
					<p><a href="./home">Back Home</a></p>
					<p>Already have an account? <a href="login" class="p-color">Sign In</a></p>
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