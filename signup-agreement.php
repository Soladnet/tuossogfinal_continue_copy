<?php

function clean($value) {
    // If magic quotes not turned on add slashes.
    if (!get_magic_quotes_gpc()) {
        // Adds the slashes.
        $value = addslashes($value);
    }
    // Strip any tags from the value.
    $value = strip_tags($value);
    // Return the value out of the function.
    return $value;
}

if (isset($_COOKIE['user_auth'])) {
    include_once './encryptionClass.php';
    include_once './GossoutUser.php';
    $encrypt = new Encryption();
    $user = new GossoutUser(0);
    $id = $encrypt->safe_b64decode($_COOKIE['user_auth']);
    if (is_numeric($id)) {
        $user->setUserId($id);
        $user->getProfile();
    } else {
        include_once './LoginClass.php';
        $login = new Login();
        $login->logout();
    }
}
$token = clean($_GET['param']);
if ($token != "") {
//    $_SESSION['verified'] = 'Skipped';
//}
//if (!$_SESSION['verified']) {
    include_once './Config.php';
    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
    if ($mysql->connect_errno > 0) {
        throw new Exception("Connection to server failed!");
    } else {
        $str = "Update user_login_details SET activated = 'Y' WHERE token = '$token' AND activated = 'N'";
        $str1 = "SELECT id from user_login_details WHERE token = '$token'";
        if ($run1 = $mysql->query($str1)) {
            if ($run1->num_rows == 1) {
                if ($run = $mysql->query($str)) {
                    if ($mysql->affected_rows == 1) {
                        $_SESSION['verified'] = 'Verified';
                    } else {
                        $_SESSION['verified'] = 'Already verified';
                    }
                }
            } else {
                $_SESSION['verified'] = 'Token not valid';
            }
        }
    }
}
//else {
//    if (isset($_COOKIE['user_auth'])) {
//        include_once './encryptionClass.php';
//        $enc = new Encryption();
//        $id = $enc->safe_b64decode($_COOKIE['user_auth']);
//        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
//        if ($mysql->connect_errno > 0) {
//            throw new Exception("Connection to server failed!");
//        } else {
//            $str = "SELECT activated From user_login_details WHERE id ='$id'";
//            if ($run = $mysql->query($str)) {
//                if ($run->num_rows == 1) {
//                    $r = $run->fetch_array();
//                    if ($r[0] == 'N')
//                        $_SESSION['verified'] = 'Skipped';
//                    else
//                        $_SESSION['verified'] = 'Already verified';
//                }
//            }
//        }
//    }
//}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Agreement</title>
        <meta name="description" content="Start or join existing communities/interests on Gossout and start sharing pictures and videos. People use Gossout search, Discover and connect with communities">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.css">
        <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script>
            $(function() {
                $("#show-suggested-friends,#show-suggested-community,#gossbag-text,#messages-text,#gossbag-close,#messages-close,#user-actions,#user-more-option,#show-full-profile,#search,#search-close,#new-message-btn,#loadCommore,#joinleave").click(function() {
                    showOption(this);
                });
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
            });
        </script>
    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login"><?php
                    echo isset($user) ? "Welcome <a href='home'>" . $user->getFullname() . "</a> [ <a href='login_exec'>Logout</a> ]" :
                            'Already have an account? <a href="login">Login Here</a> | <a href="signup-personal">Sign up</a>'
                    ?></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="logo" id="logo"><img alt=""></div>
            </div>
            <div class="index-intro">		
                <div class="index-intro-2">
                    <div class="registration">
                        <div class="index-intro-1">

                            <h1>
                                <?php
                                if (isset($_SESSION['verified'])) {
                                    if ($_SESSION['verified'] == 'Verified') {
                                        echo "Verification Successful!";
                                    } else if ($_SESSION['verified'] == 'Already verified') {
                                        echo "Already Verified";
                                    } else if ($_SESSION['verified'] == 'Token not valid') {
                                        echo "Invalid Link";
                                    }
                                } else {
                                    echo "Please read carefully!";
                                }
                                ?>
                            </h1>

                        </div>
                        <progress max="100" value="95" style='margin-top: 5px;'>95% done!</progress>
                        <hr>
                        <ul>
                            <li>
                                <p class="success">
                                    Click here to see our <a href="tos">Terms of Service</a>.
                                </p>
                                <p class="info">
                                    We use <a href="http://en.wikipedia.org/wiki/HTTP_cookie">cookies</a>  to ensure that we give 
                                    you the best experience on our website. <!-- We also use cookies 
                                    to ensure we show you advertising that is relevant to you. --> 
                                    If you continue, we'll assume that you 
                                    are happy to receive all <a href="http://en.wikipedia.org/wiki/HTTP_cookie">cookies</a> on this website. 

                                </p>
                            </li>
                        </ul>
                        <?php
                        if (!isset($_COOKIE['user_auth']))
                            echo '<div class="button"><a href="login">Login here!</a></div>';
                        else
                            echo '<div class="button"><a href="home">Finish!</a></div>';
                        ?>

                        </form>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="index-shadow-bottom"></div>
            <div class="index-content-wrapper">
                <?php
                include("footer.php");
                unset($_SESSION['verified']);
                ?>
            </div>

        </div>
    </body>
</html>