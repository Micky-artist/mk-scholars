<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");
include("./php/uploadScholarship.php");

if (!hasPermission('PublishApplication')) {
    header("Location: ./index");
    exit;
}

?>

<!DOCTYPE html>
<html dir="ltr" lang="en">
<?php include("./partials/head.php"); ?>

<!-- Additional CSS for this page -->
<link rel="stylesheet" type="text/css" href="./assets/libs/select2/dist/css/select2.min.css" />
<link rel="stylesheet" type="text/css" href="./assets/libs/jquery-minicolors/jquery.minicolors.css" />
<link rel="stylesheet" type="text/css" href="./assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" />
<link rel="stylesheet" type="text/css" href="./assets/libs/quill/dist/quill.snow.css" />
<link type="text/css" rel="stylesheet" href="./assets/css/jquery-te-1.4.0.css">

<body>
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
                        <h4 class="page-title">Upload Scholarships</h4>
                        <div class="ms-auto text-end">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Upload Scholarship
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
                                <!-- <div class="card"> -->
                                <form class="form-horizontal" enctype="multipart/form-data" method="post">
                                    <div class="card-body">
                                        <h4 class="card-title">Upload Scholarships</h4>
                                        <div class="form-group row">
                                            <label for="cono1" class="card-title">Scholarships Title</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="ScholarshipTitle" id="title"
                                                    placeholder="Scholarships Title" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="cono1" class="card-title">Scholarship Status</label>
                                            <div class="col-sm-9">
                                                <select type="text" name="ScholarshipStatus" class="form-control" id="lname">
                                                    <option value="-1" selected disabled>Select Status</option>
                                                    <option value="0" selected>Hidden</option>
                                                    <option value="1">Free</option>
                                                    <option value="2">Payable</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="cono1" class="card-title">Amount</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" name="ScholarshipPrice" id="title"
                                                    placeholder="Amount (Optional)" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="cono1" class="card-title">Scholarship Country</label>
                                        <div class="col-sm-9">
                                            <select type="text" name="ScholarshipCountry" class="form-control" id="lname">
                                                <option value="0" selected disabled>Select Country</option>
                                                <?php include("./php/selectCountries.php"); ?><?php  ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="cono1" class="card-title">File Upload</label>
                                        <div class="col-md-9">
                                            <div class="custom-file">
                                                <input type="file" name="ScholarshipImage" accept="image/*" class="custom-file-input"
                                                    id="validatedCustomFile" required />
                                                <label class="custom-file-label" for="validatedCustomFile">Choose
                                                    Image 1...</label>
                                                <div class="invalid-feedback">
                                                    Example invalid custom file feedback
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <label class="card-title">Scholarship Link</label>
                                    <textarea type="text" id="post" name="ScholarshipLink" class="form-control" rows="2" required></textarea>
                                    <br>
                                    <label class="card-title">Scholarship Youtube Link</label>
                                    <textarea type="text" id="post" name="scholarshipYoutubeLink" class="form-control" rows="2" required></textarea>
                                    <br>
                                    <label class="card-title">Scholarship description</label>
                                    <textarea type="text" id="textdescription" name="ScholarshipDescription" class="form-control" rows="30" cols="20" required></textarea>
                            </div>

                        </div>
                        <div class="border-top">
                            <div class="card-body">
                                <button name="uploadScholarship" class="btn btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        <?php
        include("./partials/footer.php");
        ?>
    </div>
    </div>

    <!-- jQuery and Bootstrap JS (required for some plugins) -->
    <script src="./assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="./assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- TinyMCE Editor -->
    <script type="text/javascript" src="../tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof tinymce !== 'undefined') {
                tinymce.init({
                    selector: '#textdescription',
                    height: 500,
                    menubar: true,
                    plugins: [
                        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                        'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                        'insertdatetime', 'media', 'table', 'help', 'wordcount'
                    ],
                    toolbar: 'undo redo | blocks | ' +
                        'bold italic underline strikethrough | forecolor backcolor | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'bullist numlist | outdent indent | ' +
                        'removeformat | link image | table | code | fullscreen | help',
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }',
                    branding: false,
                    promotion: false,
                    setup: function(editor) {
                        editor.on('change', function() {
                            editor.save();
                        });
                    }
                });
            } else {
                console.error('TinyMCE library not loaded. Check the script path.');
            }
        });
    </script>
</body>

</html>