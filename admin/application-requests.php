<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");


if (!hasPermission('ApplicationSupportRequest')) {
  header("Location: ./index");
  exit;
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
                <?php include("./php/fetchApplicationRequests.php"); ?>
              </div>
            </div>
          </div>
          <!-- column -->
        </div>
      </div>
      <?php
      include("./partials/footer.php");
      ?>
    </div>
  </div>

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