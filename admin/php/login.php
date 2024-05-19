<?php


if (isset($_POST['submit'])) {
    $adminName = mysqli_real_escape_string($conn, $_POST['adminName']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $validateAdminLogin = mysqli_query($conn, "SELECT * FROM users WHERE email='$adminName' AND status=1");
    if ($validateAdminLogin->num_rows > 0) {
        $account = mysqli_fetch_assoc($validateAdminLogin);
        $adminPassword = $account['password'];
        if (password_verify($password, $adminPassword)) {
            $_SESSION['AdminName'] = $account['username'];
            $_SESSION['adminId'] = $account['userId'];
            $_SESSION['accountstatus'] = $account['status'];
            $status = $account['status'];
            if ($status == 1) {
                echo ('
    <script type="text/javascript">
    window.location.href="./index";
    </script>');
            }

        } else {
            echo ('
    <script type="text/javascript">
    alert("Incorect password");
    </script>');
            // $msg = 'Login Failed';
            $msg = 'Incorect password';
            $class = 'formMsgFail';
        }
    } else {
        echo ('
        <script type="text/javascript">
        alert("Login Failed");
        </script>');
        $msg = 'Login Failed';
        $class = 'formMsgFail';
    }
}
?>