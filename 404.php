<?php
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
?>
<!doctype html>
<html>
    <head>
        <?php
        include './webbase.php';
        ?>
        <title>404 Error</title>
        <meta name="description" content="Start or join existing communities/interests on Gossout and start sharing pictures and videos. People use Gossout search, Discover and connect with communities">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.1.css">
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script>
            $(function() {
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
                $("#searchField").focus();
            });
            function validate() {
                if ($.trim($("#searchField").val()).length === 0) {
                    return false;
                } else {
                    return true;
                }
            }
        </script>
    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login" id="name-login-cont"><?php
                    echo isset($user) ? "Welcome <a href='home'>" . $user->getFullname() . "</a> [ <a href='login_exec'>Logout</a> ]" :
                            'Already have an account? <a href="login">Login Here</a> | <a href="signup-personal">Sign up</a>'
                    ?></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="logo" id="logo"><img alt=""></span></div>
            </div>
            <div class="index-intro">
                <form action="index-search-results.php" method="GET" id="searchForm" onsubmit="return validate();">
                    <div class="index-intro-1">
                        <h1>Oops, we couldn't find the page you were looking for. Go to our <a href="home">homepage</a> or search for it below</h1>


                        <input class="main-search" type="text" placeholder="..." name="g" autocomplete="off" id="searchField">

                        <span><center><input class="button-big" id="search-field-submit" type="submit" value="Search" /> <!--<button class="button-big"><a href="">Sign up</a></button>--></center></span>
                        <div class="clear"></div>

                    </div>
                </form>
                </br>
                </br>
                </br>

                <div class="index-shadow-bottom"></div>
                <div class="index-content-wrapper">

                    <?php
                    include("footer.php");
                    ?>
                </div>

            </div>
    </body>
</html>