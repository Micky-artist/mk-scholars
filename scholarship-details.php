<?php

include("./dbconnection/connection.php");
include("./php/selectScholarshipDetails.php")

?>

<!DOCTYPE html>
<html lang="en">
<?php include("./partials/head.php") ?>

<head>
	<title>Mk Scholars <?php echo $scholarshipData['scholarshipTitle'] ?></title>

	<!-- Open Graph metadata for social sharing -->
	<meta property="og:title" content="Mk Scholars <?php echo $scholarshipData['scholarshipTitle'] ?>" />
	<meta property="og:description" content="<?php echo $scholarshipData['scholarshipTitle'] ?>" />
	<meta property="og:image" content="https://admin.mkscholars.com/uploads/posts/<?php echo $scholarshipData['scholarshipImage'] ?>" />
	<meta property="og:image:width" content="1200" />
	<meta property="og:image:height" content="630" />
	<meta property="og:type" content="website" />
	<meta property="og:url" content="https://mkscholars.com/scholarship-details?scholarship-id=<?php echo $scholarshipData['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $scholarshipData['scholarshipTitle']) ?>" />

	<!-- Existing meta tags -->
	<meta name="description" content="<?php echo $scholarshipData['scholarshipTitle'] ?>">
</head>


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

		<div class="theme-inner-banner" style="background: url(https://admin.mkscholars.com/uploads/posts/<?php echo $scholarshipData['scholarshipImage'] ?>) no-repeat center;background-size:cover;">
			<div class="opacity">
				<div class="container">
					<h3><?php echo $scholarshipData['scholarshipTitle'] ?></h3>
					<ul>
						<li><a href="home">Home</a></li>
						<li>/</li>
						<li>More</li>
					</ul>
				</div> <!-- /.container -->
			</div> <!-- /.opacity -->
		</div> <!-- /.theme-inner-banner -->


		<!-- 
			=============================================
				Blog Details
			============================================== 
			-->
		<div class="theme-details-page blog-details">
			<div class="container">
				<div class="row">
					<div class="col-md-9 col-xs-12 theme-large-sidebar">
						<div class="scholarship-top-actions">
							<a class="scholarship-share-btn scholarship-whatsapp-share" target="_blank" href="https://chat.whatsapp.com/GyVRqEBwgzA22RrNOF2bLn?mode=ems_copy_t">
								<i class="fab fa-whatsapp"></i>
								<span>Join WhatsApp Group</span>
							</a>
							<a class="scholarship-share-btn scholarship-whatsapp-share" target="_blank" href="https://api.whatsapp.com/send?text=<?php echo urlencode('Check out this scholarship: https://mkscholars.com/scholarship-details?scholarship-id=' . $scholarshipData['scholarshipId'] . '&scholarship-title=' . preg_replace('/\s+/', '-', $scholarshipData['scholarshipTitle'])); ?>">
								<i class="fas fa-share-alt"></i>
								<span>Share on WhatsApp</span>
							</a>
						</div>


						<div class="SocialMediaIcons">
							<a href="#"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#87CEEB" class="bi bi-facebook" viewBox="0 0 16 16">
									<path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
								</svg></a>
							<a href="https://x.com/MkScholars"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="#000" class="bi bi-twitter-x" viewBox="0 0 16 16">
									<path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
								</svg></a>
							<a href="https://www.youtube.com/@mkscholars"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="red" class="bi bi-youtube" viewBox="0 0 16 16">
									<path d="M8.051 1.999h.089c.822.003 4.987.033 6.11.335a2.01 2.01 0 0 1 1.415 1.42c.101.38.172.883.22 1.402l.01.104.022.26.008.104c.065.914.073 1.77.074 1.957v.075c-.001.194-.01 1.108-.082 2.06l-.008.105-.009.104c-.05.572-.124 1.14-.235 1.558a2.01 2.01 0 0 1-1.415 1.42c-1.16.312-5.569.334-6.18.335h-.142c-.309 0-1.587-.006-2.927-.052l-.17-.006-.087-.004-.171-.007-.171-.007c-1.11-.049-2.167-.128-2.654-.26a2.01 2.01 0 0 1-1.415-1.419c-.111-.417-.185-.986-.235-1.558L.09 9.82l-.008-.104A31 31 0 0 1 0 7.68v-.123c.002-.215.01-.958.064-1.778l.007-.103.003-.052.008-.104.022-.26.01-.104c.048-.519.119-1.023.22-1.402a2.01 2.01 0 0 1 1.415-1.42c.487-.13 1.544-.21 2.654-.26l.17-.007.172-.006.086-.003.171-.007A100 100 0 0 1 7.858 2zM6.4 5.209v4.818l4.157-2.408z" />
								</svg></a>
						</div>
						<div class="title">
							<span><?php echo $scholarshipData['scholarshipUpdateDate'] ?></span>
							<h4><?php echo $scholarshipData['scholarshipTitle'] ?></h4>

							<!-- <ul>
									<li>by admin</li>
									<li>|</li>
									<li>Business</li>
								</ul> -->
						</div> <!-- /.title -->
						<img src="https://admin.mkscholars.com/uploads/posts/<?php echo $scholarshipData['scholarshipImage'] ?>" alt=""> <br>
						<p><?php echo $scholarshipData['scholarshipDetails'] ?></p>
						<br>
						<div class="scholarship-action-buttons">
							<a href="apply?scholarshipId=<?php echo $scholarshipData['scholarshipId']; ?>" class="scholarship-btn scholarship-btn-primary ask-help-main-btn">
								<i class="fas fa-hand-holding-heart"></i>
								<span>Ask for Help</span>
							</a>
							<a class="scholarship-btn scholarship-btn-secondary" target="_blank" href="<?php echo $scholarshipData['scholarshipLink'] ?>">
								<i class="fas fa-external-link-alt"></i>
								<span>Open Application Link</span>
							</a>
							<a class="scholarship-btn scholarship-btn-danger" target="_blank" href="<?php echo $scholarshipData['scholarshipYoutubeLink'] ?>">
								<i class="fab fa-youtube"></i>
								<span>Youtube Guide Video</span>
							</a>
							<a class="scholarship-btn scholarship-btn-success" target="_blank" href="https://chat.whatsapp.com/GFcmIyDmgs7JK7wnEGvRvw">
								<i class="fab fa-whatsapp"></i>
								<span>Join WhatsApp Group</span>
							</a>
						</div>

						<!-- <div class="scholarshipTable">
							<div>This is the title</div>
							<a href="">This is the link</a>
						</div> -->
					</div> <!-- /.theme-large-sidebar -->


					<div class="col-md-3 col-sm-6 col-xs-12 theme-sidebar">
						<div>
							<h5>Application process guiding videos</h5>
							<div class="videosContainerDiv">
								<?php
								// 1) Fetch all active videos
								$videos = [];
								$result = mysqli_query($conn, "SELECT * FROM youtubeVideos WHERE VideoStatus = 1");
								if ($result && $result->num_rows > 0) {
									while ($row = mysqli_fetch_assoc($result)) {
										$videos[] = $row;
									}
								}

								$numVideos = count($videos);

								if ($numVideos > 0) {
									// Loop through videos (ads removed)
									foreach ($videos as $video) {
										// Output the video embed/link and title
										echo $video['videoLink'];
										?>
										<b>
											<p><?php echo htmlspecialchars($video['VideoTitle'], ENT_QUOTES); ?></p>
										</b><br>
										<?php
										$slotIndex++;
									}
								} else {
									// No videos
									?>
									<div class="no-results">
										<p>No videos found</p>
									</div>
								<?php
								}
								?>
							</div>

						</div>
						<br>
						<div>
							<a href="https://www.youtube.com/@mkscholars" target="_blank" class="scholarship-btn scholarship-btn-youtube">
								<i class="fab fa-youtube"></i>
								<span>Visit our Youtube Channel @mkscholars</span>
							</a>
						</div>
						<br>

						<form method="post" class="sidebar-search">
							<input type="text" name="searchValue" placeholder="Search...">
							<button name="search" class="s-color-bg tran3s"><i class="fa fa-search" aria-hidden="true"></i></button>
						</form>
						<div class="sidebar-recent-post">
							<h5>Recently Uploaded</h5>

							<ul class="scholarship-list">
								<?php
								// 1) Validate & sanitize scholarship ID
								if (isset($_GET['scholarship-id']) && is_numeric($_GET['scholarship-id'])) {
									$presentScholarshipId = (int) $_GET['scholarship-id'];
								} else {
									$presentScholarshipId = 0;
								}

								// 2) Prepare and execute the query
								if (isset($_POST['search']) && !empty($_POST['searchValue'])) {
									$stmt = $conn->prepare("SELECT *
              FROM scholarships
             WHERE scholarshipStatus != 0
               AND scholarshipDetails LIKE ?
             ORDER BY scholarshipId DESC
             LIMIT 7
        ");
									$searchParam = '%' . $_POST['searchValue'] . '%';
									$stmt->bind_param('s', $searchParam);
								} else {
									$stmt = $conn->prepare("SELECT *
              FROM scholarships
             WHERE scholarshipStatus != 0
               AND scholarshipId != ?
             ORDER BY scholarshipId DESC
             LIMIT 7
        ");
									$stmt->bind_param('i', $presentScholarshipId);
								}
								$stmt->execute();
								$result = $stmt->get_result();
								$stmt->close();

								// 3) Fetch all into an array
								$items = [];
								while ($row = $result->fetch_assoc()) {
									$items[] = $row;
								}
								$numItems = count($items);

								if ($numItems > 0) {
									// Loop through items without ads
									foreach ($items as $sch) {
									?>
										<li class="clearfix">
											<img
												src="https://admin.mkscholars.com/uploads/posts/<?php echo htmlspecialchars($sch['scholarshipImage'], ENT_QUOTES) ?>"
												alt="<?php echo htmlspecialchars($sch['scholarshipTitle'], ENT_QUOTES) ?>"
												class="float-left">
											<div class="post float-left">
												<a
													href="scholarship-details?scholarship-id=<?php echo $sch['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', '-', $sch['scholarshipTitle']) ?>"
													class="tran3s">
													<?php echo htmlspecialchars($sch['scholarshipTitle'], ENT_QUOTES) ?>
												</a>
												<span><?php echo htmlspecialchars($sch['scholarshipUpdateDate'], ENT_QUOTES) ?></span>
											</div>
										</li>
									<?php
									}
								} else {
									// No results
									?>
									<li class="clearfix">No Results found</li>
								<?php
								}
								?>
							</ul>


						</div>



					</div> <!-- /.theme-sidebar -->
				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</div> <!-- /.theme-details-page -->




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

		<!-- Ask for Help Popup Modal -->
		<div class="help-popup-overlay" id="helpPopupModal">
			<div class="help-popup-modal" onclick="event.stopPropagation()">
				<button type="button" class="help-popup-close" id="helpPopupClose" aria-label="Close">
					<i class="fas fa-times"></i>
				</button>
				<div class="help-popup-content">
					<div class="help-popup-icon">
						<i class="fas fa-hand-holding-heart"></i>
					</div>
					<h3 class="help-popup-title">Need Help with <?php echo htmlspecialchars($scholarshipData['scholarshipTitle'], ENT_QUOTES); ?>?</h3>
					<p class="help-popup-message">
						Our team is here to guide you through the entire application process. Get personalized assistance and increase your chances of success!
					</p>
					<div class="help-popup-buttons">
						<a href="apply?scholarshipId=<?php echo $scholarshipData['scholarshipId']; ?>" class="help-popup-btn help-popup-btn-primary">
							<i class="fas fa-check-circle"></i>
							<span>Yes, I Need Help</span>
						</a>
						<button type="button" class="help-popup-btn help-popup-btn-close" id="helpPopupCloseBtn">
							<i class="fas fa-times"></i>
							<span>Maybe Later</span>
						</button>
					</div>
				</div>
			</div>
		</div>

		<style>
			/* Scholarship Action Buttons Redesign */
			.scholarship-top-actions {
				display: flex;
				gap: 1rem;
				flex-wrap: wrap;
				margin-bottom: 2rem;
			}

			.scholarship-share-btn {
				display: inline-flex;
				align-items: center;
				gap: 0.5rem;
				padding: 1.25rem 2rem;
				border-radius: 10px;
				text-decoration: none;
				font-weight: 600;
				font-size: 1.5rem;
				transition: all 0.3s ease;
				border: none;
				cursor: pointer;
			}

			.scholarship-whatsapp-share {
				background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
				color: white;
				box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
			}

			.scholarship-whatsapp-share:hover {
				transform: translateY(-2px);
				box-shadow: 0 6px 20px rgba(37, 211, 102, 0.4);
				color: white;
			}

			.scholarship-action-buttons {
				display: flex;
				flex-wrap: wrap;
				gap: 1rem;
				margin-top: 2rem;
				padding-top: 2rem;
				border-top: 2px solid #e0e0e0;
			}

			.scholarship-btn {
				flex: 1;
				min-width: 200px;
				display: inline-flex;
				align-items: center;
				justify-content: center;
				gap: 0.75rem;
				padding: 1.5rem 2.5rem;
				border-radius: 12px;
				text-decoration: none;
				font-weight: 600;
				font-size: 1.75rem;
				transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
				border: none;
				cursor: pointer;
				position: relative;
				overflow: hidden;
				box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
			}

			.scholarship-btn i {
				font-size: 1.85rem;
			}

			.scholarship-btn::before {
				content: '';
				position: absolute;
				top: 50%;
				left: 50%;
				width: 0;
				height: 0;
				border-radius: 50%;
				background: rgba(255, 255, 255, 0.2);
				transform: translate(-50%, -50%);
				transition: width 0.6s, height 0.6s;
			}

			.scholarship-btn:hover::before {
				width: 300px;
				height: 300px;
			}

			.scholarship-btn-primary {
				background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
				color: white;
				box-shadow: 0 4px 20px rgba(14, 119, 194, 0.4);
			}

			.scholarship-btn-primary:hover {
				transform: translateY(-3px);
				box-shadow: 0 8px 25px rgba(14, 119, 194, 0.5);
				color: white;
			}

			.scholarship-btn-secondary {
				background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
				color: white;
				box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
			}

			.scholarship-btn-secondary:hover {
				transform: translateY(-3px);
				box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
				color: white;
			}

			.scholarship-btn-danger {
				background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
				color: white;
				box-shadow: 0 4px 20px rgba(220, 53, 69, 0.4);
			}

			.scholarship-btn-danger:hover {
				transform: translateY(-3px);
				box-shadow: 0 8px 25px rgba(220, 53, 69, 0.5);
				color: white;
			}

			.scholarship-btn-success {
				background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
				color: white;
				box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
			}

			.scholarship-btn-success:hover {
				transform: translateY(-3px);
				box-shadow: 0 8px 25px rgba(40, 167, 69, 0.5);
				color: white;
			}

			.scholarship-btn-youtube {
				background: linear-gradient(135deg, #FF0000 0%, #CC0000 100%);
				color: white;
				box-shadow: 0 4px 20px rgba(255, 0, 0, 0.4);
				width: 100%;
			}

			.scholarship-btn-youtube:hover {
				transform: translateY(-3px);
				box-shadow: 0 8px 25px rgba(255, 0, 0, 0.5);
				color: white;
			}

			/* Help Popup Modal Styles */
			.help-popup-overlay {
				display: none;
				position: fixed;
				top: 0;
				left: 0;
				right: 0;
				bottom: 0;
				background: rgba(0, 0, 0, 0.7);
				backdrop-filter: blur(8px);
				z-index: 10000;
				align-items: center;
				justify-content: center;
				padding: 1rem;
				animation: fadeIn 0.3s ease;
			}

			.help-popup-overlay.show {
				display: flex;
				align-items: center;
				justify-content: center;
			}

			@keyframes fadeIn {
				from { opacity: 0; }
				to { opacity: 1; }
			}

			@keyframes slideUpBounce {
				from {
					opacity: 0;
					transform: translateY(50px) scale(0.9);
				}
				to {
					opacity: 1;
					transform: translateY(0) scale(1);
				}
			}

			.help-popup-modal {
				background: white;
				border-radius: 24px;
				max-width: 480px;
				width: 100%;
				box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
				position: relative;
				animation: slideUpBounce 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
				overflow: hidden;
				margin: auto;
			}

			.help-popup-close {
				position: absolute;
				top: 1rem;
				right: 1rem;
				width: 40px;
				height: 40px;
				border-radius: 50%;
				background: rgba(0, 0, 0, 0.1);
				border: none;
				color: #4a5568;
				cursor: pointer;
				display: flex;
				align-items: center;
				justify-content: center;
				transition: all 0.3s ease;
				z-index: 10;
				font-size: 1.2rem;
			}

			.help-popup-close:hover {
				background: rgba(220, 53, 69, 0.1);
				color: #dc3545;
				transform: rotate(90deg) scale(1.1);
			}

			.help-popup-content {
				padding: 3rem 2rem 2rem;
				text-align: center;
			}

			.help-popup-icon {
				width: 80px;
				height: 80px;
				margin: 0 auto 1.5rem;
				background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
				border-radius: 50%;
				display: flex;
				align-items: center;
				justify-content: center;
				box-shadow: 0 8px 25px rgba(14, 119, 194, 0.3);
				animation: pulse 2s infinite;
			}

			@keyframes pulse {
				0%, 100% {
					transform: scale(1);
				}
				50% {
					transform: scale(1.05);
				}
			}

			.help-popup-icon i {
				font-size: 2.5rem;
				color: white;
			}

			.help-popup-title {
				font-size: 2.25rem;
				font-weight: 700;
				color: #2d3748;
				margin-bottom: 1rem;
				line-height: 1.3;
			}

			.help-popup-scholarship-name {
				display: flex;
				align-items: center;
				justify-content: center;
				gap: 0.75rem;
				background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
				padding: 1rem 1.5rem;
				border-radius: 12px;
				margin: 1.25rem 0;
				border-left: 4px solid #0E77C2;
			}

			.help-popup-scholarship-name i {
				color: #0E77C2;
				font-size: 1.5rem;
				flex-shrink: 0;
			}

			.help-popup-scholarship-name span {
				font-size: 1.2rem;
				font-weight: 600;
				color: #2d3748;
				text-align: center;
				line-height: 1.4;
			}

			.help-popup-message {
				font-size: 1.5rem;
				color: #4a5568;
				line-height: 1.6;
				margin-bottom: 2rem;
			}

			.help-popup-buttons {
				display: flex;
				flex-direction: column;
				gap: 0.75rem;
			}

			.help-popup-btn {
				display: flex;
				align-items: center;
				justify-content: center;
				gap: 0.75rem;
				padding: 1.5rem 2.5rem;
				border-radius: 12px;
				font-weight: 600;
				font-size: 1.5rem;
				text-decoration: none;
				transition: all 0.3s ease;
				border: none;
				cursor: pointer;
				width: 100%;
			}

			.help-popup-btn i {
				font-size: 1.65rem;
			}

			.help-popup-btn-primary {
				background: linear-gradient(135deg, #0E77C2 0%, #083352 100%);
				color: white;
				box-shadow: 0 4px 20px rgba(14, 119, 194, 0.4);
			}

			.help-popup-btn-primary:hover {
				transform: translateY(-2px);
				box-shadow: 0 8px 25px rgba(14, 119, 194, 0.5);
				color: white;
			}

			.help-popup-btn-close {
				background: #e0e0e0;
				color: #4a5568;
			}

			.help-popup-btn-close:hover {
				background: #cbd5e0;
				transform: translateY(-2px);
			}

			/* Responsive Styles */
			@media (max-width: 768px) {
				.scholarship-top-actions {
					flex-direction: column;
				}

				.scholarship-share-btn {
					width: 100%;
					justify-content: center;
				}

				.scholarship-action-buttons {
					flex-direction: column;
				}

				.scholarship-btn {
					min-width: 100%;
					padding: 1.25rem 2rem;
					font-size: 1.45rem;
				}

				.scholarship-btn i {
					font-size: 1.55rem;
				}

				.scholarship-share-btn {
					padding: 1rem 1.75rem;
					font-size: 1.35rem;
				}

				.help-popup-modal {
					max-width: 100%;
					border-radius: 20px;
				}

				.help-popup-content {
					padding: 2.5rem 1.5rem 1.5rem;
				}

				.help-popup-icon {
					width: 70px;
					height: 70px;
				}

				.help-popup-icon i {
					font-size: 2rem;
				}

				.help-popup-title {
					font-size: 2rem;
				}

				.help-popup-scholarship-name {
					padding: 0.875rem 1.25rem;
					margin: 1rem 0;
				}

				.help-popup-scholarship-name i {
					font-size: 1.35rem;
				}

				.help-popup-scholarship-name span {
					font-size: 1.1rem;
				}

				.help-popup-message {
					font-size: 1.35rem;
				}

				.help-popup-btn {
					font-size: 1.35rem;
					padding: 1.25rem 2rem;
				}

				.help-popup-btn i {
					font-size: 1.5rem;
				}

				.help-popup-close {
					top: 0.75rem;
					right: 0.75rem;
					width: 36px;
					height: 36px;
				}
			}

			@media (max-width: 480px) {
				.help-popup-content {
					padding: 2rem 1.25rem 1.25rem;
				}

				.help-popup-icon {
					width: 60px;
					height: 60px;
					margin-bottom: 1rem;
				}

				.help-popup-icon i {
					font-size: 1.75rem;
				}

				.help-popup-title {
					font-size: 1.8rem;
				}

				.help-popup-scholarship-name {
					padding: 0.75rem 1rem;
					margin: 0.875rem 0;
				}

				.help-popup-scholarship-name i {
					font-size: 1.2rem;
				}

				.help-popup-scholarship-name span {
					font-size: 1rem;
				}

				.help-popup-message {
					font-size: 1.3rem;
					margin-bottom: 1.5rem;
				}

				.help-popup-btn {
					padding: 1.15rem 1.75rem;
					font-size: 1.3rem;
				}

				.help-popup-btn i {
					font-size: 1.4rem;
				}
			}
		</style>

		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const helpPopup = document.getElementById('helpPopupModal');
				const closeBtn = document.getElementById('helpPopupClose');
				const closeBtnBottom = document.getElementById('helpPopupCloseBtn');
				let popupTimer;
				const scholarshipId = <?php echo isset($scholarshipData['scholarshipId']) ? $scholarshipData['scholarshipId'] : 0; ?>;
				const storageKey = 'helpPopupLastShown_' + scholarshipId;
				let lastShownTime = localStorage.getItem(storageKey) || 0;
				const oneMinute = 60000; // 1 minute in milliseconds

				function showHelpPopup() {
					if (helpPopup) {
						helpPopup.classList.add('show');
						document.body.style.overflow = 'hidden';
						const currentTime = Date.now();
						localStorage.setItem(storageKey, currentTime.toString());
						lastShownTime = currentTime;
					}
				}

				function closeHelpPopup() {
					if (helpPopup) {
						helpPopup.classList.remove('show');
						document.body.style.overflow = '';
					}
				}

				// Show popup immediately when user enters the page
				showHelpPopup();

				// Set interval to show popup every 1 minute after page load
				popupTimer = setInterval(function() {
					const now = Date.now();
					const storedTime = parseInt(localStorage.getItem(storageKey)) || 0;
					const timeSinceLastShow = now - storedTime;
					
					// Show popup every minute if it's been at least 1 minute since last show
					if (timeSinceLastShow >= oneMinute) {
						// Only show if popup is not currently visible
						if (helpPopup && !helpPopup.classList.contains('show')) {
							showHelpPopup();
						}
					}
				}, oneMinute);

				// Close button handlers
				if (closeBtn) {
					closeBtn.addEventListener('click', closeHelpPopup);
				}

				if (closeBtnBottom) {
					closeBtnBottom.addEventListener('click', closeHelpPopup);
				}

				// Close on overlay click
				if (helpPopup) {
					helpPopup.addEventListener('click', function(e) {
						if (e.target === helpPopup) {
							closeHelpPopup();
						}
					});
				}

				// Close on Escape key
				document.addEventListener('keydown', function(e) {
					if (e.key === 'Escape' && helpPopup && helpPopup.classList.contains('show')) {
						closeHelpPopup();
					}
				});

				// Cleanup on page unload
				window.addEventListener('beforeunload', function() {
					if (popupTimer) {
						clearInterval(popupTimer);
					}
				});
			});
		</script>

	</div> <!-- /.main-page-wrapper -->
</body>

</html>