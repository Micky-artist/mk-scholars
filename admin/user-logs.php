<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");

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
      <div class="page-breadcrumb">
        <div class="row">
          <div class="col-12 d-flex no-block align-items-center">
            <h4 class="page-title">User Logs</h4>
            <div class="ms-auto text-end">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a href="./index">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">
                  User Logs
                  </li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
      <div class="container-fluid">
        
        <div class="row">
          <!-- column -->
          <div class="col-lg-6">
            <div class="card">
              <!-- <div class="card-body">
                <h4 class="card-title">User Logs</h4>
              </div> -->
              <div class="comment-widgets scrollable">
                <?php include("./php/selectUserLogs.php"); ?>
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