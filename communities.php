<?php
header('Content-type: text/html; charset=UTF-8');
include_once './Gossout_Community.php';
$page = $_GET['page'];
$param = $_GET['param'];
$param2 = $_GET['param2'];
if (trim($page) == "communities") {
    if (trim($param) != "") {
        $commExist = Community::communityExist($param);
        if (!$commExist['status']) {
            include_once './404.php';
            exit;
        }
    }
} else {
    $commExist = Community::communityExist($page);
    if (!$commExist['status']) {
        include_once './404.php';
        exit;
    }
//    $isMember = Community::isAmember($comId, $uid);
//    if($commExist['info']['type']=="Private" && )
}
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
    include_once './GossoutUser.php';
    $user = new GossoutUser(0);
    $userProfile = $user->getProfile();
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        if (($page == "communities" && trim($param) != "" && trim($param2) == "") || ($page != "communities" && trim($param) == "" && $param2 == "")) {//load community timeline
            $comname = $_GET['page'] == "communities" ? $_GET['param'] : $_GET['page'];
            $comInfo = Community::getCommunityInfo($comname);
            ?>
            <title><?php echo $comInfo['status'] ? $comInfo['comm']['name'] : "Gossout - Community" ?></title>
            <?php
        } else if (($page != "communities" && trim($param) != "" && trim($param2) == "" && is_numeric($param)) || ($page == "communities" && trim($param) != "" && trim($param2) != "" && is_numeric($param2))) {//load single post
            $comname = $_GET['page'] == "communities" ? $_GET['param'] : $_GET['page'];
            $comInfo = Community::getCommunityInfo($comname);
            ?>
            <title><?php echo $comInfo['status'] ? $comInfo['comm']['name'] : "Gossout - Community" ?></title>
            <?php
        } else {
            ?>
            <title>Gossout - Communities</title>
            <?php
        }
        ?>
        <meta http-equiv="Pragma" http-equiv="no-cache" />
        <meta http-equiv="Expires" content="-1" />
        <link rel="stylesheet" href="css/chosen.css" />
        <link rel="stylesheet" href="css/validationEngine.jquery.css">
        <link rel="stylesheet" type="text/css" href="css/chat.min.1.0.css" />
        <?php
        if (isset($_GET['param']) ? $_GET['param'] != "" ? $_GET['param'] : FALSE  : FALSE) {
            ?>
            <style>
                .progress { position:relative; width:60%; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
                .bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
                .percent { position:absolute; display:inline-block; top:3px; left:48%; }
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
        <script type="text/javascript" src="scripts/waypoints.min.js"></script>
        <script type="text/javascript">
            var current;
            var start = 0, limit = 10, currentCom = 'myCom';
            $(document).ready(function() {
                var currentLocation = window.location + "";
                var lastChar = currentLocation.substring(currentLocation.length - 1);
                if (lastChar === "/") {
                    currentLocation = currentLocation.substring(0, currentLocation.length - 1);
                }
                current = currentLocation.split("/");
<?php
if (trim($param) == "" && trim($param2) == "" && $page == "communities") {//load all community, user communities and suggested community
    ?>
                    sendData("loadCommunity", {target: "#my-communities-list", comType: 'myCom', loadImage: true, max: true, start: 0, limit: limit});
                    $('.clicked').click(function() {
                        $('#loadMoreComm').show();
                    });
                    $('#my-communities').addClass('active');
                    $("#my-communities").click(function() {
                        $('#loadMoreComm').hide();
                        currentCom = 'myCom';
                        $('#all-communities-list,#suggestion-list').hide();
                        $('#suggestions,#all').removeClass('active');
                        $('#my-communities').addClass('active');
                        $('#my-communities-list').show();
                        //                         if (!($(this).hasClass('clicked'))) {
                        sendData("loadCommunity", {target: "#my-communities-list", comType: 'myCom', loadImage: true, max: true, start: 0, limit: limit});
                        //                            $(this).addClass('clicked');
                        //                        }

                    });
                    $("#all").click(function() {
                        currentCom = 'allCom';
                        start = 0;
                        //                        $('#loadMoreComm').show();
                        $('#suggestion-list,#my-communities-list').hide();
                        $('#all-communities-list').show();
                        $('#suggestions,#my-communities').removeClass('active');
                        $('#all').addClass('active');
                        if (!($(this).hasClass('clicked'))) {
                            sendData("loadCommunity", {target: "#all-communities-list", comType: 'allCom', loadImage: true, max: true, start: start, limit: limit});
                            $(this).addClass('clicked');
                        }

                    });
                    $("#suggestions").click(function() {
                        currentCom = 'sugCom';
                        start = 0;
                        $('#all-communities-list,#my-communities-list').hide();
                        $('#suggestion-list').show();
                        //		   
                        $('#my-communities,#all').removeClass('active');
                        $('#suggestions').addClass('active');
                        sendData("loadSuggestCommunity", {
                            target: "#suggestion-list",
                            loadImage: true,
                            max: true,
                            start: start,
                            Limit: limit
                        });
                    });
                    var theTraget;
                    $('#loadMoreComm').click(function() {
                        $('#loader1').show();
                        if (currentCom === 'allCom') {
                            theTraget = '#all-communities-list';
                            start = parseInt($('#loadMoreComm').attr('allcomm'));
                        }
                        else if (currentCom === 'myCom') {
                            theTraget = '#my-communities-list';
                            start = parseInt($('#loadMoreComm').attr('mycomm'));
                        }
                        else if (currentCom === 'sugCom') {
                            theTraget = '#suggestion-list';
                            start = parseInt($('#loadMoreComm').attr('sugcomm'));
                        } else {

                        }
                        if (currentCom === 'allCom' || currentCom === 'myCom') {
                            sendData("loadCommunity", {target: theTraget, comType: currentCom, loadImage: false, max: true, start: start, limit: limit, more: true});
                            return false;
                        } else if (currentCom === 'sugCom') {
                            sendData("loadSuggestCommunity", {target: theTraget, loadImage: true, max: true, start: start, Limit: limit, more: true});
                            return false;
                        } else {

                        }

                    });
    <?php
} else {
    if (($page == "communities" && trim($param) != "" && trim($param2) == "") || ($page != "communities" && trim($param) == "" && $param2 == "")) {//load community timeline
        ?>
                        sendData("loadCommunity", {target: "#rightcolumn", loadImage: true, max: true, loadAside: true, comname: "<?php echo trim($param) == "" ? $page : $param ?>", start: 0, limit: 10});
        <?php
    } else if (($page != "communities" && trim($param) != "" && trim($param2) == "" && is_numeric($param)) || ($page == "communities" && trim($param) != "" && trim($param2) != "" && is_numeric($param2))) {//load single post
        ?>
                        sendData("loadCommunity", {target: "#rightcolumn", loadImage: true, max: true, loadAside: true, loadPost: "<?php echo trim($param2) == "" ? $param : $param2 ?>", comname: "<?php echo trim($param2) == "" ? $page : $param ?>", start: 0, limit: 10});
        <?php
    } else if (($page == "communities" && trim($param) != "" && trim($param2) != "" && $param2 != "members") || ($page != "communities" && trim($param) != "" && trim($param2) == "" && $param == "members")) {//load community members
    }
}
?>
                var user = readCookie('user_auth');
                if (user !== 0) {
                    sendData("loadNotificationCount", {title: document.title});
                } else if (user === 0) {
                    $('.nouser').hide();
                }
                $(".fancybox").fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    minWidth: 250
                });
                $("#commMsgForm").ajaxForm({
                    beforeSubmit: function() {
                        if ($.trim($('#messageTitle').val()) === "" || $.trim($('#message').val()) === "") {
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
//                        alert('Success');
                    },
                    complete: function(xhr) {
//                    alert('Complete');
                    },
                    data: {
                        uid: readCookie("user_auth")
                    }
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
            <div class="logo" id="logo"><img alt=""></div>

            <div class="content">
                <span id="rightcolumn" class="">
                    <?php if ($_GET['page'] == "communities" && $_GET['param'] == "") { ?>
                        <div class="communities-list">
                            <div>
                                <div style="float:left;background:#f8f8f8;padding:3px;width:94px;border:1px solid #c6c6c6;"><a href="create-community">Create new </a></div>
                                <div><h3>&nbsp;Communities of your own World! It's pretty easy!</h3></div>
                            </div>
                            <div class="clear"></div>      
                            <h1>Communities</h1>
                            <div class="clear"></div>


                            <div class="community-box">
                                <div class="timeline-filter">
                                    <ul>
                                        <li><span class="icon-16-earth"></span></li>
                                        <li id="my-communities" class="nouser"><a>My Communities</a></li>
                                        <!--<li id="suggestions"><a>Suggested</a></li>-->
                                        <li id="all"><div ><a>All</a></li>


                                    </ul>
                                </div>
                                <div class="clear"></div>
                                <div id="my-communities-list">

                                </div>
                                <div id="suggestion-list">

                                </div>
                                <div id="all-communities-list">

                                </div>




                            </div>
                            <div class="button" style="float:left;margin-top: -10px;display:none" id="loadMoreComm" allcomm="10" mycomm="10" sugcomm="10">
                                <a href="">Load more > ></a>
                            </div>&nbsp;<img src='images/loading.gif' style='border:none;margin-top: -10px;display:none' id="loader1"/>

                        </div>
                    <?php } ?>

                </span>

                <?php
                if (($page == "communities" && trim($param) != "" && trim($param2) == "") || ($page == "communities" && trim($param) != "" && trim($param2) != "") || ($page != "communities" && trim($param) == "" && $param2 == "") || ($page != "communities" && trim($param) != "" && $param2 == "")) {
                    include("sample-community-aside.php");
                } else {
                    include("aside.php");
                }
                ?>


            </div>
            <?php
            include("footer.php");
            ?>
            <style>
                .commMsgInput{
                    border: 1px #ccc solid;
                    border-radius:3px;
                    width:100%;
                }
                .words{
                    font-size:0.8em;
                    margin-top:5px;
                }
            </style>
            <div class="contactAdminMsg" id="contactAdminMsg-<?php echo $comInfo['status'] ? $comInfo['comm']['id'] : "" ?>" style='display:none;width:500px;max-width:90%; position: fixed;right:10px;bottom: 2px;background:white;padding:5px;border: 1px #ccc solid;border-radius:3px;'>
                <div style='width:100%;cursor: pointer;'><h3>Send new message to <span id="fullCommName"><?php echo $comInfo['comm']['unique_name']; ?></span><span id='closeMsg' style='float:right;margin-right:5px;'><strong>x</strong></span></h3></div><hr>
                <div id="sendMsgDiv">
                    <div style="font-size:0.9em;margin-bottom: 5px;display:none;" id="commMsgError" class="error"><strong>Error -empty fields!</strong> All fields must be filled before your message could be sent.  </div>
                    <form name="commMsgForm" id="commMsgForm" action="tuossog-api-json.php" method="POST" enctype="application/x-www-form-urlencoded">
                        <div class="words">Message Title:</div>
                        <hr/>
                        <input type="text" name="messageTitle" id="messageTitle" class="commMsgInput" placeholder="Type the title of you message here (compulsory)"/>
                        <p><div class="words" style="margin-top:5px;">Message:</div>
                        <hr/>
                        <textarea style="min-height:250px;" name="message" id="message" placeholder="Type your message here. . ." class="commMsgInput"></textarea>
                        <!--<hr><input type="file" name="commMsgFile" id="commMsgFile" style="border-radius: 3px;border:1px solid #ebebeb;height:30px;width:100px;"/>-->
                        <p></p>
                        <hr/>

                        <input type="submit" id="sendMsg" name="sendMsg" class="button submit" value="Send Message" style="float: left;">
                        <input type="hidden" name="param" value="Send-Community-Message"/><div id="loadMoreImg" style="display:none;"> &nbsp;<img src="images/loading.gif"/></div>
                        <input type="hidden" name ="comId" value="<?php echo $comInfo['comm']['id']; ?>" />
                    </form>
                    <br/><br/>
                </div>
                <div id="feedBackMsgDiv" style="display:none;">

                    <div style="font-size:0.9em;margin-bottom: 14px;" class="success"><strong>Successful delivery!</strong> Your message with the details below was sent successfully. Be aware that the response to this message shall be delivered to your Inbox as soon as the Administrator replies.</div>
                    <div class="" style="font-size:0.9em;margin-top:-5px;"><h3><span id="returnTitle"></span></h3></div>
                    <hr/>
                    <div class="" style="font-size:0.8em;padding:5px;" id="returnMessage"></div>

                </div>

            </div>
        </div>
    </body>
</html>