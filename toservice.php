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
        <title>Gossout - We care About you and your community!</title>
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
            <center><h3>Terms of Service</h3></center>
            <p>&nbsp;</p>           
            <p>&nbsp;</p>           
            <p>&nbsp;</p>
            <div class="index-intro">

                <?php  ?>
                    <div class="index-intro-2-container">
                        <div class="index-intro-2" id='index-three-icon' style="max-width:700px;">
                            <a href='terms'>
                                <div class="index-functions" id="index-search-icon" style='width:230px;'>
                                    <div style="margin: 0 auto; width:68px">
                                        <img src='images/tos_other.png' alt='privacy'>
                                    </div>
                                    <h3>Terms</h3>
                                    <center><p>Terms of Use</p></center>
                                </div>    
                            </a>
                            <a href='privacy'>
                                <div class="index-functions" id="index-search-icon">
                                    <div style="margin: 0 auto; width:56px">
                                        <img src='images/tos_privacy.png' alt='privacy'>
                                    </div>
                                    <h3>Privacy Policy</h3>
                                    <center><p>Privacy & Confidentiality</p></center>
                                </div>    
                            </a>
                            <a href='rights'>
                                <div class="index-functions" id="index-search-icon" style='width:230px;'>
                                    <div style="margin: 0 auto; width:56px">
                                        <img src='images/tos_rights.png' alt='privacy'>
                                    </div>
                                    <h3>Rights</h3>
                                    <center><p>Rights & Responsibilities</p></center>
                                </div>    
                            </a>
                            <div class="clear"></div>
                        </div>
                      
                    </div>
                
                <?php  ?>
                    <p>&nbsp;</p>
<!--                    <center>
                        <div style='border-radius:5px;border:1px #ccc solid;background: white;width: 90%;height: auto;text-align: left;'>
                           <div style="margin: 2em;margin-top:1em;text-align:justify">
                               Your privacy is very important to us.  Any information you provide to Gossout is subject to our Privacy Policy, which governs our collection and use of your information. You understand that through your use of the Services you consent to the collection and use (as set forth in the Privacy Policy) of this information, including the transfer of this information to Nigeria and/or  other countries for storage, processing and use by Gossout. As part of providing you the Services, we may need to provide you with certain communications, such as Service announcements and administrative messages. These communications are considered part of the Services and your Gossout account, which you may not be able to opt out from receiving.   
                                <ul style="list-style-type:disc">
                                     <li style='text-align:justify'><strong>SHARING YOUR CONTENT AND INFORMATION</strong><br/>
                                        All Content, whether publicly posted or privately transmitted, is the sole responsibility of the person who originated such Content. We may not monitor or control the Content posted via the Services and, we cannot take responsibility for such Content. Any use or reliance on any Content or materials posted via the Services or obtained by you through the Services is at your own risk.
                                        We do not endorse, support, represent or guarantee the completeness, truthfulness, accuracy, or reliability of any Content or communications posted via the services or endorse any opinions expressed via the Services. You understand that by using the Services, you may be exposed to Content that may be offensive, harmful, inaccurate or otherwise inappropriate, or in some cases postings 
                                        that have been mislabelled or are otherwise deceptive. Under no circumstances will Gossout be liable in any way for any Content, including, but not limited to, any errors or omissions in any Content posted, emailed, transmitted, or otherwise made available via the Services or broadcast elsewhere.
                                     </li>
                                    <li><strong>SAFETY MEASURES</strong><br/>
                                        We do our best to keep Gossout safe, but we cannot guarantee it. We need your help to keep Gossout safe, which includes the following commitments by you:
                                        <ul style="margin-left:2em;list-style-type:lower-roman;">
                                            <li>You will not post unauthorised commercial communications (such as 	spam) on Gossout</li>
                                            <li>You will not collect users' content or information, or otherwise access Gossout, using automated means (such as harvesting bots, robots, spiders, or scrapers) without our prior permission. </li>
                                            <li>You will not engage in unlawful multi-level marketing, such as pyramid scheme on Gossout. </li>
                                            <li>You will not upload viruses or other malicious code. </li>
                                            <li>You will not solicit login information or access an account belonging to some else. </li>
                                            <li>You will not bully, intimidate, or harass any user. </li>
                                            <li>You will not post content that: is hate speech, threatening or pornographic; incites violence, or contains nudity or graphic or gratuitous violence.</li>
                                            <li>You will not develop or operate a third-party application containing alcohol-related, dating or other mature content (including advertisements) without appropriate age-based restrictions. </li>
                                            <li>You will not use Gossout to do anything unlawful, misleading, malicious, or discriminatory </li>
                                            <li>You will not do anything that could disable, overburden, or impair the 		proper working or appearance of Gossout, such as denial of service attack or interference with page rendering or other Gossout functionality. </li>
                                        </ul>
                                    </li>
                                    <li><strong>REGISTRATION AND ACCOUNT SECURITY</strong><br/>
                                        Gossout users provide their real names and information, and we need your help to keep it that way. Here are some commitments you make to us relating to registering and maintaining the security of your account:
                                        <ul style="margin-left:2em;list-style-type:lower-roman;">
                                            <li>You will not provide any false personal information on Gossout, or create an account for anyone other than yourself without permission.</li>
                                            <li>You will not create more than one personal account.</li>
                                            <li>If we disable your account, you will not create another one without our permission.</li>
                                            <li>You will not use Gossout if you are under 13 </li>
                                            <li>You will not use Gossout if you are convicted sex offender.</li>
                                            <li>You will keep your contact information accurate and up-to-date.</li>
                                            <li>You will not share your password (or in the case of developers, your secret key), let anyone else access your account, or do anything else that might jeopardise the security of your account.</li>
                                            <li>You will not transfer your account (including any Page or application 	you administer) to anyone without first getting our written permission, i.e. the user must obtain your approval when a user carries out any activity that is likely to give rise to legal issues.</li>
                                            <li>If you select a username or similar identifier for your account or Page, we reserve the right to remove or reclaim it if we believe it is appropriate (such as when a trademark owner complains about a user name that does not closely relate to a user's actual name).</li>
                                           
                                        </ul>
                                    </li>
                                    
                                </ul>
                            </div> 
                        </div>
                    </center>-->
                    
                <?php  ?>
            </div>
              <p>&nbsp;</p>
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