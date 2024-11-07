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
						<a href="./scholarships" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR SCHOLARSHIPS</a>
						<a href="./login" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
			<div data-src="images/home/s5.avif">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">We connect you with scholarships from around the world to help you achieve your academic dreams.</h1>
						<a href="./scholarships" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR SCHOLARSHIPS</a>
						<a href="./login" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
			<div data-src="images/home/s4.jpg">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">Start your journey today and unlock <br> a world of possibilities!</h1>
						<a href="./scholarships" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">OUR SCHOLARSHIPS</a>
						<a href="./login" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
		</div> <!-- /#theme-main-banner -->

		<?php
		if (isset($_POST['searchCourse'])) {
			echo '<script type="text/javascript">
                window.location.href="./scholarships"

</script>';
		}
		?>


		<div class="featured-course" id="scholarships">
			<div class="container">
				<div class="theme-title">
					<h2>Our Featured Scholarships</h2>
					<p>Choose a best scholarship you need from our wide range of scholarships available, start learning today from the best teachers around the world</p>
				</div> <!-- /.theme-title -->

				<div class="row">
					<div class="col-md-4 col-sm-6 col-xs-12 allScholarshipContainer">
						<div class="single-course-grid">
							<div class="image">
								<img src="./images/home/followUs.jpeg" alt="">
							</div>
							<div class="text">
								<div>
									<a class="noHover">
										<h4>Follow Us</h4>
									</a>
								</div>
								<div>
									<a target="_blank" href="https://www.youtube.com/@mkscholars"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="red" class="bi bi-youtube" viewBox="0 0 16 16">
											<path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z" />
										</svg>
										<h5>Youtube</h5>
									</a>

								</div>
								<div>
									<a target="_blank" href="https://x.com/MkScholars"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="black" class="bi bi-twitter-x" viewBox="0 0 16 16">
											<path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
										</svg>
										<h5>X</h5>
									</a>

								</div>
								<div>
									<a target="_blank" href="#"><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="blue" class="bi bi-facebook" viewBox="0 0 16 16">
											<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
										</svg>
										<h5>Facebook</h5>
									</a>

								</div>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-6 col-xs-12 allScholarshipContainer">
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
									<a target="_blank" href="https://chat.whatsapp.com/Jm0hfcLeRVm3pbnNPx82GD">
										<svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="green" class="bi bi-whatsapp" viewBox="0 0 16 16">
											<path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
										</svg>
										<h5>Join WhatsApp Group</h5>
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
					</div>
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
						.noHover{
							justify-content: center !important;
						}
						.noHover:hover{
							background-color: #fff !important;
							cursor: default !important;
						}
					</style>
					<?php
					$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 ORDER BY scholarshipId DESC LIMIT 10");
					if ($selectScholarships->num_rows > 0) {
						while ($getScholarships = mysqli_fetch_assoc($selectScholarships)) {
					?>
							<!-- <a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo $getScholarships['scholarshipTitle'] ?>" class="tran3s"> -->
							<div class="col-md-4 col-sm-6 col-xs-12 allScholarshipContainer">
								<div class="single-course-grid">
									<div class="image">
										<img src="https://admin.mkscholars.com/uploads/posts/<?php echo $getScholarships['scholarshipImage'] ?>" alt="">
									</div>
									<div class="text">
										<h6><a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>" class="tran3s"><?php echo $getScholarships['scholarshipTitle'] ?></a></h6>
										<div class="DetailWrapper">
											<p class="postLineLimit"><?php echo $getScholarships['scholarshipDetails'] ?></p>
										</div>

										<ul class="clearfix">
											<li class="float-left"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $getScholarships['scholarshipUpdateDate'] ?></li>

											<li class="float-right"><a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>" class="tran3s free hvr-float-shadow">More</a></li>
										</ul>
									</div> <!-- /.text -->
								</div> <!-- /.single-course-grid -->
							</div> <!-- /.col- -->
							<!-- </a> -->
					<?php
						}
					}

					?>

					<style>
						.allScholarshipContainer {
							height: 14cm;
						}

						.image {
							height: 5cm;
						}

						.image img {
							object-fit: cover;
							width: 100%;
							height: 100%;
						}

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
				</div> <!-- /.row -->
				<a href="./scholarships" style="background-color: #fff; color: #4183E6; font-weight: bold;" class="theme-button hvr-rectangle-out">ALL APPLICATIONS</a>
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
					<h2>Testomonial</h2>
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
					<a href="./scholarships" class="p-bg-color hvr-float-shadow">LEARN MORE</a>
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