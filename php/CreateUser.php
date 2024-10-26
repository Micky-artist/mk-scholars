<?php
if (isset($_POST['signup'])) {
    $NoUsername = mysqli_real_escape_string($conn, $_POST['NoUsername']);
    $NoEmail = mysqli_real_escape_string($conn, $_POST['NoEmail']);
    $NoPhone = mysqli_real_escape_string($conn, $_POST['NoPhone']);
    $NoPassword = mysqli_real_escape_string($conn, $_POST['NoPassword']);
    $NoCoPassword = mysqli_real_escape_string($conn, $_POST['NoCoPassword']);
    $aggree = mysqli_real_escape_string($conn, $_POST['aggree']);
    
    if ($NoPassword != $NoCoPassword) {
        $msg = "Password Does not match! please retry.";
        $class = "alert alert-danger";
        echo mysqli_error($conn);
    }elseif( $aggree!=true ){
        $msg = "Check terms and conditions and the privacy policy";
        $class = "alert alert-danger";
        echo mysqli_error($conn);
    }else {
        if (strlen($NoPassword) < 8) {
            $msg = 'password must be above 8 characters';
            $class = 'alert alert-danger';
        } else {
            $checkAccountExistance = mysqli_query($conn, "SELECT NoEmail,NoPhone FROM normUsers WHERE NoEmail='$NoEmail' OR NoPhone='$NoPhone'");
            if ($checkAccountExistance->num_rows > 0) {
                $retrieveAccountCredentials = mysqli_fetch_assoc($checkAccountExistance);
                if ($retrieveAccountCredentials['NoEmail'] == $NoEmail) {
                    $msg = "Email already exist";
                    $class = 'alert alert-danger';
                } else {
                    $msg = "Phone Number already exist";
                    $class = 'alert alert-danger';
                }
            } else {
                $encPassword = password_hash($NoPassword, PASSWORD_DEFAULT);
                $creation_date = date('Y-m-d');
                $status = 1;
                // $verificationCode = 'SF' . date('Ymis') . rand(1, 999);
                $createUser = mysqli_query($conn, "INSERT INTO normUsers(NoUsername,NoEmail,NoPhone,NoPassword,NoStatus,NoCreationDate) 
                VALUES('$NoUsername','$NoEmail','$NoPhone','$encPassword',$status,'$creation_date')");
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