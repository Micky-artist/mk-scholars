<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

$courseId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$message = '';
$messageType = '';

// Get currencies from database
$currenciesQuery = "SELECT * FROM Currencies WHERE isActive = 1 ORDER BY displayOrder, currencyName";
$currenciesResult = mysqli_query($conn, $currenciesQuery);

// Get course data
$courseQuery = "SELECT c.*, cp.* FROM Courses c 
                LEFT JOIN CoursePricing cp ON c.courseId = cp.courseId 
                WHERE c.courseId = $courseId";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);

if (!$course) {
    header("Location: course-management.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_course'])) {
        $courseName = mysqli_real_escape_string($conn, $_POST['courseName']);
        $courseShortDescription = mysqli_real_escape_string($conn, $_POST['courseShortDescription']);
        $courseLongDescription = mysqli_real_escape_string($conn, $_POST['courseLongDescription']);
        $courseStartDate = $_POST['courseStartDate'];
        $courseRegEndDate = $_POST['courseRegEndDate'];
        $courseEndDate = $_POST['courseEndDate'];
        $courseSeats = (int)$_POST['courseSeats'];
        $courseDisplayStatus = (int)$_POST['courseDisplayStatus'];
        $coursePaymentCodeName = mysqli_real_escape_string($conn, $_POST['coursePaymentCodeName']);
        
        // Handle course photo upload
        $coursePhoto = $course['coursePhoto']; // Keep existing photo
        if (isset($_FILES['coursePhoto']) && $_FILES['coursePhoto']['error'] === UPLOAD_ERR_OK) {
            // Delete old photo if exists
            if ($coursePhoto && file_exists('./' . $coursePhoto)) {
                unlink('./' . $coursePhoto);
            }
            
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
                
                // Debug information
                error_log("Upload attempt - File: " . $_FILES['coursePhoto']['name']);
                error_log("Upload path: " . $uploadPath);
                error_log("Directory exists: " . (file_exists($uploadDir) ? 'Yes' : 'No'));
                error_log("Directory writable: " . (is_writable($uploadDir) ? 'Yes' : 'No'));
                error_log("File size: " . $_FILES['coursePhoto']['size']);
                error_log("File readable: " . (is_readable($_FILES['coursePhoto']['tmp_name']) ? 'Yes' : 'No'));
                
                // Validate file is readable and not empty
                if (is_readable($_FILES['coursePhoto']['tmp_name']) && $_FILES['coursePhoto']['size'] > 0) {
                    if (move_uploaded_file($_FILES['coursePhoto']['tmp_name'], $uploadPath)) {
                        // Verify file was moved correctly
                        if (file_exists($uploadPath) && filesize($uploadPath) === $_FILES['coursePhoto']['size']) {
                            chmod($uploadPath, 0644);
                            $coursePhoto = 'uploads/courses/images/' . $fileName;
                            error_log("File uploaded successfully: " . $uploadPath);
                        } else {
                            $message = 'Error: File upload verification failed. File exists: ' . (file_exists($uploadPath) ? 'Yes' : 'No') . ', Size match: ' . (filesize($uploadPath) === $_FILES['coursePhoto']['size'] ? 'Yes' : 'No');
                            $messageType = 'error';
                            error_log("File verification failed - Path: $uploadPath, Expected size: " . $_FILES['coursePhoto']['size'] . ", Actual size: " . filesize($uploadPath));
                        }
                    } else {
                        $message = 'Error uploading course image. Check directory permissions. Upload error: ' . $_FILES['coursePhoto']['error'];
                        $messageType = 'error';
                        error_log("Move uploaded file failed - Error: " . $_FILES['coursePhoto']['error']);
                    }
                } else {
                    $message = 'Error: Uploaded file is not readable or empty. Size: ' . $_FILES['coursePhoto']['size'] . ', Readable: ' . (is_readable($_FILES['coursePhoto']['tmp_name']) ? 'Yes' : 'No');
                    $messageType = 'error';
                    error_log("File validation failed - Size: " . $_FILES['coursePhoto']['size'] . ", Readable: " . (is_readable($_FILES['coursePhoto']['tmp_name']) ? 'Yes' : 'No'));
                }
            } else {
                $message = 'Invalid file type. Please upload JPG, PNG, GIF, or WebP images only. File type: ' . $fileExtension;
                $messageType = 'error';
                error_log("Invalid file type: " . $fileExtension);
            }
        }
        
        // Update course
        $updateQuery = "UPDATE Courses SET 
                        courseName = '$courseName',
                        courseShortDescription = '$courseShortDescription',
                        courseLongDescription = '$courseLongDescription',
                        courseStartDate = '$courseStartDate',
                        courseRegEndDate = '$courseRegEndDate',
                        courseEndDate = '$courseEndDate',
                        courseSeats = $courseSeats,
                        coursePhoto = '$coursePhoto',
                        courseDisplayStatus = $courseDisplayStatus,
                        coursePaymentCodeName = '$coursePaymentCodeName'
                        WHERE courseId = $courseId";
        
        if (mysqli_query($conn, $updateQuery)) {
            // Update or insert pricing
            $amount = (float)$_POST['amount'];
            $pricingDescription = mysqli_real_escape_string($conn, $_POST['pricingDescription']);
            $currency = $_POST['currency'];
            $discountAmount = (float)$_POST['discountAmount'];
            $discountStartDate = $_POST['discountStartDate'] ?: null;
            $discountEndDate = $_POST['discountEndDate'] ?: null;
            $isFree = isset($_POST['isFree']) ? 1 : 0;
            
            if ($course['coursePricingId']) {
                // Update existing pricing
                $pricingQuery = "UPDATE CoursePricing SET 
                                amount = $amount,
                                pricingDescription = '$pricingDescription',
                                currency = '$currency',
                                discountAmount = $discountAmount,
                                discountStartDate = " . ($discountStartDate ? "'$discountStartDate'" : 'NULL') . ",
                                discountEndDate = " . ($discountEndDate ? "'$discountEndDate'" : 'NULL') . ",
                                isFree = $isFree
                                WHERE coursePricingId = " . $course['coursePricingId'];
            } else {
                // Insert new pricing
                $pricingQuery = "INSERT INTO CoursePricing (courseId, amount, pricingDescription, currency, discountAmount, discountStartDate, discountEndDate, isFree) VALUES ($courseId, $amount, '$pricingDescription', '$currency', $discountAmount, " . ($discountStartDate ? "'$discountStartDate'" : 'NULL') . ", " . ($discountEndDate ? "'$discountEndDate'" : 'NULL') . ", $isFree)";
            }
            
            if (mysqli_query($conn, $pricingQuery)) {
                $message = 'Course updated successfully!';
                $messageType = 'success';
                
                // Refresh course data
                $courseResult = mysqli_query($conn, $courseQuery);
                $course = mysqli_fetch_assoc($courseResult);
            } else {
                $message = 'Course updated but pricing failed: ' . mysqli_error($conn);
                $messageType = 'error';
            }
        } else {
            $message = 'Error updating course: ' . mysqli_error($conn);
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

    .course-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
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
                        <h4 class="page-title">Edit Course - <?php echo htmlspecialchars($course['courseName']); ?></h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="./home">Home</a></li>
                                    <li class="breadcrumb-item"><a href="./course-management">Course Management</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit Course</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid form-container">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <strong><?php echo $messageType === 'error' ? 'Error:' : 'Success:'; ?></strong> <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_FILES['coursePhoto']) && $_FILES['coursePhoto']['error'] !== UPLOAD_ERR_OK): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <strong>Upload Error:</strong> 
                        <?php
                        $errorMessages = [
                            UPLOAD_ERR_INI_SIZE => 'File too large (server limit)',
                            UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
                            UPLOAD_ERR_PARTIAL => 'File partially uploaded',
                            UPLOAD_ERR_NO_FILE => 'No file uploaded',
                            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                            UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
                            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
                        ];
                        echo $errorMessages[$_FILES['coursePhoto']['error']] ?? 'Unknown upload error';
                        ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" id="courseForm">
                    <input type="hidden" name="update_course" value="1">
                    
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Basic Information -->
                            <div class="form-card">
                                <h5 class="section-title">Basic Information</h5>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="courseName" class="form-label">Course Name *</label>
                                            <input type="text" class="form-control" id="courseName" name="courseName" value="<?php echo htmlspecialchars($course['courseName']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseDisplayStatus" class="form-label">Status *</label>
                                            <select class="form-control" id="courseDisplayStatus" name="courseDisplayStatus" required>
                                                <option value="0" <?php echo $course['courseDisplayStatus'] == 0 ? 'selected' : ''; ?>>Not Active</option>
                                                <option value="1" <?php echo $course['courseDisplayStatus'] == 1 ? 'selected' : ''; ?>>Open</option>
                                                <option value="2" <?php echo $course['courseDisplayStatus'] == 2 ? 'selected' : ''; ?>>Closed</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="courseShortDescription" class="form-label">Short Description *</label>
                                    <textarea class="form-control" id="courseShortDescription" name="courseShortDescription" rows="3" required><?php echo htmlspecialchars($course['courseShortDescription']); ?></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="courseLongDescription" class="form-label">Detailed Description *</label>
                                    <textarea class="form-control" id="courseLongDescription" name="courseLongDescription" rows="6" required><?php echo htmlspecialchars($course['courseLongDescription']); ?></textarea>
                                </div>
                            </div>

                            <!-- Course Schedule -->
                            <div class="form-card">
                                <h5 class="section-title">Course Schedule</h5>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseStartDate" class="form-label">Start Date *</label>
                                            <input type="date" class="form-control" id="courseStartDate" name="courseStartDate" value="<?php echo $course['courseStartDate']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseRegEndDate" class="form-label">Registration End Date *</label>
                                            <input type="date" class="form-control" id="courseRegEndDate" name="courseRegEndDate" value="<?php echo $course['courseRegEndDate']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseEndDate" class="form-label">End Date *</label>
                                            <input type="date" class="form-control" id="courseEndDate" name="courseEndDate" value="<?php echo $course['courseEndDate']; ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="courseSeats" class="form-label">Available Seats *</label>
                                            <input type="number" class="form-control" id="courseSeats" name="courseSeats" min="1" value="<?php echo $course['courseSeats']; ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="coursePaymentCodeName" class="form-label">Payment Code Name</label>
                                            <input type="text" class="form-control" id="coursePaymentCodeName" name="coursePaymentCodeName" value="<?php echo htmlspecialchars($course['coursePaymentCodeName']); ?>" placeholder="e.g., COURSE2024">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Course Actions -->
                            <div class="form-card">
                                <h5 class="section-title">Course Management</h5>
                                <div class="course-actions">
                                    <a href="course-editor.php?id=<?php echo $courseId; ?>" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Edit Content
                                    </a>
                                    <a href="course-files.php?id=<?php echo $courseId; ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-folder me-2"></i>Manage Files
                                    </a>
                                    <a href="course-enrollments.php?id=<?php echo $courseId; ?>" class="btn btn-outline-info">
                                        <i class="fas fa-users me-2"></i>View Enrollments
                                    </a>
                                    <a href="course-management.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Courses
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Course Image -->
                            <div class="form-card">
                                <h5 class="section-title">Course Image</h5>
                                
                                <?php if ($course['coursePhoto']): ?>
                                    <div id="currentImage">
                                        <img src="<?php echo getImageUrl($course['coursePhoto']); ?>" class="image-preview" alt="Current course image">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeCurrentImage()">Remove Current Image</button>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="upload-area" id="imageUploadArea" <?php echo $course['coursePhoto'] ? 'style="display: none;"' : ''; ?>>
                                    <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                    <h6>Upload New Image</h6>
                                    <p class="text-muted mb-0">Click or drag image here</p>
                                    <input type="file" id="coursePhoto" name="coursePhoto" accept="image/*" style="display: none;">
                                </div>
                                
                                <div id="imagePreview" style="display: none;">
                                    <img id="previewImg" class="image-preview" alt="Course preview">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImage()">Remove New Image</button>
                                </div>
                            </div>

                            <!-- Pricing Information -->
                            <div class="form-card">
                                <h5 class="section-title">Pricing</h5>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="isFree" name="isFree" <?php echo $course['isFree'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="isFree">
                                            Free Course
                                        </label>
                                    </div>
                                </div>

                                <div id="pricingFields">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Price *</label>
                                        <div class="price-input-group">
                                            <div class="currency-display">
                                                <span class="currency-symbol" id="currencySymbol"><?php echo $course['currency'] ?? 'RWF'; ?></span>
                                                <select class="form-control currency-select" id="currency" name="currency">
                                                    <?php if ($currenciesResult && mysqli_num_rows($currenciesResult) > 0): ?>
                                                        <?php while ($currency = mysqli_fetch_assoc($currenciesResult)): ?>
                                                            <option value="<?php echo $currency['currencyCode']; ?>" 
                                                                    data-symbol="<?php echo htmlspecialchars($currency['currencySymbol']); ?>"
                                                                    <?php echo $course['currency'] == $currency['currencyCode'] ? 'selected' : ''; ?>>
                                                                <?php echo $currency['currencyCode'] . ' - ' . $currency['currencyName']; ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <option value="RWF" <?php echo $course['currency'] == 'RWF' ? 'selected' : ''; ?>>RWF - Rwandan Franc</option>
                                                        <option value="USD" <?php echo $course['currency'] == 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                                                        <option value="EUR" <?php echo $course['currency'] == 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <input type="number" class="form-control amount-input" id="amount" name="amount" step="0.01" min="0" value="<?php echo $course['amount'] ?? 0; ?>">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="pricingDescription" class="form-label">Pricing Description</label>
                                        <textarea class="form-control" id="pricingDescription" name="pricingDescription" rows="3"><?php echo htmlspecialchars($course['pricingDescription'] ?? ''); ?></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="discountAmount" class="form-label">Discount Amount</label>
                                                <input type="number" class="form-control" id="discountAmount" name="discountAmount" step="0.01" min="0" value="<?php echo $course['discountAmount'] ?? 0; ?>">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="discountStartDate" class="form-label">Discount Start</label>
                                                <input type="date" class="form-control" id="discountStartDate" name="discountStartDate" value="<?php echo $course['discountStartDate'] ?? ''; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="discountEndDate" class="form-label">Discount End</label>
                                        <input type="date" class="form-control" id="discountEndDate" name="discountEndDate" value="<?php echo $course['discountEndDate'] ?? ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="form-card">
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>Update Course
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
        const imageUploadArea = document.getElementById('imageUploadArea');
        const coursePhoto = document.getElementById('coursePhoto');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const currentImage = document.getElementById('currentImage');

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

        function handleImagePreview(file) {
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    imageUploadArea.style.display = 'none';
                    if (currentImage) currentImage.style.display = 'none';
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            coursePhoto.value = '';
            imageUploadArea.style.display = 'block';
            if (currentImage) currentImage.style.display = 'block';
            imagePreview.style.display = 'none';
        }

        function removeCurrentImage() {
            if (confirm('Are you sure you want to remove the current image?')) {
                // This would need server-side handling to remove the file
                currentImage.style.display = 'none';
                imageUploadArea.style.display = 'block';
            }
        }

        // Free course toggle
        const isFreeCheckbox = document.getElementById('isFree');
        const pricingFields = document.getElementById('pricingFields');

        isFreeCheckbox.addEventListener('change', function() {
            if (this.checked) {
                pricingFields.style.display = 'none';
                document.getElementById('amount').value = '0';
            } else {
                pricingFields.style.display = 'block';
            }
        });

        // Initialize pricing fields visibility
        if (isFreeCheckbox.checked) {
            pricingFields.style.display = 'none';
        }

        // Form validation
        document.getElementById('courseForm').addEventListener('submit', function(e) {
            console.log('Form submission started');
            
            // Check if file is selected
            const fileInput = document.getElementById('coursePhoto');
            if (fileInput && fileInput.files.length > 0) {
                console.log('File selected for upload:', fileInput.files[0].name);
                console.log('File size:', fileInput.files[0].size);
                console.log('File type:', fileInput.files[0].type);
            } else {
                console.log('No file selected for upload');
            }
            
            const startDate = new Date(document.getElementById('courseStartDate').value);
            const regEndDate = new Date(document.getElementById('courseRegEndDate').value);
            const endDate = new Date(document.getElementById('courseEndDate').value);

            if (regEndDate >= startDate) {
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
            
            console.log('Form validation passed, submitting...');
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Handle currency selection
        document.getElementById('currency').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const symbol = selectedOption.getAttribute('data-symbol');
            document.getElementById('currencySymbol').textContent = symbol || selectedOption.value;
        });

        // Initialize currency symbol on page load
        document.addEventListener('DOMContentLoaded', function() {
            const currencySelect = document.getElementById('currency');
            if (currencySelect) {
                const selectedOption = currencySelect.options[currencySelect.selectedIndex];
                const symbol = selectedOption.getAttribute('data-symbol');
                document.getElementById('currencySymbol').textContent = symbol || selectedOption.value;
            }
        });
    </script>
</body>
</html>
