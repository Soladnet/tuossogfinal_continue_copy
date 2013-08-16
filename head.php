
<link rel="shortcut icon" href="favicon.ico">
<link rel="stylesheet" href="css/hint.min.css">
<link rel="stylesheet" href="css/jackedup.css">
<link rel="stylesheet" type="text/css" href="css/jquery.jscrollpane.css" />
<link rel="stylesheet" media="screen" href="css/style.min.1.0.1.css">
<link rel="stylesheet" href="css/jackedup.css">

<script type="text/javascript" src="scripts/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.4"></script>
<script src="scripts/jquery.sticky.js"></script>
<script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
<script src="scripts/jquery.jscrollpane.min.js"></script>
<script src="scripts/jquery.mousewheel.js"></script>
<script src="scripts/mwheelIntent.js"></script>
<?php
if (isset($_GET['page']) && trim($_GET['page']) == "user") {
    ?>
    <meta name="description" id="metaDescription" content="<?php echo $user->getFullname() . " [" . $user->getScreenName() . "] is on Gossout and is interested in " . ($user->getInterestTag() == "" ? "a lot of things" : $user->getInterestTag()) ?>">
    <meta name="keywords" id="metaKeywords" content="<?php echo ($user->getFullname() == "" ? "" : $user->getFullname() . ",") . $user->getInterestTag() == "" ? "Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs" : $user->getInterestTag() . ",Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs" ?>">
    <?php
} else if (($_GET['page'] == "communities" && trim($_GET['param']) != "" && trim($_GET['param2']) == "") || ($_GET['page'] == "communities" && trim($_GET['param']) != "" && trim($_GET['param2']) != "") || ($_GET['page'] != "communities" && trim($_GET['param']) == "" && $_GET['param2'] == "") || ($_GET['page'] != "communities" && trim($_GET['param']) != "" && $_GET['param'] == "")) {//load community info
    ?>

    <?php
} else if (($_GET['page'] != "communities" && trim($_GET['param']) != "" && trim($_GET['param2']) == "" && is_numeric($_GET['param'])) || ($_GET['page'] == "communities" && trim($_GET['param']) != "" && trim($_GET['param2']) != "" && is_numeric($_GET['param2']))) {//load single post
    ?>
    
    <?php
} else {
    ?>
    <meta name="description" id="metaDescription" content="Start or join existing communities/interests on Gossout and start sharing pictures and videos. People use Gossout search, Discover and connect with communities">
    <meta name="keywords" id="metaKeywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
    <?php
}
?>
<meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
<script>
    $(function() {
        $("#show-suggested-friends,#show-suggested-community,#gossbag-text,#messages-text,#gossbag-close,#messages-close,#user-actions,#user-more-option,#show-full-profile,#search,#search-close,#new-message-btn,#loadCommore,#joinleave,.openChatButton").click(function() {
            showOption(this);
        });
        $.ajaxSetup({
            url: 'tuossog-api-json.php',
            dataType: "json",
            type: "POST",
            error: function(jqXHR, textStatus, errorThrown) {
                manageError(jqXHR, textStatus, errorThrown);
            },
            data: {
                uid: readCookie("user_auth")
            },
            timeout: 1000 * 60 * 10
        });
        if (Modernizr.inlinesvg) {
            $('#logo').html('<img src="images/gossout-logo-text-svg.svg" alt="Gossout" />');
        } else {
            $('#logo').html('<img src="images/gossout-logo-text-svg.png" alt="Gossout" />');
        }
        $("#nav-user").sticky({topSpacing: -4});
    });
</script>
<script type="text/javascript" src="scripts/script.js"></script>