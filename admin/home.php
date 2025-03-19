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

<style>
        .dashboard-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            min-height: 250px;
        }
        
        .dashboard-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .gradient-1 {
            background: linear-gradient(45deg, #4e73df, #224abe);
        }
        
        .gradient-2 {
            background: linear-gradient(45deg, #1cc88a, #13855c);
        }
        
        .gradient-3 {
            background: linear-gradient(45deg, #f6c23e, #dda20a);
        }
        
        .gradient-4 {
            background: linear-gradient(45deg, #e74a3b, #c03526);
        }
        
        .gradient-5 {
            background: linear-gradient(45deg, #36b9cc, #258391);
        }
    </style>
<body>
  <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
    <!-- ============================================================== -->
    <!-- Topbar header - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <?php
include("./partials/header.php");
?>
    <!-- ============================================================== -->
    <!-- End Topbar header -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <?php
include("./partials/navbar.php");
?>
    <!-- ============================================================== -->
    <!-- End Left Sidebar - style you can find in sidebar.scss  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page wrapper  -->
    <!-- ============================================================== -->
    <div class="page-wrapper">
      <!-- ============================================================== -->
      <!-- Bread crumb and right sidebar toggle -->
      <!-- ============================================================== -->
    
      <div class="container py-5">
        <h1 class="text-center mb-5">Admin Dashboard</h1>
        
        <div class="row g-4">
            <!-- Message Students Card -->
            <div class="col-md-3">
                <a href="chat-ground" class="text-decoration-none">
                    <div class="card dashboard-card gradient-1 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-comments card-icon"></i>
                            <h3 class="card-title mb-3">Message Students</h3>
                            <p class="card-text">Send announcements and individual messages to students</p>
                            <!-- <span class="badge bg-light text-dark mt-2">New Messages: 5</span> -->
                        </div>
                    </div>
                </a>
            </div>

            <!-- Student Applications Card -->
            <div class="col-md-3">
                <a href="scholarships" class="text-decoration-none">
                    <div class="card dashboard-card gradient-2 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-file-alt card-icon"></i>
                            <h3 class="card-title mb-3">Applications</h3>
                            <p class="card-text">Review and manage student scholarship applications</p>
                            <!-- <span class="badge bg-light text-dark mt-2">Pending: 12</span> -->
                        </div>
                    </div>
                </a>
            </div>

            <!-- User Logs Card -->
            <div class="col-md-3">
                <a href="user-logs" class="text-decoration-none">
                    <div class="card dashboard-card gradient-3 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-clipboard-list card-icon"></i>
                            <h3 class="card-title mb-3">User Logs</h3>
                            <p class="card-text">Monitor system activities and user interactions</p>
                            <!-- <span class="badge bg-light text-dark mt-2">Today's Logs: 42</span> -->
                        </div>
                    </div>
                </a>
            </div>

            <!-- Manage Users Card -->
            <div class="col-md-3">
                <a href="users" class="text-decoration-none">
                    <div class="card dashboard-card gradient-4 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-users card-icon"></i>
                            <h3 class="card-title mb-3">Manage Users</h3>
                            <p class="card-text">View and manage all system users and permissions</p>
                            <!-- <span class="badge bg-light text-dark mt-2">Total Users: 154</span> -->
                        </div>
                    </div>
                </a>
            </div>

            <!-- Scholarships Card -->
            <div class="col-md-3">
                <a href="scholarships" class="text-decoration-none">
                    <div class="card dashboard-card gradient-5 text-white">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <i class="fas fa-graduation-cap card-icon"></i>
                            <h3 class="card-title mb-3">Scholarships</h3>
                            <p class="card-text">Upload scholarship programs and opportunities</p>
                            <!-- <span class="badge bg-light text-dark mt-2">Active: 23</span> -->
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
      <!-- ============================================================== -->
      <!-- End Bread crumb and right sidebar toggle -->
      <!-- ============================================================== -->
      <!-- ============================================================== -->
      <!-- Container fluid  -->
      <!-- ============================================================== -->
      <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
          <div class="col-md-6">
            <!-- <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-0">Pending Services</h4>
              </div>
              <div class="comment-widgets scrollable">
              <?php include("./php/selectPendingServices.php"); ?>
              </div>
            </div> -->
            <!-- accoridan part -->
           
            <!-- toggle part -->
          
            <!-- card new -->
           
          </div>
          <div class="col-md-6">
            <!-- <div class="card">
              <div class="card-body">
                <h4 class="card-title mb-0">Pending Projects</h4>
              </div>
              <div class="comment-widgets scrollable">
              <?php include("./php/selectPendingPosts.php"); ?>
              </div>
            </div> -->
            <!-- accoridan part -->
           
            <!-- toggle part -->
          
            <!-- card new -->
           
          </div>
       
        </div>
        <!-- row -->
      
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Right sidebar -->
        <!-- ============================================================== -->
        <!-- .right-sidebar -->
        <!-- ============================================================== -->
        <!-- End Right sidebar -->
        <!-- ============================================================== -->
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
  <!-- slimscrollbar scrollbar JavaScript -->
  <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
  <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
  <!--Wave Effects -->
  <script src="./dist/js/waves.js"></script>
  <!--Menu sidebar -->
  <script src="./dist/js/sidebarmenu.js"></script>
  <!--Custom JavaScript -->
  <script src="./dist/js/custom.min.js"></script>
</body>

</html>