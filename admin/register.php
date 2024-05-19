<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");
include("./php/userSignUp.php");
?>

<!DOCTYPE html>
<html dir="ltr">
<title>Icyeza Interiors</title>
<?php
include("./partials/head.php");
?>
<?php
include("./partials/header.php");
?>


<body style="background-color:#343A40; height:100vh;">
<?php
// include("./partials/navbar.php");
?>
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
            <span class="db">
              <img src="./assets/images/logo.png" alt="logo" style="max-width: 150px;" />
            </span>
          </div>
          <!-- Form -->
          <div class="<?php echo $class ?>">
          <?php echo $msg ?>
          </div>
          <form class="form-horizontal mt-3" method="POST">
            <div class="row pb-4">
              <div class="col-12">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-success text-white h-100" id="basic-addon1"><i
                        class="mdi mdi-account fs-4"></i></span>
                  </div>
                  <input type="text" name="username" class="form-control form-control-lg" value="<?php echo $username ?>" placeholder="Username"
                    aria-label="Username" aria-describedby="basic-addon1" required />
                </div>
                <!-- email -->
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-danger text-white h-100" id="basic-addon1"><i
                        class="mdi mdi-email fs-4"></i></span>
                  </div>
                  <input type="text" name="email" class="form-control form-control-lg" value="<?php echo $email ?>" placeholder="Email Address"
                    aria-label="Username" aria-describedby="basic-addon1" required />
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-warning text-white h-100" id="basic-addon2"><i
                        class="mdi mdi-lock fs-4"></i></span>
                  </div>
                  <input type="password" name="pwd" class="form-control form-control-lg" placeholder="Password"
                    aria-label="Password" aria-describedby="basic-addon1" required />
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-info text-white h-100" id="basic-addon2"><i
                        class="mdi mdi-lock fs-4"></i></span>
                  </div>
                  <input type="password" name="pwdrepeat" class="form-control form-control-lg"
                    placeholder="Confirm Password" aria-label="Password" aria-describedby="basic-addon1" required />
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text bg-info text-white h-100" id="basic-addon2"><i
                        class="mdi mdi-account fs-4"></i></span>
                  </div>
                  <select name="usertype" class="form-control form-control-lg" id="" required>
                    <option value="0" selected>-Select user type-</option>
                    <option value="1">Admin</option>
                    <option value="2">Guest</option>
                  </select>
                  <!-- <input type="input"  placeholder=" Confirm Password" aria-label="Password" aria-describedby="basic-addon1" required /> -->
                </div>
              </div>
            </div>
            <div class="row border-top border-secondary">
              <div class="col-12">
                <div class="form-group">
                  <div class="pt-3 d-grid">
                    <button class="btn btn-block btn-lg btn-info" name="signup">
                      Sign Up
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
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
  </script>
</body>

</html>