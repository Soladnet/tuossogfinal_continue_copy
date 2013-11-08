<?php
header('Content-type: text/html; charset=UTF-8');
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
        <title>Welcome to Gossout</title>
        <meta name="description" content="Start or join existing communities/interests on Gossout and start sharing pictures and videos. People use Gossout search, Discover and connect with communities">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.2.css">
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <link rel=" stylesheet" type="text/css" href="css/joyride-2.0.3.css">
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <script src="scripts/jquery.joyride-2.0.3.js"></script>
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
        
            function setCookie(c_name, value, exdays)
            {
                var exdate = new Date();
                exdate.setDate(exdate.getDate() + exdays);
                var c_value = escape(value) + ((exdays === null) ? "" : "; expires=" + exdate.toUTCString());
                document.cookie = c_name + "=" + c_value;
            }

            function getCookie(c_name)
            {
                var c_value = document.cookie;
                var c_start = c_value.indexOf(" " + c_name + "=");
                if (c_start === -1)
                {
                    c_start = c_value.indexOf(c_name + "=");
                }
                if (c_start === -1)
                {
                    c_value = null;
                }
                else
                {
                    c_start = c_value.indexOf("=", c_start) + 1;
                    var c_end = c_value.indexOf(";", c_start);
                    if (c_end === -1)
                    {
                        c_end = c_value.length;
                    }
                    c_value = unescape(c_value.substring(c_start, c_end));
                }
                return c_value;
            }

            function callTour() {
                $('#joyRideTipContent').joyride({
                    autoStart: true
                });
            }

            if (getCookie('index_user_status') === null) {
                setCookie('index_user_status', 'new_user', 2592000);
                $(window).load(function() {
                    $('#joyRideTipContent').joyride({
                        autoStart: true
                    });
                });
            }
        </script>
    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login" id="name-login-cont"><?php
echo isset($user) ? "Welcome <a href='home'>" . $user->getFullname() . "</a> [ <a href='login_exec'>Logout</a> ]" :
        'Already have an account? <a href="login">Login Here</a> | <a href="signup-personal">Sign up</a>'
?> | <a href onclick='javascript:callTour();
                        return false;'>Take a Tour</a></span>
                <div class="clear"></div>
            </div><br>
            <div class="index-banner">
                <div class="logo" id="logo"><img alt=""></span></div>
            </div>
            <div class="index-intro">
                <form action="index-search-results.php" method="GET" id="searchForm" onsubmit="return validate();">
                    <div class="index-intro-1">
                        <h1>Join communities, share stories, photos, and opinions with people!
                        </h1>

                        <center>
                            <input class="main-search" type="text" placeholder="..." name="g" autocomplete="off" id="searchField" style="width:90%;">
                        </center>

                        <span><center>
                                <input type="submit" id="search-field-submit" class="submit button" value="Get result!" id="postBtn" style="font-size:20px;padding-right:20px;padding-left:20px;">
                        <div class="clear"></div>

                    </div>
                </form>
                <br><br>
                <center>
                    <div class="index-intro-2-container" style="width:600px;">
                        <div>	
                            <center>
                                <div class="index-functions" id="index-search-icon">
                                    <div style="margin: 0 auto;">
                                        <img src="images/search1.png" alt="Search"/>
                                    </div>
                                    <h3>Search</h3>
                                    <p>Communities and Friends</p>
                                </div> 

                                <div class="index-functions" id="index-discover-icon">
                                    <div style="margin: 0 auto;">
                                        <img src="images/discover.png" alt="Discover"/>
                                    </div>
                                    <h3>Discover</h3>
                                    <p>Communities &amp; Friends</p>
                                </div>

                                <div class="index-functions" id="index-connect-icon">
                                    <div style="margin: 0 auto;">
                                        <img src="images/share1.png" alt="Search"/>
                                    </div>
                                    <h3>Connect</h3>
                                    <p>Meet People, Share Interests</p>
                                </div>

                                <div class="clear"></div>
                            </center>
                        </div>
                    </div>
                </center>
            </div>
            <div class="index-shadow-bottom"></div>
            <div class="index-content-wrapper">
                <span id="footer-links">
                    <?php
                    include("footer.php");
                    ?>
                </span>
            </div>
            <div>
                <ol id="joyRideTipContent">

                    <li data-text="Next" data-id="name-login-cont">
                        <h2>Welcome!</h2>
                        <p>Welcome to Gossout! You can login or signup as a new user here. Click <strong>Next</strong> to continue or <strong>X</strong> to close this hint and have fun!</p>
                    </li>
                    <li data-button="Next" data-options="tipLocation:bottom;tipAnimation:fade" data-id="searchField">
                        <h2>Search!</h2>
                        <p>Gossout allows you to search people, posts, and communities!</p>
                    </li>
                    <li data-button="Next" data-options="tipLocation:right" data-id="search-field-submit">
                        <h2>Submit</h2>
                        <p>This button allows you to submit your search query. You can also press <strong>Enter</strong> or <strong>Return</strong> key on your keyboard to do the same.</p>
                    </li>

                    <li data-button="Next" data-id="index-three-icon" data-options="tipLocation:left"> 
                        <h2>Gossout!</h2>
                        <p>Gossout goes beyond meeting people: start your own community! Join other communities! Share and discover more than ever!</p>
                    </li>
                    <li data-id="footer-links" data-options="tipLocation:top">
                        <h2>Terms &amp; Privacy</h2>
                        <p>See our terms and privacy for more info.</p>
                    </li>
                </ol>
            </div>

        </div>

    </body>
</html>