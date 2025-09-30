<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

// Check if admin is logged in
$isAdminLoggedIn = isset($_SESSION['AdminName']) && isset($_SESSION['adminId']) && isset($_SESSION['accountstatus']) && $_SESSION['accountstatus'] == 1;

if (!$isAdminLoggedIn) {
  // Show login required message instead of redirecting
  $showLoginMessage = true;
} elseif (!hasPermission('ViewApplications')) {
  $showPermissionMessage = true;
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
          <?php if (isset($showLoginMessage)): ?>
            <div class="col-12">
              <div class="card">
                <div class="card-body text-center py-5">
                  <div class="mb-4">
                    <i class="fas fa-lock fa-3x text-muted"></i>
                  </div>
                  <h3 class="card-title">Admin Login Required</h3>
                  <p class="card-text text-muted mb-4">
                    You need to be logged in as an administrator to access the subscriptions page.
                  </p>
                  <a href="./authentication-login" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Login to Admin Panel
                  </a>
                </div>
              </div>
            </div>
          <?php elseif (isset($showPermissionMessage)): ?>
            <div class="col-12">
              <div class="card">
                <div class="card-body text-center py-5">
                  <div class="mb-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                  </div>
                  <h3 class="card-title">Access Denied</h3>
                  <p class="card-text text-muted mb-4">
                    You don't have permission to view subscriptions. Please contact your administrator.
                  </p>
                  <a href="./index" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                  </a>
                </div>
              </div>
            </div>
          <?php else: ?>
            <?php include("./view_subscriptions.php"); ?>
          <?php endif; ?>
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