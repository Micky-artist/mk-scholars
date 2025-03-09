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
					<h3>APPLICATIONS</h3>
					<ul>
						<li><a href="home">Home</a></li>
						<li>/</li>
						<li>Applications</li>
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
						<ul class="course-menu">
							<?php
							$menuItems = [
								'' => 'All',
								'jobapplications' => 'Job Applications',
								'scholarships' => 'Scholarships'
							];

							$currentKey = isset($_GET['key']) ? $_GET['key'] : '';

							foreach ($menuItems as $key => $label) {
								$activeClass = ($currentKey == $key) ? 'active' : '';
								$href = ($key == '') ? './applications' : "?key=$key";
								echo "<li><a href='$href' class='tran3s $activeClass'>$label</a></li>";
							}
							?>
						</ul>

						<div class="row">
							<?php
							// Pagination settings
							$records_per_page = 25; // Number of records to display per page
							$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
							$offset = ($page - 1) * $records_per_page;

							// Initialize the base query
							$query = "SELECT * FROM scholarships WHERE scholarshipStatus != 0";
							$params = [];
							$types = "";
							$resultHeading = "";
							
							// Handle country filter
							if (isset($_GET['i']) && !empty($_GET['i']) && is_numeric($_GET['i']) && 
								isset($_GET['Country_name']) && !empty($_GET['Country_name'])) {
								
								$countryId = (int)$_GET['i']; // Cast to integer for additional safety
								$Country_name = htmlspecialchars($_GET['Country_name'], ENT_QUOTES, 'UTF-8');
								
								$query .= " AND country = ?";
								$params[] = $countryId;
								$types .= "i"; // Integer parameter
								
								$resultHeading = "<h5>Showing results of " . $Country_name . ".</h5><br>";
							} 
							// Handle search text
							elseif (isset($_GET['search']) && isset($_GET['searchText']) && !empty($_GET['searchText'])) {
								$search = $_GET['searchText'];
								
								$query .= " AND scholarshipDetails LIKE ?";
								$params[] = "%$search%";
								$types .= "s"; // String parameter
								
								$resultHeading = "<h5>Showing results of \"" . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . "\"</h5><br>";
							} 
							// Handle key search
							elseif (isset($_GET['key']) && !empty($_GET['key'])) {
								$key = $_GET['key'];
								
								$query .= " AND scholarshipDetails LIKE ?";
								$params[] = "%$key%";
								$types .= "s"; // String parameter
								
								$resultHeading = "<h5>Showing results of \"" . htmlspecialchars($key, ENT_QUOTES, 'UTF-8') . "\"</h5><br>";
							}
							
							// Add the ordering to all queries
							$query .= " ORDER BY scholarshipId DESC";
							
							// Query for counting total records (for pagination)
							$count_query = str_replace("SELECT *", "SELECT COUNT(*) as total", $query);
							$count_stmt = $conn->prepare($count_query);
							
							// Bind parameters if there are any
							if (!empty($params)) {
								$count_stmt->bind_param($types, ...$params);
							}
							
							$count_stmt->execute();
							$count_result = $count_stmt->get_result();
							$total_records = $count_result->fetch_assoc()['total'];
							$total_pages = ceil($total_records / $records_per_page);
							$count_stmt->close();
							
							// Add LIMIT clause for pagination
							$query .= " LIMIT ?, ?";
							$params[] = $offset;
							$params[] = $records_per_page;
							$types .= "ii"; // Two integer parameters for LIMIT
							
							// Prepare and execute the statement
							$stmt = $conn->prepare($query);
							
							// Bind parameters if there are any
							if (!empty($params)) {
								$stmt->bind_param($types, ...$params);
							}
							
							$stmt->execute();
							$selectScholarships = $stmt->get_result();
							
							// Output the result heading if it exists
							if (!empty($resultHeading)) {
								echo $resultHeading;
							}
							
							$stmt->close();
							
							if ($selectScholarships->num_rows > 0) {
								while ($getScholarships = mysqli_fetch_assoc($selectScholarships)) {
							?>
									<div class="col-md-4 col-sm-6 col-xs-12 allScholarshipContainer">
										<div class="single-course-grid">
											<div class="image">
												<img src="https://admin.mkscholars.com/uploads/posts/<?php echo $getScholarships['scholarshipImage'] ?>" alt="<?php echo htmlspecialchars($getScholarships['scholarshipTitle']) ?>">
											</div>
											<div class="text">
												<h6><a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>" class="tran3s"><?php echo $getScholarships['scholarshipTitle'] ?></a></h6>
												<div class="DetailWrapper">
													<p class="postLineLimit"><?php echo $getScholarships['scholarshipDetails'] ?></p>
												</div>

												<ul class="clearfix">
													<li class="float-left"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $getScholarships['scholarshipUpdateDate'] ?></li>

													<li class="float-right"><a href="scholarship-details?scholarship-id=<?php echo $getScholarships['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', "-", $getScholarships['scholarshipTitle']) ?>" class="tran3s textfont free hvr-float-shadow">READ MORE</a></li>
												</ul>
											</div> <!-- /.text -->
										</div> <!-- /.single-course-grid -->
									</div> <!-- /.col- -->
								<?php
								}
							} else {
								?>
								<div class="col-xs-12">
									<p>No results found</p>
								</div>
							<?php
							}

							?>
							<style>
								.allScholarshipContainer {
									height: 450px;
									margin-bottom: 20px;
								}

								.image {
									height: 200px;
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
									height: 120px;
									overflow: hidden;
								}

								.course-pagination {
									text-align: center;
									margin: 30px 0;
								}

								.course-pagination li {
									display: inline-block;
									margin: 0 3px;
								}

								.course-pagination li a {
									display: block;
									width: 40px;
									height: 40px;
									line-height: 40px;
									border-radius: 50%;
									background: #f7f7f7;
									font-weight: 600;
									color: #666;
								}

								.course-pagination li a.active,
								.course-pagination li a:hover {
									background: #cd2122;
									color: #fff;
								}
							</style>
						</div> <!-- /.row -->

						<!-- Pagination -->
						<?php if ($total_pages > 1): ?>
						<ul class="course-pagination">
							<?php if ($page > 1): ?>
								<li><a href="<?php echo generatePaginationLink($page - 1); ?>" class="tran3s">Prev</a></li>
							<?php endif; ?>
							
							<?php
							// Calculate range of page numbers to display
							$start_page = max(1, $page - 2);
							$end_page = min($total_pages, $page + 2);
							
							for ($i = $start_page; $i <= $end_page; $i++): 
							?>
								<li><a href="<?php echo generatePaginationLink($i); ?>" class="tran3s <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a></li>
							<?php endfor; ?>
							
							<?php if ($page < $total_pages): ?>
								<li><a href="<?php echo generatePaginationLink($page + 1); ?>" class="tran3s">Next</a></li>
							<?php endif; ?>
						</ul>
						<?php endif; ?>

						<?php
						// Helper function to generate pagination links preserving existing GET parameters
						function generatePaginationLink($page_num) {
							$params = $_GET;
							$params['page'] = $page_num;
							return '?' . http_build_query($params);
						}
						?>
					</div> <!-- /.featured-course -->

					<div class="col-md-3 col-sm-6 col-xs-12 course-sidebar">
						<form method="get" class="course-sidebar-search">
							<input type="text" name="searchText" placeholder="Search Scholarship..." value="<?php echo isset($_GET['searchText']) ? htmlspecialchars($_GET['searchText']) : ''; ?>">
							<button type="submit" name="search"><i class="fa fa-search" aria-hidden="true"></i></button>
						</form>

						<div class="course-sidebar-list">
							<h6>Countries</h6>
							<ul>
								<li><a href="applications" class="tran3s">Show All (Reset)</a></li>
								<?php include("./php/selectCountriesLI.php") ?>
							</ul>
						</div>
						
						<div class="course-sidebar-list">
							<h6>Tags</h6>
							<ul>
								<?php
								$selectTags = mysqli_query($conn, "SELECT * FROM PostTags WHERE TagStatus = 1");
								if ($selectTags->num_rows > 0) {
									while ($tagData = mysqli_fetch_assoc($selectTags)) {
								?>
										<li><a href="?key=<?php echo htmlspecialchars($tagData['TagValue'], ENT_QUOTES, 'UTF-8'); ?>" class="tran3s"><?php echo htmlspecialchars($tagData['TagName'], ENT_QUOTES, 'UTF-8'); ?></a></li>

									<?php
									}
								} else {
									?>
									<li>No tags available</li>
								<?php
								}
								?>
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

</html>