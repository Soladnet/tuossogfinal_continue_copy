<?php
include_once './GossoutUser.php';
include_once './encryptionClass.php';
$user = new GossoutUser(0);
$user->setEmail($_POST['email']);
$id = $user->getId();
$arr = array("status" => FALSE, "msg" => "Invalid email address or email is not attached to any account");
if ($id != "") {
    $encrypt = new Encryption();
    $user->setUserId($id);
    $user->getProfile();
    $resetToken = $encrypt->encode($user->getToken() . "-" . $user->getId() . "-" . $_POST['email'] . "-" . time());
    $prevResetInfo = $user->getUnExpiredPasswordResetInfo();
    $fullname = trim($user->getFullname());
    $headers = "From: Password reset request<no-reply-reset-password@gossout.com>\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
    if ($prevResetInfo['status']) {
        $arr['status'] = TRUE;
        $arr['msg'] = "A confirmation link have been sent to <strong>$_POST[email]</strong>. Login to your email client to continue.<br/>In order to keep your Gossout account secure, you'll have to confirm that <strong>$_POST[email]</strong> is attached to your account.";
        $to = "$fullname<$_POST[email]>";
        $subject = "Password reset request";
        $htmlHead = "<!doctype html><html><head><meta charset='utf-8'><style>a:hover{color: #000;}a:active , a:focus{color: green;}.index-functions:hover{/*cursor: pointer;*/ color: #99C43D !important;-webkit-box-shadow: inset 0px 0px 1px 1px #ddd;box-shadow: inset 0px 0px 1px 1px #ddd;}.index-functions:active{color: #C4953D !important;-webkit-box-shadow: inset 0px 0px 1px 2px #ddd;box-shadow: inset 0px 0px 1px 2px #ddd;}</style></head><body style='font-family: 'Segoe UI',sans-serif;background-color: #f9f9f9;color: #000000;line-height: 2em;'><div style='max-width: 800px;margin: 0 auto;background-color: #fff;border: 1px solid #f2f2f2;padding: 10px'><div class='header'><img style='float: right;top: 0px;' src='http://service.gossout.com/images/gossout-logo-text-and-image-Copy.png'/><br><h2>Password reset request, </h2><p style='margin: 3px;'><span class='user-name'>Hi, <a style='color: #62a70f;text-decoration: none;'>$fullname</a></span>, we received a password reset request on your behalf. If you initiated this process, please click on the confirmation link below to continue, else ignore this email as we will stop the process if we do not hear from you in the next 24hour.</p><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'></div><div style='background-color: #fff;padding: 1em;'><p style='margin: 3px;font-size: .9em;'><strong>Link:</strong></p><p style='margin: 3px;font-size: .9em;'><a style='color: #62a70f;text-decoration: none;' href='http://gossout.com/password-reset/$resetToken'>Click here to confirm password request</a></p><p>OR</p><p style='margin: 3px;font-size: .9em;'><strong>Copy the link below and paste into your browser address bar</strong></p><p style='margin: 3px;font-size: .9em;'>http://gossout.com/password-reset/$resetToken</p></div><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'><div style='background-color: #f9f9f9;padding: 10px;font-size: .8em;'><center><div class='index-intro-2'><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/community-resize.png'/></span></div><h3 style='text-align: center;height: 1em;'>Discover</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Communities &Friends</p></div><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/connect-pple.png'/></span></div><h3 style='text-align: center;height: 1em;'>Connect</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Meet People, Share Interests</p></div><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/search.png'/></span></div><h3 style='text-align: center;height: 1em;'>Search</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Communities, People and Posts</p></div></div></center><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'><table cellspacing='5px'><tr ><td colspan='3'> ©<?php echo date('Y');?><a style='color: #62a70f;text-decoration: none;' href='http://www.gossout.com'>Gossout</a></td></tr></table></div></div></body></html>";
        @mail($to, $subject, $htmlHead, $headers);
    } else {
        $res = $user->addResetInfor($resetToken);
        if ($res['status']) {
            $arr['status'] = TRUE;
            $arr['msg'] = "A confirmation link have been sent to <strong>$_POST[email]</strong>. Login to your email client to continue.<br/>In order to keep your Gossout account secure, you'll have to confirm that <strong>$_POST[email]</strong> is attached to your account.";
            $to = "$fullname<$_POST[email]>";
            $subject = "Password reset request";
            $htmlHead = "<!doctype html><html><head><meta charset='utf-8'><style>a:hover{color: #000;}a:active , a:focus{color: green;}.index-functions:hover{/*cursor: pointer;*/ color: #99C43D !important;-webkit-box-shadow: inset 0px 0px 1px 1px #ddd;box-shadow: inset 0px 0px 1px 1px #ddd;}.index-functions:active{color: #C4953D !important;-webkit-box-shadow: inset 0px 0px 1px 2px #ddd;box-shadow: inset 0px 0px 1px 2px #ddd;}</style></head><body style='font-family: 'Segoe UI',sans-serif;background-color: #f9f9f9;color: #000000;line-height: 2em;'><div style='max-width: 800px;margin: 0 auto;background-color: #fff;border: 1px solid #f2f2f2;padding: 10px'><div class='header'><img style='float: right;top: 0px;' src='http://service.gossout.com/images/gossout-logo-text-and-image-Copy.png'/><br><h2>Password reset request, </h2><p style='margin: 3px;'><span class='user-name'>Hi, <a style='color: #62a70f;text-decoration: none;'>$fullname</a></span>, we received a password reset request on your behalf. If you initiated this process, please click on the confirmation link below to continue, else ignore this email as we will stop the process if we do not hear from you in the next 24hour.</p><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'></div><div style='background-color: #fff;padding: 1em;'><p style='margin: 3px;font-size: .9em;'><strong>Link:</strong></p><p style='margin: 3px;font-size: .9em;'><a style='color: #62a70f;text-decoration: none;' href='http://gossout.com/password-reset/$resetToken'>Click here to confirm password request</a></p><p>OR</p><p style='margin: 3px;font-size: .9em;'><strong>Copy the link below and paste into your browser address bar</strong></p><p style='margin: 3px;font-size: .9em;'>http://gossout.com/password-reset/$resetToken</p></div><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'><div style='background-color: #f9f9f9;padding: 10px;font-size: .8em;'><center><div class='index-intro-2'><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/community-resize.png'/></span></div><h3 style='text-align: center;height: 1em;'>Discover</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Communities &Friends</p></div><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/connect-pple.png'/></span></div><h3 style='text-align: center;height: 1em;'>Connect</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Meet People, Share Interests</p></div><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/search.png'/></span></div><h3 style='text-align: center;height: 1em;'>Search</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Communities, People and Posts</p></div></div></center><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'><table cellspacing='5px'><tr ><td colspan='3'> ©<?php echo date('Y');?><a style='color: #62a70f;text-decoration: none;' href='http://www.gossout.com'>Gossout</a></td></tr></table></div></div></body></html>";
            @mail($to, $subject, $htmlHead, $headers);
        } else {
            $arr['status'] = FALSE;
            $arr['msg'] = "Password reset is temporaryly out of service. Please try again some other time.";
            @mail("feedback@gossout.com", "Password reset attempt failed", "<!doctype html><html><body></p>Password reset attempt failed. More Information below</p><p>Fullname: $fullname</p><p>User ID: $id</p><p>Token: $resetToken</p><p>Server Error: $res[msg]</p></body></html>", $headers);
        }
    }
}
?>
<!doctype html>
<html>
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Password Reset</title>
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.css">
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script type="text/javascript" src="scripts/jquery-1.9.1.min.js"></script>
        <script>
            $(document).ready(function() {
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
                <span class="index-login">No account? <a href="signup-personal">Signup Here!</a> | <a href="login">Login here</a></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="index-logo">
                    <div class="logo" id="logo"><img alt=""></div>
                </div>
            </div>
            <div class="index-intro">


                <div class="index-intro-2">
                    <div class="registration">
                        <div class="index-intro-1">
                            <h1>
                                Password Recovery!
                            </h1>
                            <hr>
                        </div>

                        <p class="<?php echo $arr['status'] ? "success" : "error" ?>">
                            <?php
                            echo $arr['msg'];
                            ?>
                        </p>

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