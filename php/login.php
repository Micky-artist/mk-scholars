<?php

if (isset($_POST['login'])) {
    $NoUserName = mysqli_real_escape_string($conn, $_POST['username']);
    $NoPassword = mysqli_real_escape_string($conn, $_POST['password']);
    $validateLogin = mysqli_query($conn, "SELECT * FROM normUsers WHERE (NoEmail='$NoUserName' OR NoPhone='$NoUserName') AND NoStatus=1");
    if ($validateLogin->num_rows > 0) {
        $account = mysqli_fetch_assoc($validateLogin);
        $userPassword = $account['NoPassword'];
        if (password_verify($NoPassword, $userPassword)) {
            $_SESSION['username'] = $account['NoUsername'];
            $_SESSION['userId'] = $account['NoUserId'];
            $_SESSION['status'] = $account['NoStatus'];
            $status = $account['NoStatus'];
            if ($status == 1) {
                echo ('
    <script type="text/javascript">
    window.location.href="./index";
    </script>');
            }else{
                $msg = 'Account is not active';
                $class = 'alert alert-danger';
            }
        } else {
    //         echo ('
    // <script type="text/javascript">
    // alert("Incorect password");
    // </script>');
            // $msg = 'Login Failed';
            $msg = 'Incorect password';
            $class = 'alert alert-danger';
        }
    } else {
        // echo ('
        // <script type="text/javascript">
        // alert("Login Failed");
        // </script>');
        $msg = 'Login Failed';
        $class = 'alert alert-danger';
    }
}
?>