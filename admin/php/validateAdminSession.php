<?php
if(!(isset($_SESSION['AdminName'])) || !(isset($_SESSION['adminId'])) || !isset($_SESSION['accountstatus']) || $_SESSION['accountstatus']==0){
    session_destroy();
    
    // Check if this is an AJAX request
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // Return JSON for AJAX requests
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Session expired. Please login again.']);
        exit;
    } else {
        // Standardize to PHP header redirect for non-AJAX requests
        header("Location: authentication-login.php");
        exit;
    }
}
?>
<?php
$access = [];

// Check database connection
if(!isset($conn) || !$conn) {
    die("Database connection failed");
}

// Validate user authentication and authorization
if(isset($_SESSION['adminId']) && isset($_SESSION['AdminName']) && isset($_SESSION['accountstatus'])) {
    if($_SESSION['accountstatus'] == 1) {
        // Use prepared statements to prevent SQL injection
        $AdminId = intval($_SESSION['adminId']); // Use adminId, not AdminName for queries
        
        $stmt = $conn->prepare("SELECT * FROM AdminRights WHERE AdminId = ?");
        if(!$stmt) {
            die("Prepared statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $AdminId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result && $result->num_rows > 0) {
            while($accessRestriction = $result->fetch_assoc()) {
                $access[] = $accessRestriction;
            }
        } else {
            // Log access attempt with no permissions
            error_log("Admin ID $AdminId attempted access with no permissions");
            session_destroy();
            header("Location: authentication-login.php?reason=no_permissions");
            exit();
        }
        
        $stmt->close();
    } else {
        // Log deactivated account access attempt
        error_log("Deactivated account (Admin ID: {$_SESSION['adminId']}) attempted access");
        session_destroy();
            header("Location: authentication-login.php?status=account_deactivated");
        exit();
    }
} else {
    // Log unauthorized access attempt
    error_log("Unauthorized access attempt from IP: " . $_SERVER['REMOTE_ADDR']);
    session_destroy();
    header("Location: authentication-login.php?status=not_logged_in");
    exit();
}

// Don't directly echo the access array - security risk
// Instead, use it for authorization checks in your application logic
// echo $access; // This was a security risk

// Example of how to properly use the access array:
function hasPermission($permissionName) {
    global $access;
    foreach($access as $right) {
        if(isset($right[$permissionName]) && $right[$permissionName] != null && $right[$permissionName] == 1) {
            return true;
        }
    }
    return false;
}

// Example usage
// if(hasPermission('ManageCountries')) {
//     echo "Ok".$_SESSION['adminId'];
// } else {
//     echo "No".$_SESSION['adminId'];
// }

// Function to check if admin has access to a specific course
function hasCourseAccess($courseId) {
    global $conn, $access;
    
    // Super admins (with ManageRights) have access to all courses
    if (hasPermission('ManageRights')) {
        return true;
    }
    
    // Check if course access table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'AdminCourseAccess'");
    if (!$tableCheck || $tableCheck->num_rows == 0) {
        // If table doesn't exist, allow access (backward compatibility)
        return true;
    }
    
    // Check if admin has been granted access to this course
    $adminId = isset($_SESSION['adminId']) ? (int)$_SESSION['adminId'] : 0;
    if ($adminId <= 0 || $courseId <= 0) {
        return false;
    }
    
    $checkSql = "SELECT accessId FROM AdminCourseAccess WHERE adminId = ? AND courseId = ?";
    $stmt = $conn->prepare($checkSql);
    if (!$stmt) {
        error_log("Error preparing course access check: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("ii", $adminId, $courseId);
    $stmt->execute();
    $result = $stmt->get_result();
    $hasAccess = $result->num_rows > 0;
    $stmt->close();
    
    return $hasAccess;
}

// Function to validate course access and redirect if unauthorized
function validateCourseAccess($courseId, $redirectUrl = 'course-management.php') {
    if (!hasCourseAccess($courseId)) {
        // Check if this is an AJAX request
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
        
        if ($isAjax) {
            // Return JSON for AJAX requests
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You do not have access to this course.']);
            exit;
        } else {
            // Redirect for regular requests
            $_SESSION['flash'] = 'You do not have access to this course.';
            header("Location: " . $redirectUrl);
            exit;
        }
    }
}
?>