<?php

include_once './LoginClass.php';
include_once './Config.php';
if (isset($_POST['doVerify'])) {

    $token = Login::clean($_POST['token']);
    $pass = md5(Login::clean($_POST['paword']));
    $cpass = md5(Login::clean($_POST['cpaword']));
    $email = $_POST['mail']; // since email was not used until login process, login class is left to clean this data
    $date = Login::clean(trim($_POST['dob_yr']) . '-' . trim($_POST['dob_month']) . '-' . trim($_POST['dob_day']));
    $tz = Login::clean($_POST['tz']);

    if ($pass != $cpass && ($pass == "" || $cpass == "")) {
        $_SESSION['error'] = "Password Mismatch";
        header("Location:" . $_SERVER['HTTP_REFERER']);
        exit;
    }
    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
    if ($mysql->connect_errno > 0) {
        throw new Exception("Connection to server failed!");
    } else {
        $sql = "Update user_login_details SET password = '$pass' WHERE token = '$token'";
        $sql1 = "Update user_personal_info SET dob = '$date' WHERE id = (Select id From user_login_details WHERE token = '$token')";
        if ($mysql->query($sql1) && $mysql->query($sql)) {
            $login = new Login();
            if (isset($_POST['mail'])) {
                $login->setPassword($pass, FALSE);
                $login->setUsername($email);
                $login->setTimezone($tz);
                $response = $login->confirmLogin();
                if ($response['status']) {
                    $mysql->query("Update user_login_details SET activated = 'Y' WHERE token = '$token' AND activated = 'N'");
                    header("Location:settings");
                } else {
                    $_SESSION['login_error']['data'] = $_POST;
                    header("Location:login?login_error=");
                }
            } else {
                $login->logout();
            }
        } else {//handle error here
            $login->logout();
        }
    }
} else {
    $login = new Login();
    if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['tz'])) {
        $login->setPassword($_POST['password']);
        $login->setUsername($_POST['email']);
        $login->setTimezone($_POST['tz']);

        if (isset($_POST['remember'])) {
            $login->setRememberStatus($_POST['remember']);
        }

        $response = $login->confirmLogin();
        if ($response['status']) {
            header("Location:home");
        } else {
            $_SESSION['login_error']['data'] = $_POST;
            header("Location:login?login_error=");
        }
    } else {
        $login->logout();
    }
}
?>
