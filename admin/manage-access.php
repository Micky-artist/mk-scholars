<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

// Handle GET requests (Activate/Deactivate) BEFORE any HTML output
if(isset($_GET['Deactivate'])){
  $Deactivate = (int)$_GET['Deactivate'];
  $updateUserStatus = mysqli_query($conn,"UPDATE users SET status = 0 WHERE userId = $Deactivate");
  if($updateUserStatus){
    $_SESSION['flash'] = 'User deactivated successfully!';
    header("Location: manage-access.php");
    exit;
  }
}
if(isset($_GET['Activate'])){
  $Activate = (int)$_GET['Activate'];
  $updateUserStatus = mysqli_query($conn,"UPDATE users SET status = 1 WHERE userId = $Activate");
  if($updateUserStatus){
    $_SESSION['flash'] = 'User activated successfully!';
    header("Location: manage-access.php");
    exit;
  }
}

// Handle form submission BEFORE any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['is_super_admin']) && $_SESSION['is_super_admin']) {
  $adminId = (int)$_POST['adminId'];
  
  // Define all possible rights fields
  $allRights = [
    'ManageRights', 'ManageCountries', 'ViewApplications', 'DeleteApplication',
    'EditApplication', 'PublishApplication', 'ApplicationSupportRequest', 'CourseApplication',
    'ChatGround', 'ViewUsers', 'ManageUsers', 'ViewTags', 'AddTag', 'DeleteTag',
    'ManageYoutubeVideo', 'DeleteYoutubeVideo', 'ManageUserLogs', 'AddAdmin'
  ];
  
  // Collect rights - checkboxes send '1' when checked, nothing when unchecked
  $rights = [];
  foreach ($allRights as $right) {
    $rights[$right] = (isset($_POST[$right]) && ($_POST[$right] == '1' || $_POST[$right] === 'on')) ? 1 : 0;
  }

  // Validate adminId
  if ($adminId <= 0) {
    $_SESSION['flash'] = 'Error: Invalid admin ID';
    header("Location: manage-access.php");
    exit;
  }

  // Check if the admin already has rights
  $checkSql = "SELECT * FROM AdminRights WHERE AdminId = ?";
  $checkStmt = $conn->prepare($checkSql);
  $checkStmt->bind_param("i", $adminId);
  $checkStmt->execute();
  $checkResult = $checkStmt->get_result();
  $checkStmt->close();

  if ($checkResult->num_rows > 0) {
    // Build UPDATE statement with all rights
    $updateFields = [];
    $updateValues = [];
    foreach ($rights as $field => $value) {
      $updateFields[] = "`$field` = ?";
      $updateValues[] = $value;
    }
    
    $updateSql = "UPDATE AdminRights SET " . implode(', ', $updateFields) . " WHERE AdminId = ?";
    $updateStmt = $conn->prepare($updateSql);
    
    // Bind parameters: all rights values + adminId
    $types = str_repeat('i', count($updateValues)) . 'i';
    $updateValues[] = $adminId;
    $updateStmt->bind_param($types, ...$updateValues);
    
    if ($updateStmt->execute()) {
      $_SESSION['flash'] = 'Rights updated successfully!';
      $updateStmt->close();
      header("Location: manage-access.php");
      exit;
    } else {
      $_SESSION['flash'] = "Error: " . $updateStmt->error;
      $updateStmt->close();
      header("Location: manage-access.php");
      exit;
    }
  } else {
    // Insert new rights
    $columns = array_keys($rights);
    $values = array_values($rights);
    
    $columnNames = implode(', ', array_map(function($col) { return "`$col`"; }, $columns));
    $placeholders = implode(', ', array_fill(0, count($values), '?'));
    
    $insertSql = "INSERT INTO AdminRights (AdminId, $columnNames) VALUES (?, $placeholders)";
    $insertStmt = $conn->prepare($insertSql);
    
    $types = 'i' . str_repeat('i', count($values));
    $insertValues = array_merge([$adminId], $values);
    $insertStmt->bind_param($types, ...$insertValues);
    
    if ($insertStmt->execute()) {
      $_SESSION['flash'] = 'Rights created successfully!';
      $insertStmt->close();
      header("Location: manage-access.php");
      exit;
    } else {
      $_SESSION['flash'] = "Error: " . $insertStmt->error;
      $insertStmt->close();
      header("Location: manage-access.php");
      exit;
    }
  }
}

?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php
include("./partials/head.php");
?>

<body>
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
    data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <?php
    include("./partials/header.php");
    ?>
    <?php
    include("./partials/navbar.php");
    ?>
    <div class="page-wrapper">
      
      <div class="container-fluid">
        
        <div class="row">
          <!-- column -->
          <div class="col-lg-14">
            <div class="card">
              <div class="comment-widgets scrollable">
                <?php include("./manageAdminAccess.php"); ?>
              </div>
            </div>
          </div>
          <!-- column -->
        </div>
      </div>
      <!-- ============================================================== -->
      <!-- End Container fluid  -->
      <!-- ============================================================== -->
      <!-- ============================================================== -->
      <!-- footer -->
      <!-- ============================================================== -->
      <?php
      include("./partials/footer.php");
      ?>
      <!-- ============================================================== -->
      <!-- End footer -->
      <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Page wrapper  -->
    <!-- ============================================================== -->
  </div>
  <!-- ============================================================== -->
  <!-- End Wrapper -->
  <!-- ============================================================== -->
  <!-- ============================================================== -->
  <!-- All Jquery -->
  <!-- ============================================================== -->

  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap tether Core JavaScript -->
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
  <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
  <!--Wave Effects -->
  <script src="./dist/js/waves.js"></script>
  <!--Menu sidebar -->
  <script src="./dist/js/sidebarmenu.js"></script>
  <!--Custom JavaScript -->
  <script src="./dist/js/custom.min.js"></script>
  <!--This page JavaScript -->
  <!-- <script src="./dist/js/pages/dashboards/dashboard1.js"></script> -->
  <!-- Charts js Files -->
  <script src="./assets/libs/flot/excanvas.js"></script>
  <script src="./assets/libs/flot/jquery.flot.js"></script>
  <script src="./assets/libs/flot/jquery.flot.pie.js"></script>
  <script src="./assets/libs/flot/jquery.flot.time.js"></script>
  <script src="./assets/libs/flot/jquery.flot.stack.js"></script>
  <script src="./assets/libs/flot/jquery.flot.crosshair.js"></script>
  <script src="./assets/libs/flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
  <script src="./dist/js/pages/chart/chart-page-init.js"></script>
</body>

</html>