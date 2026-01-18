<?php
// Include session configuration for persistent sessions
include("./config/session.php");

// Error handling for database connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try to include database connection safely
try {
    include_once("./dbconnection/connection.php");
} catch (Exception $e) {
    error_log("Database connection error in home.php: " . $e->getMessage());
    // Continue without database connection
}

// Check if database connection is working
$dbWorking = false;
if (isset($conn) && $conn) {
    try {
        $dbWorking = mysqli_ping($conn);
    } catch (Exception $e) {
        error_log("Database ping error: " . $e->getMessage());
        $dbWorking = false;
    }
}

// If database is not working, show a simplified version
if (!$dbWorking) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MK Scholars - Database Connection Issue</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; padding: 15px; margin: 15px 0; border-radius: 5px; }
            .info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; padding: 15px; margin: 15px 0; border-radius: 5px; }
            .button { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
            h1 { color: #333; text-align: center; }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>⚠️ Database Connection Issue</h1>
            
            <div class="warning">
                <strong>Database Connection Problem:</strong><br>
                The website is experiencing database connectivity issues. This might be due to:
                <ul>
                    <li>Database server being down</li>
                    <li>Incorrect database credentials</li>
                    <li>Database not existing</li>
                    <li>Server configuration issues</li>
                </ul>
            </div>
            
            <div class="info">
                <strong>What you can do:</strong><br>
                • <a href="./db-test.php">Test Database Connection</a><br>
                • <a href="./server-status.php">Check Server Status</a><br>
                • <a href="./test-server.php">Test Basic PHP</a><br>
                • <a href="./index-fallback.php">View Fallback Page</a>
            </div>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="./" class="button">Try Main Site Again</a>
                <a href="./db-test.php" class="button">Database Test</a>
                <a href="./server-status.php" class="button">Server Status</a>
            </div>
            
            <hr>
            <p style="text-align: center; color: #666;">
                <strong>MK Scholars</strong><br>
                For support: mkscholars250@gmail.com | +250798611161
            </p>
        </div>
    </body>
    </html>
    <?php
    exit; // Stop execution here
}
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
						<a href="./applications" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">ALL APPLICATIONS</a>
						<a href="./courses" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
			<div data-src="images/home/s5.avif">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">We connect you with scholarships from around the world to help you achieve your academic dreams.</h1>
						<a href="./applications" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">ALL APPLICATIONS</a>
						<a href="./courses" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
			<div data-src="images/home/s4.jpg">
				<div class="camera_caption">
					<div class="container text-center">
						<h1 class="wow fadeInUp animated">Start your journey today and unlock <br> a world of possibilities!</h1>
						<a href="./applications" class="tran3s wow fadeInLeft animated hvr-rectangle-out button-one" data-wow-delay="0.2s">ALL APPLICATIONS</a>
						<a href="./courses" class="tran3s wow fadeInRight animated hvr-rectangle-out" data-wow-delay="0.299s">ENROLL NOW</a>
					</div> <!-- /.container -->
				</div> <!-- /.camera_caption -->
			</div>
		</div> <!-- /#theme-main-banner -->

		<!-- 
			=============================================
				Meek Store Advertisement
			============================================== 
		-->
		<div class="meek-store-ad">
			<div class="container">
				<div class="ad-content">
					<div class="ad-left">
						
						<div class="ad-products">
							<div class="product-item">
								<img src="./images/MeekStore.JPG" alt="Meek Store - Fashion & Sneakers" class="ad-main-image">
							</div>
						</div>
					</div>
					<div class="ad-right">
						<div class="ad-cta">
							<h3>VISIT FOR GOOD DEALS!</h3>
							<a href="https://www.instagram.com/meekstyles?igsh=MTNmanh5OWs1dzd0dA==" target="_blank" class="visit-store-btn">Visit Store</a>
						</div>
					</div>
				</div>
			</div>
		</div>

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
					<h2>Available Scholarships</h2>
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
											<path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a3.9 3.9 0 0 0-.92-.598 2.5 2.5 0 0 0-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334" />
										</svg>
										<h5>Instagram</h5>
									</a>

								</div>
							</div>
						</div>
					</div> -->
					<?php include('./partials/stayInTouch.php'); ?>
					
					<!-- Writing Services Card -->
					<div class="allScholarshipContainer">
						<div class="writing-services-card">
							<div class="writing-services-header">
								<div class="header-content">
									<div class="header-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
											<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
											<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
										</svg>
									</div>
									<div class="header-text">
										<h3 class="services-title">WRITING SERVICES</h3>
										<p class="services-subtitle">We are offering expert writing & proofreading services for:</p>
									</div>
								</div>
								<div class="header-decoration">
									<div class="decoration-line"></div>
									<div class="decoration-dot"></div>
									<div class="decoration-line"></div>
								</div>
							</div>
							<div class="text">
								
								<div class="writing-services-list">
									<div class="service-item">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
											<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
											<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
										</svg>
										<span>Personal Statements & Essays</span>
									</div>
									<div class="service-item">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
											<path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
											<path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h1V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
										</svg>
										<span>Resume & CV</span>
									</div>
									<div class="service-item">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope" viewBox="0 0 16 16">
											<path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
										</svg>
										<span>Recommendation Letters</span>
									</div>
									<div class="service-item">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-chat-quote" viewBox="0 0 16 16">
											<path d="M2.5 3a.5.5 0 0 0 0 1h11a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h7a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h8a.5.5 0 0 0 0-1z"/>
											<path d="M5 4.5a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1z"/>
										</svg>
										<span>Motivation Letters</span>
									</div>
									<div class="service-item">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
											<path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5M1 4.5a.5.5 0 0 1 .5-.5h13a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5z"/>
										</svg>
										<span>Cover Letters & Portfolios</span>
									</div>
									<div class="writing-services-cta">
									<a href="./writing-services" class="apply-writing-service">
										<span>Get Professional Help</span>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-right" viewBox="0 0 16 16">
											<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
										</svg>
									</a>
								</div>
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

						.noHover {
							justify-content: center !important;
						}

						.noHover:hover {
							background-color: #fff !important;
							cursor: default !important;
						}

						/* Meek Store Advertisement Styling */
						.meek-store-ad {
							background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
							padding: 60px 0;
							margin: 40px 0;
							border-radius: 20px;
							box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
						}

						.ad-content {
							display: flex;
							align-items: center;
							gap: 40px;
							max-width: 1200px;
							margin: 0 auto;
						}

						.ad-left {
							flex: 1;
							display: flex;
							flex-direction: column;
							gap: 30px;
						}

						.ad-logo {
							display: flex;
							align-items: center;
							gap: 20px;
						}

						.logo-icon {
							width: 80px;
							height: 80px;
							background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
							border-radius: 16px;
							display: flex;
							align-items: center;
							justify-content: center;
							box-shadow: 0 8px 24px rgba(14, 119, 194, 0.3);
						}

						.logo-m {
							font-size: 2.5rem;
							font-weight: 700;
							color: white;
							font-family: 'Georgia', serif;
						}

						.logo-text h2 {
							font-size: 2.5rem;
							font-weight: 700;
							color: #083352;
							margin: 0;
							font-family: 'Georgia', serif;
						}

						.logo-text p {
							font-size: 1.1rem;
							color: #666;
							margin: 5px 0 0 0;
							font-weight: 500;
							text-transform: uppercase;
							letter-spacing: 1px;
						}

						.ad-products {
							text-align: center;
						}

						.ad-main-image {
							max-width: 100%;
							height: auto;
							border-radius: 16px;
							box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
							transition: transform 0.3s ease;
						}

						.ad-main-image:hover {
							transform: scale(1.02);
						}

						.ad-right {
							flex: 1;
							text-align: center;
						}

						.ad-cta h3 {
							font-size: 2rem;
							font-weight: 700;
							color: #083352;
							margin-bottom: 30px;
							text-transform: uppercase;
							letter-spacing: 1px;
						}

						.contact-info {
							margin-bottom: 30px;
						}

						.contact-info .contact-item {
							display: flex;
							align-items: center;
							justify-content: center;
							gap: 15px;
							margin-bottom: 15px;
							padding: 15px;
							background: white;
							border-radius: 12px;
							box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
							transition: all 0.3s ease;
						}

						.contact-info .contact-item:hover {
							transform: translateY(-3px);
							box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
						}

						.contact-info .contact-item i {
							font-size: 1.2rem;
							color: #0E77C2;
							width: 24px;
							text-align: center;
						}

						.contact-info .contact-item span {
							font-size: 1rem;
							color: #333;
							font-weight: 500;
						}

						.visit-store-btn {
							display: inline-block;
							padding: 18px 36px;
							background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
							color: white;
							text-decoration: none;
							border-radius: 50px;
							font-size: 1.1rem;
							font-weight: 600;
							text-transform: uppercase;
							letter-spacing: 1px;
							box-shadow: 0 8px 24px rgba(14, 119, 194, 0.3);
							transition: all 0.3s ease;
							border: none;
							cursor: pointer;
						}

						.visit-store-btn:hover {
							transform: translateY(-3px);
							box-shadow: 0 12px 32px rgba(14, 119, 194, 0.4);
							color: white;
							text-decoration: none;
						}

						/* Responsive Design for Meek Store Ad */
						@media (max-width: 768px) {
							.meek-store-ad {
								padding: 40px 20px;
								margin: 20px 0;
							}

							.ad-content {
								flex-direction: column;
								gap: 30px;
								text-align: center;
							}

							.ad-logo {
								justify-content: center;
							}

							.logo-text h2 {
								font-size: 2rem;
							}

							.ad-cta h3 {
								font-size: 1.5rem;
							}

							.visit-store-btn {
								padding: 15px 30px;
								font-size: 1rem;
							}
						}

						@media (max-width: 480px) {
							.meek-store-ad {
								padding: 30px 15px;
							}

							.logo-icon {
								width: 60px;
								height: 60px;
							}

							.logo-m {
								font-size: 2rem;
							}

							.logo-text h2 {
								font-size: 1.8rem;
							}

							.logo-text p {
								font-size: 1rem;
							}

							.ad-cta h3 {
								font-size: 1.3rem;
							}

							.contact-info .contact-item {
								padding: 12px;
								margin-bottom: 10px;
							}

							.contact-info .contact-item span {
								font-size: 0.9rem;
							}
						}

						/* Writing Services Card Styling */
						.writing-services-card {
							background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
							color: white;
							border-radius: 16px;
							overflow: hidden;
							box-shadow: 0 8px 24px rgba(14, 119, 194, 0.3);
							transition: all 0.3s ease;
						}

						.writing-services-card:hover {
							transform: translateY(-5px);
							box-shadow: 0 12px 32px rgba(14, 119, 194, 0.4);
						}

						/* Writing Services Header Styling */
						.writing-services-header {
							position: relative;
							padding: 30px 20px 25px 20px;
							background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
							backdrop-filter: blur(10px);
							border-bottom: 1px solid rgba(255,255,255,0.1);
						}

						.header-content {
							display: flex;
							align-items: center;
							gap: 20px;
							margin-bottom: 15px;
						}

						.header-icon {
							display: flex;
							align-items: center;
							justify-content: center;
							width: 80px;
							height: 80px;
							background: linear-gradient(135deg, #FDC713 0%, #F4B942 100%);
							border-radius: 20px;
							color: #083352;
							box-shadow: 0 8px 20px rgba(253, 199, 19, 0.3);
							transition: all 0.3s ease;
						}

						.writing-services-card:hover .header-icon {
							transform: rotate(5deg) scale(1.05);
							box-shadow: 0 12px 30px rgba(253, 199, 19, 0.4);
						}

						.header-text {
							flex: 1;
						}

						.services-title {
							font-size: 28px;
							font-weight: 700;
							margin: 0 0 8px 0;
							color: white;
							text-shadow: 0 2px 10px rgba(0,0,0,0.3);
							letter-spacing: 1px;
							line-height: 1.2;
						}

						.services-subtitle {
							font-size: 16px;
							font-weight: 400;
							margin: 0;
							color: rgba(255,255,255,0.9);
							line-height: 1.4;
						}

						.header-decoration {
							display: flex;
							align-items: center;
							justify-content: center;
							gap: 10px;
							margin-top: 15px;
						}

						.decoration-line {
							height: 2px;
							width: 50px;
							background: linear-gradient(90deg, transparent, #FDC713, transparent);
							border-radius: 1px;
						}

						.decoration-dot {
							width: 8px;
							height: 8px;
							background: #FDC713;
							border-radius: 50%;
							box-shadow: 0 0 10px rgba(253, 199, 19, 0.5);
						}

						.writing-services-card .text {
							padding: 20px;
							background: rgba(255, 255, 255, 0.1);
							backdrop-filter: blur(10px);
						}

						.writing-services-card h4 {
							color: white;
							text-align: center;
							margin-bottom: 20px;
							font-size: 22px;
							font-weight: 600;
						}

						.writing-services-list {
							margin-bottom: 20px;
						}

						.service-item {
							display: flex;
							align-items: center;
							gap: 12px;
							padding: 10px 0;
							border-bottom: 1px solid rgba(255, 255, 255, 0.2);
							color: white;
							font-size: 14px;
						}

						.service-item:last-child {
							border-bottom: none;
						}

						.service-item svg {
							color: #FDC713;
							flex-shrink: 0;
						}

						.service-item span {
							flex: 1;
							line-height: 1.4;
						}

						.writing-services-cta {
							text-align: center;
							margin-top: 20px;
						}

						.apply-writing-service {
							display: inline-flex;
							align-items: center;
							gap: 8px;
							padding: 12px 24px;
							background: #FDC713;
							color: #083352;
							text-decoration: none;
							border-radius: 25px;
							font-weight: 600;
							transition: all 0.3s ease;
							border: 2px solid #FDC713;
						}

						.apply-writing-service:hover {
							background: #083352;
							border-color: #FDC713;
							transform: translateY(-2px);
							color: #FDC713;
						}

						.apply-writing-service svg {
							transition: transform 0.3s ease;
						}

						.apply-writing-service:hover svg {
							transform: translateX(4px);
						}

						/* Responsive adjustments */
						@media (max-width: 768px) {
							.writing-services-header {
								padding: 25px 15px 20px 15px;
							}
							
							.header-content {
								gap: 15px;
								margin-bottom: 12px;
							}
							
							.header-icon {
								width: 60px;
								height: 60px;
								border-radius: 15px;
							}
							
							.header-icon svg {
								width: 32px;
								height: 32px;
							}
							
							.services-title {
								font-size: 22px;
								margin-bottom: 6px;
							}
							
							.services-subtitle {
								font-size: 14px;
							}
							
							.decoration-line {
								width: 40px;
								height: 1.5px;
							}
							
							.decoration-dot {
								width: 6px;
								height: 6px;
							}
							
							.writing-services-card .text {
								padding: 15px;
							}
							
							.writing-services-card h4 {
								font-size: 20px;
							}
							
							.service-item {
								font-size: 13px;
								padding: 8px 0;
							}
							
							.apply-writing-service {
								padding: 10px 20px;
								font-size: 14px;
							}
						}
						
						@media (max-width: 480px) {
							.header-content {
								flex-direction: column;
								text-align: center;
								gap: 15px;
							}
							
							.services-title {
								font-size: 20px;
							}
							
							.services-subtitle {
								font-size: 13px;
							}
						}
					</style>
					<div class="scholarshipsContainerDiv">
						<?php
						// 1) Fetch up to 11 scholarships
						$scholarships = [];
						$result = mysqli_query(
							$conn,
							"SELECT * 
         FROM scholarships 
         WHERE scholarshipStatus != 0 
         ORDER BY scholarshipId DESC 
         LIMIT 11"
						);
						if ($result && $result->num_rows > 0) {
							while ($row = mysqli_fetch_assoc($result)) {
								$scholarships[] = $row;
							}
						}

						$numPosts = count($scholarships);

						if ($numPosts > 0) {
							// Loop through scholarships (ads removed)
							foreach ($scholarships as $s) {
							?>
								<div class="scholarship-card">
									<div class="card-image">
										<img
											src="https://admin.mkscholars.com/uploads/posts/<?php echo htmlspecialchars($s['scholarshipImage'], ENT_QUOTES) ?>"
											alt="<?php echo htmlspecialchars($s['scholarshipTitle'], ENT_QUOTES) ?>">
										<div class="image-overlay"></div>
									</div>
									<div class="card-content">
										<h3 class="card-title">
											<a
												href="scholarship-details?scholarship-id=<?php echo $s['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', '-', $s['scholarshipTitle']) ?>">
												<?php echo htmlspecialchars($s['scholarshipTitle'], ENT_QUOTES) ?>
											</a>
										</h3>
										<div class="card-description">
											<?php 
											if (empty($s['scholarshipDetails'])) {
											    echo '<p>No description available.</p>';
											} else {
											    // Strip all HTML tags, decode entities, and limit length for card view
											    $plainText = strip_tags($s['scholarshipDetails']);
											    $decodedText = html_entity_decode($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
											    $truncated = (mb_strlen($decodedText) > 150) ? mb_substr($decodedText, 0, 150) . '...' : $decodedText;
											    echo '<p>' . htmlspecialchars($truncated, ENT_QUOTES, 'UTF-8') . '</p>';
											}
											?>
										</div>
										<div class="card-footer">
											<div class="date-info">
												<svg class="calendar-icon" viewBox="0 0 24 24">
													<path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 2v14
                                         c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V6
                                         c0-1.1-.9-2-2-2zm0 16H5V10h14v10zM5 8V6h14v2H5z" />
												</svg>
												<span><?php echo $s['scholarshipUpdateDate'] ?></span>
											</div>
											<div class="card-actions">
												<a
													href="#"
													class="apply-button ask-help-btn"
													data-scholarship-id="<?php echo $s['scholarshipId']; ?>"
													data-details-url="scholarship-details?scholarship-id=<?php echo $s['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', '-', $s['scholarshipTitle']) ?>">
													<span>Ask Help</span>
													<div class="button-hover-effect"></div>
												</a>
												<a
													href="scholarship-details?scholarship-id=<?php echo $s['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', '-', $s['scholarshipTitle']) ?>"
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
						} else {
							// No scholarships found
							?>
							<div class="no-results">
								<p>No results found</p>
							</div>
						<?php
						}
						?>
					</div>

				</div>
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
						color: white !important;
						/* Adjusted padding */
						/* background: linear-gradient(135deg, #ff6b6b, #a855f7); */
						background: linear-gradient(135deg, #0E77C2, #083352);
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
						color: white !important;
						transition: left 0.3s ease;
					}

					.apply-button:hover .button-hover-effect {
						left: 0;
						color: white;
					}

					.read-more-button {
						position: relative;
						display: inline-flex;
						align-items: center;
						padding: 0.5rem 1rem;
						/* Adjusted padding */
						background: transparent;
						border: 2px solid #0E77C2;
						border-radius: 8px;
						color: #2d3436;
						text-decoration: none;
						transition: all 0.3s ease;
					}

					.read-more-button:hover {
						border-color: #083352;
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

					/* Eligibility Notice Modal Styles */
					.eligibility-modal-overlay {
						display: none;
						position: fixed;
						top: 0;
						left: 0;
						right: 0;
						bottom: 0;
						background: rgba(0, 0, 0, 0.6);
						backdrop-filter: blur(5px);
						z-index: 9999;
						align-items: center;
						justify-content: center;
						animation: fadeIn 0.3s ease;
						padding: 1rem;
						overflow-y: auto;
					}

					.eligibility-modal-overlay.show {
						display: flex;
					}

					@keyframes fadeIn {
						from { opacity: 0; }
						to { opacity: 1; }
					}

					@keyframes slideUp {
						from {
							opacity: 0;
							transform: translateY(30px) scale(0.95);
						}
						to {
							opacity: 1;
							transform: translateY(0) scale(1);
						}
					}

					.eligibility-modal {
						background: white;
						border-radius: 20px;
						max-width: 550px;
						width: 100%;
						max-height: 90vh;
						overflow-y: auto;
						box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
						position: relative;
						animation: slideUp 0.3s ease;
						margin: auto;
						display: flex;
						flex-direction: column;
					}

					.eligibility-modal-header {
						background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
						padding: 2rem 1.5rem;
						display: flex;
						align-items: center;
						gap: 1rem;
						color: white;
						flex-shrink: 0;
					}

					.eligibility-modal-icon {
						width: 60px;
						height: 60px;
						background: rgba(255, 255, 255, 0.2);
						border-radius: 12px;
						display: flex;
						align-items: center;
						justify-content: center;
						backdrop-filter: blur(10px);
						flex-shrink: 0;
					}

					.eligibility-modal-icon i {
						font-size: 1.75rem;
						color: white;
					}

					.eligibility-modal-title {
						flex: 1;
						margin: 0;
						font-weight: 700;
						font-size: 2.25rem;
						line-height: 1.3;
					}

					.eligibility-modal-close {
						background: rgba(255, 255, 255, 0.2);
						border: none;
						width: 40px;
						height: 40px;
						border-radius: 8px;
						color: white;
						cursor: pointer;
						display: flex;
						align-items: center;
						justify-content: center;
						transition: all 0.2s ease;
						backdrop-filter: blur(10px);
						flex-shrink: 0;
					}

					.eligibility-modal-close:hover {
						background: rgba(255, 255, 255, 0.3);
						transform: rotate(90deg);
					}

					.eligibility-modal-close i {
						font-size: 1.1rem;
					}

					.eligibility-modal-content {
						padding: 2.5rem 2rem;
						text-align: center;
						flex: 1;
						display: flex;
						flex-direction: column;
						justify-content: center;
					}

					.eligibility-question {
						font-size: 2.25rem;
						font-weight: 700;
						color: #2d3748;
						margin-bottom: 1.25rem;
						line-height: 1.4;
					}

					.eligibility-message {
						font-size: 1.6rem;
						color: #4a5568;
						line-height: 1.7;
						margin: 0;
					}

					.eligibility-modal-footer {
						padding: 2rem 1.5rem;
						background: #f8f9fa;
						display: flex;
						flex-direction: column;
						gap: 0.875rem;
						border-top: 1px solid #e0e0e0;
						flex-shrink: 0;
					}

					.eligibility-btn {
						padding: 1.5rem 2.5rem;
						border-radius: 12px;
						font-weight: 600;
						font-size: 1.6rem;
						text-decoration: none;
						display: flex;
						align-items: center;
						justify-content: center;
						transition: all 0.3s ease;
						border: none;
						cursor: pointer;
						width: 100%;
					}

					.eligibility-btn i {
						margin-right: 0.5rem;
						font-size: 1.7rem;
					}

					.eligibility-btn-primary {
						background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
						color: white;
						box-shadow: 0 4px 15px rgba(14, 119, 194, 0.3);
					}

					.eligibility-btn-primary:hover {
						transform: translateY(-2px);
						box-shadow: 0 6px 20px rgba(14, 119, 194, 0.4);
					}

					.eligibility-btn-secondary {
						background: white;
						color: #0E77C2;
						border: 2px solid #0E77C2;
					}

					.eligibility-btn-secondary:hover {
						background: #0E77C2;
						color: white;
						transform: translateY(-2px);
					}

					.eligibility-btn-close {
						background: #e0e0e0;
						color: #4a5568;
					}

					.eligibility-btn-close:hover {
						background: #cbd5e0;
						transform: translateY(-2px);
					}

					/* Tablet styles */
					@media (max-width: 992px) {
						.eligibility-modal {
							max-width: 500px;
						}

						.eligibility-modal-header {
							padding: 1.75rem 1.5rem;
						}

						.eligibility-modal-content {
							padding: 2rem 1.75rem;
						}

						.eligibility-question {
							font-size: 1.9rem;
						}

						.eligibility-message {
							font-size: 1.45rem;
						}

						.eligibility-modal-title {
							font-size: 1.9rem;
						}

						.eligibility-btn {
							font-size: 1.4rem;
							padding: 1.25rem 2rem;
						}

						.eligibility-btn i {
							font-size: 1.5rem;
						}
					}

					/* Mobile styles */
					@media (max-width: 768px) {
						.eligibility-modal-overlay {
							padding: 0.75rem;
						}

						.eligibility-modal {
							width: 100%;
							max-width: 100%;
							max-height: 95vh;
							border-radius: 16px;
							margin: 0;
						}

						.eligibility-modal-header {
							padding: 1.5rem 1.25rem;
						}

						.eligibility-modal-icon {
							width: 50px;
							height: 50px;
						}

						.eligibility-modal-icon i {
							font-size: 1.5rem;
						}

						.eligibility-modal-title {
							font-size: 1.3rem;
						}

						.eligibility-modal-close {
							width: 36px;
							height: 36px;
						}

						.eligibility-modal-content {
							padding: 1.75rem 1.5rem;
						}

						.eligibility-question {
							font-size: 1.75rem;
							margin-bottom: 1rem;
						}

						.eligibility-message {
							font-size: 1.35rem;
							line-height: 1.6;
						}

						.eligibility-modal-title {
							font-size: 1.8rem;
						}

						.eligibility-modal-footer {
							padding: 1.5rem 1.25rem;
							gap: 0.75rem;
						}

						.eligibility-btn {
							padding: 1.15rem 1.75rem;
							font-size: 1.3rem;
						}

						.eligibility-btn i {
							font-size: 1.4rem;
						}
					}

					/* Small mobile styles */
					@media (max-width: 480px) {
						.eligibility-modal-overlay {
							padding: 0.5rem;
						}

						.eligibility-modal {
							border-radius: 12px;
						}

						.eligibility-modal-header {
							padding: 1.25rem 1rem;
						}

						.eligibility-modal-icon {
							width: 45px;
							height: 45px;
						}

						.eligibility-modal-icon i {
							font-size: 1.3rem;
						}

						.eligibility-modal-title {
							font-size: 1.15rem;
						}

						.eligibility-modal-close {
							width: 32px;
							height: 32px;
						}

						.eligibility-modal-content {
							padding: 1.5rem 1.25rem;
						}

						.eligibility-question {
							font-size: 1.5rem;
							margin-bottom: 0.875rem;
						}

						.eligibility-message {
							font-size: 1.25rem;
						}

						.eligibility-modal-title {
							font-size: 1.55rem;
						}

						.eligibility-modal-footer {
							padding: 1.25rem 1rem;
						}

						.eligibility-btn {
							padding: 1rem 1.5rem;
							font-size: 1.2rem;
						}

						.eligibility-btn i {
							font-size: 1.3rem;
						}
					}

					/* Very small screens */
					@media (max-width: 360px) {
						.eligibility-modal-title {
							font-size: 1.4rem;
						}

						.eligibility-question {
							font-size: 1.4rem;
						}

						.eligibility-message {
							font-size: 1.2rem;
						}

						.eligibility-btn {
							padding: 0.9rem 1.25rem;
							font-size: 1.15rem;
						}

						.eligibility-btn i {
							font-size: 1.25rem;
						}
					}
				</style>

				<!-- Eligibility Notice Modal -->
				<div class="eligibility-modal-overlay" id="eligibilityModal">
					<div class="eligibility-modal" onclick="event.stopPropagation()">
						<div class="eligibility-modal-header">
							<div class="eligibility-modal-icon">
								<i class="fas fa-info-circle"></i>
							</div>
							<h4 class="eligibility-modal-title">Check Your Eligibility</h4>
							<button type="button" class="eligibility-modal-close" aria-label="Close">
								<i class="fas fa-times"></i>
							</button>
						</div>
						<div class="eligibility-modal-content">
							<p class="eligibility-question">
								Have you checked your eligibility for this application?
							</p>
							<p class="eligibility-message">
								Please make sure you meet all the requirements before proceeding with the application assistance request.
							</p>
						</div>
						<div class="eligibility-modal-footer">
							<button type="button" class="eligibility-btn eligibility-btn-primary" id="proceedBtn">
								<i class="fas fa-check-circle me-2"></i>Proceed
							</button>
							<a href="#" class="eligibility-btn eligibility-btn-secondary" id="readApplicationBtn">
								<i class="fas fa-file-alt me-2"></i>Read Application
							</a>
							<button type="button" class="eligibility-btn eligibility-btn-close" id="closeModalBtn">
								<i class="fas fa-times me-2"></i>Close
							</button>
						</div>
					</div>
				</div>

				<!-- /.row -->
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
					<a href="./courses" class="tran3s hvr-float-shadow">Register Now</a>
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
				Awesome Testimonial Section
			============================================== 
			-->
		<div class="awesome-testimonials">
			<div class="testimonials-background">
				<div class="floating-shapes">
					<div class="shape shape-1"></div>
					<div class="shape shape-2"></div>
					<div class="shape shape-3"></div>
					<div class="shape shape-4"></div>
				</div>
			</div>
			
			<div class="container">
				<div class="testimonials-header">
					<div class="header-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
							<path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828zM3.794.641c.058-.17.301-.17.359 0l.406 1.162a1.89 1.89 0 0 0 1.199 1.199L7.02 3.41c.17.058.17.301 0 .359l-1.262.407a1.89 1.89 0 0 0-1.199 1.199L3.953 6.537c-.058.17-.301.17-.359 0L3.187 5.375a1.89 1.89 0 0 0-1.199-1.199L.726 3.769a.18.18 0 0 1 0-.359l1.262-.406a1.89 1.89 0 0 0 1.199-1.199z"/>
						</svg>
						<span>Success Stories</span>
					</div>
					<h2 class="testimonials-title">
						<span class="title-gradient">Real Stories,</span>
						<span class="title-highlight">Real Success</span>
					</h2>
					<p class="testimonials-subtitle">Discover how MK Scholars transformed dreams into reality for students worldwide. Their journeys inspire us every day.</p>
				</div>

				<div class="testimonials-slider-container">
					<div class="testimonials-slider">
						<!-- Testimonial Slides -->
						<div class="testimonial-slide">
							<div class="testimonial-card featured-card">
								<div class="card-header">
									<div class="quote-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
											<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
										</svg>
									</div>
									<div class="stars">
										<span>★★★★★</span>
									</div>
								</div>
								<p class="testimonial-text">I am incredibly grateful to MK Scholars for helping me secure a full scholarship to study Pharmacy in Turkey. Their support made my dreams come true.</p>
								<div class="student-profile">
									<div class="avatar">
										<span>J</span>
									</div>
									<div class="student-info">
										<h4>Josue NSHUTI</h4>
										<p>Pharmacy Student</p>
										<div class="university">
											<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
												<path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/>
												<path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>
											</svg>
											<span>Ege University, Turkey</span>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="testimonial-slide">
							<div class="testimonial-card">
								<div class="card-header">
									<div class="quote-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
											<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
										</svg>
									</div>
									<div class="stars">★★★★★</div>
								</div>
								<p class="testimonial-text">MK SCHOLARS helped me prepare for the UCAT entrance exam at the University of Rwanda. With their guidance, I passed the exam and secured admission to study Bachelor of Medicine and Surgery.</p>
								<div class="student-profile">
									<div class="avatar">A</div>
									<div class="student-info">
										<h4>Abikunda Ndekwe Aristide</h4>
										<p>Medicine Student, University of Rwanda</p>
									</div>
								</div>
							</div>
						</div>

						<div class="testimonial-slide">
							<div class="testimonial-card">
								<div class="card-header">
									<div class="quote-icon">
										<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" viewBox="0 0 16 16">
											<path d="M12 12a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1h-1.388q0-.527.062-1.054.093-.558.31-.992t.559-.683q.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 9 7.558V11a1 1 0 0 0 1 1zm-6 0a1 1 0 0 0 1-1V8.558a1 1 0 0 0-1-1H4.612q0-.527.062-1.054.094-.558.31-.992.217-.434.559-.683.34-.279.868-.279V3q-.868 0-1.52.372a3.3 3.3 0 0 0-1.085.992 4.9 4.9 0 0 0-.62 1.458A7.7 7.7 0 0 0 3 7.558V11a1 1 0 0 0 1 1z"/>
										</svg>
									</div>
									<div class="stars">★★★★★</div>
								</div>
								<p class="testimonial-text">I am so grateful to MK Scholars for helping me get a Mastercard full scholarship at the University of Rwanda. Their support and guidance made my dream come true.</p>
								<div class="student-profile">
									<div class="avatar">U</div>
									<div class="student-info">
										<h4>Uwera Peace</h4>
										<p>Scholarship Recipient, University of Rwanda</p>
									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Slider Navigation -->
					<div class="slider-nav">
						<button class="nav-btn prev-btn" onclick="changeSlide(-1)">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
								<path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
							</svg>
						</button>
						<button class="nav-btn next-btn" onclick="changeSlide(1)">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
								<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
							</svg>
						</button>
					</div>

					<!-- Slider Dots -->
					<div class="slider-dots">
						<span class="dot active" onclick="currentSlide(1)"></span>
						<span class="dot" onclick="currentSlide(2)"></span>
						<span class="dot" onclick="currentSlide(3)"></span>
					</div>
				</div>

				<!-- View All Testimonials Link -->
				<div class="view-all-testimonials">
					<a href="./testimonials" class="view-all-btn">
						<span>View All Success Stories</span>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
							<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
						</svg>
					</a>
				</div>

								<div class="testimonials-cta">
					<h3>Ready to Start Your Success Story?</h3>
					<p>Join thousands of students who achieved their dreams with MK Scholars</p>
					<a href="./applications" class="cta-button">
						<span>Start Your Journey</span>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
							<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/>
						</svg>
					</a>
				</div>
			</div>
		</div>

		<style>
			/* Awesome Testimonials Section */
			.awesome-testimonials {
				position: relative;
				padding: 100px 0;
				background: linear-gradient(135deg, #f8f9fc 0%, #e9ecf4 100%);
				overflow: hidden;
			}

			.testimonials-background {
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				pointer-events: none;
			}

			.floating-shapes {
				position: absolute;
				width: 100%;
				height: 100%;
			}

			.shape {
				position: absolute;
				border-radius: 50%;
				opacity: 0.1;
				animation: float 6s ease-in-out infinite;
			}

			.shape-1 {
				width: 120px;
				height: 120px;
				background: linear-gradient(135deg, #0E77C2, #083352);
				top: 10%;
				left: 5%;
				animation-delay: -2s;
			}

			.shape-2 {
				width: 80px;
				height: 80px;
				background: linear-gradient(135deg, #FDC713, #F4B942);
				top: 20%;
				right: 10%;
				animation-delay: -4s;
			}

			.shape-3 {
				width: 150px;
				height: 150px;
				background: linear-gradient(135deg, #0E77C2, #083352);
				bottom: 15%;
				left: 10%;
				animation-delay: -1s;
			}

			.shape-4 {
				width: 100px;
				height: 100px;
				background: linear-gradient(135deg, #FDC713, #F4B942);
				bottom: 10%;
				right: 5%;
				animation-delay: -3s;
			}

			@keyframes float {
				0%, 100% {
					transform: translateY(0px) rotate(0deg);
				}
				50% {
					transform: translateY(-20px) rotate(180deg);
				}
			}

			/* Header Styling */
			.testimonials-header {
				text-align: center;
				margin-bottom: 80px;
			}

			.header-badge {
				display: inline-flex;
				align-items: center;
				gap: 10px;
				background: linear-gradient(135deg, #0E77C2, #083352);
				color: white;
				padding: 12px 24px;
				border-radius: 50px;
				font-size: 14px;
				font-weight: 600;
				margin-bottom: 20px;
				box-shadow: 0 8px 30px rgba(14, 119, 194, 0.3);
				animation: pulse-badge 3s ease-in-out infinite;
			}

			@keyframes pulse-badge {
				0%, 100% {
					box-shadow: 0 8px 30px rgba(14, 119, 194, 0.3);
				}
				50% {
					box-shadow: 0 12px 40px rgba(14, 119, 194, 0.5);
				}
			}

			.testimonials-title {
				font-size: 4rem;
				font-weight: 800;
				margin: 0 0 20px 0;
				line-height: 1.2;
			}

			.title-gradient {
				background: linear-gradient(135deg, #0E77C2, #083352);
				-webkit-background-clip: text;
				-webkit-text-fill-color: transparent;
				background-clip: text;
			}

			.title-highlight {
				color: #FDC713;
				text-shadow: 0 4px 15px rgba(253, 199, 19, 0.3);
			}

			.testimonials-subtitle {
				font-size: 1.4rem;
				color: #666;
				max-width: 700px;
				margin: 0 auto;
				line-height: 1.7;
			}

			/* Slider Layout */
			.testimonials-slider-container {
				position: relative;
				margin-bottom: 60px;
			}

			.testimonials-slider {
				overflow: hidden;
				width: 100%;
			}

			.testimonial-slide {
				display: none;
				width: 100%;
				justify-content: center;
			}

			.testimonial-slide.active {
				display: flex;
			}

			.testimonial-slide .testimonial-card {
				max-width: 800px;
				margin: 0 auto;
			}

			/* Testimonial Cards */
			.testimonial-card {
				background: white;
				border-radius: 20px;
				padding: 30px;
				box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
				transition: all 0.4s ease;
				position: relative;
				overflow: hidden;
			}

			.testimonial-card::before {
				content: '';
				position: absolute;
				top: 0;
				left: 0;
				right: 0;
				height: 4px;
				background: linear-gradient(135deg, #0E77C2, #FDC713);
				opacity: 0;
				transition: opacity 0.3s ease;
			}

			.testimonial-card:hover::before {
				opacity: 1;
			}

			.testimonial-card:hover {
				transform: translateY(-10px);
				box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
			}

			.featured-card {
				background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
				color: white;
			}

			.featured-card::before {
				background: linear-gradient(135deg, #FDC713, #F4B942);
			}

			/* Slider Navigation */
			.slider-nav {
				position: absolute;
				top: 50%;
				transform: translateY(-50%);
				width: 100%;
				display: flex;
				justify-content: space-between;
				pointer-events: none;
				z-index: 10;
			}

			.nav-btn {
				width: 50px;
				height: 50px;
				background: linear-gradient(135deg, #0E77C2, #083352);
				border: none;
				border-radius: 50%;
				color: white;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				box-shadow: 0 8px 25px rgba(14, 119, 194, 0.3);
				transition: all 0.3s ease;
				pointer-events: all;
			}

			.nav-btn:hover {
				transform: scale(1.1);
				box-shadow: 0 12px 35px rgba(14, 119, 194, 0.4);
			}

			.prev-btn {
				margin-left: -25px;
			}

			.next-btn {
				margin-right: -25px;
			}

			/* Slider Dots */
			.slider-dots {
				display: flex;
				justify-content: center;
				gap: 15px;
				margin-top: 30px;
			}

			.dot {
				width: 12px;
				height: 12px;
				border-radius: 50%;
				background: rgba(14, 119, 194, 0.3);
				cursor: pointer;
				transition: all 0.3s ease;
			}

			.dot.active,
			.dot:hover {
				background: #0E77C2;
				transform: scale(1.2);
			}

			/* View All Testimonials Button */
			.view-all-testimonials {
				text-align: center;
				margin-bottom: 40px;
			}

			.view-all-btn {
				display: inline-flex;
				align-items: center;
				gap: 12px;
				background: linear-gradient(135deg, #FDC713, #F4B942);
				color: #083352;
				padding: 16px 32px;
				border-radius: 50px;
				text-decoration: none;
				font-weight: 600;
				font-size: 1.1rem;
				box-shadow: 0 8px 25px rgba(253, 199, 19, 0.3);
				transition: all 0.3s ease;
			}

			.view-all-btn:hover {
				transform: translateY(-3px);
				box-shadow: 0 12px 35px rgba(253, 199, 19, 0.4);
				color: #083352;
				text-decoration: none;
			}

			.view-all-btn svg {
				transition: transform 0.3s ease;
			}

			.view-all-btn:hover svg {
				transform: translateX(5px);
			}

			/* Card Header */
			.card-header {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-bottom: 20px;
			}

			.quote-icon {
				display: flex;
				align-items: center;
				justify-content: center;
				width: 60px;
				height: 60px;
				background: linear-gradient(135deg, #FDC713, #F4B942);
				border-radius: 15px;
				color: #083352;
				box-shadow: 0 8px 25px rgba(253, 199, 19, 0.3);
			}

			.quote-icon.small {
				width: 40px;
				height: 40px;
				border-radius: 10px;
			}

			.featured-card .quote-icon {
				background: linear-gradient(135deg, #FDC713, #F4B942);
				box-shadow: 0 10px 30px rgba(253, 199, 19, 0.4);
			}

			.stars {
				color: #FDC713;
				font-size: 1.2rem;
				text-shadow: 0 2px 8px rgba(253, 199, 19, 0.3);
			}

			.stars.small {
				font-size: 1rem;
			}

			/* Testimonial Text */
			.testimonial-text {
				font-size: 1.3rem;
				line-height: 1.8;
				margin-bottom: 25px;
				color: #555;
				font-weight: 400;
			}

			.featured-card .testimonial-text {
				color: rgba(255, 255, 255, 0.95);
				font-size: 1.5rem;
				font-weight: 400;
			}

			/* Student Profile */
			.student-profile {
				display: flex;
				align-items: center;
				gap: 15px;
			}

			.avatar {
				width: 50px;
				height: 50px;
				background: linear-gradient(135deg, #0E77C2, #083352);
				border-radius: 15px;
				display: flex;
				align-items: center;
				justify-content: center;
				color: white;
				font-weight: 700;
				font-size: 1.2rem;
				box-shadow: 0 8px 20px rgba(14, 119, 194, 0.3);
			}

			.featured-card .avatar {
				background: linear-gradient(135deg, #FDC713, #F4B942);
				color: #083352;
				box-shadow: 0 8px 25px rgba(253, 199, 19, 0.4);
			}

			.student-info h4 {
				margin: 0 0 5px 0;
				font-size: 1.1rem;
				font-weight: 700;
				color: #333;
			}

			.featured-card .student-info h4 {
				color: white;
			}

			.student-info p {
				margin: 0;
				color: #666;
				font-size: 0.9rem;
			}

			.featured-card .student-info p {
				color: rgba(255, 255, 255, 0.8);
			}

			.university {
				display: flex;
				align-items: center;
				gap: 8px;
				margin-top: 5px;
				color: #888;
				font-size: 0.85rem;
			}

			.featured-card .university {
				color: rgba(255, 255, 255, 0.7);
			}

			/* Stats Section */
			.testimonials-stats {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
				gap: 30px;
				margin-bottom: 60px;
				text-align: center;
			}

			.stat-item {
				background: white;
				padding: 40px 20px;
				border-radius: 20px;
				box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
				transition: all 0.3s ease;
			}

			.stat-item:hover {
				transform: translateY(-5px);
				box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
			}

			.stat-number {
				font-size: 3rem;
				font-weight: 800;
				background: linear-gradient(135deg, #0E77C2, #FDC713);
				-webkit-background-clip: text;
				-webkit-text-fill-color: transparent;
				background-clip: text;
				margin-bottom: 10px;
			}

			.stat-label {
				color: #666;
				font-weight: 600;
				text-transform: uppercase;
				letter-spacing: 1px;
				font-size: 0.9rem;
			}

			/* CTA Section */
			.testimonials-cta {
				text-align: center;
				background: white;
				padding: 50px 40px;
				border-radius: 25px;
				box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
			}

			.testimonials-cta h3 {
				font-size: 2.2rem;
				font-weight: 700;
				margin: 0 0 15px 0;
				background: linear-gradient(135deg, #0E77C2, #083352);
				-webkit-background-clip: text;
				-webkit-text-fill-color: transparent;
				background-clip: text;
			}

			.testimonials-cta p {
				font-size: 1.1rem;
				color: #666;
				margin-bottom: 30px;
			}

			.cta-button {
				display: inline-flex;
				align-items: center;
				gap: 12px;
				background: linear-gradient(135deg, #0E77C2, #083352);
				color: white;
				padding: 18px 36px;
				border-radius: 50px;
				text-decoration: none;
				font-weight: 600;
				font-size: 1.1rem;
				box-shadow: 0 15px 40px rgba(14, 119, 194, 0.3);
				transition: all 0.3s ease;
			}

			.cta-button:hover {
				transform: translateY(-3px);
				box-shadow: 0 20px 50px rgba(14, 119, 194, 0.4);
				color: white;
				text-decoration: none;
			}

			.cta-button svg {
				transition: transform 0.3s ease;
			}

			.cta-button:hover svg {
				transform: translateX(5px);
			}

			/* Responsive Design */
			@media (max-width: 768px) {
				.awesome-testimonials {
					padding: 60px 0;
				}

				.testimonials-header {
					margin-bottom: 50px;
				}

				.testimonials-title {
					font-size: 2.5rem;
				}

				.testimonials-grid {
					gap: 20px;
					margin-bottom: 50px;
				}

				.featured-card {
					grid-column: span 1;
					transform: none;
				}

				.testimonial-card {
					padding: 25px;
				}

				.testimonials-stats {
					gap: 20px;
					margin-bottom: 40px;
				}

				.stat-item {
					padding: 30px 15px;
				}

				.stat-number {
					font-size: 2.5rem;
				}

				.testimonials-cta {
					padding: 40px 25px;
				}

				.testimonials-cta h3 {
					font-size: 1.8rem;
				}
			}

			@media (max-width: 480px) {
				.testimonials-title {
					font-size: 2rem;
				}

				.testimonials-subtitle {
					font-size: 1rem;
				}

				.header-badge {
					padding: 10px 20px;
					font-size: 12px;
				}

				.testimonial-card {
					padding: 20px;
				}

				.quote-icon {
					width: 50px;
					height: 50px;
				}

				.testimonial-text {
					font-size: 1rem;
				}

				.student-profile {
					flex-direction: column;
					align-items: flex-start;
					gap: 10px;
				}

				.stat-number {
					font-size: 2rem;
				}

				.testimonials-cta h3 {
					font-size: 1.5rem;
				}

				.cta-button {
					padding: 15px 30px;
					font-size: 1rem;
				}
			}
		</style>

		<script>
			// Testimonial Slider Functionality
			let currentSlideIndex = 0;
			const slides = document.querySelectorAll('.testimonial-slide');
			const dots = document.querySelectorAll('.dot');

			function showSlide(index) {
				// Hide all slides
				slides.forEach(slide => slide.classList.remove('active'));
				dots.forEach(dot => dot.classList.remove('active'));
				
				// Show current slide
				if (slides[index]) {
					slides[index].classList.add('active');
					dots[index].classList.add('active');
				}
			}

			function changeSlide(direction) {
				currentSlideIndex += direction;
				
				if (currentSlideIndex >= slides.length) {
					currentSlideIndex = 0;
				} else if (currentSlideIndex < 0) {
					currentSlideIndex = slides.length - 1;
				}
				
				showSlide(currentSlideIndex);
			}

			function currentSlide(index) {
				currentSlideIndex = index - 1;
				showSlide(currentSlideIndex);
			}

			// Auto-play slider
			function autoPlay() {
				currentSlideIndex++;
				if (currentSlideIndex >= slides.length) {
					currentSlideIndex = 0;
				}
				showSlide(currentSlideIndex);
			}

			// Initialize slider when page loads
			document.addEventListener('DOMContentLoaded', function() {
				showSlide(0);
				
				// Auto-play every 5 seconds
				setInterval(autoPlay, 5000);
			});

			// Touch/swipe support for mobile
			let touchStartX = 0;
			let touchEndX = 0;

			document.querySelector('.testimonials-slider').addEventListener('touchstart', function(e) {
				touchStartX = e.changedTouches[0].screenX;
			});

			document.querySelector('.testimonials-slider').addEventListener('touchend', function(e) {
				touchEndX = e.changedTouches[0].screenX;
				handleSwipe();
			});

			function handleSwipe() {
				if (touchEndX < touchStartX - 50) {
					changeSlide(1); // Swipe left, next slide
				}
				if (touchEndX > touchStartX + 50) {
					changeSlide(-1); // Swipe right, previous slide
				}
			}
		</script>

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
					<h4>Your path to Scholarships, Jobs and Success</h4>
					<p>
						At MK Scholars, we are here to help you unlock amazing opportunities to study, work, and grow. We make it easy for students like you to find scholarships, study programs, job opportunities, and internships around the world. Our team provides the latest updates on these opportunities and helps you with every part of the application process. <br><br>

						Whether you need help writing essays, proofreading your work, applying for financial aid, preparing for tests, or coaching for interviews, we’ve got you covered. We are here to support you every step of the way!
						<br><br>
						Apply for a scholarship, job, internship, or study program now and join world-class institutions where leaders are made. You have the power to make a difference in the world. Take responsibility and be the change! Be different, be better.

					</p>
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

		<!-- Eligibility Notice Modal Handler -->
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const eligibilityModal = document.getElementById('eligibilityModal');
				const proceedBtn = document.getElementById('proceedBtn');
				const readApplicationBtn = document.getElementById('readApplicationBtn');
				const closeModalBtn = document.getElementById('closeModalBtn');
				const closeBtn = document.querySelector('.eligibility-modal-close');
				let currentApplyUrl = '';
				let currentDetailsUrl = '';

				function openEligibilityModal(applyUrl, detailsUrl) {
					currentApplyUrl = applyUrl;
					currentDetailsUrl = detailsUrl;
					if (eligibilityModal) {
						eligibilityModal.classList.add('show');
						document.body.style.overflow = 'hidden';
						readApplicationBtn.href = detailsUrl;
					}
				}

				function closeEligibilityModal() {
					if (eligibilityModal) {
						eligibilityModal.classList.remove('show');
						document.body.style.overflow = '';
						currentApplyUrl = '';
						currentDetailsUrl = '';
					}
				}

				// Handle Ask Help button clicks
				document.querySelectorAll('.ask-help-btn').forEach(btn => {
					btn.addEventListener('click', function(e) {
						e.preventDefault();
						const scholarshipId = this.getAttribute('data-scholarship-id');
						const detailsUrl = this.getAttribute('data-details-url');
						const applyUrl = `apply?scholarshipId=${scholarshipId}`;
						openEligibilityModal(applyUrl, detailsUrl);
					});
				});

				// Proceed button - go to application page
				if (proceedBtn) {
					proceedBtn.addEventListener('click', function() {
						if (currentApplyUrl) {
							window.location.href = currentApplyUrl;
						}
					});
				}

				// Read Application button - handled by href in link
				// Close button
				if (closeModalBtn) {
					closeModalBtn.addEventListener('click', closeEligibilityModal);
				}

				// Close icon button
				if (closeBtn) {
					closeBtn.addEventListener('click', closeEligibilityModal);
				}

				// Close modal when clicking overlay
				if (eligibilityModal) {
					eligibilityModal.addEventListener('click', function(e) {
						if (e.target === eligibilityModal) {
							closeEligibilityModal();
						}
					});
				}
			});
		</script>

	</div> <!-- /.main-page-wrapper -->
</body>


</html>