<title>Logging out...</title>
<?php
session_start();
if(isset($_SESSION['username']) && isset($_SESSION['userId'])){
    session_destroy();
    echo('
    <script type="text/javascript">
    window.location.href="../index";
    </script>
    ');
}
