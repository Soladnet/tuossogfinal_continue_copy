<?php
header('Content-type: text/html; charset=UTF-8');
if (!isset($_GET['g'])) {
    header("Location: index");
    exit;
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
?>
<!doctype html>
<html lang="en">
    <head>
        <title><?php echo $_GET['g'] . " - Gossout Search" ?></title>
        <meta name="description" content="Start or join existing communities/interests on Gossout and start sharing pictures and videos. People use Gossout search, Discover and connect with communities">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.1.css">
        <link rel="stylesheet" href="css/jackedup.css" type="text/css"/>
        <link rel="stylesheet" href="css/hint.min.css">
        
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <link rel="shortcut icon" href="favicon.ico">
        <script src="scripts/humane.min.js"></script>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <script type="text/javascript" src="scripts/searchscript.js?v=1.1"></script>
        <script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.4"></script>
        <script src="scripts/jquery.timeago.js" type="text/javascript"></script>
        <script src="scripts/test_helpers.js" type="text/javascript"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script>
            $(function() {
                $.ajaxSetup({
                    url: 'tuossog-api-json.php',
                    dataType: "json",
                    timeout: 60000 * 10,
                    type: "post",
                    error: function(jqXHR, textStatus, errorThrown) {
                        manageError(jqXHR, textStatus, errorThrown, {uid: readCookie("user_auth")});
                    }
                });
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
<?php
if (isset($_GET['s']) && trim($_GET['s']) != "") {
    if ($_GET['s'] == 'post') {
        ?>
                        sendData("loadPostResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadPostResult", loadImage: true, start: 0, limit: 15});
                        $(".index-search-results-communities").remove();
                        $(".index-search-results-friends").remove();
        <?php
    } else if ($_GET['s'] == 'com') {
        ?>
                        sendData("loadCommunityResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadCommunityResult", loadImage: true, start: 0, limit: 15});
                        $(".index-search-results-posts").remove();
                        $(".index-search-results-friends").remove();
        <?php
    } else if ($_GET['s'] == 'people') {
        ?>
                        sendData("loadPeopleResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadPeopleResult", loadImage: true, start: 0, limit: 15});
                        $(".index-search-results-communities").remove();
                        $(".index-search-results-posts").remove();
        <?php
    } else {
        ?>
                        sendData("loadPostResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadPostResult", loadImage: true});
                        sendData("loadCommunityResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadCommunityResult", loadImage: true});
                        sendData("loadPeopleResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadPeopleResult", loadImage: true});
        <?php
    }
} else {
    ?>
                    sendData("loadPostResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadPostResult", loadImage: true});
                    sendData("loadCommunityResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadCommunityResult", loadImage: true});
                    sendData("loadPeopleResult", {term: "<?php echo $_GET['g'] ?>", target: "#loadPeopleResult", loadImage: true});
    <?php
}
?>
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
                <span class="index-login"><?php echo isset($user) ? "Welcome <a href='home'>" . $user->getFullname() . "</a> [ <a href='login_exec'>Logout</a> ]" : 'Already have an account? <a href="login">Login Here</a> | <a href="signup-personal">Sign up</a>' ?></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="logo" id="logo"><img alt=""></div>
            </div>
            <div class="index-intro">
                <div class="index-intro-1">
                    <h1>Search Results</h1>
                    <form method="GET" action="index-search-results.php" autocomplete="off" onsubmit="return validate()">
                        <input class="main-search" name="g" id="searchField" type="text" <?php echo isset($_GET['g']) ? "value='" . $_GET['g'] . "'" : 'placeholder="Search term here"' ?>>
                        <span><center><input class="button-big" type="submit" value="Search" /> <!--<button class="button-big"><a href="">Sign up</a></button>--></center></span>
                    </form>
                    <div class="clear"></div>

                    <hr>
                </div>
                <div class="index-intro-2-container">
                    <div class="index-search-results">	
                        <div class="index-search-results-posts">
                            <h2>Posts</h2>
                            <hr>
                            <span id="loadPostResult"></span>
                        </div>
                        <div class="index-search-results-communities">
                            <h2>Communities</h2>
                            <hr>
                            <span id="loadCommunityResult"></span>
                        </div>
                        <div class="index-search-results-friends">
                            <h2>People</h2>
                            <hr>
                            <span id="loadPeopleResult"></span>
                            <!--                            <div class="index-search-result">
                                                            <h3><a href="">Muhammad Abdullahi Kori</a></h3>
                                                            <img class="float-left" src="images/3.jpg">
                            
                                                            <p> <span class="icon-16-location"></span>Abuja, Federal Capital Territory, Nigeria </p>
                                                            <p> <span class="icon-16-calendar"></span>Joined on Feb 18, 2013 </p>
                                                            <p> <span class="icon-16-male"></span>Male </p>
                                                            <p> <span class="icon-16-female"></span>Female </p>
                                                            <div class="clear"></div>
                                                        </div>-->
                        </div>
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