<?php
if(!(isset($_SESSION['AdminName'])) && !(isset($_SESSION['adminId']))){
    echo('
    <script type="text/javascript">
    window.location.href="authentication-login";
    </script>
    ');
}
?>