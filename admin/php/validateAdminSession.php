<?php
if(!(isset($_SESSION['AdminName'])) || !(isset($_SESSION['adminId'])) || !isset($_SESSION['accountstatus']) || $_SESSION['accountstatus']==0){
    session_destroy();
    echo('
    <script type="text/javascript">
    window.location.href="authentication-login";
    </script>
    ');
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
        header("Location: authentication-login?status=account_deactivated");
        exit();
    }
} else {
    // Log unauthorized access attempt
    error_log("Unauthorized access attempt from IP: " . $_SERVER['REMOTE_ADDR']);
    session_destroy();
    header("Location: authentication-login?status=not_logged_in");
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
?>