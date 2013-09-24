<?php
//print_r($_SESSION);
header('Content-type: text/html; charset=UTF-8');
include_once './Gossout_Community.php';
$helve = $_GET['param'];
if (trim($helve) == "") {
    include_once './404.php';
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
        include_once './404.php';
        exit;
    }
    $commm = new Community();
    $comFind = Community::communityExist($helve);
    $comId = $comFind['status'];
    if (!$comId) {
        include_once './404.php';
        exit;
    } else {
        $commm->setCommunityId($comId);
        $isAmember = $commm->isAmember($comId, $uid);
        $isCreator = Community::isCreator($comId, $uid);
        if (!$isCreator['status']) {
            include_once './404.php';
            exit;
        }
        $helv = Community::commNameFromHelve($helve);
    }
    if (!empty($_GET['param2'])) {
        if (is_numeric($_GET['param2'])) {
            include_once 'Gossout_Community.php';
            $pmsg = Community::getParentMsg($_GET['param2']);
            if (!$pmsg['status']) {
                include_once './404.php';
                exit;
            }
        } else {
            include_once './404.php';
            exit;
        }
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

        <style>
            .progress{position:relative; width:60%; border: 1px solid #ddd; padding: 1px; border-radius: 3px;}
            .bar{background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
            .percent{position:absolute; display:inline-block; top:3px; left:48%;}
        </style>
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

        <script type="text/javascript">
            var current; <?php
        $start = 2;
        $limitChild = 20;
        ?>
            var start = 0, limit = <?php echo $start; ?>, comId = <?php echo $comId; ?>;
            var helveArray = $.trim((window.location + '')).split('/');
<?php if (empty($_GET['param2'])) { ?>
        (helveArray[helveArray.length - 1] == '') ? helve = helveArray[helveArray.length - 2] : helve = helveArray[helveArray.length - 1]; 
<?php } elseif (!empty($_GET['param2']) && is_numeric($_GET['param2'])) { ?>
        helve = helveArray[helveArray.length - 2]
<?php } ?>
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
        $('#sendMsgDiv').hide();
        $(".fancybox").fancybox({
            openEffect: 'none',
            closeEffect: 'none',
            minWidth: 250
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
        $('.showHideRemLink').on('click', function(){
            $('.showHideRem').toggle();
            $('.showHideRemDot').toggle();
            $(this).text(($(this).text()==='[Show more]') ? '[Show less]' : '[Show more]')
            return false;
        })
        $('.noAnchor').on('click',function(){
            $(this).parent().siblings('.showHideRem').toggle();
            $(this).parent().siblings('.showHideRemDot').toggle();
            $(this).text(($(this).text()==='[Show more]') ? '[Show less]' : '[Show more]')
            return false;
        })
      
        prepareDynamicDates();
        $(".timeago").timeago();
        $('#moreChildMsg').on('click',function(){
            limit =  <?php echo $limitChild; ?>;
            parentId = parseInt($(this).attr('parent'));
            start = parseInt($(this).attr('start'));
            sendData("loadChildren", {target: "#inboxCommMsg1", loadImage: false, start: start, limit:limit, parentId:parentId, append: true}); 
            $('#moreChildMsg').attr('start', parseInt($('#moreChildMsg').attr('start'))+limit);
            return false;
        });
        
        $("#replyMsgForm").ajaxForm({
            beforeSubmit: function() {
                if ($.trim($('#realMessage').val()) === "") {
                    $('#commMsgSuc').hide();
                    $('#commMsgError').slideDown(300);
                    $('.commMsgInput').css('border-color', '#8A1F11');
                    setTimeout(function() {
                        $('.commMsgInput').css('border-color', '#CCCCCC');
                        $('#commMsgError').slideUp(300);
                    }, 10000);
                    return false;
                } else {
                    $('loadMoreImg2').show();
                }
            },
           
            success: function(responseText, statusText, xhr, $form) {
                if(responseText.status){
                    $('#commMsgError,#sendMsgDiv').hide();
                    $('.hideSuc').slideDown();
                    $('.commMsgInput').val("");
                    var htmlstr = '<div style="" class="individualMsg" id="justElemnt">' +
                        '<span class="convDates">[' + 'You' + ']'+
                        '<div style="float:right;">[<span class="timeago convDates" title="'+responseText.time+'" ></span>]</div>' +
                        '</span><br><hr>'+ nl2br(responseText.message)+
                        '<br></div>';
                    prepareDynamicDates();
                    $(".timeago").timeago();
                    setTimeout(function(){
                        $('.hideSuc').slideUp(1000);
                        $('.hideSuc1').show();// just a line break for formating
                    }, 4000);
                    setTimeout(function(){
                        $('#inboxCommMsg1').prepend(htmlstr);
                        prepareDynamicDates();
                        $(".timeago").timeago()
                    },4000)
                  
                }
            },
            complete: function(xhr) {  
         
            },
            data: {
                uid: readCookie("user_auth")
            }
        });
        $('.showHideCompose').on('click', function(){
            $('#sendMsgDiv').toggle();
            return false;
        });
                    
                  
    });

        </script>

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
            .commMsgSender,.noAnchor{
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
                            <?php if (empty($_GET['param2'])) { ?>
                                <div class="showClickOption">
                                    <span class="read_message first_opt" rel=""><span class="icon-16-mail"></span><a href class="profile-meta r_m">Show message</a></span><br class="first_opt"><hr class="first_opt">
                                    <span class="view_profile" rel=""><span class="icon-16-user"></span><a href="" class="profile-meta real-profile-link">View profile</a></span>
                                </div>
                                <h3 class="inboxCommMsgDiv">Community message: <span id="<?php echo $comId; ?>" class="commFullInfo"><?php echo $helv['status']; ?></span></h3><hr class="mainCommCont">
                                <div class="success mainCommCont" id="welcomemsg">
                                    <p>Be aware that
                                        messages sent through this panel in response to your Community members' messages or otherwise, go to the directed member's Inbox.
                                    </p>
                                </div>
                                <div class="success mainCommCont" id="initialMessage" style="display:none;">
                                    <p></p>
                                </div>

                                <hr style="margin-top:-10px;">

                                <div style="margin-top:-36px;">
                                    <br>
                                    <br>
                                    <!--<div  style="border-bottom: 1px solid #efefef;width:100%;"></div>-->       
                                    <div class="aside profile-meta showCommMsg mainCommCont" id="inboxCommMsgDiv">
                                        <div id="inboxCommMsg">

                                        </div>
                                        <hr>
                                        <p>
                                        <div class="button" style="float:left;" id="comm_more_inbox" start="<?php echo 2; ?>">
                                            <a href="">More messages > ></a>
                                        </div>&nbsp;<img src='images/loading.gif' style='border:none;margin-top: -10px;display:none' id="loader1"/>
                                    </div>

                                    <div class="aside profile-meta showCommMsg" id="sentCommMsgDiv"> 
                                        <div id="sentCommMsg">

                                        </div>
                                    </div>

                                </div>
                                <?php
                            } elseif (!empty($_GET['param2']) && is_numeric($_GET['param2'])) {
                                $msgId = $_GET['param2'];
                                $pmsg = Community::getParentMsg($msgId);
                                $user = new GossoutUser($pmsg['parentMsg']['sender_id']);
                                $user->setUserId($pmsg['parentMsg']['sender_id']);
                                $pix = $user->getProfilePix();
//                                print_r($pix);
                                ?>
                                <h3 class="inboxCommMsgDiv">Community message: <span id="<?php echo $comId; ?>" class="commFullInfo"><?php echo $helv['status']; ?></span>, [<?php echo $pmsg['parentMsg']['fullname']; ?>]</h3><hr class="mainCommCont">
                                <div>
                                    <a href="user/<?php echo $pmsg['parentMsg']['username']; ?>"><img src="<?php echo $pix['pix']['thumbnail45']; ?>" style="float:left;margin:2px;height:50px;"/></a>
                                    <span><h3 style="margin-bottom: -18px;"><?php echo $pmsg['parentMsg']['message_title']; ?></h3></span><br>
                                    <a href="user/<?php echo $pmsg['parentMsg']['username']; ?>"><?php echo $pmsg['parentMsg']['fullname'] . ' [' . $pmsg['parentMsg']['username'] . ']'; ?></a>
                                    <a href="community-message/<?php echo $_GET['param']; ?>" style="float:right;font-size:12px;" id="commInboxShow"> [Back]</a> 
                                    <a href style="float:right;font-size:12px;margin-right:10px;" class="showHideCompose" id="replyMsg"> [Reply] </a>
                                </div>
                                <p>

                                <div class="success mainCommCont" id="welcomemsg" style="margin-top:10px;margin-bottom: 0.7em;">
                                    <h3>Original message as at: <?php echo $pmsg['parentMsg']['time']; ?></h3><hr>
                                    <p> 
                                        <?php echo (strlen($pmsg['parentMsg']['message']) <= 160) ? $pmsg['parentMsg']['message'] : substr($pmsg['parentMsg']['message'], 0, 160) . '<span class="showHideRemDot"> . . .</span>'; ?>

                                        <span style="display: none;"class="showHideRem" id="showHideRem-<?php echo $pmsg['parentMsg']['id']; ?>">
                                            <?php echo(strlen($pmsg['parentMsg']['message']) > 160) ? trim(substr($pmsg['parentMsg']['message'], 160)) : ""; ?>
                                        </span><br>
                                        <?php if (strlen($pmsg['parentMsg']['message']) > 160) echo '<a href class="showHideRemLink" style="float:right;">[Show more]</a>'; ?>

                                    </p>
                                    <br>
                                </div>
                                <!--<hr style="margin-top:-10px;">-->
                                <style>
                                    .convDates{
                                        font-size: 12px;
                                        color:gray;
                                    }
                                    .individualMsg{
                                        margin-bottom:5px;width:100%;padding:1%;background:#fcfcfc;min-height: 60px;border:1px #ddd solid;border-radius: 3px; 
                                    }
                                    .showHide1{
                                        float:right;margin-top:-2px;
                                    }
                                </style>
                                <div style="font-size:0.9em;margin-bottom: -10px;display:none;" id="commMsgSuc" class="success hideSuc"><strong>Success!</strong> Your message was sent successfully.  </div>
                                <br style="display:none;" class="hideSuc">
                                <!--<br style="display:none;" class="hideSuc1">-->
                                <div class="aside profile-meta showCommMsg replyMsgTins" id="sendMsgDivD1" style="">
                                    <center>
                                        <div id="sendMsgDiv" style="width:100%;margin:0 auto;overflow: hidden;">
                                            <div style="font-size:0.9em;margin-bottom: 5px;display:none;" id="commMsgError" class="error"><strong>Error -empty fields!</strong> Empty form submission not allowed!.  </div>
                                            <form name="replyMsgForm" id="replyMsgForm" class="replyMsgTins" action="tuossog-api-json.php" method="POST" enctype="application/x-www-form-urlencoded">
                                                <div class="words" style="margin:2px;float:left">Message:</div><br>
                                                <hr/>
                                                <textarea style="min-height:250px;" name="realMessage" id="realMessage" placeholder="Type your message here. . ." class="commMsgInput"></textarea>
                                                <!--<hr><input type="file" name="commMsgFile" id="commMsgFile" style="border-radius: 3px;border:1px solid #ebebeb;height:30px;width:100px;"/>-->
                                                <hr><p>
                                                    <input type="submit" id="sendResponse" name="sendResponse" class="button submit" value="Send Message" style="float: left;">

                                                    <input type="hidden" name="parent" value="<?php echo $msgId; ?>"/>
                                                    <input type="hidden" name="param" value="sendMsgFromAdmin"/>
                                                <div id="loadMoreImg2" style="float:left;display:none;"> &nbsp;<img src="images/loading.gif"/></div>
                                                <input type="hidden" name ="comId" value="<?php echo $comId; ?>" />
                                                <input type="hidden" name ="receiverId" value="<?php echo $comId; ?>" />

                                                <p>
                                            </form> 
                                            <div style="float:right;"  class="replyMsgTins" >
                                                <a href style="float:left;font-size:12px;" class="showHideCompose" id="showHideCompose"> [Hide] </a>

                                            </div>
                                            <!--<hr>-->
                                            <br><br>
                                        </div>
                                    </center>
                                </div>
                                <div style="margin-top:-40px;">
                                    <br>
                                    <!--<div  style="border-bottom: 1px solid #efefef;width:100%;"></div>-->       
                                    <div class="aside profile-meta showCommMsg mainCommCont" id="inboxCommMsgDiv" style="">
                                        <?php
                                        $childs = Community::getChildren($msgId, 0, $limitChild, $uid);
                                        if ($childs['status']) {
                                            ?>
                                            <div id="inboxCommMsg1">
                                                <?php foreach ($childs['children'] as $k) { ?>
                                                    <div style="" class="individualMsg">
                                                        <span class="convDates"><?php echo "[" . $k['fullname'] . "]"; ?>
                                                            <div style="float:right;">[<span class="timeago convDates" title="<?php echo $k['time']; ?>" ></span>]</div></span> 
                                                        <br> <hr><?php echo (strlen($k['message']) <= 160) ? nl2br($k['message']) : nl2br(substr($k['message'], 0, 160)) . '<span class="showHideRemDot">. . .</span>' ?><span style="display: none;" id="<?php echo $k['id']; ?>" class="showHideRem"><?php echo(strlen($k['message']) > 160) ? nl2br(trim(substr($k['message'], 160))) : ""; ?></span>
                                                        <?php if (strlen($k['message']) > 160) { ?>
                                                            <br>
                                                            <div class="showHide1"><a href="" class="noAnchor">[Show more]</a></div><br>
                                                        <?php } ?>
                                                    </div>

                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                        <hr>
                                        <p>
                                            <?php if (empty($_GET['param2'])) { ?>
                                            <div class="button" style="float:left;" id="comm_more_inbox" start="<?php echo 2; ?>">
                                                <a href="">More messages > ></a>
                                            </div>&nbsp;<img src='images/loading.gif' style='border:none;margin-top: -10px;display:none' id="loader1"/>
                                            <?php
                                        } if ($childs['status']) {
                                            if (!empty($_GET['param2']) && is_numeric($_GET['param2']) && count($childs['children']) === $limitChild) {
                                                ?>
                                                <div class="button getChildren" id="moreChildMsg" style="float:left;" start="<?php echo $limitChild; ?>" parent="<?php echo $msgId; ?>">
                                                    <a href="">More message > ></a>
                                                </div>&nbsp;<img src='images/loading.gif' style='border:none;margin-top: -10px;display:none' id="loader1"/>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>

                                </div>
                            <?php } ?>

                        </div>
                    <?php } ?>
                </span>
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