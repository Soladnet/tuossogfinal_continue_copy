<?php
if (session_id() == "") {
    session_name('GSID');
    session_start();
}
$ip = $_SERVER['REMOTE_ADDR'];
$json = @file_get_contents('http://smart-ip.net/geoip-json/' . $ip);
$ipData = json_decode($json, true);
$timezone = "Africa/Lagos";
if ($ipData['timezone']) {
    $timezone = $ipData['timezone'];
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Login</title>
        <meta name="description" content="Login and start experiencing your communities! Gossout he future!">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.1.css">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <?php
        if (isset($_SESSION['login_error'])) {
            ?>
            <link rel="stylesheet" href="css/bigbox.css">
            <script type="text/javascript" src="scripts/humane.min.js"></script>
            <script>
                humane.log("Login failed", {timeout: 10000, clickToClose: true, addnCls: 'humane-bigbox-error'});
            </script>
            <?php
        }
        ?>
        <script type="text/javascript">
            function getTimeZone() {
                var timezone = (-(new Date().getTimezoneOffset())) / 60;
                return timezone;
            }
            $(document).ready(function() {
                $("#tz").val("<?php echo $timezone ?>");
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" />');
                } else {
                    $('#logo').html('<img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" />');
                }
            });
        </script>
    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login">No account? <a href="signup-personal">Signup Here!</a></span>
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
                                We knew you'd come back ;) How've you been?
                            </h1>
                            <hr>
                        </div>
                        <form action="login_exec" method="post">
                            <ul>
                                <li>
                                    <label for="email">e-mail Address</label>
                                    <input class="input-fields" name="email" placeholder="email@awesome.com" type="text" value="" spellcheck="false" required/>
                                </li>
                                <li>
                                    <label for="password">Password</label>
                                    <input class="input-fields" name="password" placeholder="******" type="password" value="" spellcheck="false" required/>
                                    <input name="tz" type="hidden" id="tz" />
                                </li>
                                <li><input type="checkbox" name="remember" value="TRUE"> Remember me</li>
                            </ul>
                            <input class="button-big" type="submit" value="Login">
                            <p class="float-right"><a href="password-recovery">Forgot Password?...</a></p>
                            <div class="clear"></div>						
                        </form>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="index-content-wrapper">
                <?php
                include("footer.php");
                ?>
            </div>

        </div>
    </body>
</html>
<?php
unset($_SESSION['login_error']);
?>