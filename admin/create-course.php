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
        $courseRegEndDate = $_POST['courseRegEndDate'];
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
        
        // Insert course
        $insertQuery = "INSERT INTO Courses (courseName, courseShortDescription, courseLongDescription, courseStartDate, courseRegEndDate, courseEndDate, courseSeats, coursePhoto, courseDisplayStatus, coursePaymentCodeName, courseCreatedBy) VALUES ('$courseName', '$courseShortDescription', '$courseLongDescription', '$courseStartDate', '$courseRegEndDate', '$courseEndDate', $courseSeats, '$coursePhoto', $courseDisplayStatus, '$coursePaymentCodeName', " . $_SESSION['adminId'] . ")";
        
        if (mysqli_query($conn, $insertQuery)) {
            $courseId = mysqli_insert_id($conn);
            
            // Insert pricing
            $amount = (float)$_POST['amount'];
            $pricingDescription = mysqli_real_escape_string($conn, $_POST['pricingDescription']);
            $currency = $_POST['currency'];
            $discountAmount = (float)$_POST['discountAmount'];
            $discountStartDate = $_POST['discountStartDate'] ?: null;
            $discountEndDate = $_POST['discountEndDate'] ?: null;
            $isFree = isset($_POST['isFree']) ? 1 : 0;
            
            $pricingQuery = "INSERT INTO CoursePricing (courseId, amount, pricingDescription, currency, discountAmount, discountStartDate, discountEndDate, isFree) VALUES ($courseId, $amount, '$pricingDescription', '$currency', $discountAmount, " . ($discountStartDate ? "'$discountStartDate'" : 'NULL') . ", " . ($discountEndDate ? "'$discountEndDate'" : 'NULL') . ", $isFree)";
            
            if (mysqli_query($conn, $pricingQuery)) {
                $message = 'Course created successfully!';
                $messageType = 'success';
                
                // Redirect to course editor
                header("Location: course-editor.php?id=$courseId");
                exit;
            } else {
                $message = 'Course created but pricing failed: ' . mysqli_error($conn);
                $messageType = 'error';
            }
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
                                            <label for="courseStartDate" class="form-label">Start Date *</label>
                                            <input type="date" class="form-control" id="courseStartDate" name="courseStartDate" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseRegEndDate" class="form-label">Registration End Date *</label>
                                            <input type="date" class="form-control" id="courseRegEndDate" name="courseRegEndDate" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="courseEndDate" class="form-label">End Date *</label>
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
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="coursePaymentCodeName" class="form-label">Payment Code Name</label>
                                            <input type="text" class="form-control" id="coursePaymentCodeName" name="coursePaymentCodeName" placeholder="e.g., COURSE2024">
                                        </div>
                                    </div>
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
                                <h5 class="section-title">Pricing</h5>
                                
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
                                        <label for="amount" class="form-label">Price *</label>
                                            <div class="price-input-group">
                                            <div class="currency-display">
                                                <span class="currency-symbol" id="currencySymbol">RWF</span>
                                                <select class="form-control currency-select" id="currency" name="currency">
                                                    <?php if ($currenciesResult && mysqli_num_rows($currenciesResult) > 0): ?>
                                                        <?php while ($currency = mysqli_fetch_assoc($currenciesResult)): ?>
                                                            <option value="<?php echo $currency['currencyCode']; ?>" 
                                                                    data-symbol="<?php echo htmlspecialchars($currency['currencySymbol']); ?>"
                                                                    <?php echo $currency['currencyCode'] === 'RWF' ? 'selected' : ''; ?>>
                                                                <?php echo $currency['currencyCode'] . ' - ' . $currency['currencyName']; ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    <?php else: ?>
                                                        <option value="RWF">RWF - Rwandan Franc</option>
                                                        <option value="USD">USD - US Dollar</option>
                                                        <option value="EUR">EUR - Euro</option>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <input type="number" class="form-control amount-input" id="amount" name="amount" step="0.01" min="0" value="0">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="pricingDescription" class="form-label">Pricing Description</label>
                                        <textarea class="form-control" id="pricingDescription" name="pricingDescription" rows="3" placeholder="Additional pricing information"></textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="discountAmount" class="form-label">Discount Amount</label>
                                                <input type="number" class="form-control" id="discountAmount" name="discountAmount" step="0.01" min="0" value="0">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="discountStartDate" class="form-label">Discount Start</label>
                                                <input type="date" class="form-control" id="discountStartDate" name="discountStartDate">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="discountEndDate" class="form-label">Discount End</label>
                                        <input type="date" class="form-control" id="discountEndDate" name="discountEndDate">
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
        const imageUploadArea = document.getElementById('imageUploadArea');
        const coursePhoto = document.getElementById('coursePhoto');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

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
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            coursePhoto.value = '';
            imageUploadArea.style.display = 'block';
            imagePreview.style.display = 'none';
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

        // Form validation
        document.getElementById('courseForm').addEventListener('submit', function(e) {
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
