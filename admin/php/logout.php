<title>Logging out...</title>
<?php
session_start();
if(isset($_SESSION['AdminName']) && isset($_SESSION['adminId'])){
    session_destroy();
    echo('
    <script type="text/javascript">
    window.location.href="../index";
    </script>
    ');
}
