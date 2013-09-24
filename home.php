<?php
header('Content-type: text/html; charset=UTF-8');
if (isset($_COOKIE['user_auth'])) {
    include_once './encryptionClass.php';
    include_once './GossoutUser.php';
    include_once './Gossout_Community.php';
    $encrypt = new Encryption();
    $uid = $encrypt->safe_b64decode($_COOKIE['user_auth']);
    if (is_numeric($uid)) {
        $user = new GossoutUser($uid);
        $userProfile = $user->getProfile();
        $userCommunity = new Community();
        $userCommunity->setUser($uid);
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
        <title>Gossout</title>
        <meta http-equiv="Pragma" http-equiv="no-cache" />
        <meta http-equiv="Expires" content="-1" />
        <link rel="stylesheet" href="css/jackedup.css" />
        <link rel="stylesheet" href="css/chosen.css" />
        <link rel=" stylesheet" type="text/css" href="css/joyride-2.0.3.css">
        <style>
            .progress { position:relative; width:60%; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
            .bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
            .percent { position:absolute; display:inline-block; top:3px; left:48%; }
        </style>
        <?php
        include ("head.php");
        ?>
        <script src="scripts/waypoints.min.js" type="text/javascript"></script>
        <script src="scripts/jquery.joyride-2.0.3.js"></script>
        <script src="scripts/jquery.timeago.js" type="text/javascript"></script>
        <script src="scripts/test_helpers.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.4"></script>
        <script type="text/javascript" src="scripts/humane.min.js"></script>
        <script src="scripts/chosen.jquery.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/jquery.form.js"></script>
        <script type="text/javascript">
            var community = {};
            function processCom(val) {
                if (val.length > 0) {
                    $.each(val, function(i, com) {
                        community[com.id] = com;
                    });
                }
            }
            $(document).ready(function() {
                sendData("loadNotificationCount", {title: document.title});
                sendData("loadTimeline", {target: ".timeline-container", loadImage: true, start: 0, limit: 20});
                $(".chzn-select").chosen();
                $(".fancybox").fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    minWidth: 250
                });
                var start = 10, limit = 10;
                $('#loadMoreNotifDiv').hide();
                $('#loadMoreTimeLine').click(function() {
                    start = parseInt($('.loadMoreTimeLine').attr("timeLine"));
                    $('#loadMoreImg').show();
                    sendData("loadTimeline", {target: ".timeline-container", loadImage: false, loadMore: true, start: start, limit: 10});
                    return false;

                });
                $("#uploadImagePost").click(function() {
                    $("#uploadInput").focus().trigger('click');
                });
                var bar = $('.bar');
                var percent = $('.percent');
                $("#timelineForm").ajaxForm({
                    beforeSubmit: function(formData, jqForm, options) {
                        $(".progress").show();
                        var percentVal = '0%';
                        bar.width(percentVal);
                        percent.html(percentVal);

                        $("#postBtn,textarea").prop('disabled', true);
                        $("#hiddenComm").val($(".chzn-select").val());
                        if ($(".chzn-select").val() === null) {
                            humane.log("You must select a community first.", {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                            $("#postBtn,textarea").prop('disabled', false);
                            $(".progress").hide(500);
                            return false;
                        } else if ($("#postText").val() === "") {
                            $("#postBtn,textarea").prop('disabled', false);
                            $(".progress").hide(500);
                            return false;
                        }
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                    }, success: function(responseText, statusText, xhr, $form) {
                        var percentVal = '100%';
                        bar.width(percentVal);
                        percent.html(percentVal);
                        var hiddenCom = $("#hiddenComm").val().split(",");
                        var post = $("#postText").val();
                        if (!responseText.error) {
                            if (responseText.time) {
                                var htmlstr = "";
                                $.each(responseText.id, function(i, id) {
                                    htmlstr += '<div class="timeline-news-single"><div class="timeline-news-profile-pic">' + '<img onload="OnImageLoad(event);" src="' + responseText.photo + '">' + '</div><p><a>You</a> posted to <a href="' + community[hiddenCom[i]].unique_name + '">' + community[hiddenCom[i]].name + '</a></p>' + '<p class="timeline-time timeago" title="' + responseText.time + '">' + responseText.time + '</p><p>' + nl2br(linkify(post)) + '</p>';
                                    if (responseText.post_photo) {
                                        htmlstr += '<p class="timeline-photo-upload">';
                                        $.each(responseText.post_photo, function(k, photo) {
                                            htmlstr += '<a href="' + photo.original + '" class="fancybox" rel="gallery' + id + '"><img src="' + photo.thumbnail + '"></a>';
                                        });
                                        htmlstr += '</p><div class="clear"></div>';
                                    }
                                    htmlstr += '<!--<p class="post-meta"><span id="post-new-comment-show-' + id + '" class=""><span class="icon-16-comment"></span>Comment(20)</span>' + '<span class="post-meta-gossout"><span class="icon-16-share"></span><a href="#share-123456" class="fancybox " id="inline">Share(20)</a></span></p>--><div class="clear"></div></div>';
                                });
                                $(".timeline-container").prepend(htmlstr);
                                prepareDynamicDates();
                                $(".timeago").timeago();
                            }
                        }
                        if ($("#filesSelected").html() !== "") {
                            $("#filesSelected").html("");
                        }
                    }, complete: function(response, statusText, xhr, $form) {
                        $(".progress").hide(500);
                        $("#postBtn,textarea").prop('disabled', false);
                        $("#timelineForm").clearForm();
                        $('select').trigger('liszt:updated');
                    },
                    data: {
                        param: "post",
                        uid: readCookie("user_auth")
                    }
                });
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-svg.png" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-svg.png" alt="Gossout" /></a>');
                }

            });
            function setCookie(c_name, value, exdays) {
                var exdate = new Date();
                exdate.setDate(exdate.getDate() + exdays);
                var c_value = escape(value) + ((exdays === null) ? "" : "; expires=" + exdate.toUTCString());
                document.cookie = c_name + "=" + c_value;
            }
            function getCookie(c_name) {
                var c_value = document.cookie;
                var c_start = c_value.indexOf(" " + c_name + "=");
                if (c_start === -1) {
                    c_start = c_value.indexOf(c_name + "=");
                }
                if (c_start === -1) {
                    c_value = null;
                } else {
                    c_start = c_value.indexOf("=", c_start) + 1;
                    var c_end = c_value.indexOf(";", c_start);
                    if (c_end === -1) {
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
            if (getCookie('home_user_status') === null) {
                setCookie('home_user_status', 'new_user', 2592000);
                $(window).load(function() {
                    $('#joyRideTipContent').joyride({
                        autoStart: true
                    });
                });
            }
        </script>
    </head>
    <body>
        <div class="page-wrapper">
            <?php
            include ("nav.php");
            include ("nav-user.php");
            ?>
            <div class="logo" id="logo"><img alt=""></div>

            <div class="content">
                <div class="posts">
                    <?php
                    include("post-box.php");
                    include("timeline.php");
                    ?>

                </div>

                <?php
                include("aside.php");
                ?>			
            </div>
            <span id='footer-links'>
                <?php
                include("footer.php");
                ?>
            </span>
        </div>
<!--Tour tip contents starts here-->
        <div>
            <ol id="joyRideTipContent">
                <li data-text="Next" data-id="communities" data-options="tipLocation:bottom;tipAnimation:fade">
                    <h2>Communities!</h2>
                    <p>Do more on communities: create, join, view and manage.</p>
                </li>
                <li data-button="Next" data-options="tipLocation:bottom;tipAnimation:fade" data-id="gossbag-text">
                    <h2>Gossbag!</h2>
                    <p>Your Gossbag contains notifications on post, comments, and communities that may interest you.</p>
                </li>
                <li data-button="Next" data-options="tipLocation:bottom" data-id="messages-text">
                    <h2>Messages!</h2>
                    <p>This is your instant message inbox. Click to see unread messages. You can click <strong>Go to messages</strong> to see all conversations.</p>
                </li>

                <li data-button="Next" data-id="user-actions" data-options="tipLocation:left"> 
                    <h2>Settings!</h2>
                    <p>Edit and modify your profile information here. 
                    </p>
                </li>
                <li data-id="show-full-profile" data-options="tipLocation:top">
                    <h2>Show Profile!</h2>
                    <p>This link shows your basic profile information when clicked.</p>
                </li>
                <li data-text="Next" data-id="post-box" data-options="tipLocation:left">
                    <h2>Share!</h2>
                    <p>Share what is new with your communities.</p>
                </li>
                <li data-text="Next" data-id="community-select-list" data-options="tipLocation:bottom">
                    <h2>Where to Share</h2>
                    <p>Select at least one community to share your interest with.</p>
                </li>
                <li data-text="Next" data-id="uploadImagePost" data-options="tipLocation:left">
                    <h2>Add Photos!</h2>
                    <p>Use this button to select single or multiple pictures to improve your readers' experience.</p>
                </li>
                <li data-id="aside-wrapper-comm" data-options="tipLocation:right">
                    <h2>Your Communities!</h2>
                    <p>Your first five(5) communities are listed here. To see more, click on <strong>Show all</strong>.<br/>Get suggestion by clicking on <strong>Suggest communities</strong>.</p>
                </li>
                <li data-id="aside-wrapper-frnd" data-options="tipLocation:left">
                    <h2>Your Friends!</h2>
                    <p>Your friends are listed here. Click <strong>Show all</strong> to see more of your friends or <strong>Suggested Friends</strong> to see  people you can connect with.</p>
                </li>
            </ol>
        </div>
<!--Tour tip contents ends here-->
        <script>
            $(document).ready(function() {
                processCom(<?php echo $comm['status'] ? json_encode($comm['community_list']) : "{}" ?>);
            });
        </script>
    </body>
</html>