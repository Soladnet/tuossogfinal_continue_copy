<?php
include_once './encryptionClass.php';
include_once './GossoutUser.php';
$encrypt = new Encryption();
$tokenSupplied = $_GET['param'];
$decodedToken = $encrypt->decode($tokenSupplied);
$decodedTokenSplit = explode('-', $decodedToken);
$arr = array("status" => FALSE, "msg" => "This link is invalid or has expired. Please request for new password reset link <a href='password-recovery'>here</a>");
if (count($decodedTokenSplit) == 4) {
    if (is_numeric($decodedTokenSplit[1])) {
        $user = new GossoutUser($decodedTokenSplit[1]);
        $user->getProfile();
        $dbValueForPasswordReset = $user->getUnExpiredPasswordResetInfo();
        if ($dbValueForPasswordReset['status']) {
            $arr['status'] = TRUE;
            $user->makePasswordTokenExpire($tokenSupplied);
        } else {
            $arr['msg'] = "This link has expired. Please request for new password reset link <a href='password-recovery'>here</a>";
        }
    }
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Password Reset</title>
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.css">
        <link rel="stylesheet" href="css/validationEngine.jquery.css">
        <link rel="stylesheet" href="css/jackedup.css">
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script type="text/javascript" src="scripts/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="scripts/jquery.form.js"></script>
        <script type="text/javascript" src="scripts/languages/jquery.validationEngine-en.js"></script>
        <script type="text/javascript" src="scripts/jquery.validationEngine.js"></script>
        <script type="text/javascript" src="scripts/humane.min.js"></script>
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    dataType: "json"
                });

                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
                $("#passwordForm").validationEngine();
                var bar = $('.bar');
                var percent = $('.percent');
                $("#changePassForm").ajaxForm({
                    beforeSend: function() {
                        $("#profileText").show();
                        var percentVal = '0%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                    }, uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                    success: function(responseText, statusText, xhr, $form) {
                        var percentVal = '100%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                        var msg = "";
                        if (responseText.status) {
                            msg = responseText.message ? responseText.message : "Profile updated successfully";
                            humane.log(msg, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-success'});
                        } else {
                            if (responseText.error) {
                                humane.log(responseText.error.message, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                            } else {
                                msg = responseText.message ? responseText.message : "Profile was not updated";
                                humane.log(msg, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                            }
                        }
                        $(":password").val("");
                        if (responseText.status)
                            $(".registration").html("<p class='success'>" + msg + "</p>");
                    },
                    complete: function(xhr) {
                        $("#profileText").html("");
                    },
                    data: {
                        param: "settings"
                    }
                });
            });
        </script>
        <style>
            .progress { position:relative; width:400px; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
            .bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
            .percent { position:absolute; display:inline-block; top:3px; left:48%; }
        </style>
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
                        <?php
                        if ($arr['status']) {
                            ?>
                            <div class="index-intro-1">
                                <h1>
                                    Password Reset
                                </h1>
                                <hr>
                            </div>
                            <h2><?php echo $user->getFullname() ?></h2>
                            <h4><a><?php echo $user->getEmail() ?></a></h4>
                            <hr>
                            <form id="changePassForm" method="POST" action="tuossog-api-json.php">
                                <ul>						
                                    <li>
                                        <label for="password">New Password</label>
                                        <input  name="uid" type="hidden" value="<?php echo $encrypt->safe_b64encode($user->getId()) ?>" /> 
                                        <input  name="npass" type="password" placeholder="Minimum of 6 characters" spellcheck="false" class="input-fields validate[required,minSize[6]]" min="6" required id="password" /> 
                                    </li>
                                    <li>
                                        <label for="cpassword">Confirm Password</label>
                                        <input  name="cnpass" type="password" placeholder="Re-type password" spellcheck="false" class="input-fields validate[required,equals[password],minSize[6]"  min="6" required /> 
                                    </li>
                                    <input class="button-big" type="submit" value="Update Passport" />
                                </ul>
                                <div class="progress" id="profileText" style="display: none">
                                    <div class="bar"></div >
                                    <div class="percent">0%</div >
                                </div>
                            </form>
                            <div class="clear"></div>
                            <?php
                        } else {
                            ?>
                            <p class="error"><?php echo $arr['msg'] ?></p>
                            <?php
                        }
                        ?>
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