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
<html lang="en">
    <head>
        <title>Gossout - Rights</title>
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.css">
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
        </script>
        <style>
            ul li{
                margin-top: .5em;
            }
        </style>
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
            <center><h3>Rights and Responsibilities</h3></center>
            <p>&nbsp;</p>           
            <p>&nbsp;</p>           
            <p>&nbsp;</p>
            <div class="index-intro">

                <?php ?>

                <?php ?>
                <p>&nbsp;</p>
                <center>
                    <div style='border-radius:5px;border:1px #ccc solid;background: white;width: 90%;height: auto;text-align: left;'>
                        <div style="margin: 2em;margin-top:1em;text-align:justify">
                            This Statement of Rights and Responsibilities derives from the Gossout Principles, and is our Terms of Service that governs our relationship with users and others who interact with Gossout. By using or accessing Gossout, you agree to this Statement (Terms of Service), as updated from time to time.
                            <ul style="list-style-type:disc">
                                <li style='text-align:justify'><strong>YOUR RIGHTS</strong><br/>
                                    <ul style='list-style-type:lower-roman;'>
                                        <li>You retain your rights to any Content you submit, post or display on or through the Services. By submitting,  posting or displaying Content on or through the Services, you grant us a worldwide, non-exclusive, royalty-free license (with the right to sublicense) to use, copy, reproduce, process, adapt, modify, publish, transmit, display and distribute such Content in any and all media or distribution methods (now known or later developed).</li>

                                        <li>You agree that this license includes the right for Gossout to provide, promote, and improve the Services and to make Content submitted to or through the services available to other companies, organizations or individuals who partner with Gossout for the syndication, broadcast, distribution or publication of such Content on other media services, subject to our terms and conditions for such Content use.</li>

                                        <li>You are responsible for your use of the Services, for any Content you provide, and for any consequences thereof, including the use of your Content by other users and our third party partners. You understand that your content may be syndicated, broadcast, distributed, or published by our partners and if you do not have the right to submit Content for such use, it may subject you to liability. Gossout will not be responsible or liable for any use of your content by Gossout in accordance with these terms. You represent and warrant that you have all the rights, power and authority necessary to grant the rights herein to any content that you submit.</li>
                                    </ul>

                                </li>

                                <li style='text-align:justify'><strong>GOSSOUT'S RIGHTS</strong><br/>
                                    <ul>
                                        <li>All right, title, and interest in and to the Services (excluding Content provided by users) are and will remain the exclusive property of Gossout and its licensors. The Services are protected by copyright, trademark and other laws of both the Federal Republic of Nigeria and foreign countries. Nothing in the terms gives you a right to use the Gossout name or any of the Gossout trademarks, logos, domain names, and other distinctive brand features. Any feedback, comments, or suggestions you may provide regarding Gossout, or the Services is entirely voluntary and we will be free to use such feedback, comments or suggestions as we see fit and without any obligation to you.</li>
                                    </ul>

                                </li>
                                <li style='text-align:justify'><strong>PROTECTING OTHER PEOPLES RIGHTS</strong><br/>
                                    We respect other people's rights and expect you to do the same. In this regard the following holds:
                                    <ul style="margin-left:2em;list-style-type:lower-roman;">
                                        <li>You will not post Content or take any action on Gossout that infringes or violates someone else's rights or otherwise violates the law</li>

                                        <li>We can remove any Content or information you post on Gossout if we believe that it violates this Statement or our policies.</li>

                                        <li>If we remove your content for infringing someone else's copyright, and you believed we removed it by mistake, we will provide you with an opportunity to appeal.</li>
                                        <li>If you repeatedly infringe other people's intellectual property rights, we will disable your account when appropriate.</li>

                                        <li>You will not use our copyrights or trademarks, or any confusingly similar marks, without our prior written permission. </li>

                                        <li>you collect information from users, you will, obtain their consent, make it clear you (and not Gossout) are the one collecting their information, and post a privacy policy explaining what information you collect and how you will use it.</li>
                                        <li>You will not post anyone's identification documents or sensitive financial information on Gossout.</li>

                                        <li>You will not tag users or send email invitations to non-users without 	their consent. </li>
                                    </ul>
                                    <p>&nbsp;</p>   
                                    <p>Effective: 8th May 2013.</p>
                                </li>

                            </ul>
                        </div> 
                    </div>
                </center>
                <p>&nbsp;</p>

                <?php ?>
            </div>
            <div class="index-shadow-bottom"></div>
            <div class="index-content-wrapper">
                <span id="footer-links">
                    <?php
                    include("footer.php");
                    ?>
                </span>
            </div>


        </div>

    </body>
</html>