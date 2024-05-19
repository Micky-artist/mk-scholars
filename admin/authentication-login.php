<!DOCTYPE html>
<html dir="ltr">
<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateSignInSignUp.php");
include("./partials/head.php");
include("./php/login.php");

?>

<body style="background-color:#343A40; height:100vh; align-items:center; justify-content:center;">
  <div class="main-wrapper">
    <div class="preloader">
      <div class="lds-ripple">
        <div class="lds-pos"></div>
        <div class="lds-pos"></div>
      </div>
    </div>
    <div class="
          auth-wrapper
          d-flex
          no-block
          justify-content-center
          align-items-center
          bg-dark
        ">
      <div class="auth-box bg-dark border-top border-secondary">
        <div>
          <div class="text-center pt-3 pb-3">
            <!-- <span class="db"><img src="./assets/images/logo.png" style="max-width: 150px;" alt="logo" /></span> -->
              <h1>MK Scholars</h1>
          </div>
          <!-- Form -->
          <form class="form-horizontal mt-3"  method="POST">
            <div class="row pb-4">
              <div class="col-12">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-success text-white h-100"><i
                        class="mdi mdi-account fs-4"></i></span>
                  </div>
                  <input type="text" class="form-control form-control-lg" placeholder="Email" aria-label="Username"
                    aria-describedby="basic-addon1" name="adminName" required/>
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-warning text-white h-100"><i
                        class="mdi mdi-lock fs-4"></i></span>
                  </div>
                  <input type="password" class="form-control form-control-lg" placeholder="Password" aria-label="Password"
                    aria-describedby="basic-addon1" name="password" required/>
                </div>
              </div>
            </div>
            <div class="row border-top border-secondary">
              <div class="col-12">
                <div class="form-group">
                  <div class="pt-3">
                    <button class="btn btn-success w-100 text-white" name="submit">
                      Login
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <!-- <div id="recoverform">
          <div class="text-center">
            <span class="text-white">Enter your e-mail address below and we will send you
              instructions how to recover a password.</span>
          </div>
          <div class="row mt-3">
            <form class="col-12" action="index.html">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text bg-danger text-white h-100" id="basic-addon1"><i
                      class="mdi mdi-email fs-4"></i></span>
                </div>
                <input type="text" class="form-control form-control-lg" placeholder="Email Address"
                  aria-label="Username" aria-describedby="basic-addon1" />
              </div>
              <div class="row mt-3 pt-3 border-top border-secondary">
                <div class="col-12">
                  <a class="btn btn-success text-white" href="#" id="to-login" name="action">Back To Login</a>
                  <button class="btn btn-info float-end" type="button" name="action">
                    Recover
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div> -->
      </div>
    </div>
    <!-- ============================================================== -->
    <!-- Login box.scss -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page wrapper scss in scafholding.scss -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Page wrapper scss in scafholding.scss -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Right Sidebar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- Right Sidebar -->
    <!-- ============================================================== -->
  </div>
  <!-- ============================================================== -->
  <!-- All Required js -->
  <!-- ============================================================== -->
  <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
  <!-- Bootstrap tether Core JavaScript -->
  <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <!-- ============================================================== -->
  <!-- This page plugin js -->
  <!-- ============================================================== -->
  <script>
    $(".preloader").fadeOut();
    // ==============================================================
    // Login and Recover Password
    // ==============================================================
    $("#to-recover").on("click", function () {
      $("#loginform").slideUp();
      $("#recoverform").fadeIn();
    });
    $("#to-login").click(function () {
      $("#recoverform").hide();
      $("#loginform").fadeIn();
    });
  </script>
</body>

</html>