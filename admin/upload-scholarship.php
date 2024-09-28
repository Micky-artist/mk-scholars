<?php
session_start();
include("./dbconnections/connection.php");
include("./php/validateAdminSession.php");
include("./php/uploadScholarship.php");

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
                                                    <option value="0" selected>Hidden</option>
                                                    <option value="1">Free</option>
                                                    <option value="2">Payable</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="cono1" class="card-title">Amount</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="ScholarshipPrice" id="title"
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
                            </div>
                            <label class="card-title">Scholarship Link</label>
                            <textarea type="text" id="post" name="ScholarshipLink" class="form-control" rows="2" required></textarea>
                            <br>
                            <label class="card-title">Scholarship Youtube Link</label>
                            <textarea type="text" id="post" name="scholarshipYoutubeLink" class="form-control" rows="2" required></textarea>
                            <br>
                            <label class="card-title">Scholarship description</label>
                            <textarea type="text" id="post" name="ScholarshipDescription" class="form-control" rows="20" required></textarea>
                            <!-- <textarea type="text" id="post" name="ScholarshipDescription" class="text-jqte" rows="20" required></textarea> -->

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
    <script type="text/javascript">
        $('.text-jqte').jqte();


        function displayImg(input, _this) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    _this.parent().parent().parent().find('.img-field').attr('src', e.target.result);
                    _this.siblings('label').html(input.files[0]['name'])
                    _this.siblings('input[name="fname"]').val('<?php echo strtotime(date('y-m-d H:i:s')) ?>_' + input.files[0]['name'])
                    var p = $('<p></p>')

                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#manage-category').submit(function(e) {
            e.preventDefault();
            start_load();

            $.ajax({
                url: 'ajax.php?action=save_post',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                success: function(resp) {
                    resp = JSON.parse(resp)
                    if (resp.status == 1) {
                        alert_toast("Data successfully updated.", 'success');
                        setTimeout(function() {
                            location.replace('index.php?page=preview_post&id=' + resp.id)

                        }, 1500)
                    }
                }
            })
        })
    </script>
    <script>
        //***********************************//
        // For select 2
        //***********************************//
        $(".select2").select2();

        /*colorpicker*/
        $(".demo").each(function() {
            //
            // Dear reader, it's actually very easy to initialize MiniColors. For example:
            //
            //  $(selector).minicolors();
            //
            // The way I've done it below is just for the demo, so don't get confused
            // by it. Also, data- attributes aren't supported at this time...they're
            // only used for this demo.
            //
            $(this).minicolors({
                control: $(this).attr("data-control") || "hue",
                position: $(this).attr("data-position") || "bottom left",

                change: function(value, opacity) {
                    if (!value) return;
                    if (opacity) value += ", " + opacity;
                    if (typeof console === "object") {
                        console.log(value);
                    }
                },
                theme: "bootstrap",
            });
        });
        /*datwpicker*/
        jQuery(".mydatepicker").datepicker();
        jQuery("#datepicker-autoclose").datepicker({
            autoclose: true,
            todayHighlight: true,
        });
        var quill = new Quill("#editor", {
            theme: "snow",
        });
    </script>
</body>

</html>