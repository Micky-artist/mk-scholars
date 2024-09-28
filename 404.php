<!DOCTYPE html>
<html lang="en">
	
<?php include("./partials/head.php") ?>


	<body>
		<div class="main-page-wrapper">

			
			<header class="theme-menu-wrapper">
				<div class="header-wrapper">
					<div class="container">
						<!-- Logo -->
						<div class="logo float-left"><a href="index.html"><img src="images/logo/logo.png" alt="Logo"></a></div>

						<!-- Curt Button -->
						<button class="cart-button float-right">
							<i class="flaticon-cart"></i>
						</button>

						<!-- Search Bar -->
						<div class="search-option float-right">
					   		<button class="search tran3s dropdown-toggle" id="searchDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="flaticon-search"></i></button>
					   		<form action="#" class="p-color-bg dropdown-menu tran3s" aria-labelledby="searchDropdown">
					   			<input type="text" placeholder="Search....">
					   			<button class="p-bg-color"><i class="fa fa-search" aria-hidden="true"></i></button>
					   		</form>
					   </div> <!-- /.search-option -->

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
								<ul class="nav">
									<li class="dropdown-holder"><a href="index.html" class="tran3s">Home</a>
										<ul class="sub-menu">
											<li><a href="index.html">Home version one</a></li>
											<li><a href="index-2.html">Home version Two</a></li>
											<li><a href="index-3.html">Home video background</a></li>
										</ul>
									</li>
									<li class="dropdown-holder"><a href="#" class="tran3s">COURSES</a>
										<ul class="sub-menu">
											<li><a href="course-2-column.html">Course 2 Column</a></li>
											<li><a href="course-3-column.html">Course 3 Column</a></li>
											<li><a href="course-details.html">Course Details</a></li>
										</ul>
									</li>
									<li class="dropdown-holder"><a href="#" class="tran3s">EVENTS</a>
										<ul class="sub-menu">
											<li><a href="event-list.html">Event List</a></li>
											<li><a href="event-grid.html">Event Grid</a></li>
											<li><a href="event-single.html">Event Single</a></li>
										</ul>
									</li>
									<li class="dropdown-holder"><a href="#" class="tran3s">Pages</a>
										<ul class="sub-menu">
											<li><a href="our-teacher.html">Our teachers</a></li>
											<li><a href="teachers-profile.html">teachers profile</a></li>
											<li><a href="about-us.html">About Us</a></li>
											<li><a href="404.html">Error page</a></li>
											<li><a href="faq.html">FAQ Page</a></li>
											<li><a href="login.html">Login</a></li>
											<li><a href="sign-up.html">sign up</a></li>
										</ul>
									</li>
									<li class="dropdown-holder"><a href="#" class="tran3s">PORTFOLIO</a>
										<ul class="sub-menu">
											<li><a href="portfolio-2-column.html">portfolio 2 column</a></li>
											<li><a href="portfolio-3-column.html">portfolio 3 column</a></li>
											<li><a href="portfolio-4-column.html">portfolio 4 column</a></li>
										</ul>
									</li>
									<li class="dropdown-holder"><a href="#" class="tran3s">BLOG</a>
										<ul class="sub-menu">
											<li><a href="blog.html">Blog</a></li>
											<li><a href="blog-details.html">Blog details</a></li>
										</ul>
									</li>
									<li class="dropdown-holder"><a href="#" class="tran3s">SHOP</a>
										<ul class="sub-menu">
											<li><a href="shop.html">Shop Page</a></li>
											<li><a href="shop-single.html">Shop Details</a></li>
										</ul>
									</li>
									<li><a href="contact-us.html" class="tran3s">Contact</a></li>
								</ul>
						   </div><!-- /.navbar-collapse -->
						</nav> <!-- /.theme-main-menu -->
					</div> <!-- /.container -->
				</div>
			</header> <!-- /.theme-menu-wrapper -->
			

			<!-- 
			=============================================
				Theme Inner Banner
			============================================== 
			-->

			<div class="theme-inner-banner" style="background: url(images/home/banner-9.jpg) no-repeat center;background-size:cover;">
				<div class="opacity">
					<div class="container">
						<h3>Error</h3>
						<ul>
							<li><a href="index.html">Home</a></li>
							<li>/</li>
							<li><a href="index.html">pages</a></li>
							<li>/</li>
							<li>Error</li>
						</ul>
					</div> <!-- /.container -->
				</div> <!-- /.opacity -->
			</div> <!-- /.theme-inner-banner -->

			
			<!-- 
			=============================================
				Error Page
			============================================== 
			-->
			<div class="error-page text-center">
				<div class="container">
					<h2>404</h2>
					<p>Well, something isn’t right! The page you are looking for cannot be found. </p>
					<form action="#">
						<input type="text" placeholder="Search">
						<button><i class="fa fa-search" aria-hidden="true"></i></button>
					</form>
				</div> <!-- /.container -->
			</div> <!-- /.error-page -->
			

			
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