<?php
include_once './webbase.php';
include_once 'Config.php';
$token = clean($_GET['param']);
include_once './Config.php';

function clean($value) {
    if (!get_magic_quotes_gpc()) {
        $value = addslashes($value);
    }
    $value = strip_tags($value);
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

$bulkReg = FALSE;
if ($token != "") {
    $ip = $_SERVER['REMOTE_ADDR'];
    $json = file_get_contents('http://smart-ip.net/geoip-json/' . $ip);
    $ipData = json_decode($json, true);
    $timezone = "Africa/Lagos";
    if ($ipData['timezone']) {
        $timezone = $ipData['timezone'];
    }
    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
    if ($mysql->connect_errno > 0) {
        throw new Exception("Connection to server failed!");
    } else {
        $string1 = "SELECT * FROM (SELECT userId, activated, firstname, lastname, email, dob, token from success_uploaded_users JOIN user_login_details ON userId = user_login_details.`id` JOIN user_personal_info ON userId = user_personal_info.id) as md WHERE token = '$token'";
        if ($run1 = $mysql->query($string1)) {
            if ($run1->num_rows == 1) {
                $rows = $run1->fetch_assoc();
                if ($rows['activated'] === 'N') {
                    $s = "Select unique_name, commId, userId From success_uploaded_users JOIN bulk_registration ON uploadId = bulk_registration.id JOIN community on commId = community.id JOIN user_login_details ON userId = (SELECT id  From user_login_details WHERE  `token` = '$token') Limit 1";
                    if ($r = $mysql->query($s)) {
                        if ($r->num_rows == 1) {
                            $rs = $r->fetch_assoc();
                            $commId = encodeText($rs['commId']);
                            $userId = encodeText($rs['userId']);
                            $helve = $rs['unique_name'];//
                            $bulkReg = TRUE;
                            $vEmail = $rows['email'];
                            list($year, $month, $day) = explode('-', $rows['dob']);
                            $month = (int) $month;
                        }
                    }
                } else {
                    include_once '404.php';
                    exit();
                }
            } else {
                $str = "Update user_login_details SET activated = 'Y' WHERE token = '$token' AND activated = 'N'";
                $str1 = "SELECT id from user_login_details WHERE token = '$token'";
                if ($run1 = $mysql->query($str1)) {
                    if ($run1->num_rows == 1) {
                        if ($run = $mysql->query($str)) {
                            if ($mysql->affected_rows == 1) {
                                $_SESSION['verified'] = 'Verified';
                            } else {
                                include_once '404.php';
                                exit();
                            }
                        }
                    } else {
                        include_once '404.php';
                        exit();
                    }
                }
            }
        }
    }
}
?>
<!doctype html>

<html lang="en">
    <head>
        <?php ?>
        <title>Gossout Account Verification</title>
        <meta name="description" content="Start or join existing communities/interests on Gossout and start sharing pictures and videos. People use Gossout search, Discover and connect with communities">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.css">
        <link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
        <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <script src="scripts/languages/jquery.validationEngine-en.js" type="text/javascript"></script>
        <script src="scripts/jquery.validationEngine.js" type="text/javascript"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script>
            $(function() {
                jQuery("#formID1").validationEngine();
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
        <?php if (!$bulkReg) { ?>
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
                                        We use <a target="_blank" href="http://en.wikipedia.org/wiki/HTTP_cookie">cookies</a>  to ensure that we give 
                                        you the best experience on our website. <!-- We also use cookies 
                                        to ensure we show you advertising that is relevant to you. --> 
                                        If you continue, we'll assume that you 
                                        are happy to receive all <a target="_blank" href="http://en.wikipedia.org/wiki/HTTP_cookie">cookies</a> on this website. 

                                    </p>
                                </li>
                            </ul>
                            <?php
                            if (!isset($_COOKIE['user_auth']))
                                echo '<div class="button"><a href="login">Login here!</a></div>';
                            else
                                echo '<div class="button"><a href="home">Finish!</a></div>';
                            ?>
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
        <?php }else { ?>
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
                                    Welcome! Verify once to have all of Gossout. 
                                </h1>
                                <?php
                                if (isset($_SESSION['error'])) {
                                    ?>
                                    <div class="error"><center><?php
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                                    ?></center></div>
                                    <?php
                                }
                                ?>
                            </div>	
                            <hr>
                            <form id="formID1" class="formular" action="login_exec" method="POST">
                                <ul>
                                    <li>
                                        You have been subscribed to <?php echo $helve; ?> community. Would you love to be part if this community?<br> 
                                        <div style="margin-top:10px;"><label for="email"> Yes, add me <input style="margin:6px 40px 0 0;" type="radio" name="allowCom" value="1" checked="checked"> No, not now <input type="radio"  style="margin-top:6px;" name="allowCom" value="0"></label></div>
                                        <hr>
                                        <label for="email">eMail Address</label>
                                        <input type="text" name="mail" class="text-input input-fields" readonly="" value="<?php echo $vEmail; ?>">
                                    </li>
                                    <li>
                                        <label for="password">Password</label>
                                        <input  name="paword" type="password" placeholder="Minimum of 6 characters" spellcheck="false" class="validate[required,minSize[6]] text-input input-fields" value="" min="6" maxlength="255" required id="paword"/> 
                                    </li>

                                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                                    <input name="tz" type="hidden" id="tz" value="<?php echo $timezone ?>"/>
                                    <li>
                                        <label for="cpassword">Confirm Password</label>
                                        <input  name="cpaword" type="password" placeholder="Re-type password" spellcheck="false" class="validate[required,equals[paword]] text-input input-fields" value="" min="6" maxlength="255" required /> 
                                    </li>
                                    <li>
                                        <label for="dob">Date of Birth (mm-dd-yyyy)</label>
                                        <select name="dob_month" required>
                                            <option  <?php echo ($month === 1) ? "selected" : ""; ?> value="1">January</option>
                                            <option  <?php echo ($month === 2) ? "selected" : ""; ?> value="2">February</option>
                                            <option  <?php echo ($month === 3) ? "selected" : ""; ?> value="3">March</option>
                                            <option  <?php echo ($month === 4) ? "selected" : ""; ?> value="4">April</option>
                                            <option  <?php echo ($month === 5) ? "selected" : ""; ?> value="5">May</option>
                                            <option  <?php echo ($month === 6) ? "selected" : ""; ?> value="6">June</option>
                                            <option  <?php echo ($month === 7) ? "selected" : ""; ?> value="7">July</option>
                                            <option  <?php echo ($month === 8) ? "selected" : ""; ?> value="8">August</option>
                                            <option  <?php echo ($month === 9) ? "selected" : ""; ?> value="9">September</option>
                                            <option  <?php echo ($month === 10) ? "selected" : ""; ?> value="10">October</option>
                                            <option  <?php echo ($month === 11) ? "selected" : ""; ?> value="11">November</option>
                                            <option  <?php echo ($month === 12) ? "selected" : ""; ?> value="12">December</option>
                                        </select>
                                        <input type="number"  name="dob_day" min="1" max="31" size="2" required placeholder="DD" value="<?php echo isset($day) ? $day : ""; ?>"/>
                                        <input type="number" max="<?php echo date("Y") - 13 ?>" min="<?php echo date("Y") - 53; ?>" size="4" name="dob_yr" required placeholder="YYYY" value="<?php echo isset($year) ? $year : ""; ?>"/>
                                    </li>
                                </ul>
                                <br>
                                <input type="submit" id="search-field-submit" class="submit button" value="Verify" name="doVerify" style="font-size:20px;padding-right:20px;padding-left:20px;">
                                <input type="hidden" name="commId" value="<?php echo $commId; ?>">
                                       <input type="hidden" name="uId" value="<?php echo $userId ?>">
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
            <?php
        }
        $bulkReg = FALSE;
        ?>
    </body>
</html>

