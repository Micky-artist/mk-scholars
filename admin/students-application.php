<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");
include("./php/tagOperations.php");
?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="keywords"
        content="wrappixel, admin dashboard, html css dashboard, web dashboard, bootstrap 5 admin, bootstrap 5, css3 dashboard, bootstrap 5 dashboard, Matrix lite admin bootstrap 5 dashboard, frontend, responsive bootstrap 5 admin template, Matrix admin lite design, Matrix admin lite dashboard bootstrap 5 dashboard template" />
    <meta name="description"
        content="Matrix Admin Lite Free Version is powerful and clean admin dashboard template, inpired from Bootstrap Framework" />
    <meta name="robots" content="noindex,nofollow" />
    <title>Mk Scholars Admin Panel</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="./assets/images/favicon.png" />
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="./assets/libs/select2/dist/css/select2.min.css" />
    <link rel="stylesheet" type="text/css" href="./assets/libs/jquery-minicolors/jquery.minicolors.css" />
    <link rel="stylesheet" type="text/css"
        href="./assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="./assets/libs/quill/dist/quill.snow.css" />
    <link href="./dist/css/style.min.css" rel="stylesheet" />

    <!-- editor codes -->
    <link type="text/css" rel="stylesheet" href="./assets/css/jquery-te-1.4.0.css">
</head>

<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
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
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Student Application</h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                    Student Application
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <!-- editor -->
            <div class="container-fluid">
                <div class="row">

                    <div class="col-12">

                        <div class="card">
                            <div class="card-body">
                                <div class="border-top">
                                    <table class="table">
                                        <thead>
                                            <th scope="col">#</th>
                                            <th scope="col">Video Title</th>
                                            <th scope="col">Video Status</th>
                                            <th scope="col">Actions</th>
                                        </thead>
                                        <?php $selectYoutubeVideos = mysqli_query($conn, "SELECT * FROM youtubeVideos");
                                        $count = 1;
                                        if ($selectYoutubeVideos->num_rows > 0) {
                                            while ($videosData = mysqli_fetch_assoc($selectYoutubeVideos)) {
                                        ?>
                                                <tr>
                                                    <td><?php echo $count++ ?></td>
                                                    <td><?php echo $videosData['VideoTitle'] ?></td>
                                                    <td><?php echo $videosData['VideoStatus'] == 1 ? "Active" : "Not Active"; ?></td>
                                                    <td>
                                                        <!-- <a href="?">Edit</a> -->
                                                        <?php if ($videosData['VideoStatus'] == 1) { ?>
                                                            <a href="?Activate=<?php echo $videosData['videoId'] ?>">Activate</a>
                                                        <?php } else { ?>

                                                            <a href="?DeActivate=<?php echo $videosData['videoId'] ?>">De Activate</a>
                                                        <?php } ?>

                                                    </td>

                                                </tr>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td colspan="5"><?php echo "No Values"; ?></td>
                                            </tr>
                                        <?php

                                        }
                                        ?>

                                    </table>
                                </div>
                            </div>

                        </div>
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
        <!-- slimscrollbar scrollbar JavaScript -->
        <script src="./assets/libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>
        <script src="./assets/extra-libs/sparkline/sparkline.js"></script>
        <!--Wave Effects -->
        <script src="./dist/js/waves.js"></script>
        <!--Menu sidebar -->
        <script src="./dist/js/sidebarmenu.js"></script>
        <!--Custom JavaScript -->
        <script src="./dist/js/custom.min.js"></script>
        <!-- This Page JS -->
        <script src="./assets/libs/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
        <script src="./dist/js/pages/mask/mask.init.js"></script>
        <script src="./assets/libs/select2/dist/js/select2.full.min.js"></script>
        <script src="./assets/libs/select2/dist/js/select2.min.js"></script>
        <script src="./assets/libs/jquery-asColor/dist/jquery-asColor.min.js"></script>
        <script src="./assets/libs/jquery-asGradient/dist/jquery-asGradient.js"></script>
        <script src="./assets/libs/jquery-asColorPicker/dist/jquery-asColorPicker.min.js"></script>
        <script src="./assets/libs/jquery-minicolors/jquery.minicolors.min.js"></script>
        <script src="./assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
        <script src="./assets/libs/quill/dist/quill.min.js"></script>

        <script type="text/javascript" src="./assets/js/jquery-te-1.4.0.min.js" charset="utf-8"></script>

</body>

</html>