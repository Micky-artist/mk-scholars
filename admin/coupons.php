<?php
session_start();
include("./dbconnections/connection.php");
include("./php/accessRestriction.php");

// Debug: Check if tables exist
$debugInfo = '';
$tablesExist = true;

// Check if Coupons table exists
$tableCheck = mysqli_query($conn, "SHOW TABLES LIKE 'Coupons'");
if (!$tableCheck || mysqli_num_rows($tableCheck) == 0) {
    $tablesExist = false;
    $debugInfo .= "Coupons table does not exist. ";
}

// Check if CouponRedemptions table exists
$tableCheck2 = mysqli_query($conn, "SHOW TABLES LIKE 'CouponRedemptions'");
if (!$tableCheck2 || mysqli_num_rows($tableCheck2) == 0) {
    $tablesExist = false;
    $debugInfo .= "CouponRedemptions table does not exist. ";
}

// Check database connection
if (!$conn) {
    $debugInfo .= "Database connection failed. ";
    $tablesExist = false;
}

// Handle form submissions
$message = '';
$messageType = '';

// If tables don't exist, show setup message
if (!$tablesExist) {
    $message = "Database tables not found. Please run the SQL script: " . $debugInfo;
    $messageType = "warning";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'create':
                $code = strtoupper(trim($_POST['code']));
                $description = trim($_POST['description']);
                $discount_type = $_POST['discount_type'];
                $discount_value = floatval($_POST['discount_value']);
                $scope_type = $_POST['scope_type'];
                $scope_id = !empty($_POST['scope_id']) ? intval($_POST['scope_id']) : null;
                $max_uses = !empty($_POST['max_uses']) ? intval($_POST['max_uses']) : null;
                $per_user_limit = !empty($_POST['per_user_limit']) ? intval($_POST['per_user_limit']) : null;
                $valid_from = !empty($_POST['valid_from']) ? $_POST['valid_from'] : null;
                $valid_to = !empty($_POST['valid_to']) ? $_POST['valid_to'] : null;
                $status = $_POST['status'];

                // Check if code already exists
                $checkStmt = $conn->prepare("SELECT id FROM Coupons WHERE code = ?");
                $checkStmt->bind_param("s", $code);
                $checkStmt->execute();
                if ($checkStmt->get_result()->num_rows > 0) {
                    $message = "Coupon code already exists!";
                    $messageType = "danger";
                } else {
                    $stmt = $conn->prepare("INSERT INTO Coupons (code, description, discount_type, discount_value, scope_type, scope_id, max_uses, per_user_limit, valid_from, valid_to, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssdsiiisss", $code, $description, $discount_type, $discount_value, $scope_type, $scope_id, $max_uses, $per_user_limit, $valid_from, $valid_to, $status);
                    
                    if ($stmt->execute()) {
                        $message = "Coupon created successfully!";
                        $messageType = "success";
                    } else {
                        $message = "Error creating coupon: " . $stmt->error;
                        $messageType = "danger";
                    }
                    $stmt->close();
                }
                $checkStmt->close();
                break;

            case 'update':
                $id = intval($_POST['id']);
                $code = strtoupper(trim($_POST['code']));
                $description = trim($_POST['description']);
                $discount_type = $_POST['discount_type'];
                $discount_value = floatval($_POST['discount_value']);
                $scope_type = $_POST['scope_type'];
                $scope_id = !empty($_POST['scope_id']) ? intval($_POST['scope_id']) : null;
                $max_uses = !empty($_POST['max_uses']) ? intval($_POST['max_uses']) : null;
                $per_user_limit = !empty($_POST['per_user_limit']) ? intval($_POST['per_user_limit']) : null;
                $valid_from = !empty($_POST['valid_from']) ? $_POST['valid_from'] : null;
                $valid_to = !empty($_POST['valid_to']) ? $_POST['valid_to'] : null;
                $status = $_POST['status'];

                // Check if code already exists (excluding current record)
                $checkStmt = $conn->prepare("SELECT id FROM Coupons WHERE code = ? AND id != ?");
                $checkStmt->bind_param("si", $code, $id);
                $checkStmt->execute();
                if ($checkStmt->get_result()->num_rows > 0) {
                    $message = "Coupon code already exists!";
                    $messageType = "danger";
                } else {
                    $stmt = $conn->prepare("UPDATE Coupons SET code = ?, description = ?, discount_type = ?, discount_value = ?, scope_type = ?, scope_id = ?, max_uses = ?, per_user_limit = ?, valid_from = ?, valid_to = ?, status = ? WHERE id = ?");
                    $stmt->bind_param("sssdsiiisssi", $code, $description, $discount_type, $discount_value, $scope_type, $scope_id, $max_uses, $per_user_limit, $valid_from, $valid_to, $status, $id);
                    
                    if ($stmt->execute()) {
                        $message = "Coupon updated successfully!";
                        $messageType = "success";
                    } else {
                        $message = "Error updating coupon: " . $stmt->error;
                        $messageType = "danger";
                    }
                    $stmt->close();
                }
                $checkStmt->close();
                break;

            case 'delete':
                $id = intval($_POST['id']);
                $stmt = $conn->prepare("DELETE FROM Coupons WHERE id = ?");
                $stmt->bind_param("i", $id);
                
                if ($stmt->execute()) {
                    $message = "Coupon deleted successfully!";
                    $messageType = "success";
                } else {
                    $message = "Error deleting coupon: " . $stmt->error;
                    $messageType = "danger";
                }
                $stmt->close();
                break;
        }
    }
}

