<?php

include_once './LoginClass.php';
$token = trim($_POST['token']);
include_once './Config.php';
if (isset($_POST['doVerify'])) {
    $pass = md5($_POST['paword']);
    $date = trim($_POST['dob_yr']) . '-' . trim($_POST['dob_month']) . '-' . trim($_POST['dob_day']);
    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
    if ($mysql->connect_errno > 0) {
        throw new Exception("Connection to server failed!");
    } else {
        $sql = "Update user_login_details SET password = '$pass' WHERE token = '$token' Limit 1";
        $sql1 = "Update user_personal_info SET dob = '$date' WHERE id = (Select id From user_login_details WHERE token = '$token')";
        if ($mysql->query($sql1) && $mysql->query($sql)) {
            $login = new Login();
            if (isset($_POST['mail']) && isset($pass)) {
                $login->setPassword($_POST['password']);
                $login->setUsername($_POST['email']);
//                $login->setTimezone($_POST['tz']);
                $response = $login->confirmLogin();
                if ($response['status']) {
                    header("Location:settings");
                } else {
                    $_SESSION['login_error']['data'] = $_POST;
                    header("Location:login?login_error=");
                }
            } else {
                $login->logout();
            }
            exit(0);
        } else {//handle error here
        }
        exit();
    }
}
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
?>
