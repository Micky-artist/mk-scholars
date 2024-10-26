<header class="theme-menu-wrapper">
	<div class="header-wrapper">
		<div class="navPadding2 container">
			<!-- Logo -->
			<div class="logo float-left">

				<a href="index">
					<img src="images/logo/logoRound.png" width="100" height="100" alt="Logo">
					<!-- <h3>MK Scholars</h3> -->
				</a>
			</div>

			<!-- ============================ Theme Menu ========================= -->
			<nav class="theme-main-menu float-right navbar" id="mega-menu-wrapper">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="navbar-collapse-1">
					<ul class="navPadding nav">
						<li><a href="index" class="tran3s">Home</a>
						</li>
						<li><a href="scholarships" class="tran3s">scholarships</a>
						</li>
						</li>
						<!-- <li><a href="scholarships" class="tran3s">Apply Now</a> -->
						</li>
						<li><a href="contact-us" class="tran3s">Contact</a></li>

						<li class="dropdown-holder"><a href="#" class="tran3s">More</a>
							<ul class="sub-menu">
								<!-- <li><a href="about-us" class="tran3s">About Us</a> -->
								<li><a href="about-us">About Us</a></li>
								<li><a href="faq">FAQ</a></li>
								<?php
								if (isset($_SESSION['username']) && isset($_SESSION['userId'])) {
								?>
									<li><a href="./php/logout.php">Log Out: <?php echo $_SESSION['username']; ?></a></li>
								<?php
								} else {
								?>
									<li><a href="login">Login</a></li>
									<li><a href="sign-up">sign up</a></li>
								<?php
								}
								?>

							</ul>
						</li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</nav> <!-- /.theme-main-menu -->
		</div> <!-- /.container -->
	</div>
</header> <!-- /.theme-menu-wrapper -->