// Fetch all coupons (only if tables exist)
$couponsResult = false;
$coursesResult = false;
$scholarshipsResult = false;

if ($tablesExist) {
    $couponsQuery = "SELECT c.*, 
                            CASE 
                                WHEN c.scope_type = 'course_pricing' THEN cp.pricingDescription
                                WHEN c.scope_type = 'scholarship' THEN s.scholarshipTitle
                                ELSE 'Global'
                            END as scope_name,
                            COUNT(cr.id) as usage_count
                     FROM Coupons c
                     LEFT JOIN CoursePricing cp ON c.scope_type = 'course_pricing' AND c.scope_id = cp.courseId
                     LEFT JOIN scholarships s ON c.scope_type = 'scholarship' AND c.scope_id = s.scholarshipId
                     LEFT JOIN CouponRedemptions cr ON c.id = cr.coupon_id
                     GROUP BY c.id
                     ORDER BY c.created_at DESC";

    $couponsResult = mysqli_query($conn, $couponsQuery);

    // Fetch courses for scope selection
    $coursesQuery = "SELECT c.courseId, c.courseName, cp.coursePaymentCodeName, cp.pricingDescription 
                     FROM Courses c 
                     JOIN CoursePricing cp ON c.courseId = cp.courseId 
                     WHERE c.courseDisplayStatus = 1 
                     ORDER BY c.courseName";
    $coursesResult = mysqli_query($conn, $coursesQuery);

    // Fetch scholarships for scope selection
    $scholarshipsQuery = "SELECT scholarshipId, scholarshipTitle FROM scholarships WHERE scholarshipStatus != 0 ORDER BY scholarshipTitle";
    $scholarshipsResult = mysqli_query($conn, $scholarshipsQuery);
}
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include("./partials/head.php"); ?>
    
    <style>
        .coupon-code {
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .discount-badge {
            background: linear-gradient(135deg, #00b894, #00a085);
            color: white;
            padding: 6px 12px;
            border-radius: 15px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .scope-badge {
            background: linear-gradient(135deg, #636e72, #2d3436);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-active {
            background: linear-gradient(135deg, #00b894, #00a085);
            color: white;
        }

        .status-inactive {
            background: linear-gradient(135deg, #e17055, #d63031);
            color: white;
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
        }

        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        <?php include("./partials/header.php"); ?>
        <?php include("./partials/left-sidebar.php"); ?>

        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-5 align-self-center">
                        <h4 class="page-title">Coupon Management</h4>
                        <div class="d-flex align-items-center">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Coupons</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="col-7 align-self-center">
                        <div class="d-flex no-block justify-content-end align-items-center">
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#couponModal">
                                <i class="fas fa-plus"></i> Create New Coupon
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (!$tablesExist): ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="card-title"><i class="fas fa-database"></i> Database Setup Required</h4>
                                    <p class="text-muted">The coupon system requires database tables to be created first.</p>
                                    <div class="alert alert-info">
                                        <h5><i class="fas fa-list-ol"></i> Setup Instructions:</h5>
                                        <ol class="text-start">
                                            <li>Open your database management tool (phpMyAdmin, MySQL Workbench, etc.)</li>
                                            <li>Select your database</li>
                                            <li>Go to the SQL tab</li>
                                            <li>Copy and paste the contents of <code>/sql/coupons.sql</code></li>
                                            <li>Execute the SQL script</li>
                                            <li>Refresh this page</li>
                                        </ol>
                                        <p><strong>SQL File Location:</strong> <code>/Applications/XAMPP/xamppfiles/htdocs/mkscholars/sql/coupons.sql</code></p>
                                    </div>
                                    <button class="btn btn-primary" onclick="window.location.reload()">
                                        <i class="fas fa-sync-alt"></i> Refresh Page
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">All Coupons</h4>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Description</th>
                                                <th>Discount</th>
                                                <th>Scope</th>
                                                <th>Usage</th>
                                                <th>Status</th>
                                                <th>Valid Period</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($couponsResult && mysqli_num_rows($couponsResult) > 0): ?>
                                                <?php while ($coupon = mysqli_fetch_assoc($couponsResult)): ?>
                                                    <tr>
                                                        <td><span class="coupon-code"><?php echo htmlspecialchars($coupon['code']); ?></span></td>
                                                        <td><?php echo htmlspecialchars($coupon['description']); ?></td>
                                                        <td>
                                                            <span class="discount-badge">
                                                                <?php 
                                                                if ($coupon['discount_type'] === 'percent') {
                                                                    echo $coupon['discount_value'] . '%';
                                                                } else {
                                                                    echo '$' . number_format($coupon['discount_value'], 2);
                                                                }
                                                                ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="scope-badge"><?php echo ucfirst($coupon['scope_type']); ?></span>
                                                            <?php if ($coupon['scope_name']): ?>
                                                                <br><small><?php echo htmlspecialchars($coupon['scope_name']); ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="usage-info">
                                                                <?php echo $coupon['usage_count']; ?> uses
                                                                <?php if ($coupon['max_uses']): ?>
                                                                    / <?php echo $coupon['max_uses']; ?> max
                                                                <?php endif; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="status-badge status-<?php echo $coupon['status']; ?>">
                                                                <?php echo ucfirst($coupon['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if ($coupon['valid_from'] || $coupon['valid_to']): ?>
                                                                <?php if ($coupon['valid_from']): ?>
                                                                    From: <?php echo date('M j, Y', strtotime($coupon['valid_from'])); ?><br>
                                                                <?php endif; ?>
                                                                <?php if ($coupon['valid_to']): ?>
                                                                    To: <?php echo date('M j, Y', strtotime($coupon['valid_to'])); ?>
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                No expiry
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-warning" onclick="editCoupon(<?php echo htmlspecialchars(json_encode($coupon)); ?>)">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger" onclick="deleteCoupon(<?php echo $coupon['id']; ?>, '<?php echo htmlspecialchars($coupon['code']); ?>')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="8" class="text-center">No coupons found</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php include("./partials/footer.php"); ?>
        </div>
    </div>

    <!-- Coupon Modal -->
    <div class="modal fade" id="couponModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="couponModalTitle">Create New Coupon</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="couponForm">
                    <div class="modal-body">
                        <input type="hidden" name="action" id="formAction" value="create">
                        <input type="hidden" name="id" id="couponId">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="code">Coupon Code *</label>
                                    <input type="text" class="form-control" id="code" name="code" required>
                                    <small class="form-text text-muted">Enter a unique code (will be converted to uppercase)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="status">Status *</label>
                                    <select class="form-control" id="status" name="status" required>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="discount_type">Discount Type *</label>
                                    <select class="form-control" id="discount_type" name="discount_type" required>
                                        <option value="percent">Percentage</option>
                                        <option value="fixed">Fixed Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="discount_value">Discount Value *</label>
                                    <input type="number" class="form-control" id="discount_value" name="discount_value" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="scope_type">Scope Type *</label>
                                    <select class="form-control" id="scope_type" name="scope_type" required onchange="toggleScopeId()">
                                        <option value="global">Global</option>
                                        <option value="course_pricing">Course Pricing</option>
                                        <option value="scholarship">Scholarship</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="scope_id">Scope Item</label>
                                    <select class="form-control" id="scope_id" name="scope_id">
                                        <option value="">Select an item</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="max_uses">Max Uses</label>
                                    <input type="number" class="form-control" id="max_uses" name="max_uses" min="1">
                                    <small class="form-text text-muted">Leave empty for unlimited uses</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="per_user_limit">Per User Limit</label>
                                    <input type="number" class="form-control" id="per_user_limit" name="per_user_limit" min="1">
                                    <small class="form-text text-muted">Max uses per user</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="valid_from">Valid From</label>
                                    <input type="datetime-local" class="form-control" id="valid_from" name="valid_from">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="valid_to">Valid To</label>
                                    <input type="datetime-local" class="form-control" id="valid_to" name="valid_to">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Coupon</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the coupon <strong id="deleteCouponCode"></strong>?</p>
                    <p class="text-danger">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteCouponId">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Course and scholarship data for scope selection
        const courses = <?php echo $coursesResult ? json_encode(mysqli_fetch_all($coursesResult, MYSQLI_ASSOC)) : '[]'; ?>;
        const scholarships = <?php echo $scholarshipsResult ? json_encode(mysqli_fetch_all($scholarshipsResult, MYSQLI_ASSOC)) : '[]'; ?>;

        function toggleScopeId() {
            const scopeType = document.getElementById('scope_type').value;
            const scopeIdSelect = document.getElementById('scope_id');
            
            scopeIdSelect.innerHTML = '<option value="">Select an item</option>';
            
            if (scopeType === 'course_pricing') {
                courses.forEach(course => {
                    const option = document.createElement('option');
                    option.value = course.courseId;
                    option.textContent = `${course.courseName} - ${course.pricingDescription}`;
                    scopeIdSelect.appendChild(option);
                });
            } else if (scopeType === 'scholarship') {
                scholarships.forEach(scholarship => {
                    const option = document.createElement('option');
                    option.value = scholarship.scholarshipId;
                    option.textContent = scholarship.scholarshipTitle;
                    scopeIdSelect.appendChild(option);
                });
            }
        }

        function editCoupon(coupon) {
            document.getElementById('couponModalTitle').textContent = 'Edit Coupon';
            document.getElementById('formAction').value = 'update';
            document.getElementById('couponId').value = coupon.id;
            document.getElementById('code').value = coupon.code;
            document.getElementById('description').value = coupon.description || '';
            document.getElementById('discount_type').value = coupon.discount_type;
            document.getElementById('discount_value').value = coupon.discount_value;
            document.getElementById('scope_type').value = coupon.scope_type;
            document.getElementById('status').value = coupon.status;
            document.getElementById('max_uses').value = coupon.max_uses || '';
            document.getElementById('per_user_limit').value = coupon.per_user_limit || '';
            document.getElementById('valid_from').value = coupon.valid_from ? coupon.valid_from.replace(' ', 'T') : '';
            document.getElementById('valid_to').value = coupon.valid_to ? coupon.valid_to.replace(' ', 'T') : '';
            
            toggleScopeId();
            if (coupon.scope_id) {
                document.getElementById('scope_id').value = coupon.scope_id;
            }
            
            new bootstrap.Modal(document.getElementById('couponModal')).show();
        }

        function deleteCoupon(id, code) {
            document.getElementById('deleteCouponId').value = id;
            document.getElementById('deleteCouponCode').textContent = code;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Reset form when modal is closed
        document.getElementById('couponModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('couponForm').reset();
            document.getElementById('couponModalTitle').textContent = 'Create New Coupon';
            document.getElementById('formAction').value = 'create';
            document.getElementById('couponId').value = '';
        });

        // Auto-convert code to uppercase
        document.getElementById('code').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    </script>
</body>
</html>
