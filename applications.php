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
			<div class="container-fluid" style="padding: 0 5%;">
				<div class="row">
					<div class="col-md-9 col-xs-12 float-right featured-course">
						<ul class="course-menu">
							<?php
							$menuItems = [
								'' => 'All',
								'jobapplications' => 'Job Applications',
								'scholarshipapplications' => 'Scholarships',
								'internshipapplications' => 'Internship',
								'trainingapplications' => 'Trainings',
								'fellowshipapplications' => 'Fellowships',
							];

							$currentKey = isset($_GET['key']) ? $_GET['key'] : '';

							foreach ($menuItems as $key => $label) {
								$activeClass = ($currentKey == $key) ? 'active' : '';
								$href = ($key == '') ? './applications' : "?key=$key";
								echo "<li><a href='$href' class='tran3s $activeClass'>$label</a></li>";
							}
							?>
						</ul>

						<div class="scholarshipsContainerDiv">
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
							if (
								isset($_GET['i']) && !empty($_GET['i']) && is_numeric($_GET['i']) &&
								isset($_GET['Country_name']) && !empty($_GET['Country_name'])
							) {

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
													<a href="#" class="apply-button">
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
							} else {
								?>
								<div class="col-xs-12">
									<p>No results found</p>
								</div>
							<?php
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
									border: 2px solid #e0e0e0;
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
									width: 1cm;
									height:1cm;
									display: flex;
									justify-content: center;
									align-items: center;
									/* border-radius: 50%; */
									background: #f7f7f7;
									font-weight: 600;
									color: #666;
								}

								.course-pagination li a.active,
								.course-pagination li a:hover {
									background: purple;
									color: #fff;
								}

								.searchBtn {
									background-color: purple !important;
									display: flex !important;
									justify-content: center !important;
									align-items: center !important;
									color: #fff !important;
								}

								.course-menu {
									background-color: #ebebff;
									display: flex;
									justify-content: space-evenly !important;
									align-items: center !important;
									flex-wrap: wrap;
									padding: 10px 0px 0px 0px;
								}

								.course-menu .active {
									background-color: purple !important;
								}

								.course-menu .tran3s {
									text-transform: uppercase;
									border: 1px solid purple;
								}

								.course-menu .tran3s:hover {
									background-color: purple !important;
								}
							</style>
						</div> <!-- /.row -->

						<!-- Pagination -->
						<?php if ($total_pages > 1): ?>
							<ul class="course-pagination">
								<?php if ($page > 1): ?>
									<li><a href="<?php echo generatePaginationLink($page - 1); ?>" class="tran3s"><</a></li>
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
									<li><a href="<?php echo generatePaginationLink($page + 1); ?>" class="tran3s">></a></li>
								<?php endif; ?>
							</ul>
						<?php endif; ?>

						<?php
						// Helper function to generate pagination links preserving existing GET parameters
						function generatePaginationLink($page_num)
						{
							$params = $_GET;
							$params['page'] = $page_num;
							return '?' . http_build_query($params);
						}
						?>
					</div> <!-- /.featured-course -->

					<div class="col-md-3 col-sm-6 col-xs-12 course-sidebar">
						<form method="get" class="course-sidebar-search">
							<input type="text" name="searchText" placeholder="Search Scholarship..." value="<?php echo isset($_GET['searchText']) ? htmlspecialchars($_GET['searchText']) : ''; ?>">
							<button class="searchBtn" type="submit" name="search"><i class="fa fa-search" aria-hidden="true"></i></button>
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
			</div> 
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