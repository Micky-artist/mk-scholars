<?php
include("./dbconnection/connection.php");
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
				Theme Main Banner
			============================================== 
			-->

		<div id="theme-main-banner" class="banner-two">
			<div data-src="images/home/s7.jpg">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">Welcome to our global scholarship platform, where opportunities for education are just a click away. </h1>
						<a href="#" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR COURSE</a>
						<a href="#" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
			<div data-src="images/home/s5.avif">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">We connect you with scholarships from around the world to help you achieve your academic dreams.</h1>
						<a href="#" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR COURSE</a>
						<a href="#" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
			<div data-src="images/home/s4.jpg">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">Start your journey today and unlock <br> a world of possibilities!</h1>
						<a href="#" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR COURSE</a>
						<a href="#" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
		</div> <!-- /#theme-main-banner -->



		<!-- 
			=============================================
				Find Course Form
			============================================== 
			-->
		<div class="find-course">
			<div class="opacity color-two">
				<div class="container">
					<div class="theme-title">
						<h2>Find the Best Courses</h2>
						<p>Find your preferred course from our wide range of courses easily, with just a one search here</p>
					</div> <!-- /.theme-title -->

					<form action="#">
						<div class="row">
							<div class="col-md-3 col-xs-6">
								<div class="single-input">
									<input type="text" placeholder="Course Name">
								</div> <!-- /.single-input -->
							</div> <!-- /.col- -->
							<div class="col-md-3 col-xs-6">
								<div class="single-input">
									<select class="selectpicker">
										<option>Course Cateogries</option>
										<option>Course Demo one</option>
										<option>Course Demo two</option>
										<option>Course Demo three</option>
									</select>
								</div> <!-- /.single-input -->
							</div> <!-- /.col -->
							<div class="col-md-3 col-xs-6">
								<div class="single-input">
									<select class="selectpicker">
										<option>Course Level</option>
										<option>Course Level one</option>
										<option>Course Level two</option>
										<option>Course Level three</option>
									</select>
								</div> <!-- /.single-input -->
							</div> <!-- /.col -->
							<div class="col-md-3 col-xs-6">
								<div class="single-input">
									<select class="selectpicker">
										<option>Course Instructors</option>
										<option>Course Instructors one</option>
										<option>Course Instructors two</option>
										<option>Course Instructors three</option>
									</select>
								</div> <!-- /.single-input -->
							</div> <!-- /.col -->
						</div> <!-- /.row -->
						<button style="background-color: #fff; color: #4183E6; font-weight: bold;" class="p-bg-color hvr-rectangle-out">Search Course</button>
					</form>
				</div> <!-- /.container -->
			</div> <!-- /.opacity -->
		</div> <!-- /.find-course -->


		<!-- 
			=============================================
				Course selection
			============================================== 
			-->
		<div class="course-selection">
			<div class="container">
				<div class="theme-title">
					<h2>What you want to Become?</h2>
					<p>Stay Focused, and learn new skills. Get skilled today and become what you have always wanted to be.</p>
				</div> <!-- /.theme-title -->

				<div class="row">
					<div class="col-md-4 col-xs-6">
						<div class="image">
							<img src="images/home/4.jpg" alt="">
							<div class="opacity">
								<div>
									<h5><a href="#">Become a Software Engineer</a></h5>
									<p>Course Duration 60 hours</p>
								</div>
							</div> <!-- /.opacity -->
						</div> <!-- /.image -->
					</div> <!-- /.col- -->
					<div class="col-md-4 col-xs-6">
						<div class="image">
							<img src="images/home/5.jpg" alt="">
							<div class="opacity">
								<div>
									<h5><a href="#">Study Law</a></h5>
									<p>Request Help Now</p>
								</div>
							</div> <!-- /.opacity -->
						</div> <!-- /.image -->
					</div> <!-- /.col- -->
					<div class="col-md-4 col-xs-6">
						<div class="image">
							<img src="images/home/6.jpg" alt="">
							<div class="opacity">
								<div>
									<h5><a href="#">Study Mechanical Engineering / Mechatronics</a></h5>
									<p>Request Help Now</p>
								</div>
							</div> <!-- /.opacity -->
						</div> <!-- /.image -->
					</div> <!-- /.col- -->
					<div class="col-md-4 col-xs-6">
						<div class="image">
							<img src="images/home/7.jpg" alt="">
							<div class="opacity">
								<div>
									<h5><a href="#">Become a SEO Specialist</a></h5>
									<p>Request Help Now</p>
								</div>
							</div> <!-- /.opacity -->
						</div> <!-- /.image -->
					</div> <!-- /.col- -->
					<div class="col-md-4 col-xs-6">
						<div class="image">
							<img src="images/home/8.jpg" alt="">
							<div class="opacity">
								<div>
									<h5><a href="#">Become an Entrepreneur Leader</a></h5>
									<p>Request Help Now</p>
								</div>
							</div> <!-- /.opacity -->
						</div> <!-- /.image -->
					</div> <!-- /.col- -->
					<div class="col-md-4 col-xs-6">
						<div class="image">
							<img src="images/home/9.jpg" alt="">
							<div class="opacity">
								<div>
									<h5><a href="#">Study Business Analytics & Intelligence.</a></h5>
									<p>Course Duration 60 hours</p>
								</div>
							</div> <!-- /.opacity -->
						</div> <!-- /.image -->
					</div> <!-- /.col- -->
				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</div> <!-- /.course-selection -->




		<!-- 
			=============================================
				Featured Course
			============================================== 
			-->
		<div class="featured-course" id="scholarships">
			<div class="container">
				<div class="theme-title">
					<h2>Our Featured Scholarships</h2>
					<p>Choose a best scholarship you need from our wide range of scholarships available, start learning today from the best teachers around the world</p>
				</div> <!-- /.theme-title -->

				<div class="row">
					<?php
					$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 ORDER BY scholarshipUpdateDate DESC");
					if ($selectScholarships->num_rows > 0) {
						while ($getScholarships = mysqli_fetch_assoc($selectScholarships)) {
					?>
							<div class="col-md-4 col-sm-6 col-xs-12">
								<div class="single-course-grid">
									<div class="image"><img src="https://admin.mkscholars.com/uploads/posts/<?php echo $getScholarships['scholarshipImage'] ?>" alt=""></div>
									<div class="text">
										<h6><a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo $getScholarships['scholarshipTitle'] ?>" class="tran3s"><?php echo $getScholarships['scholarshipTitle'] ?></a></h6>
										<div class="DetailWrapper">
											<p class="postLineLimit"><?php echo $getScholarships['scholarshipDetails'] ?></p>
										</div>
										<style>
											.postLineLimit {
												text-overflow: ellipsis;
												display: -webkit-box;
												-webkit-line-clamp: 4;
												line-clamp: 4;
												-webkit-box-orient: vertical;
												overflow: hidden;
											}
											.DetailWrapper {
												height: 5cm;
												overflow: hidden;
											}
										</style>
										<ul class="clearfix">
											<li class="float-left"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $getScholarships['scholarshipUpdateDate'] ?></li>

											<li class="float-right"><a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo $getScholarships['scholarshipTitle'] ?>" class="tran3s free hvr-float-shadow">More</a></li>
										</ul>
									</div> <!-- /.text -->
								</div> <!-- /.single-course-grid -->
							</div> <!-- /.col- -->
					<?php
						}
					}

					?>
				</div> <!-- /.row -->
				<a href="#" class="theme-button hvr-rectangle-out">ALL COURSES</a>
			</div> <!-- /.container -->
		</div> <!-- /.featured-course -->


		<!-- 
			=============================================
				SignUp Banner 
			============================================== 
			-->
		<div class="signUp-banner">
			<div class="count-particles">
				<span class="js-count-particles">--</span>
			</div>
			<div id="particles-js"></div>
			<div class="opacity">
				<div class="container">
					<h2>Ready to take your Skills to the next Level?</h2>
					<p>Start learning the skills that you need, start your free trial today</p>
					<a href="#" class="tran3s hvr-float-shadow">Sign Up</a>
				</div> <!-- /.container -->
			</div> <!-- /.opacity -->
		</div> <!-- /.signUp-banner -->




		<!-- 
			=============================================
				Pricing Plan
			============================================== 
			-->




		<!-- 
			=============================================
				Testomonial
			============================================== 
			-->
		<div class="testomonial">
			<div class="container">
				<div class="theme-title">
					<h2>Testomonial</h2>
					<p>What our valuable clients have to say about our courses, tutorials and online classes</p>
				</div>
				<div class="slider-wrapper">
					<div class="row">
						<div class="testimonial-slider">
							<div class="item">
								<div class="item-wrapper">
									<!-- <img src="images/home/c1.jpg" alt=""> -->
									<h6>Salomon Uwimana</h6>
									<span>M&S Innovation Lab Ltd CTO</span>
									<p>I'm thankfull for MK Scholars because it helped me secure a full paid Scholarship at African Leadership University.</p>
								</div>
							</div>
							<div class="item">
								<div class="item-wrapper">
									<!-- <img src="images/home/c2.jpg" alt=""> -->
									<h6>Umwari Grace</h6>
									<span>Student At ALU</span>
									<p>MK Scholars Helped me secure a grant at African Leadership University and I'm Thankful for it.</p>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>



		<!-- 
			=============================================
				Our Blog
			============================================== 
			-->
		<!-- <div class="our-blog">
			<div class="container">
				<div class="theme-title">
					<h2>Latest News</h2>
					<p>Want to keep up with the latest news of MK Scholars? Check out all the latest news of ours here</p>
				</div> 

				<div class="row">
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="single-post">
							<div class="image"><img src="images/blog/1.jpg" alt=""></div>
							<div class="text">
								<h6><a href="blog-details.html" class="tran3s">How one of our students empowering Entrepreneurship.</a></h6>
								<ul class="info clearfix">
									<li class="float-left"><i class="fa fa-calendar" aria-hidden="true"></i> <a href="#">March 10, 2017</a></li>
									<li class="float-left"><i class="fa fa-user" aria-hidden="true"></i> <a href="#">Michael</a></li>
									<li class="float-right"><i class="fa fa-heart" aria-hidden="true"></i> <a href="#">50</a></li>
									<li class="float-right"><i class="fa fa-comments-o" aria-hidden="true"></i> <a href="#">10</a></li>
								</ul>
								<p>When Kylia found HEC Paris’ How to Become an Entrepreneur for Social Change course on Coursera, she was working as a Communications Manager for a bank in France and struggling to find meaning in her job</p>
								<a href="blog-details.html" class="tran3s">READ MORE</a>
							</div> 
						</div> 
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12">
						<div class="single-post">
							<div class="image"><img src="images/blog/2.jpg" alt=""></div>
							<div class="text">
								<h6><a href="blog-details.html" class="tran3s">Learner story: How I became a Data Scientist by taking the courses.</a></h6>
								<ul class="info clearfix">
									<li class="float-left"><i class="fa fa-calendar" aria-hidden="true"></i> <a href="#">March 10, 2017</a></li>
									<li class="float-left"><i class="fa fa-user" aria-hidden="true"></i> <a href="#">Michael</a></li>
									<li class="float-right"><i class="fa fa-heart" aria-hidden="true"></i> <a href="#">50</a></li>
									<li class="float-right"><i class="fa fa-comments-o" aria-hidden="true"></i> <a href="#">10</a></li>
								</ul>
								<p>When Kylia found HEC Paris’ How to Become an Entrepreneur for Social Change course on Coursera, she was working as a Communications Manager for a bank in France and struggling to find meaning in her job</p>
								<a href="blog-details.html" class="tran3s">READ MORE</a>
							</div>
						</div> 
					</div>
					<div class="col-md-4 hidden-sm col-xs-12">
						<div class="single-post">
							<div class="image"><img src="images/blog/3.jpg" alt=""></div>
							<div class="text">
								<h6><a href="blog-details.html" class="tran3s">Making the leap from data science to a great professional?</a></h6>
								<ul class="info clearfix">
									<li class="float-left"><i class="fa fa-calendar" aria-hidden="true"></i> <a href="#">March 10, 2017</a></li>
									<li class="float-left"><i class="fa fa-user" aria-hidden="true"></i> <a href="#">Michael</a></li>
									<li class="float-right"><i class="fa fa-heart" aria-hidden="true"></i> <a href="#">50</a></li>
									<li class="float-right"><i class="fa fa-comments-o" aria-hidden="true"></i> <a href="#">10</a></li>
								</ul>
								<p>When Kylia found HEC Paris’ How to Become an Entrepreneur for Social Change course on Coursera, she was working as a Communications Manager for a bank in France and struggling to find meaning in her job</p>
								<a href="blog-details.html" class="tran3s">READ MORE</a>
							</div> 
						</div> 
					</div> 
				</div>
			</div> 
		</div> -->


		<!-- 
			=============================================
				Instructor Banner
			============================================== 
			-->
		<div class="instructor-banner clearfix">
			<div class="main-content float-right">
				<div class="wrapper">
					<h4>Apply for a Scholarship Now</h4>
					<p>Be a part of the world class institution where leaders are made. Make a difference in the world by taking the responsibility. Be different.</p>
					<a href="#" class="p-bg-color hvr-float-shadow">LEARN MORE</a>
				</div> <!-- /.wrapper -->
			</div> <!-- /.main-content -->
		</div> <!-- /.instructor-banner -->


		<!-- 
			=============================================
				Partner Logo
			============================================== 
			-->
		<!-- <div class="partent-logo-section">
			<div class="container">
				<div id="partner-logo">
					<div class="item"><img src="images/logo/p-1.png" alt="logo"></div>
					<div class="item"><img src="images/logo/p-2.png" alt="logo"></div>
					<div class="item"><img src="images/logo/p-3.png" alt="logo"></div>
					<div class="item"><img src="images/logo/p-4.png" alt="logo"></div>
					<div class="item"><img src="images/logo/p-5.png" alt="logo"></div>
					<div class="item"><img src="images/logo/p-6.png" alt="logo"></div>
				</div>
			</div>
		</div>  -->
		<!-- /.partent-logo-section -->




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
		<!-- Camera Slider -->
		<script type='text/javascript' src='vendor/Camera-master/scripts/jquery.mobile.customized.min.js'></script>
		<script type='text/javascript' src='vendor/Camera-master/scripts/jquery.easing.1.3.js'></script>
		<script type='text/javascript' src='vendor/Camera-master/scripts/camera.min.js'></script>
		<!-- Mega menu  -->
		<script type="text/javascript" src="vendor/bootstrap-mega-menu/js/menu.js"></script>

		<!-- WOW js -->
		<script type="text/javascript" src="vendor/WOW-master/dist/wow.min.js"></script>
		<!-- owl.carousel -->
		<script type="text/javascript" src="vendor/owl-carousel/owl.carousel.min.js"></script>
		<!-- partical Js -->
		<script type="text/javascript" src="vendor/particles.js-master/particles.min.js"></script>
		<script type="text/javascript" src="vendor/particles.js-master/demo/js/lib/stats.js"></script>
		<script type="text/javascript" src="vendor/particles.js-master/demo/js/app.js"></script>
		<!-- Feedback Star -->
		<script type="text/javascript" src="vendor/rateYo-master/src/jquery.rateyo.js"></script>

		<!-- Theme js -->
		<script type="text/javascript" src="js/theme.js"></script>

	</div> <!-- /.main-page-wrapper -->
</body>


</html>