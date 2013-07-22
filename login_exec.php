<?php

include_once './LoginClass.php';
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
