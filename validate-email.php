<?php
if (isset($_SESSION['auth']['token']) && isset($_SESSION['auth']['email'])) {
    $token = $_SESSION['auth']['token'];
    $email = $_SESSION['auth']['email'];
    $url = "http://www.gossout.com/signup-agreement/" . $token;
    $name = toSentenceCase(trim($_SESSION['auth']['firstname'] . ' ' . $_SESSION['auth']['lastname']) . ',');
    $msg = "<!doctype html><html><head><meta charset='utf-8'><style>a:hover{color: #000;}a:active , a:focus{color: green;}.index-functions:hover{/*cursor: pointer;*/ color: #99C43D !important;-webkit-box-shadow: inset 0px 0px 1px 1px #ddd;box-shadow: inset 0px 0px 1px 1px #ddd;}.index-functions:active{color: #C4953D !important;-webkit-box-shadow: inset 0px 0px 1px 2px #ddd;box-shadow: inset 0px 0px 1px 2px #ddd;}/*********************************************/ </style></head><body style='font-family: 'Segoe UI',sans-serif;background-color: #f9f9f9;color: #000000;line-height: 2em;'><div style='max-width: 800px;margin: 0 auto;background-color: #fff;border: 1px solid #f2f2f2;padding: 10px'><div class='header'><img style='float: right;top: 0px;' src='http://www.gossout.com/images/gossout-logo-text-and-image-Copy.png'/><br><h2>Email Verification</h2><p style='margin: 3px;'><span class='user-name'>Hi <a style='color: #62a70f;text-decoration: none;'>" . $name . "</a><br/><p>Welcome to gossout! To complete your registration, you need to verify your email. Please click the link below to verify your email.<br><br><a style='color: #62a70f;text-decoration: none;' href=$url>Click Here > ></a><br><br> Or copy and paste the url below into your browser's address bar: <br/><br>$url<br><br></p><p> </p> Thanks<br/> Gossout Team </span></p><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'></div><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'><div style='background-color: #f9f9f9;padding: 10px;font-size: .8em;'><center><div class='index-intro-2'><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://www.gossout.com/images/community-resize.png'/></span></div><h3 style='text-align: center;height: 1em;'>Discover</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Communities &Friends</p></div><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://www.gossout.com/images/connect-pple.png'/></span></div><h3 style='text-align: center;height: 1em;'>Connect</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Meet People, Share Interests</p></div><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://www.gossout.com/images/search.png'/></span></div><h3 style='text-align: center;height: 1em;'>Search</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Communities, People and Posts</p></div></div></center><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'><table cellspacing='5px'><tr ><td colspan='3'>&copy " . date('Y') . "<a style='color: #62a70f;text-decoration: none;' href='http://www.gossout.com'>Gossout</a></td></tr></table></div></div></body></html>";
    $to = "$name<$email>";
    $subject = "Email Verification";
    $headers = "From: Gossout Team<feedback@gossout.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    $res = @mail($to, $subject, $msg, $headers);
} else {
    $notSet = FALSE;
}

function toSentenceCase($str) {
    $arr = explode(' ', $str);
    $exp = array();
    foreach ($arr as $x) {
        if (strtolower($x) == "of") {
            $exp[] = strtolower($x);
        } else {
            if (strlen($x) > 0) {
                $exp[] = strtoupper($x[0]) . substr($x, 1);
            } else {
                $exp[] = strtoupper($x);
            }
        }
    }
    return implode(' ', $exp);
}

//
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Signup 2/3</title>
        <meta name="description" content="Start or join existing communities/interests on Gossout and start sharing pictures and videos. People use Gossout search, Discover and connect with communities">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <link rel="stylesheet" media="screen" href="css/style.min.css">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <?php
        if (isset($_SESSION['signup_login_error'])) {
            ?>
            <link rel="stylesheet" href="css/jackedup.css">
            <script type="text/javascript" src="scripts/humane.min.js"></script>
            <script>
                humane.log("<?php echo $_SESSION['signup_login_error']['message']; ?>", {timeout: 10000, clickToClose: true, addnCls: 'humane-jackedup-error'});
            </script>
            <?php
        }
        ?>
        <link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
        <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <script src="scripts/languages/jquery.validationEngine-en.js" type="text/javascript"></script>
        <script src="scripts/jquery.validationEngine.js" type="text/javascript"></script>
        <script>
            $(document).ready(function() {
                jQuery("#formID").validationEngine();
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
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
                <span class="index-login">Already have an account? <a href="login">Login Here!</a></span>
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
                                Email verification
                            </h1>
                        </div>	
                        <!--<progress max="100" value="50" >2 of 3 Completed</progress>-->
                        <hr>
                        <?php
                        if (!isset($notSet)) {
                            ?>
                            <form id="formID" class="formular" action="signup-photo" method="POST">
                                <ul>
                                    <li>
                                        <label for="email">An email verification link has been sent to <?php echo "<a>$email</a>" ?>, you need to click on this link to verify you email.</label>

                                    </li>
                                </ul>
                                <!--                            <div class="button"><a href="validate-email">Skip</a></div>-->
                                <div class="button"><a href="validate-email">Resend Link</a></div>
                                <div class="button"><a href="signup-agreement">Skip and Verify Later</a></div>
                                <br>
                                <!--<input class="button-big" type="submit" value="Take me in"/>-->
                            </form>
                            <?php
                        } else {
                            ?>
                            <ul>
                                <li>
                                    <p class="error">Oops! Your session have expired. Please <a href="login">login</a> and try again.</p>
                                </li>
                            </ul>
                            <?php
                        }
                        ?>
                        <div class="clear"></div>
                    </div>
                </div>
                <?php if (false) { ?>

                <?php } ?>

            </div>
            <div class="index-shadow-bottom"></div>
            <div class="index-content-wrapper">
                <?php
                include("footer.php");
                ?>
            </div>

        </div>
    </body>
</html>
<?php
//unset($_SESSION['signup_login_error']);
?>