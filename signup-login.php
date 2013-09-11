<?php
if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['gender']) && (is_numeric($_POST['dob_day']) && $_POST['dob_day'] <= 31) && (is_numeric($_POST['dob_month']) && $_POST['dob_month'] <= 12) && (is_numeric($_POST['dob_yr']) && $_POST['dob_yr'] > 1959)) {
    if ($_POST['dob_day'] > 0 && $_POST['dob_yr'] > 0 && trim($_POST['first_name']) != "" && trim($_POST['last_name']) != "") {
        $_SESSION['data'] = $_POST;
    } else {
        $_SESSION['signup_perosnal_error']['message'] = "Month or Year must be entered correctly";
        $_SESSION['signup_perosnal_error']['data'] = $_POST;
        header("Location: signup-personal?signup_error=1");
    }
} else {
    if (!isset($_GET['signup_login_error'])) {
        $_SESSION['signup_perosnal_error']['message'] = "All fields in this stage are required";
        $_SESSION['signup_perosnal_error']['data'] = $_POST;
        header("Location: signup-personal?signup_error=1");
        exit;
    }
}
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
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.2.css">
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
                                You're almost there...! 
                            </h1>
                        </div>	
                        <progress max="100" value="50" >2 of 3 Completed</progress>
                        <hr>
                        <form id="formID" class="formular" action="signup-photo" method="POST">
                            <ul>
                                <li>
                                    <label for="email">eMail Address</label>
                                    <input  name="email" type="email" spellcheck="false" placeholder="e.g   your.email@example.com" class="validate[required,custom[email],ajax[ajaxUserCallPhp]] text-input input-fields" value="<?php echo isset($_SESSION['signup_login_error']['data']['email']) ? $_SESSION['signup_login_error']['data']['email'] : "" ?>" maxlength="50" required /> 
                                </li>
                                <li>
                                    <label for="password">Password</label>
                                    <input  name="password" type="password" placeholder="Minimum of 6 characters" spellcheck="false" class="validate[required,minSize[6]] text-input input-fields" value="" min="6" maxlength="255" required id="password"/> 
                                </li>
                                <li>
                                    <label for="cpassword">Confirm Password</label>
                                    <input  name="cpassword" type="password" placeholder="Re-type password" spellcheck="false" class="validate[required,equals[password]] text-input input-fields" value="" min="6" maxlength="255" required /> 
                                </li>
                            </ul>
                            <br>
                            <input class="button-big" type="submit" value="Next!"/>
                        </form>
                        <div class="clear"></div>
                    </div>
                </div>
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
unset($_SESSION['signup_login_error']);
?>