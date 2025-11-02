<?php
include("./dbconnection/connection.php");
session_start();

// Load admin session and permissions if admin is logged in
$isAdmin = false;
$adminPermissions = [];

if (isset($_SESSION['adminId']) && isset($_SESSION['AdminName'])) {
    include("./admin/dbconnections/connection.php");
    include("./admin/php/validateAdminSession.php");
    $isAdmin = true;
    
    // Get admin permissions
    if (isset($access) && is_array($access)) {
        foreach ($access as $right) {
            $adminPermissions[] = $right;
        }
    }
}

// Helper function to check admin permission
function hasAdminPermission($permissionName) {
    global $isAdmin, $adminPermissions;
    if (!$isAdmin || empty($adminPermissions)) {
        return false;
    }
    foreach ($adminPermissions as $right) {
        if (isset($right[$permissionName]) && $right[$permissionName] == 1) {
            return true;
        }
    }
    return false;
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
					<!-- Mobile-First Search and Filter Section -->
					<div class="col-12 mobile-search-section">
						<div class="mobile-search-wrapper">
							<!-- Search Input -->
							<form method="get" class="mobile-search-form">
								<div class="search-input-group">
									<input type="text" name="searchText" placeholder="Search Scholarship..." 
										   value="<?php echo isset($_GET['searchText']) ? htmlspecialchars($_GET['searchText']) : ''; ?>"
										   class="mobile-search-input">
									<button class="mobile-search-btn" type="submit" name="search">
										<svg class="search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
											<path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
										</svg>
									</button>
								</div>
							</form>

							<!-- Countries Filter -->
							<div class="mobile-countries-section">
								<h6 class="countries-title">Filter by Country</h6>
								<div class="countries-grid">
									<a href="applications" class="country-item reset-all">
										<span class="country-name">Show All</span>
										<span class="country-reset">(Reset)</span>
									</a>
									<?php
									$selectCountries = mysqli_query($conn, "SELECT DISTINCT(s.country), c.CountryName, c.countryId FROM countries c INNER JOIN scholarships s ON s.country = c.countryId WHERE s.scholarshipStatus !=0 order by c.CountryName DESC");
									if ($selectCountries->num_rows > 0) {
										while ($getCountries = mysqli_fetch_assoc($selectCountries)) {
											?>
											<a href="?i=<?php echo $getCountries['countryId'] ?>&Country_name=<?php echo $getCountries['CountryName'] ?>" class="country-item">
												<span class="country-name"><?php echo htmlspecialchars($getCountries['CountryName'], ENT_QUOTES, 'UTF-8') ?></span>
											</a>
											<?php
										}
									}
									?>
								</div>
							</div>
						</div>
					</div>

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
							// $Country_name = htmlspecialchars($_GET['Country_name'], ENT_QUOTES, 'UTF-8');

							$query .= " AND country = ?";
							$params[] = $countryId;
							$types .= "i"; // Integer parameter
							$resultHeading = "<h5>Showing results of \"" . htmlspecialchars($_GET['Country_name'], ENT_QUOTES, 'UTF-8') . "\"</h5><br>";

							// $resultHeading = "<h5>Showing results of " . $Country_name . ".</h5><br><br>";
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


						?>
						<div class="scholarshipsContainerDiv">
							<?php
							// 1) collect all scholarships
							$scholarships = [];
							if ($selectScholarships->num_rows > 0) {
								while ($row = mysqli_fetch_assoc($selectScholarships)) {
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
												<a href="scholarship-details?scholarship-id=<?php echo $s['scholarshipId'] ?>&scholarship-title=<?php echo preg_replace('/\s+/', '-', $s['scholarshipTitle']) ?>">
													<?php echo htmlspecialchars($s['scholarshipTitle'], ENT_QUOTES) ?>
												</a>
											</h3>
											<div class="card-description">
												<p><?php echo htmlspecialchars($s['scholarshipDetails'], ENT_QUOTES) ?></p>
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
									<a href="apply?scholarshipId=<?php echo $s['scholarshipId']; ?>" 
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
									$slotIndex++;
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
						</style>
						<style>
							.allScholarshipContainer {
								height: 450px;
								margin-bottom: 20px;
							}

							.image {
								height: 200px;
								overflow: hidden;
								/* Added to contain images properly */
							}

							.image img {
								object-fit: cover;
								width: 100%;
								height: 100%;
								transition: transform 0.3s ease;
								/* Added for hover effect */
							}

							.image img:hover {
								transform: scale(1.05);
								/* Subtle zoom effect on hover */
							}

							.postLineLimit {
								text-overflow: ellipsis;
								display: -webkit-box;
								-webkit-line-clamp: 4;
								line-clamp: 4;
								/* Standard property alongside webkit version */
								-webkit-box-orient: vertical;
								overflow: hidden;
							}

							.DetailWrapper {
								height: 120px;
								overflow: hidden;
							}

							/* Modern Pagination Styling */
							.modern-pagination-wrapper {
								background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
								border-radius: 16px;
								padding: 30px;
								margin: 40px 0;
								box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.pagination-info {
								display: flex;
								justify-content: space-between;
								align-items: center;
								margin-bottom: 25px;
								padding: 0 10px;
							}

							.pagination-text {
								font-size: 14px;
								color: #6c757d;
								font-weight: 500;
							}

							.pagination-total {
								font-size: 14px;
								color: #495057;
								font-weight: 600;
								background: rgba(8, 51, 82, 0.1);
								padding: 6px 12px;
								border-radius: 20px;
							}

							.modern-pagination {
								display: flex;
								justify-content: center;
								margin-bottom: 25px;
							}

							.pagination-list {
								display: flex;
								list-style: none;
								margin: 0;
								padding: 0;
								gap: 8px;
								align-items: center;
								flex-wrap: wrap;
								justify-content: center;
							}

							.pagination-item {
								margin: 0;
							}

							.pagination-link {
								display: flex;
								align-items: center;
								justify-content: center;
								min-width: 44px;
								height: 44px;
								padding: 0 16px;
								border-radius: 12px;
								background: white;
								color: #495057;
								text-decoration: none;
								font-weight: 600;
								font-size: 15px;
								transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
								border: 2px solid transparent;
								box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
								position: relative;
								overflow: hidden;
							}

							.pagination-link:hover {
								background: #667eea;
								color: white;
								transform: translateY(-2px);
								box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
								border-color: #667eea;
							}

							.pagination-link.pagination-active {
								background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
								color: white;
								border-color: #667eea;
								box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
								transform: scale(1.05);
							}

							.pagination-link.pagination-prev,
							.pagination-link.pagination-next {
								gap: 8px;
								font-size: 14px;
								min-width: 120px;
							}

							.pagination-icon {
								width: 18px;
								height: 18px;
								transition: transform 0.3s ease;
							}

							.pagination-link:hover .pagination-icon {
								transform: scale(1.2);
							}

							.pagination-link.pagination-prev:hover .pagination-icon {
								transform: translateX(-2px) scale(1.2);
							}

							.pagination-link.pagination-next:hover .pagination-icon {
								transform: translateX(2px) scale(1.2);
							}

							.pagination-ellipsis {
								display: flex;
								align-items: center;
								justify-content: center;
								min-width: 44px;
								height: 44px;
								padding: 0 16px;
							}

							.pagination-dots {
								color: #6c757d;
								font-size: 18px;
								font-weight: 600;
								letter-spacing: 2px;
							}

							.pagination-quick-jump {
								display: flex;
								align-items: center;
								justify-content: center;
								gap: 15px;
								padding: 20px;
								background: rgba(255, 255, 255, 0.7);
								border-radius: 12px;
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.quick-jump-text {
								font-size: 14px;
								color: #495057;
								font-weight: 500;
							}

							.quick-jump-form {
								display: flex;
								gap: 10px;
								align-items: center;
							}

							.quick-jump-input {
								width: 80px;
								height: 40px;
								padding: 8px 12px;
								border: 2px solid #e9ecef;
								border-radius: 8px;
								font-size: 14px;
								text-align: center;
								transition: all 0.3s ease;
								background: white;
							}

							.quick-jump-input:focus {
								outline: none;
								border-color: #667eea;
								box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
							}

							.quick-jump-button {
								height: 40px;
								padding: 8px 16px;
								background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
								color: white;
								border: none;
								border-radius: 8px;
								font-weight: 600;
								font-size: 14px;
								cursor: pointer;
								transition: all 0.3s ease;
							}

							.quick-jump-button:hover {
								transform: translateY(-2px);
								box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
							}

							/* Responsive Design */
							@media (max-width: 768px) {
								.modern-pagination-wrapper {
									padding: 20px;
									margin: 30px 0;
								}

								.pagination-info {
									flex-direction: column;
									gap: 15px;
									text-align: center;
								}

								.pagination-list {
									gap: 6px;
								}

								.pagination-link {
									min-width: 40px;
									height: 40px;
									font-size: 14px;
								}

								.pagination-link.pagination-prev,
								.pagination-link.pagination-next {
									min-width: 100px;
									font-size: 13px;
								}

								.pagination-quick-jump {
									flex-direction: column;
									gap: 15px;
									text-align: center;
								}

								.quick-jump-form {
									justify-content: center;
								}
							}

							@media (max-width: 480px) {
								.modern-pagination-wrapper {
									padding: 15px;
									margin: 20px 0;
								}

								.pagination-list {
									gap: 4px;
								}

								.pagination-link {
									min-width: 36px;
									height: 36px;
									font-size: 13px;
									padding: 0 12px;
								}

								.pagination-link.pagination-prev,
								.pagination-link.pagination-next {
									min-width: 80px;
									font-size: 12px;
								}

								.pagination-icon {
									width: 16px;
									height: 16px;
								}
							}

							.searchBtn {
								background-color: #083352 !important;
								display: flex !important;
								justify-content: center !important;
								align-items: center !important;
								color: #fff !important;
								border: none !important;
								/* Added to ensure consistent appearance */
								padding: 10px 20px !important;
								/* Added consistent padding */
								cursor: pointer !important;
								/* Added pointer cursor */
								transition: background-color 0.3s ease !important;
								/* Smooth transition */
							}

							.searchBtn:hover {
								background-color: #0a4066 !important;
								/* Slightly lighter on hover */
							}

							.course-menu {
								background-color: #ebebff;
								display: flex;
								justify-content: space-evenly !important;
								align-items: center !important;
								flex-wrap: wrap;
								padding: 15px 0;
								/* Consistent padding top and bottom */
								margin-bottom: 20px;
								/* Added margin below menu */
								border-radius: 6px;
								/* Rounded corners for modern look */
							}

							.course-menu .active {
								background-color: #083352 !important;
								color: #fff !important;
								/* Ensure text is visible on active background */
							}

							.course-menu .tran3s {
								text-transform: uppercase;
								border: 1px solid #083352;
								padding: 8px 15px;
								/* Added consistent padding */
								border-radius: 4px;
								/* Rounded corners */
								margin: 5px;
								/* Added margin for spacing in wrap situations */
								transition: all 0.3s ease;
								/* Renamed from tran3s to be more specific */
								text-decoration: none;
								/* Remove underline from links */
								color: #083352;
								/* Match border color */
							}

							.course-menu .tran3s:hover {
								background-color: #083352 !important;
								color: #fff !important;
							}

							/* Added responsive adjustments */
							@media (max-width: 768px) {
								.course-pagination li a {
									width: 35px;
									height: 35px;
								}

								.course-menu {
									padding: 10px 0;
								}
							}

							/* Mobile-First Search and Filter Styling */
							.mobile-search-section {
								margin-bottom: 30px;
								order: -1; /* Move to top */
							}

							.mobile-search-wrapper {
								background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
								border-radius: 16px;
								padding: 25px;
								box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.mobile-search-form {
								margin-bottom: 25px;
							}

							.search-input-group {
								display: flex;
								gap: 0;
								border-radius: 12px;
								overflow: hidden;
								box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
								background: white;
							}

							.mobile-search-input {
								flex: 1;
								padding: 15px 20px;
								border: none;
								font-size: 16px;
								outline: none;
								background: white;
							}

							.mobile-search-input::placeholder {
								color: #6c757d;
								font-weight: 500;
							}

							.mobile-search-btn {
								padding: 15px 20px;
								background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
								border: none;
								color: white;
								cursor: pointer;
								transition: all 0.3s ease;
								display: flex;
								align-items: center;
								justify-content: center;
								min-width: 60px;
							}

							.mobile-search-btn:hover {
								transform: translateY(-2px);
								box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
							}

							.search-icon {
								width: 20px;
								height: 20px;
								stroke: currentColor;
							}

							.mobile-countries-section {
								border-top: 1px solid rgba(0, 0, 0, 0.1);
								padding-top: 20px;
							}

							.countries-title {
								font-size: 16px;
								font-weight: 600;
								color: #495057;
								margin-bottom: 15px;
								text-align: center;
							}

							.countries-grid {
								display: grid;
								grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
								gap: 12px;
								max-height: 300px;
								overflow-y: auto;
								padding: 10px;
								background: rgba(255, 255, 255, 0.7);
								border-radius: 12px;
								border: 1px solid rgba(255, 255, 255, 0.8);
							}

							.country-item {
								display: flex;
								flex-direction: column;
								align-items: center;
								padding: 12px 8px;
								background: white;
								border-radius: 8px;
								text-decoration: none;
								color: #495057;
								font-weight: 500;
								font-size: 14px;
								transition: all 0.3s ease;
								border: 2px solid transparent;
								box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
								text-align: center;
								min-height: 60px;
								justify-content: center;
							}

							.country-item:hover {
								transform: translateY(-2px);
								box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
								border-color: #667eea;
								color: #667eea;
							}

							.country-item.reset-all {
								background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
								color: white;
								font-weight: 600;
							}

							.country-item.reset-all:hover {
								transform: translateY(-2px);
								box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
							}

							.country-name {
								display: block;
								margin-bottom: 2px;
							}

							.country-reset {
								font-size: 12px;
								opacity: 0.8;
							}

							/* Hide desktop sidebar on mobile */
							.desktop-sidebar {
								display: none;
							}

							/* Show mobile search section on mobile */
							.mobile-search-section {
								display: block;
							}

							/* Desktop Styles */
							@media (min-width: 769px) {
								.mobile-search-section {
									display: none;
								}

								.desktop-sidebar {
									display: block;
								}
							}

							/* Mobile Responsive Adjustments */
							@media (max-width: 480px) {
								.mobile-search-wrapper {
									padding: 20px;
								}

								.mobile-search-input {
									padding: 12px 16px;
									font-size: 15px;
								}

								.mobile-search-btn {
									padding: 12px 16px;
									min-width: 50px;
								}

								.countries-grid {
									grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
									gap: 10px;
									max-height: 250px;
								}

								.country-item {
									padding: 10px 6px;
									font-size: 13px;
									min-height: 50px;
								}

								.countries-title {
									font-size: 15px;
									margin-bottom: 12px;
								}
							}

							/* Ensure proper spacing and layout */
							.featured-course {
								margin-top: 0;
							}

							/* Improve mobile menu spacing */
							@media (max-width: 768px) {
								.course-menu {
									margin-top: 20px;
								}
								
								.scholarshipsContainerDiv {
									margin-top: 20px;
								}
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

						<!-- End of row div -->


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

						<!-- Modern Pagination -->
						<?php if ($total_pages > 1): ?>
							<div class="modern-pagination-wrapper">
								<div class="pagination-info">
									<span class="pagination-text">Showing page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
									<span class="pagination-total">Total: <?php echo $total_records; ?> applications</span>
								</div>
								
								<nav class="modern-pagination" aria-label="Applications pagination">
									<ul class="pagination-list">
										<!-- Previous Button -->
										<?php if ($page > 1): ?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink($page - 1); ?>" class="pagination-link pagination-prev" aria-label="Previous page">
													<svg class="pagination-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
														<path d="M15 18l-6-6 6-6"/>
													</svg>
													<span class="pagination-text">Previous</span>
												</a>
											</li>
										<?php endif; ?>

										<!-- First Page -->
										<?php if ($page > 3): ?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink(1); ?>" class="pagination-link">1</a>
											</li>
											<?php if ($page > 4): ?>
												<li class="pagination-item pagination-ellipsis">
													<span class="pagination-dots">•••</span>
												</li>
											<?php endif; ?>
										<?php endif; ?>

										<!-- Page Numbers -->
										<?php
										$start_page = max(1, $page - 1);
										$end_page = min($total_pages, $page + 1);

										for ($i = $start_page; $i <= $end_page; $i++):
										?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink($i); ?>" 
												   class="pagination-link <?php echo ($i == $page) ? 'pagination-active' : ''; ?>" 
												   aria-current="<?php echo ($i == $page) ? 'page' : 'false'; ?>">
													<?php echo $i; ?>
												</a>
											</li>
										<?php endfor; ?>

										<!-- Last Page -->
										<?php if ($page < $total_pages - 2): ?>
											<?php if ($page < $total_pages - 3): ?>
												<li class="pagination-item pagination-ellipsis">
													<span class="pagination-dots">•••</span>
												</li>
											<?php endif; ?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink($total_pages); ?>" class="pagination-link"><?php echo $total_pages; ?></a>
											</li>
										<?php endif; ?>

										<!-- Next Button -->
										<?php if ($page < $total_pages): ?>
											<li class="pagination-item">
												<a href="<?php echo generatePaginationLink($page + 1); ?>" class="pagination-link pagination-next" aria-label="Next page">
													<span class="pagination-text">Next</span>
													<svg class="pagination-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
														<path d="M9 18l6-6-6-6"/>
													</svg>
												</a>
											</li>
										<?php endif; ?>
									</ul>
								</nav>

								<!-- Quick Jump -->
								<div class="pagination-quick-jump">
									<span class="quick-jump-text">Go to page:</span>
									<form class="quick-jump-form" method="get" onsubmit="return validatePageInput(this);">
										<?php
										// Preserve existing GET parameters
										foreach ($_GET as $key => $value) {
											if ($key !== 'page') {
												echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
											}
										}
										?>
										<input type="number" name="page" min="1" max="<?php echo $total_pages; ?>" 
											   class="quick-jump-input" placeholder="<?php echo $page; ?>" 
											   aria-label="Page number">
										<button type="submit" class="quick-jump-button">Go</button>
									</form>
								</div>
							</div>
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

					<!-- Desktop Sidebar (Hidden on Mobile) -->
					<div class="col-md-3 col-sm-6 col-xs-12 course-sidebar desktop-sidebar">
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

		<!-- Pagination JavaScript -->
		<script>
		// Validate page input for quick jump
		function validatePageInput(form) {
			const input = form.querySelector('input[name="page"]');
			const pageNum = parseInt(input.value);
			const maxPage = parseInt(input.getAttribute('max'));
			const minPage = parseInt(input.getAttribute('min'));
			
			if (isNaN(pageNum) || pageNum < minPage || pageNum > maxPage) {
				alert('Please enter a valid page number between ' + minPage + ' and ' + maxPage);
				input.focus();
				return false;
			}
			
			return true;
		}

		// Eligibility Notice Modal Handler
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

		// Add smooth scrolling to pagination links
		document.addEventListener('DOMContentLoaded', function() {
			const paginationLinks = document.querySelectorAll('.pagination-link');
			
			paginationLinks.forEach(link => {
				link.addEventListener('click', function(e) {
					// Smooth scroll to top of page when navigating
					if (this.href && this.href.includes('page=')) {
						e.preventDefault();
						const href = this.href;
						
						// Smooth scroll to top
						window.scrollTo({
							top: 0,
							behavior: 'smooth'
						});
						
						// Navigate after scroll animation
						setTimeout(() => {
							window.location.href = href;
						}, 500);
					}
				});
			});

			// Add loading state to pagination buttons
			paginationLinks.forEach(link => {
				link.addEventListener('click', function() {
					if (this.href && this.href.includes('page=')) {
						this.style.pointerEvents = 'none';
						this.style.opacity = '0.7';
						
						// Reset after navigation
						setTimeout(() => {
							this.style.pointerEvents = '';
							this.style.opacity = '';
						}, 1000);
					}
				});
			});
		});
		</script>

	</div> <!-- /.main-page-wrapper -->
</body>

</html>