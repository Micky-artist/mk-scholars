
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
						<a href="./applications" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR APPLICATIONS</a>
						<a href="./login" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
			<div data-src="images/home/s5.avif">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">We connect you with scholarships from around the world to help you achieve your academic dreams.</h1>
						<a href="./applications" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR APPLICATIONS</a>
						<a href="./login" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
			<div data-src="images/home/s4.jpg">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">Start your journey today and unlock <br> a world of possibilities!</h1>
						<a href="./applications" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR APPLICATIONS</a>
						<a href="./login" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
		</div> <!-- /#theme-main-banner -->

		<?php
		if (isset($_POST['searchCourse'])) {
			echo '<script type="text/javascript">
                window.location.href="./applications"

</script>';
		}
		?>


		<div class="featured-course" id="scholarships">
			<div class="container">
				<div class="theme-title">
					<h2>Our Featured Scholarships</h2>
					<p>Choose a best scholarship you need from our wide range of scholarships available, start learning today from the best teachers around the world</p>
				</div> <!-- /.theme-title -->

				<div class="scholarshipsContainerDiv">

					<!-- <div class="col-md-4 col-sm-6 col-xs-12 allScholarshipContainer">
						<div class="single-course-grid">
							<div class="image">
								<img src="./images/home/getInTouch.jpg" alt="">
							</div>
							<div class="text">
								<div>
									<a class="noHover">
										<h4>Stay In Touch</h4>
									</a>
								</div>
								

								<div>
									<a target="_blank" href="#">
										<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black " class="bi bi-telephone-fill" viewBox="0 0 16 16">
											<path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
										</svg>
										<h5>+250 798 611 161</h5>
									</a>

								</div>
								<div>
									<a target="_blank" href="#">
										<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
											<path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
										</svg>
										<h5>Instagram</h5>
									</a>

								</div>
							</div>
						</div>
					</div> -->
					<?php include('./partials/stayInTouch.php'); ?>
					<style>
						.text div a {
							display: flex;
							flex-direction: row;
							align-items: center;
							justify-content: space-between;
							padding: 10px 20px;
							margin: 10px 0px;
							border-bottom: 1px solid silver;
							text-decoration: none;
							color: black;
						}

						.text div a:hover {
							background-color: silver;
						}

						.noHover {
							justify-content: center !important;
						}

						.noHover:hover {
							background-color: #fff !important;
							cursor: default !important;
						}
					</style>
					<?php
					$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 ORDER BY scholarshipId DESC LIMIT 11");
					if ($selectScholarships->num_rows > 0) {
						while ($getScholarships = mysqli_fetch_assoc($selectScholarships)) {
					?>
							<div class="scholarship-card">
								<div class="card-image">
									<img src="https://admin.mkscholars.com/uploads/posts/<?php echo $getScholarships['scholarshipImage'] ?>"
										alt="<?php echo $getScholarships['scholarshipTitle'] ?>">
									<div class="image-overlay"></div>
								</div>

								<div class="card-content">
									<h3 class="card-title">
										<a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>">
											<?php echo $getScholarships['scholarshipTitle'] ?>
										</a>
									</h3>

									<div class="card-description">
										<p><?php echo $getScholarships['scholarshipDetails'] ?></p>
									</div>

									<div class="card-footer">
										<div class="date-info">
											<svg class="calendar-icon" viewBox="0 0 24 24">
												<path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5z" />
											</svg>
											<span><?php echo $getScholarships['scholarshipUpdateDate'] ?></span>
										</div>

										<div class="card-actions">
											<a target="_blank" href="./apply?scholarshipId=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>" class="apply-button">
												<span>Apply Now</span>
												<div class="button-hover-effect"></div>
											</a>

											<a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>"
												class="read-more-button">
												<span>Read More</span>
												<div class="button-arrow">→</div>
											</a>
										</div>
									</div>
								</div>
							</div>

					<?php
						}
					}

					?>
					<style>
						.scholarshipsContainerDiv {
							display: flex;
							flex-direction: row;
							flex-wrap: wrap;
							justify-content: center;
							width: 100%;
							/* background-color: #2d3436; */
						}

						.scholarship-card {
							position: relative;
							width: 100%;
							max-width: 320px;
							/* Reduced width */
							background: #ffffff;
							border-radius: 16px;
							box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
							overflow: hidden;
							transition: transform 0.3s ease, box-shadow 0.3s ease;
							margin: 1rem;
						}

						.scholarship-card:hover {
							transform: translateY(-5px);
							box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15);
						}

						.card-image {
							position: relative;
							height: 200px;
							/* Adjusted height */
							overflow: hidden;
						}

						.card-image img {
							width: 100%;
							height: 100%;
							object-fit: cover;
							transition: transform 0.4s ease;
						}

						.scholarship-card:hover .card-image img {
							transform: scale(1.05);
						}

						.image-overlay {
							position: absolute;
							top: 0;
							left: 0;
							width: 100%;
							height: 100%;
							background: linear-gradient(180deg, rgba(0, 0, 0, 0) 40%, rgba(0, 0, 0, 0.7) 100%);
						}

						.card-content {
							padding: 1.25rem;
							position: relative;
						}

						.card-title {
							font-size: 18px;
							margin: 0 0 0.75rem 0;
							line-height: 1.3;
						}

						.card-title a {
							color: #2d3436;
							text-decoration: none;
							background-image: linear-gradient(to right, #2d3436 50%, transparent 50%);
							background-size: 200% 2px;
							background-position: 100% 100%;
							background-repeat: no-repeat;
							transition: background-position 0.3s ease;
							padding: 5px 5px;
						}

						.card-title a:hover {
							background-position: 0% 100%;
						}

						.card-description {
							height: 4.5em;
							/* 3 lines * 1.5em line-height */
							overflow: hidden;
							margin-bottom: 1.25rem;
							/* Adjusted margin */
						}

						.card-description p {
							margin: 0;
							color: #636e72;
							line-height: 1.5em;
							display: -webkit-box;
							-webkit-line-clamp: 3;
							-webkit-box-orient: vertical;
							overflow: hidden;
						}

						.card-footer {
							display: flex;
							justify-content: space-between;
							align-items: center;

						}

						.date-info {
							display: flex;
							align-items: center;
							gap: 0.5rem;
							color: #636e72;
							font-size: 14px;
							/* Adjusted font size */
						}

						.calendar-icon {
							width: 16px;
							/* Adjusted size */
							height: 16px;
							/* Adjusted size */
							fill: #636e72;
						}

						.card-actions {
							display: flex;
							gap: 0.5rem;
							/* Adjusted gap */
						}

						.apply-button {
							position: relative;
							display: inline-flex;
							align-items: center;
							padding: 0.5rem 1rem;
							/* Adjusted padding */
							background: linear-gradient(135deg, #ff6b6b, #a855f7);
							color: white;
							border-radius: 8px;
							text-decoration: none;
							overflow: hidden;
							transition: transform 0.3s ease;
						}

						.button-hover-effect {
							position: absolute;
							width: 100%;
							height: 100%;
							background: rgba(255, 255, 255, 0.1);
							left: -100%;
							transition: left 0.3s ease;
						}

						.apply-button:hover .button-hover-effect {
							left: 0;
						}

						.read-more-button {
							position: relative;
							display: inline-flex;
							align-items: center;
							padding: 0.5rem 1rem;
							/* Adjusted padding */
							background: transparent;
							border: 2px solid #a855f7;
							border-radius: 8px;
							color: #2d3436;
							text-decoration: none;
							transition: all 0.3s ease;
						}

						.read-more-button:hover {
							border-color: #a855f7;
							background: rgba(168, 85, 247, 0.05);
						}

						.button-arrow {
							margin-left: 0.5rem;
							transform: translateX(0);
							transition: transform 0.3s ease;
						}

						.read-more-button:hover .button-arrow {
							transform: translateX(3px);
						}
					</style>

				</div> <!-- /.row -->
				<a href="./applications" style="background-color: #fff; color: #4183E6; font-weight: bold;" class="theme-button hvr-rectangle-out">ALL APPLICATIONS</a>
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
					<p>Start learning the skills that you need, signup to start accessing exclussive scholarships now</p>
					<a href="sign-up" class="tran3s hvr-float-shadow">Sign Up</a>
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
					<h2>Testimonial</h2>
					<p>What our valuable clients have to say about our courses, tutorials and online classes</p>
				</div>
				<div class="slider-wrapper">
					<div class="row">
						<div class="testimonial-slider">

							<div class="item">
								<div class="item-wrapper">
									<h6>Josue NSHUTI</h6>
									<span>Student at Ege University</span>
									<p>I am incredibly grateful to MK Scholars for helping me secure a full scholarship to study Pharmacy in Turkey. Their support made my dreams come true. I highly recommend MK Scholars to other students looking for opportunities. They truly care and can help you achieve your goals, just like they did for me!</p>
								</div>
							</div>
							<div class="item">
								<div class="item-wrapper">
									<h6>Uwera Peace</h6>
									<span>Student at University of Rwanda </span>
									<p>I am so grateful to Mk Scholars for helping me get a Mastercard full scholarship at the University of Rwanda. Their support and guidance made my dream come true. Thank you, Mk Scholars, for believing in me and giving me the chance to achieve a brighter future.</p>
								</div>
							</div>
							<div class="item">
								<div class="item-wrapper">
									<h6>Rukundo Desire</h6>
									<span>Student at University of cassino and southern lazio</span>
									<p>I am very grateful to MK Scholars for helping me get admission and a visa to study in Italy. Their support made the whole process easy and stress-free. Without their help, I wouldn't have achieved this dream. Thank you, MK Scholars, for making it possible!</p>
								</div>
							</div>
							<div class="item">
								<div class="item-wrapper">
									<h6>RUTIKANGA Jean Damour</h6>
									<span>Student at Mohammed VI Polytechnic University</span>
									<p>I am very grateful to MK Scholars for helping me secure a full scholarship to study Architecture in Morocco. Their support made my dream possible, and I feel encouraged to pursue my passion. I highly recommend MK Scholars to other students seeking opportunities for their education. Thank you, MK Scholars!</p>
								</div>
							</div>
							<div class="item">
								<div class="item-wrapper">
									<h6>Christella Ineza</h6>
									<span>A student at the University of Global Health Equity (UGHE)</span>
									<p>MK Scholars have been tremendously helpful in obtaining a full scholarship at UGHE, and their help was not in vain. I am filled with gratitude for their assistance!</p>
								</div>
							</div>
							<div class="item">
								<div class="item-wrapper">
									<h6>Salomon Uwimana</h6>
									<span>M&S Innovation Lab Ltd CTO</span>
									<p>I'm thankfull for MK Scholars because it helped me secure a full paid Scholarship at African Leadership University.</p>
								</div>
							</div>
							<div class="item">
								<div class="item-wrapper">
									<h6>Umwari Grace</h6>
									<span>Student At ALU</span>
									<p>MK Scholars Helped me secure a grant at African Leadership University and I'm Thankful for it.</p>
								</div>
							</div>
							<!-- 
							<div class="item">
								<div class="item-wrapper">
									<h6>NSHUTI</h6>
									<span>Student</span>
									<p>incredibly</p>
								</div>
							</div> -->
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
					<a href="./applications" class="p-bg-color hvr-float-shadow">LEARN MORE</a>
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