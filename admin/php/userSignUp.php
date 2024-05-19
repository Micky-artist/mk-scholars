<?php
if (isset($_POST['signup'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['pwd']);
    $copassword = mysqli_real_escape_string($conn, $_POST['pwdrepeat']);
    $usertype = mysqli_real_escape_string($conn, $_POST['usertype']);

    if ($password != $copassword) {
        $msg = "Password Does not match! please retry.";
        $class = "alert alert-danger";
    } else {
        if (strlen($password) < 8) {
            $msg = 'password must be above 8 characters';
            $class = 'alert alert-danger';
        } else {
            $checkAccountExistance = mysqli_query($conn, "SELECT email,username FROM users WHERE email='$email' OR username='$username'");
            if ($checkAccountExistance->num_rows > 0) {
                $retrieveAccountCredentials = mysqli_fetch_assoc($checkAccountExistance);
                if ($retrieveAccountCredentials['email'] == $email) {
                    $msg = "Email already exist";
                    $class = 'alert alert-danger';
                } else {
                    $msg = "Username already exist";
                    $class = 'alert alert-danger';
                }
            } else {
                $encPassword = password_hash($password, PASSWORD_DEFAULT);
                $creation_date = date('y-m-d');
                // $verificationCode = 'SF' . date('Ymis') . rand(1, 999);
                $createUser = mysqli_query($conn, "INSERT INTO users(username,email,password,status) 
                VALUES('$username','$email','$encPassword',$usertype)");
                if ($createUser) {
                    $msg = 'User account Created';
                    $class = 'alert alert-success';
    // ');
                } else {
                    $msg = "User account not created";
                    $class = 'alert alert-danger';
                    ;
                }
            }
        }

    }
}
?>