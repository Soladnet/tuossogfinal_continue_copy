<?php
header('Content-type: text/html; charset=UTF-8');
if (isset($_COOKIE['user_auth'])) {
    include_once './encryptionClass.php';
    include_once './GossoutUser.php';
    $encrypt = new Encryption();
    $uid = $encrypt->safe_b64decode($_COOKIE['user_auth']);
    if (is_numeric($uid)) {
        $user = new GossoutUser($uid);
        $userProfile = $user->getProfile();
    }
} else {
    header("Location: login");
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - All notification</title>
        <?php
        include ("head.php");
        ?>
        <script type="text/javascript" src="scripts/humane.min.js"></script>
        <script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.4"></script>
        <script src="scripts/jquery.timeago.js" type="text/javascript"></script>
        <script src="scripts/test_helpers.js" type="text/javascript"></script>
        <script type="text/javascript">
            var limit = 10;
            function doSeparatGoss(pointer) {
                var hold = $('#current-notification');
                $('#loadMoreNotifDiv').hide();
                $('.noResult').click(function() {
                    $('#loadMoreNotifDiv').hide();
                });
                if (pointer === 'wink-notification-icon') {
                    if ($("#" + pointer).hasClass("clicked")) {
                        $("#individual-notification-box-w").show();
                         $('.loadMoreGossContent').attr('hold', 'Wink');
                        if($("#" + pointer).hasClass("showmore"))
                            $('#loadMoreNotifDiv').show();
                    } else {
                        $("#" + pointer).addClass("clicked");
                        sendData("loadWink", {target: "#individual-notification-box-w", loadImage: true, start: 0, limit: 10});
                        $('.loadMoreGossContent').attr('hold', 'Wink');
                        $("#individual-notification-box-w").show();
                    }
                    hold.text('Wink');
                } else if (pointer === 'comment-notification-icon') {
                    hold.text('Comment');
                    if ($("#" + pointer).hasClass("clicked")) {
                        $("#individual-notification-box-c").show();
                        $('.loadMoreGossContent').attr('hold', 'Comment');
                        if($("#" + pointer).hasClass("showmore"))
                            $('#loadMoreNotifDiv').show();
                    } else {
                        $("#" + pointer).addClass("clicked");
                        sendData("loadGossComment", {target: "#individual-notification-box-c", loadImage: true, start: 0, limit: 10});
                        $('.loadMoreGossContent').attr('hold', 'Comment');
                        $("#individual-notification-box-c").show();
                    }

                } else if (pointer === 'frq-notification-icon') {
                    hold.text('Friend Request');
                    
                    if ($("#" + pointer).hasClass("clicked")) {
                        $("#individual-notification-box-f").show();
                        $('.loadMoreGossContent').attr('hold', 'Frq');
                        if($("#" + pointer).hasClass("showmore"))
                            $('#loadMoreNotifDiv').show();
                    } else {
                        $("#" + pointer).addClass("clicked");
                        sendData("loadGossFrq", {target: "#individual-notification-box-f", loadImage: true, start: 0, limit: 10});
                        $('.loadMoreGossContent').attr('hold', 'Frq');
                        $("#individual-notification-box-f").show();
                    }
                } else if (pointer === 'post-notification-icon') {
                    hold.text('Post');
                    if ($("#" + pointer).hasClass("clicked")) {
                        $("#individual-notification-box-p").show();
                         $('.loadMoreGossContent').attr('hold', 'Post');
                        if($("#" + pointer).hasClass("showmore"))
                            $('#loadMoreNotifDiv').show();
                    } else {
                        $("#" + pointer).addClass("clicked");
                        sendData("loadGossPost", {target: "#individual-notification-box-p", loadImage: true, start: 0, limit: 10});
                        $('.loadMoreGossContent').attr('hold', 'Post');
                        $("#individual-notification-box-p").show();
                    }
                } else if (pointer === 'all-notification-icon') {
                    hold.text('All Notifications');
                    if ($("#" + pointer).hasClass("clicked")) {
                        $("#individual-notification-box-a").show();
                        $('.loadMoreGossContent').attr('hold', 'all');
                        if($("#" + pointer).hasClass("showmore"))
                            $('#loadMoreNotifDiv').show();
                    } else {
                        $("#" + pointer).addClass("clicked");
                        
                        $('.loadMoreGossContent').attr('hold', 'all');
                        $("#individual-notification-box-a").show();
                    }
                }
            }
            $(document).ready(function() {
                $(".fancybox").fancybox({
                    openEffect: 'none',
                    closeEffect: 'none'

                });
                sendData("loadNotificationCount", {title: document.title});

                $('.gossbag-separation-icons').click(function() {
                    $('.gossbag-separation-icons.active').removeClass('active');
                    $(this).addClass('active');
                    $(".box").hide();
                    doSeparatGoss($(this).attr('id'));
                });
               
                $('#loadMoreNotifDiv').hide();
                $('.loadMoreGossContent').click(function() {
                    var hold = $(this).attr('hold');
                    var winkStart = $(this).attr('wink');
                    var commentStart = $(this).attr('comment');
                    var postStart = $(this).attr('posts');
                    var frqStart = $(this).attr('frq');
                    var allStart = $(this).attr('all');

                    if (hold === 'Wink') {
                        $("#loadMoreImg").show();
                        sendData("loadWink", {target: "#individual-notification-box-w", start: winkStart, limit: limit, status: "append"});
                    } else if (hold === 'Comment') {
                        $("#loadMoreImg").show();
                        sendData("loadGossComment", {target: "#individual-notification-box-c", start: commentStart, limit: limit, status: "append"});
                    } else if (hold === 'Post') {
                        $("#loadMoreImg").show();
                        sendData("loadGossPost", {target: "#individual-notification-box-p", start: postStart, limit: limit, status: "append"});
                    } else if (hold === 'Frq') {
                        $("#loadMoreImg").show();
                        sendData("loadGossFrq", {target: "#individual-notification-box-f", start: frqStart, limit: limit, status: "append"});
                    } else if (hold === 'all') {
                        $("#loadMoreImg").show();
                        sendData("loadGossbag", {target: "#individual-notification-box-a", start: allStart, limit: limit, status: "append"});
                    }
                    return false;
                });
            });
        </script>
    </head>
    <body>
        <div class="page-wrapper">
            <?php
            include ("nav.php");
            include ("nav-user.php");
            ?>
            <div class="logo"><img src="images/gossout-logo-text-svg.svg" alt=""></div>

            <div class="content">
                <div class="all-notifications-list">
                    <h1>Notifications</h1>
                    <span style="float:left;" class="all-notifications-message" id="current-notification">All Notifications

                    </span>

                    <div class="timeline-filter">

                        <ul>
                            <li class="gossbag-separation-li gossbag-separation-icons" id="wink-notification-icon" rel="wink-notification-icon" title="Wink"><span class="icon-16-eye"></span></li>
                            <li class="gossbag-separation-li gossbag-separation-icons" id="frq-notification-icon" rel="frq-notification-icon" title="Friend Request"><span class="icon-16-user-add"></span></li>
                            <li class="gossbag-separation-li gossbag-separation-icons" id="comment-notification-icon" rel="comment-notification-icon" title="Comments"><span class="icon-16-comment"></span></li>
                            <li class="gossbag-separation-li gossbag-separation-icons" id="post-notification-icon" rel="post-notification-icon" title="Posts"><span class="icon-16-pencil"></span></li>
                            <li class="active gossbag-separation-li gossbag-separation-icons" id="all-notification-icon" rel="all-notification-icon" title="All"><span class="">All</span></li>

                        </ul>
                    </div>
                    <div class="clear"></div>
                    <span class="box" id="individual-notification-box-a" style="display: block"></span>
                    <span class="box" id="individual-notification-box-w" style="display: block"></span>
                    <span class="box" id="individual-notification-box-c" style="display: block"></span>
                    <span class="box" id="individual-notification-box-f" style="display: block"></span>
                    <span class="box" id="individual-notification-box-p" style="display: block"></span>

                    <div class="button" style="float:left;" id="loadMoreNotifDiv">
                        <a href="" all="10" class="loadMoreGossContent" comment="10" frq="10" hold="all" wink="10" id="loadMoreNotif" posts="10" >Load more > ></a>
                    </div>
                    <div id="loadMoreImg" style="display: none">&nbsp;<img src="images/loading.gif"/></div>
                    <script>
                        $(document).ready(function() {
                            sendData("loadGossbag", {target: "#individual-notification-box-a", loadImage: true, start: 0, limit: 10});
                        });
                    </script>
                </div>

                <?php
                include("aside.php");
                ?>
            </div>
            <?php
            include("footer.php");
            ?>
        </div>

    </body>
</html>