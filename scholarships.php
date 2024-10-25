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
				Theme Inner Banner
			============================================== 
			-->

		<div class="theme-inner-banner" style="background: url(images/home/banner-2.jpg) no-repeat center;background-size:cover;">
			<div class="opacity">
				<div class="container">
					<h3>SCHOLARSHIPS</h3>
					<ul>
						<li><a href="home">Home</a></li>
						<li>/</li>
						<li>Scholarships</li>
					</ul>
				</div> <!-- /.container -->
			</div> <!-- /.opacity -->
		</div> <!-- /.theme-inner-banner -->



		<!-- 
			=============================================
				Featured Course 3 Column
			============================================== 
			-->
		<div class="feature-course-3-column">
			<div class="container">
				<div class="row">
					<div class="col-md-9 col-xs-12 float-right featured-course">
						<!-- <ul class="course-menu">
							<li><a href="#" class="tran3s active">New</a></li>
							<li><a href="#" class="tran3s">Trending</a></li>
							<li><a href="#" class="tran3s">Popular</a></li>
							<li><a href="#" class="tran3s">Most Rated</a></li>
						</ul> -->

						<div class="row">
							<?php
							if((isset($_GET['i']) && !empty($_GET['i'])) && (isset($_GET['Country_name']) && !empty($_GET['Country_name']))){
								$countryId = $_GET['i'];
								$Country_name = $_GET['Country_name'];
								$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 AND country=$countryId ORDER BY scholarshipId DESC");
								echo "<h5>Showing results of ".$Country_name.".</h5><br>";
							}elseif(isset($_GET['search'])){
								$search=$_GET['searchText'];
								$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 AND scholarshipDetails LIKE '%$search%' ORDER BY scholarshipId DESC");
								echo "<h5>Showing results of ".$search.".</h5><br>";
							}elseif(isset($_GET['key']) && !empty($_GET['key'])){
								$key=$_GET['key'];
								$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 AND scholarshipDetails LIKE '%$key%' ORDER BY scholarshipId DESC");
								echo "<h5>Showing results of ".$key.".</h5><br>";
							}elseif(isset($_GET['course']) && !empty($_GET['course'])){
								$course=$_GET['course'];
								$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 AND scholarshipDetails LIKE '%$course%' ORDER BY scholarshipId DESC");
								echo "<h5>Showing results of ".$course.".</h5><br>";
							}else{
								$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships WHERE scholarshipStatus != 0 ORDER BY scholarshipId DESC");
							}
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
												<h6><a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo $getScholarships['scholarshipTitle'] ?>" class="tran3s"><?php echo $getScholarships['scholarshipTitle'] ?></a></h6>
												<div class="DetailWrapper">
													<p class="postLineLimit"><?php echo $getScholarships['scholarshipDetails'] ?></p>
												</div>

												<ul class="clearfix">
													<li class="float-left"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $getScholarships['scholarshipUpdateDate'] ?></li>

													<li class="float-right"><a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo $getScholarships['scholarshipTitle'] ?>" class="tran3s free hvr-float-shadow">More</a></li>
												</ul>
											</div> <!-- /.text -->
										</div> <!-- /.single-course-grid -->
									</div> <!-- /.col- -->
									<!-- </a> -->
							<?php
								}
							}else{
								?>
								<div><p>No results found</p></div>
								<?php
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

						<!-- <ul class="course-pagination">
							<li><a href="#" class="tran3s active">1</a></li>
							<li><a href="#" class="tran3s">2</a></li>
							<li><a href="#" class="tran3s">3</a></li>
							<li><a href="#" class="tran3s">Next</a></li>
						</ul> -->
					</div> <!-- /.featured-course -->

					<div class="col-md-3 col-sm-6 col-xs-12 course-sidebar">
						<form method="get" class="course-sidebar-search">
							<input type="text" name="searchText" placeholder="Search Scholarship..." value="<?php if(isset($_GET['search'])){echo $search;} ?>">
							<button name="search"><i class="fa fa-search" aria-hidden="true"></i></button>
						</form>

						<div class="course-sidebar-list">
							<h6>Countries</h6>
							<ul>
							<li><a href="scholarships" class="tran3s">Show All (Reset)</a></li>
								<?php include("./php/selectCountriesLI.php") ?>
							</ul>
						</div>
						<div class="course-sidebar-list">
							<h6>Courses</h6>
							<ul>
								<li><a href="?course=software" class="tran3s">Software Engineering</a></li>
							</ul>
						</div>
						<div class="course-sidebar-list">
							<h6>Degree</h6>
							<ul>
								<li><a href="?key=Internship" class="tran3s">Internship</a></li>
								<li><a href="?key=Trainings" class="tran3s">Vacational Trainings</a></li>
								<li><a href="?key=Short Course" class="tran3s">Short Course</a></li>
								<li><a href="?key=Masters" class="tran3s">Masters</a></li>
								<li><a href="?key=undergraduate" class="tran3s">Undergraduate</a></li>
							</ul>
						</div>
					</div> <!-- /.course-sidebar -->
				</div> <!-- /.row -->
			</div> <!-- /.container -->
		</div> <!-- /.feature-course-3-column -->




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

<!-- Mirrored from themazine.com/html/scholars-lms/course-3-column.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 10 May 2024 11:37:07 GMT -->

</html>