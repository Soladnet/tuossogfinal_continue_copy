<?php
header('Content-type: text/html; charset=UTF-8');
include_once './Gossout_Community.php';
$helve = $_GET['param'];
if (trim($helve) == "") {
    header("HTTP/1.0 404 Not Found");
    exit;
}

if (isset($_COOKIE['user_auth'])) {
    include_once './encryptionClass.php';
    include_once './GossoutUser.php';
    $encrypt = new Encryption();
    $uid = $encrypt->safe_b64decode($_COOKIE['user_auth']);
    if (is_numeric($uid)) {
        $user = new GossoutUser($uid);
        $userProfile = $user->getProfile();
    } else {
        header("HTTP/1.0 404 Not Found");
        exit;
    }
    $commm = new Community();
    $comFind = Community::communityExist($helve);
    $comId = $comFind['status'];
    if (!$comId) {
        header("HTTP/1.0 404 Not Found");
        exit;
    } else {
        $commm->setCommunityId($comId);
        $isAmember = $commm->isAmember($comId, $uid);
        $isCreator = Community::isCreator($comId, $uid);
        if (!$isCreator['status']) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
        $helv = Community::commNameFromHelve($helve);
    }
} else {
    include_once './404.php';
    exit;
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Communities</title>
        <meta http-equiv="Pragma" http-equiv="no-cache" />
        <meta http-equiv="Expires" content="-1" />
        <link rel="stylesheet" href="css/chosen.css" />
        <link rel="stylesheet" href="css/validationEngine.jquery.css">
        <link rel="stylesheet" type="text/css" href="css/chat.min.1.0.css" />
        <?php
        if (isset($_GET['param']) ? $_GET['param'] != "" ? $_GET['param'] : FALSE  : FALSE) {
            ?>
            <style>
                .progress{position:relative; width:60%; border: 1px solid #ddd; padding: 1px; border-radius: 3px;}
                .bar{background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
                .percent{position:absolute; display:inline-block; top:3px; left:48%;}
            </style>
            <?php
        }
        ?>
        <?php
        include ("head.php");
        ?>
        <script type="text/javascript" src="scripts/humane.min.js"></script>
        <script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.4"></script>
        <script src="scripts/jquery.timeago.js" type="text/javascript"></script>
        <script src="scripts/test_helpers.js" type="text/javascript"></script>
        <script src="scripts/chosen.jquery.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/jquery.form.js"></script>
        <script type="text/javascript" src="scripts/languages/jquery.validationEngine-en.js"></script>
        <script type="text/javascript" src="scripts/jquery.validationEngine.js"></script>
        <?php if (empty($_GET['param2'])) { ?>
            <script type="text/javascript">
                var current; <?php $start = 2;?>
            var start = 0, limit = <?php echo $start; ?>, comId = <?php echo $comId; ?>;
            var helveArray = (window.location + '').split('/');
            //            var helveArray = ($.trim(helveArray1)).split("/");
            //            han
            (helveArray[helveArray.length - 1] == '') ? helve = helveArray[helveArray.length - 2] : helve = helveArray[helveArray.length - 1];
            //           alert(helve);
            $(document).ready(function() {

                sendData("loadCommunity", {target: "#rightcolumn1", loadImage: true, max: true, loadAside: true, comname: helve, start: 0, limit: 10});
                sendData("loadCommMsgInbox", {target: "#inboxCommMsg", loadImage: false, start: start, limit: limit, comId: comId, append: false, helve: helve});
                $('#comm_more_inbox').hide();
                $('#sentCommMsgDiv').hide();
                $('#inbox').on('click', function() {
                    sendData("loadCommMsgInbox", {target: "#inboxCommMsg", loadImage: true, start: start, limit: limit, comId: comId, append: false, inboxInitiator: true, helve: helve});
                })
                $('#comm_more_inbox').click(function() {
                    var start = parseInt($(this).attr('start'))
                    sendData("loadCommMsgInbox", {target: "#inboxCommMsg", loadImage: false, start: start, limit: limit, comId: comId, append: true, helve: helve});
                    $('#comm_more_inbox').attr('start', parseInt($('#comm_more_inbox').attr('start')) + limit);
                    return false;
                })
                $('#sent_message').click(function() {
                    sendData("loadCommMsgSent", {target: "#sentCommMsg", loadImage: true, start: start, limit: limit, comId: comId, sentBox: true});
                    return false;
                })
                $('#comm_more_sentMsg').on('click', function() {
                    return false;
                });
                $(".fancybox").fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    minWidth: 250
                });
                $("#commMsgForm").ajaxForm({
                    beforeSubmit: function() {
                        if ($.trim($('#messageTitle').val()) == "" || $.trim($('#message').val()) == "") {
                            $('#commMsgError').slideDown(300);
                            $('.commMsgInput').css('border-color', '#8A1F11');
                            setTimeout(function() {
                                $('.commMsgInput').css('border-color', '#CCCCCC');
                                $('#commMsgError').slideUp(300);
                            }, 10000);
                            return false;
                        } else {
                            $('#loadMoreImg').show();
                        }
                    },
                    success: function(responseText, statusText, xhr, $form) {
                        $('#sendMsgDiv').slideUp(500);
                        $('#returnTitle').html(responseText.title);
                        $('#returnMessage').html(responseText.message);
                        $('#feedBackMsgDiv').slideDown(500);
                        $('#loadMoreImg').hide();
                    },
                    complete: function(xhr) {

                    },
                    data: {
                        uid: readCookie("user_auth")
                    }
                });

                $('.gossbag-separation-icons').click(function() {
                    $('.gossbag-separation-icons').removeClass('active');
                    $(this).addClass('active');
                    if ($(this).attr('id') === 'inbox') {
                        $('.showCommMsg').hide();
                        $('#inboxCommMsgDiv').show();
                    }
                    else if ($(this).attr('id') === 'sent_message') {
                        $('.showCommMsg').hide();
                        $('#sentCommMsgDiv').show();
                    } else {
                    }

                });

            });

            </script>
        <?php } ?>
    </head>
    <body>
        <style>
            .content{
                /*min-height:600px;*/
            }
            .aside{
                /*              width: 374px;
                                float:left !important;*/
                /*min-height:400px;*/
                /*border:#efefee 1px solid;*/
                /*border-radius: 3px;*/

            }
            .hover{
                color:green !important;
            }
            .commMsgInput{
                width:100%;border-radius: 3px;border:1px solid #CCCCCC;font-family: "Segoe UI",Segoe,sans-serif;font-size: 0.9em;
            }
            .words{
                font-size: 0.8em;
                color:#777777;
                margin-bottom: -4px;
            }
            .individual-message-box{
                padding:5px;margin:5px;
            }
            .individual-message-box:hover{
                cursor: pointer;
            }
            .message_head.unread{
                font-weight:bold;
            }
            .message_content{
                display:none;
               margin-bottom:5px;
                padding:5px;
                border-radius: 3px;
                border:1px solid #efefef;
                width:98%;
                margin: 0 auto;
                background: #fcfcfc;
            }
            .all-messages-text{
                float:left;margin-left:5px;margin-top:-5px
            }
            #inboxCommMsg,#sentCommMsg{
                padding:1px;
                width:100%;
                border:1px #efefef solid;
                border-radius: 3px;
                padding-bottom: 5px;
                min-width: 305px;
                /*float:left;*/

            }
            #inboxCommMsgDiv,#sentCommMsgDiv{
                width:100%;
                padding:0;
            }
            .profile-meta{

            }
            .showClickOption{
                visibility:hidden;
                background: white;
                position:absolute;
                display: none;
                font-size: 0.75em;
                padding:3px;
                border: 1px #efefef solid;
                border-radius: 3px;
                width:120px !important;
            }
            .timeago{
                font-size: 0.8em;
                color:#cccccc;
            }
            @media only screen and (max-width: 1024px) {
                .just {
                    min-height: 400px !important;
                }
            }
            @media only screen and (max-width: 720px) {
                .just {
                    min-height: 300px !important;
                    position: relative !important;

                }
            }
            @media only screen and (max-width: 440px) {
                .just {
                    min-height: 400px !important;
                }
            }
            .all-messages-image{
                position: relative;
                padding:2px;
            }
            .senderComm{
                float:left;
                max-height: 45px;
                max-width: 60px;
                overflow: hidden;
            }
            .commEachMsgDiv{
                min-height: 54px;
               
                width: 98%;
                 border:1px #efefef solid;
                border-radius: 3px;
                margin-bottom: 4px;
            }
            .commMsgSender{
                font-size:12px;
            }
            .posts{
                min-width: 325px;
            }
        </style>
        <div class="page-wrapper">
            <?php
            include ("nav.php");
            include ("nav-user.php");
            ?>
            <div class="logo" id="logo"><img alt=""></div>

            <div class="content">

                <span id="rightcolumn" class="">
                    <?php
                    if ($isCreator['status']) {
                        ?>
                        <div class="posts">

                            <div class="showClickOption">
                                <span class="read_message first_opt" rel=""><span class="icon-16-mail"></span><a href class="profile-meta r_m">Show message</a></span><br class="first_opt"><hr class="first_opt">
                                <span class="view_profile" rel=""><span class="icon-16-user"></span><a href="" class="profile-meta real-profile-link">View profile</a></span>
                            </div>
                            <h3 class="inboxCommMsgDiv">Community message: <span id="<?php echo $comId; ?>" class="commFullInfo"><?php echo $helv['status']; ?></span></h3><hr class="mainCommCont">
                            <div class="success mainCommCont" id="welcomemsg">
                                <p>The messages in here are from your Community members. Be aware that
                                    messages sent through this panel in response to your Community members' messages or otherwise, go to the directed member's Inbox.
                                </p>
                            </div>
                            <div class="success mainCommCont" id="initialMessage" style="display:none;">
                                <p></p>
                            </div>
                            <hr>
                            <div class="timeline-filter mainCommCont"  style="float:left;">
                                <ul>
                                    <li class="active gossbag-separation-li gossbag-separation-icons" id="inbox" rel="comment-notification-icon" title="Community message inbox"><span class="icon-16-forward"></span>Inbox</li>
                                    <li class="gossbag-separation-li gossbag-separation-icons" id="sent_message" rel="post-notification-icon" title="Sent message"><span class="icon-16-reply"></span>Sent messages</li>
                                    <!--<li class=" gossbag-separation-li gossbag-separation-icons" id="compose_new" rel="all-notification-icon" title="Compose new message"><span class="icon-16-pencil"></span>Compose new</li>-->
                                </ul>
                            </div>
                            <!--<hr style="margin-top:-10px;">-->
                           
                            <div style="margin-top:-36px;">
                                <br>
                                <br>
                                <!--<div  style="border-bottom: 1px solid #efefef;width:100%;"></div>-->       
                                <div class="aside profile-meta showCommMsg mainCommCont" id="inboxCommMsgDiv">
                                    <div id="inboxCommMsg">
                                       
                                    </div>
                                    <hr>
                                    <p>
                                    <div class="button" style="float:left;" id="comm_more_inbox" start="<?php echo 2;?>">
                                        <a href="">More messages > ></a>
                                    </div>&nbsp;<img src='images/loading.gif' style='border:none;margin-top: -10px;display:none' id="loader1"/>
                                </div>

                                <div class="aside profile-meta showCommMsg" id="sentCommMsgDiv"> 
                                    <div id="sentCommMsg">

                                    </div>
                                   </div>
                                <div class="aside profile-meta showCommMsg" id="newCommMsg" style="display:none"> 

                                    <div id="sendMsgDiv" style="width:100%;">
                                        <div style="font-size:0.9em;margin-bottom: 5px;display:none;" id="commMsgError" class="error"><strong>Error -empty fields!</strong> All fields must be filled before your message could be sent.  </div>
                                        <div style="font-size:0.9em;margin-bottom: 5px;display:none;" id="commMsgSuc2" class="success"><strong>Success!</strong> Your message was sent successfully.  </div>
                                        <form name="replyMsgForm" id="replyMsgForm" action="tuossog-api-json.php" method="POST" enctype="application/x-www-form-urlencoded">
                                            <div class="words">Message Title:</div>
                                            <hr/>
                                            <input type="text" name="messageTitle" id="messageTitle" class="commMsgInput" placeholder="Type the receiver's id here"/>
                                            <br>
                                            <div class="words" style="margin-top:10px;">To:</div>
                                            <hr/>
                                            <input type="text" name="receiverName" id="receiverName" class="commMsgInput" placeholder="Type the title of you message here (compulsory)"/>
                                            <p><div class="words" style="margin-top:10px;">Message:</div>
                                            <hr/>
                                            <textarea style="min-height:250px;" name="realMessage" id="realMessage" placeholder="Type your message here. . ." class="commMsgInput"></textarea>
                                            <!--<hr><input type="file" name="commMsgFile" id="commMsgFile" style="border-radius: 3px;border:1px solid #ebebeb;height:30px;width:100px;"/>-->
                                            <p></p>
                                            <hr/>

                                            <input type="submit" id="sendResponse" name="sendResponse" class="button submit" value="Send Message" style="float: left;">
                                            <input type="hidden" name="param" value="sendMsgFromAdmin"/><div id="loadMoreImg" style="float:left;"> &nbsp;<img src="images/loading.gif"/></div>
                                            <input type="hidden" name ="comId" value="<?php echo $comId; ?>" />

                                        </form> 
                                        <div style="float:right;">
                                            <a href style="float:left;" id="commInboxShow"> [Back to message]</a> &nbsp;&nbsp;<!--<a href style="float:right;" id="">&nbsp;[Show conversations]</a> -->
                                        </div>

                                        <br/><br/>
                                    </div>
                                    <div id="feedBackMsgDiv" style="display:none;width:100%;">
                                        <div style="font-size:0.9em;margin-bottom: 14px;" class="success"><strong>Successful delivery!</strong> Your message with the details below was sent successfully. The response to your message shall be reflected in your inbox as soon as the Administrator replies.</div>
                                        <div class="" style="font-size:0.9em;margin-top:-5px;"><h3><span id="returnTitle"></span></h3></div>
                                        <hr/>
                                        <div class="" style="font-size:0.8em;padding:5px;" id="returnMessage"></div>
                                        <a href style="float:right;" id="commMsgDiv"> << Back to inbox</a>
                                    </div>
                                </div>
                            </div>

                        <?php } elseif ($isAmember['status']) { ?>



                        <?php } ?>
                </span>
            </div>
            <div class="aside">
                <div class="aside-wrapper just" style="min-height: 290px;">
                    <div class="profile-pic"><img onload="OnImageLoad(event);" class="holdam" src="images/no-pic.png" id="commPix"></div>
                    <table >
                        <tr><td><h3 id="commTitle" style="width: 11em;word-wrap: break-word">Loading...</h3></td></tr>
                        <tr><td id="comType"><span class="icon-16-lock"></span>Loading...</td></tr>
                        <tr><td class="profile-meta"><p id="commUrl" style="width: 14em;word-wrap: break-word">Loading...</p></td></tr>
                        <tr><td class="profile-meta" id="commDesc">Loading...</td></tr>
                    </table>					
                    <div class="clear"></div>
                    <div class="profile-summary">
                        <div class="profile-summary-wrapper"><a><p class="number" id="post_count">0 </p> <p class="type">Posts</p></a></div>
                        <div class="profile-summary-wrapper"><a><p class="number" id="mem_count">0 </p> <p class="type">Members</p></a></div>
                        <div class="clear"></div>
                    </div>
                    <div class="clear"></div>
                    <?php
                    if (isset($_COOKIE['user_auth'])) {
                        ?>
                        <button class="button profile-button openChatButton" id="chatButton" rel=""><span class="icon-16-chat"></span> Chat</button>
                        <?php
                    }
                    ?>
                    <button class="button profile-button" id="joinleave"><span class="icon-16-star"></span> <span id="joinleave-text">Join</span><input type="hidden" id="joinleave-comid" value="0"/></button>
                    <!--<button class="button profile-button" id="commMsgInbox"><span class="icon-16-mail"></span> <span id="commMsgInbox-text">Messages [12]</span><input type="hidden" id="commMsgInbox-comid" value="0"/></button>-->
                    <!--<button class="button profile-button" id="commMsgCompose"><span class="icon-16-mail"></span> <span id="commMsgCompose-text">Compose</span><input type="hidden" id="commMsgCompose-comid" value="0"/></button>-->
                    <span id="otherCommOption"></span>
                    <div class="clear"></div>

                </div>


                <div class="aside-wrapper">
                    <h3>Members</h3>
                    <span id="commember-aside">
                    </span>
                    <script>
                    //                $(document).ready(function() {
                    //                    sendData("loadCommunityMembers", {target: "#commember-aside", loadImage: true, comname: current[current.length - 1], start: 0, limit: 12});
                    //                });
                    //                        </script>
                    <p class="community-listing">
                    <div class="clear"></div>
                    <span>
                        <span id="showAllCommem"><span class="icon-16-dot"></span><a href="friends">Show all</a></span>
                    </span>
                    </p>
                    <div class="clear"></div>
                </div>
                <?php
                include("suggested-friends.php");
                ?>

                <div class="clear"></div>

            </div>	
            <div class="clear"></div>
            <!--<hr>-->





        </div>
    </span>
    <?php
    include("footer.php");
//    print_r($_SESSION['cd']);
    ?>

</div>
</body>
</html>