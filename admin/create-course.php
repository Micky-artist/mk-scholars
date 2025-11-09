<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

$message = '';
$messageType = '';

// Get currencies from database
$currenciesQuery = "SELECT * FROM Currencies WHERE isActive = 1 ORDER BY displayOrder, currencyName";
$currenciesResult = mysqli_query($conn, $currenciesQuery);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_course'])) {
        $courseName = mysqli_real_escape_string($conn, $_POST['courseName']);
        $courseShortDescription = mysqli_real_escape_string($conn, $_POST['courseShortDescription']);
        $courseLongDescription = mysqli_real_escape_string($conn, $_POST['courseLongDescription']);
        $courseStartDate = $_POST['courseStartDate'];
        $courseRegEndDate = !empty($_POST['courseRegEndDate']) ? $_POST['courseRegEndDate'] : null;
        $courseEndDate = $_POST['courseEndDate'];
        $courseSeats = (int)$_POST['courseSeats'];
        $courseDisplayStatus = (int)$_POST['courseDisplayStatus'];
        $coursePaymentCodeName = mysqli_real_escape_string($conn, $_POST['coursePaymentCodeName']);
        
        // Handle course photo upload
        $coursePhoto = '';
        if (isset($_FILES['coursePhoto']) && $_FILES['coursePhoto']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = './uploads/courses/images/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Validate file type
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileExtension = strtolower(pathinfo($_FILES['coursePhoto']['name'], PATHINFO_EXTENSION));
            
            if (in_array($fileExtension, $allowedTypes)) {
                $fileName = 'course_' . time() . '_' . uniqid() . '.' . $fileExtension;
                $uploadPath = $uploadDir . $fileName;
                
                // Validate file is readable and not empty
                if (is_readable($_FILES['coursePhoto']['tmp_name']) && $_FILES['coursePhoto']['size'] > 0) {
                    if (move_uploaded_file($_FILES['coursePhoto']['tmp_name'], $uploadPath)) {
                        // Verify file was moved correctly
                        if (file_exists($uploadPath) && filesize($uploadPath) === $_FILES['coursePhoto']['size']) {
                            chmod($uploadPath, 0644);
                            $coursePhoto = 'uploads/courses/images/' . $fileName;
                        } else {
                            $message = 'Error: File upload verification failed.';
                            $messageType = 'error';
                        }
                    } else {
                        $message = 'Error uploading course image. Check directory permissions.';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'Error: Uploaded file is not readable or empty.';
                    $messageType = 'error';
                }
            } else {
                $message = 'Invalid file type. Please upload JPG, PNG, GIF, or WebP images only.';
                $messageType = 'error';
            }
        }
        
        // Duplicate guard: prevent quick duplicate courses by name (last 24h)
        $dupCheckSql = "SELECT courseId FROM Courses WHERE courseName = '$courseName' AND courseCreatedDate >= (NOW() - INTERVAL 1 DAY) LIMIT 1";
        $dupRes = mysqli_query($conn, $dupCheckSql);
        if ($dupRes && mysqli_num_rows($dupRes) > 0) {
            $existing = mysqli_fetch_assoc($dupRes);
            $message = 'A course with this name was recently created. Redirecting to edit.';
            $messageType = 'warning';
            header('Location: edit-course.php?id=' . (int)$existing['courseId']);
            exit;
        }

        // Insert course (coursePaymentCodeName belongs to CoursePricing, not Courses)
        $insertQuery = "INSERT INTO Courses (courseName, courseShortDescription, courseLongDescription, courseStartDate, courseRegEndDate, courseEndDate, courseSeats, coursePhoto, courseDisplayStatus, courseCreatedBy) VALUES ('$courseName', '$courseShortDescription', '$courseLongDescription', '$courseStartDate', " . ($courseRegEndDate ? "'$courseRegEndDate'" : "NULL") . ", '$courseEndDate', $courseSeats, '$coursePhoto', $courseDisplayStatus, " . $_SESSION['adminId'] . ")";
        
        if (mysqli_query($conn, $insertQuery)) {
            $courseId = mysqli_insert_id($conn);
            
            // Insert course tags
            if (isset($_POST['tags']) && is_array($_POST['tags'])) {
                foreach ($_POST['tags'] as $tag) {
                    $tagDescription = mysqli_real_escape_string($conn, $tag['description']);
                    $tagIcon = mysqli_real_escape_string($conn, $tag['icon']);
                    $tagColor = mysqli_real_escape_string($conn, $tag['color']);
                    
                    $tagQuery = "INSERT INTO CourseTags (courseId, tagDescription, courseTagIcon, tagColor) VALUES ($courseId, '$tagDescription', '$tagIcon', '$tagColor')";
                    mysqli_query($conn, $tagQuery);
                }
            }
            
            // Insert pricing options
            if (isset($_POST['pricing_options']) && is_array($_POST['pricing_options'])) {
                foreach ($_POST['pricing_options'] as $pricing) {
                    $amount = (float)$pricing['amount'];
                    $pricingDescription = mysqli_real_escape_string($conn, $pricing['description']);
                    $currency = mysqli_real_escape_string($conn, $pricing['currency']);
                    $discountAmount = (float)$pricing['discountAmount'];
                    $discountStartDate = $pricing['discountStartDate'] ?: null;
                    $discountEndDate = $pricing['discountEndDate'] ?: null;
                    $isFree = isset($pricing['isFree']) ? 1 : 0;
                    // Generate meaningful payment code (COURSECODE-PLAN-XXXX)
                    $baseCode = strtoupper(preg_replace('/[^A-Z0-9]+/','', substr($courseName,0,10)));
                    $planCode = strtoupper(preg_replace('/[^A-Z0-9]+/','', substr($pricingDescription ?: 'PLAN',0,8)));
                    $rand = substr(strtoupper(bin2hex(random_bytes(2))), 0, 4);
                    $paymentCode = $baseCode . '-' . $planCode . '-' . $rand;
                    
                    $pricingQuery = "INSERT INTO CoursePricing (courseId, amount, pricingDescription, currency, discountAmount, discountStartDate, discountEndDate, isFree, coursePaymentCodeName) VALUES ($courseId, $amount, '$pricingDescription', '$currency', $discountAmount, " . ($discountStartDate ? "'$discountStartDate'" : 'NULL') . ", " . ($discountEndDate ? "'$discountEndDate'" : 'NULL') . ", $isFree, '" . $paymentCode . "')";
                    mysqli_query($conn, $pricingQuery);
                }
            } else {
                // Insert default pricing if no pricing options provided
                $amount = (float)$_POST['amount'];
                $pricingDescription = mysqli_real_escape_string($conn, $_POST['pricingDescription']);
                $currency = $_POST['currency'];
                $discountAmount = (float)$_POST['discountAmount'];
                $discountStartDate = $_POST['discountStartDate'] ?: null;
                $discountEndDate = $_POST['discountEndDate'] ?: null;
                $isFree = isset($_POST['isFree']) ? 1 : 0;
                // Generate meaningful payment code
                $baseCode = strtoupper(preg_replace('/[^A-Z0-9]+/','', substr($courseName,0,10)));
                $planCode = strtoupper(preg_replace('/[^A-Z0-9]+/','', substr($pricingDescription ?: 'PLAN',0,8)));
                $rand = substr(strtoupper(bin2hex(random_bytes(2))), 0, 4);
                $paymentCode = $baseCode . '-' . $planCode . '-' . $rand;
                $pricingQuery = "INSERT INTO CoursePricing (courseId, amount, pricingDescription, currency, discountAmount, discountStartDate, discountEndDate, isFree, coursePaymentCodeName) VALUES ($courseId, $amount, '$pricingDescription', '$currency', $discountAmount, " . ($discountStartDate ? "'$discountStartDate'" : 'NULL') . ", " . ($discountEndDate ? "'$discountEndDate'" : 'NULL') . ", $isFree, '" . $paymentCode . "')";
                mysqli_query($conn, $pricingQuery);
            }
            
            $message = 'Course created successfully!';
            $messageType = 'success';
            
            // Redirect to course editor
            header("Location: course-editor.php?id=$courseId");
            exit;
        } else {
            $message = 'Error creating course: ' . mysqli_error($conn);
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include("./partials/head.php"); ?>

<style>
    .form-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 2rem 0;
    }

    .form-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #e9ecef;
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .section-title {
        color: #495057;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
    }

    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        margin-top: 1rem;
    }

    .upload-area {
        border: 2px dashed #007bff;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        background: #f8f9ff;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .upload-area:hover {
        background: #e3f2fd;
        border-color: #0056b3;
    }

    .upload-area.dragover {
        background: #e3f2fd;
        border-color: #0056b3;
        transform: scale(1.02);
    }

    .required-star { color: #dc3545; }

    .price-input-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .currency-select {
        width: 120px;
    }
    
    .currency-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .currency-symbol {
        font-weight: 600;
        color: var(--primary-color);
        min-width: 20px;
    }

    .amount-input {
        flex: 1;
    }

    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .status-open { background: #d4edda; color: #155724; }
    .status-closed { background: #f8d7da; color: #721c24; }
    .status-inactive { background: #e2e3e5; color: #383d41; }

    /* Tag Icon Selector */
    .tag-icon-preview {
        display: inline-block;
        margin-left: 0.5rem;
        font-size: 1.2rem;
        color: #0E77C2;
    }

    .tag-icon-selector {
        position: relative;
    }

    .tag-icon-selector select {
        padding-right: 2.5rem;
    }

    .tag-icon-selector::after {
        content: '\f078';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        color: #6c757d;
    }

    /* Course Tags Styles */
    .tag-item {
        display: inline-flex;
        align-items: center;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 20px;
        padding: 0.375rem 0.75rem;
        margin: 0.25rem;
        font-size: 0.875rem;
    }

    .tag-item .tag-icon {
        margin-right: 0.5rem;
    }

    .tag-item .tag-remove {
        margin-left: 0.5rem;
        cursor: pointer;
        color: #dc3545;
    }

    .tag-item .tag-remove:hover {
        color: #a71e2a;
    }

    /* Pricing Options Styles */
    .pricing-option {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .pricing-option-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .pricing-option-title {
        font-weight: 600;
        color: #495057;
        margin: 0;
    }

    .pricing-option-remove {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: none;
        border: none;
        color: #dc3545;
        cursor: pointer;
        font-size: 1.2rem;
    }

    .pricing-option-remove:hover {
        color: #a71e2a;
    }

    .price-input-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .currency-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .currency-symbol {
        font-weight: 600;
        color: #495057;
        min-width: 3rem;
    }

    .currency-select {
        min-width: 120px;
    }

    .amount-input {
        flex: 1;
    }
</style>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <?php include("./partials/header.php"); ?>
        <?php include("./partials/navbar.php"); ?>
        
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Create New Course</h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="./home">Home</a></li>
                                    <li class="breadcrumb-item"><a href="./course-management">Course Management</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Create Course</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid form-container">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" id="courseForm">
                    <input type="hidden" name="create_course" value="1">
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Basic Information -->
                            <div class="form-card">
                                <h5 class="section-title">Basic Information</h5>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="courseName" class="form-label">Course Name *</label>
                                            <input type="text" class="form-control" id="courseName" name="courseName" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseDisplayStatus" class="form-label">Status *</label>
                                            <select class="form-control" id="courseDisplayStatus" name="courseDisplayStatus" required>
                                                <option value="0">Not Active</option>
                                                <option value="1" selected>Open</option>
                                                <option value="2">Closed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="courseShortDescription" class="form-label">Short Description *</label>
                                    <textarea class="form-control" id="courseShortDescription" name="courseShortDescription" rows="3" required placeholder="Brief description of the course"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="courseLongDescription" class="form-label">Detailed Description *</label>
                                    <textarea class="form-control" id="courseLongDescription" name="courseLongDescription" rows="6" required placeholder="Comprehensive description of the course content, objectives, and what students will learn"></textarea>
                                </div>
                            </div>

                            <!-- Course Schedule -->
                            <div class="form-card">
                                <h5 class="section-title">Course Schedule</h5>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseRegEndDate" class="form-label">Registration End Date (optional)</label>
                                            <input type="date" class="form-control" id="courseRegEndDate" name="courseRegEndDate">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseStartDate" class="form-label">Course Start Date <span class="required-star">*</span></label>
                                            <input type="date" class="form-control" id="courseStartDate" name="courseStartDate" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseEndDate" class="form-label">Course End Date <span class="required-star">*</span></label>
                                            <input type="date" class="form-control" id="courseEndDate" name="courseEndDate" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="courseSeats" class="form-label">Available Seats *</label>
                                            <input type="number" class="form-control" id="courseSeats" name="courseSeats" min="1" required>
                                        </div>
                                    </div>
                                    <!-- Removed Payment Code Name field; codes are generated per pricing option -->
                                </div>
                            </div>

                            <!-- Course Tags -->
                            <div class="form-card">
                                <h5 class="section-title">Course Tags</h5>
                                <p class="text-muted">Add tags to help categorize and organize your course.</p>
                                
                                <div class="mb-3">
                                    <label for="tagDescription" class="form-label">Tag Description</label>
                                    <input type="text" class="form-control" id="tagDescription" placeholder="e.g., Programming, Beginner, Online">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tagIcon" class="form-label">Tag Icon</label>
                                    <div class="tag-icon-selector">
                                        <select class="form-control" id="tagIcon" onchange="updateTagIconPreview()">
                                            <option value="fas fa-tag">General Tag</option>
                                            <option value="fas fa-code">Programming</option>
                                            <option value="fas fa-graduation-cap">Education</option>
                                            <option value="fas fa-book">Study</option>
                                            <option value="fas fa-laptop">Online</option>
                                            <option value="fas fa-users">Group</option>
                                            <option value="fas fa-clock">Time</option>
                                            <option value="fas fa-star">Featured</option>
                                            <option value="fas fa-trophy">Achievement</option>
                                            <option value="fas fa-certificate">Certificate</option>
                                            <option value="fas fa-chart-line">Analytics</option>
                                            <option value="fas fa-lightbulb">Idea</option>
                                            <option value="fas fa-rocket">Launch</option>
                                            <option value="fas fa-fire">Popular</option>
                                            <option value="fas fa-heart">Favorite</option>
                                            <option value="fas fa-gem">Premium</option>
                                            <option value="fas fa-shield-alt">Security</option>
                                            <option value="fas fa-mobile-alt">Mobile</option>
                                            <option value="fas fa-globe">Global</option>
                                            <option value="fas fa-puzzle-piece">Puzzle</option>
                                            <option value="fas fa-cogs">Technical</option>
                                            <option value="fas fa-paint-brush">Creative</option>
                                            <option value="fas fa-brain">Intelligence</option>
                                            <option value="fas fa-handshake">Partnership</option>
                                            <option value="fas fa-leaf">Eco-friendly</option>
                                        </select>
                                        <span id="tagIconPreview" class="tag-icon-preview">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="tagColor" class="form-label">Tag Color</label>
                                    <input type="color" class="form-control" id="tagColor" value="#007bff">
                                </div>
                                
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addTag()">
                                    <i class="fas fa-plus me-1"></i>Add Tag
                                </button>
                                
                                <div id="tagsList" class="mt-3">
                                    <!-- Tags will be added here dynamically -->
                                </div>
                            </div>

                            <!-- Course Content -->
                            <div class="form-card">
                                <h5 class="section-title">Course Content</h5>
                                <p class="text-muted">After creating the course, you'll be redirected to the course editor to add sections, lessons, and content.</p>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Course Image -->
                            <div class="form-card">
                                <h5 class="section-title">Course Image</h5>
                                
                                <div class="upload-area" id="imageUploadArea">
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                    <h6>Upload Course Image</h6>
                                    <p class="text-muted mb-0">Click or drag image here</p>
                                    <input type="file" id="coursePhoto" name="coursePhoto" accept="image/*" style="display: none;">
                                </div>
                                
                                <div id="imagePreview" style="display: none;">
                                    <img id="previewImg" class="image-preview" alt="Course preview">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">Remove Image</button>
                                </div>
                            </div>

                            <!-- Pricing Information -->
                            <div class="form-card">
                                <h5 class="section-title">Pricing Options</h5>
                                <p class="text-muted">Add multiple pricing tiers for your course.</p>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isFree" name="isFree">
                                        <label class="form-check-label" for="isFree">
                                            Free Course
                                        </label>
                                    </div>
                                </div>

                                <div id="pricingFields">
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addPricingOption()">
                                            <i class="fas fa-plus me-1"></i>Add Pricing Option
                                        </button>
                                    </div>
                                    
                                    <div id="pricingOptionsList">
                                        <!-- Pricing options will be added here dynamically -->
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="form-card">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Create Course
                                    </button>
                                    <a href="course-management.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Courses
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
    <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
    <script src="./dist/js/waves.js"></script>
    <script src="./dist/js/sidebarmenu.js"></script>
    <script src="./dist/js/custom.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <script>
        // Image upload functionality
        document.addEventListener('DOMContentLoaded', function() {
            const imageUploadArea = document.getElementById('imageUploadArea');
            const coursePhoto = document.getElementById('coursePhoto');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');

            if (imageUploadArea && coursePhoto) {
                imageUploadArea.addEventListener('click', () => coursePhoto.click());
                imageUploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    imageUploadArea.classList.add('dragover');
                });
                imageUploadArea.addEventListener('dragleave', () => {
                    imageUploadArea.classList.remove('dragover');
                });
                imageUploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    imageUploadArea.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        coursePhoto.files = files;
                        handleImagePreview(files[0]);
                    }
                });

                coursePhoto.addEventListener('change', (e) => {
                    if (e.target.files.length > 0) {
                        handleImagePreview(e.target.files[0]);
                    }
                });
            }
        });

        function handleImagePreview(file) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewImg = document.getElementById('previewImg');
                    const imageUploadArea = document.getElementById('imageUploadArea');
                    const imagePreview = document.getElementById('imagePreview');
                    
                    if (previewImg) previewImg.src = e.target.result;
                    if (imageUploadArea) imageUploadArea.style.display = 'none';
                    if (imagePreview) imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        window.removeImage = function() {
            const coursePhoto = document.getElementById('coursePhoto');
            const imageUploadArea = document.getElementById('imageUploadArea');
            const imagePreview = document.getElementById('imagePreview');
            
            if (coursePhoto) coursePhoto.value = '';
            if (imageUploadArea) imageUploadArea.style.display = 'block';
            if (imagePreview) imagePreview.style.display = 'none';
        }

        // Free course toggle
        document.addEventListener('DOMContentLoaded', function() {
            const isFreeCheckbox = document.getElementById('isFree');
            const pricingFields = document.getElementById('pricingFields');

            if (isFreeCheckbox && pricingFields) {
                isFreeCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        pricingFields.style.display = 'none';
                        const amountField = document.getElementById('amount');
                        if (amountField) amountField.value = '0';
                    } else {
                        pricingFields.style.display = 'block';
                    }
                });
            }
        });

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const courseForm = document.getElementById('courseForm');
            if (courseForm) {
                courseForm.addEventListener('submit', function(e) {
                    const startDate = new Date(document.getElementById('courseStartDate').value);
                    const regEndDate = new Date(document.getElementById('courseRegEndDate').value);
                    const endDate = new Date(document.getElementById('courseEndDate').value);
                    const isFreeCheckbox = document.getElementById('isFree');

                    if (document.getElementById('courseRegEndDate').value && regEndDate >= startDate) {
                        alert('Registration end date must be before course start date');
                        e.preventDefault();
                        return;
                    }

                    if (endDate <= startDate) {
                        alert('Course end date must be after course start date');
                        e.preventDefault();
                        return;
                    }

                    if (!isFreeCheckbox.checked && parseFloat(document.getElementById('amount').value) < 0) {
                        alert('Price cannot be negative');
                        e.preventDefault();
                        return;
                    }
                });
            }
        });

        // Auto-hide alerts
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Handle currency selection
        document.addEventListener('DOMContentLoaded', function() {
            const currencySelect = document.getElementById('currency');
            if (currencySelect) {
                currencySelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const symbol = selectedOption.getAttribute('data-symbol');
                    const currencySymbol = document.getElementById('currencySymbol');
                    if (currencySymbol) {
                        currencySymbol.textContent = symbol || selectedOption.value;
                    }
                });

                // Initialize currency symbol on page load
                const selectedOption = currencySelect.options[currencySelect.selectedIndex];
                const symbol = selectedOption.getAttribute('data-symbol');
                const currencySymbol = document.getElementById('currencySymbol');
                if (currencySymbol) {
                    currencySymbol.textContent = symbol || selectedOption.value;
                }
            }
        });

        // Course Tags functionality
        let tags = [];
        let pricingOptions = [];

        // Update tag icon preview
        function updateTagIconPreview() {
            const select = document.getElementById('tagIcon');
            const preview = document.getElementById('tagIconPreview');
            if (select && preview) {
                const selectedIcon = select.value;
                preview.innerHTML = `<i class="${selectedIcon}"></i>`;
            }
        }

        // Multiple Pricing Options functionality - Define functions globally
        window.addPricingOption = function() {
            console.log('addPricingOption called');
            const pricingOption = {
                description: '',
                amount: 0,
                currency: 'RWF',
                discountAmount: 0,
                discountStartDate: '',
                discountEndDate: '',
                isFree: false
            };

            pricingOptions.push(pricingOption);
            console.log('pricingOptions after push:', pricingOptions);
            renderPricingOptions();
        }

        window.removePricingOption = function(index) {
            pricingOptions.splice(index, 1);
            renderPricingOptions();
        }

        function renderPricingOptions() {
            console.log('renderPricingOptions called with pricingOptions:', pricingOptions);
            const pricingList = document.getElementById('pricingOptionsList');
            if (!pricingList) {
                console.error('pricingOptionsList element not found');
                return;
            }
            pricingList.innerHTML = '';

            pricingOptions.forEach((option, index) => {
                const optionElement = document.createElement('div');
                optionElement.className = 'pricing-option';
                optionElement.innerHTML = `
                    <button type="button" class="pricing-option-remove" onclick="removePricingOption(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                    <h6 class="pricing-option-title">Pricing Option ${index + 1}</h6>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control pricing-description" 
                               value="${option.description}" placeholder="e.g., Basic Plan, Premium Plan"
                               onchange="updatePricingOption(${index}, 'description', this.value)">
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   ${option.isFree ? 'checked' : ''} onchange="togglePricingFields(${index}); updatePricingOption(${index}, 'isFree', this.checked)">
                            <label class="form-check-label">Free Option</label>
                        </div>
                    </div>
                    
                    <div class="pricing-fields-${index}" style="${option.isFree ? 'display: none;' : ''}">
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <div class="price-input-group">
                                <div class="currency-display">
                                    <span class="currency-symbol" id="currencySymbol${index}">RWF</span>
                                    <select class="form-control currency-select" 
                                            onchange="updateCurrencySymbol(${index}, this.value); updatePricingOption(${index}, 'currency', this.value)">
                                        <option value="RWF" data-symbol="RWF" ${option.currency === 'RWF' ? 'selected' : ''}>RWF - Rwandan Franc</option>
                                        <option value="USD" data-symbol="$" ${option.currency === 'USD' ? 'selected' : ''}>USD - US Dollar</option>
                                        <option value="EUR" data-symbol="€" ${option.currency === 'EUR' ? 'selected' : ''}>EUR - Euro</option>
                                    </select>
                                </div>
                                <input type="number" class="form-control amount-input" 
                                       value="${option.amount}" step="0.01" min="0"
                                       onchange="updatePricingOption(${index}, 'amount', this.value)">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Amount</label>
                                    <input type="number" class="form-control" 
                                           value="${option.discountAmount}" step="0.01" min="0"
                                           onchange="updatePricingOption(${index}, 'discountAmount', this.value)">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="form-label">Discount Start</label>
                                    <input type="date" class="form-control" 
                                           value="${option.discountStartDate}"
                                           onchange="updatePricingOption(${index}, 'discountStartDate', this.value)">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Discount End</label>
                            <input type="date" class="form-control" 
                                   value="${option.discountEndDate}"
                                   onchange="updatePricingOption(${index}, 'discountEndDate', this.value)">
                        </div>
                    </div>
                `;
                pricingList.appendChild(optionElement);
                
                // Initialize currency symbol
                updateCurrencySymbol(index, option.currency);
            });
        }

        // Update pricing option data
        window.updatePricingOption = function(index, field, value) {
            if (pricingOptions[index]) {
                if (field === 'isFree') {
                    pricingOptions[index][field] = value;
                } else {
                    pricingOptions[index][field] = value;
                }
            }
        }

        window.togglePricingFields = function(index) {
            const fields = document.querySelector(`.pricing-fields-${index}`);
            const checkbox = document.querySelector(`input[onchange*="togglePricingFields(${index})"]`);
            
            if (checkbox && checkbox.checked) {
                if (fields) fields.style.display = 'none';
                const amountInput = document.querySelector(`.pricing-fields-${index} .amount-input`);
                if (amountInput) amountInput.value = '0';
            } else {
                if (fields) fields.style.display = 'block';
            }
        }

        window.updateCurrencySymbol = function(index, currency) {
            const select = document.querySelector(`.pricing-fields-${index} .currency-select`);
            if (select) {
                const selectedOption = select.options[select.selectedIndex];
                const symbol = selectedOption.getAttribute('data-symbol');
                const symbolElement = document.getElementById(`currencySymbol${index}`);
                if (symbolElement) {
                    symbolElement.textContent = symbol || currency;
                }
            }
        }

        function addTag() {
            const description = document.getElementById('tagDescription').value.trim();
            const icon = document.getElementById('tagIcon').value.trim();
            const color = document.getElementById('tagColor').value;

            if (!description) {
                alert('Please enter a tag description');
                return;
            }

            const tag = {
                description: description,
                icon: icon || 'fas fa-tag',
                color: color
            };

            tags.push(tag);
            renderTags();
            
            // Clear inputs
            document.getElementById('tagDescription').value = '';
            document.getElementById('tagIcon').value = '';
            document.getElementById('tagColor').value = '#007bff';
        }

        function removeTag(index) {
            tags.splice(index, 1);
            renderTags();
        }

        function renderTags() {
            const tagsList = document.getElementById('tagsList');
            tagsList.innerHTML = '';

            tags.forEach((tag, index) => {
                const tagElement = document.createElement('div');
                tagElement.className = 'tag-item';
                tagElement.style.borderColor = tag.color;
                tagElement.innerHTML = `
                    <i class="${tag.icon} tag-icon" style="color: ${tag.color}"></i>
                    <span>${tag.description}</span>
                    <i class="fas fa-times tag-remove" onclick="removeTag(${index})"></i>
                `;
                tagsList.appendChild(tagElement);
            });
        }


        // Form submission - add hidden inputs for tags and pricing options
        document.addEventListener('DOMContentLoaded', function() {
            const courseForm = document.getElementById('courseForm');
            if (courseForm) {
                courseForm.addEventListener('submit', function(e) {
                    // Add hidden inputs for tags
                    tags.forEach((tag, index) => {
                        const hiddenInputs = `
                            <input type="hidden" name="tags[${index}][description]" value="${tag.description}">
                            <input type="hidden" name="tags[${index}][icon]" value="${tag.icon}">
                            <input type="hidden" name="tags[${index}][color]" value="${tag.color}">
                        `;
                        this.insertAdjacentHTML('beforeend', hiddenInputs);
                    });
                    
                    // Add hidden inputs for pricing options
                    pricingOptions.forEach((option, index) => {
                        const hiddenInputs = `
                            <input type="hidden" name="pricing_options[${index}][description]" value="${option.description}">
                            <input type="hidden" name="pricing_options[${index}][amount]" value="${option.amount}">
                            <input type="hidden" name="pricing_options[${index}][currency]" value="${option.currency}">
                            <input type="hidden" name="pricing_options[${index}][discountAmount]" value="${option.discountAmount}">
                            <input type="hidden" name="pricing_options[${index}][discountStartDate]" value="${option.discountStartDate}">
                            <input type="hidden" name="pricing_options[${index}][discountEndDate]" value="${option.discountEndDate}">
                            <input type="hidden" name="pricing_options[${index}][isFree]" value="${option.isFree ? '1' : '0'}">
                        `;
                        this.insertAdjacentHTML('beforeend', hiddenInputs);
                    });
                });
            }
        });

        // Initialize page on load
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing pricing options...');
            console.log('pricingOptions array:', pricingOptions);
            console.log('addPricingOption function:', typeof addPricingOption);
            addPricingOption();
            updateTagIconPreview();
        });

    </script>
</body>
</html>
