<?php
header('Content-type: text/html; charset=UTF-8');
include_once './Gossout_Community.php';
$page = $_GET['page'];
if (trim($page) == "communities") {
    $comHelve = $_GET['param'];
    if (trim($comHelve) != "") {
        $commExist = Community::communityExist($comHelve);
        if (!$commExist['status']) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }
    }
} else {
    $commExist = Community::communityExist($page);
    if (!$commExist['status']) {
        header("HTTP/1.0 404 Not Found");
        exit;
    }
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
//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";
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
                if (current[current.length - 1].toLowerCase() === "communities") {
                    sendData("loadCommunity", {target: "#my-communities-list", comType: 'myCom', loadImage: true, max: true, start: 0, limit: limit});
                    $("#searchForm").validationEngine();
                    $("#searchForm").ajaxForm({
                        beforeSend: function() {
                            if (!($('#searchTerm').val().length > 2) && $('#searchTerm').val() !== "*") {
                                return false;
                            } else {
                                $(".community-box").html("<center><img src='images/loading.gif'/></center>");
                            }
                        },
                        success: function(responseText, statusText, xhr, $form) {
                            var htmlstr = "";
                            if (responseText.status === true) {
                                if (responseText.community) {
                                    $.each(responseText.community, function(i, response) {
                                        htmlstr += '<div class="community-box-wrapper"><div class="community-image">' +
                                                '<img src="' + response.thumbnail100 + '">' +
                                                '</div><div class="community-text"><div class="community-name">' +
                                                '<a href="' + response.unique_name + '">' + response.name + '</a> </div><hr><div class="details">' + (br2nl(response.description).length > 100 ? br2nl(response.description).substring(0, 100) + "..." : br2nl(response.description)) +
                                                '</div><div class="members">' + response.type + '</div><div class="members">' + response.mem_count + ' ' + (response.mem_count > 1 ? "Members" : "Member") + '</div><div class="members">' + response.post_count + ' ' + (response.post_count > 1 ? "Posts" : "Post") + '</div></div><div class="clear"></div></div>';
                                    });
                                    $(".community-box").html(htmlstr);
                                } else {
                                }
                            } else {
                                if (responseText.status) {
                                    humane.log("Community was not created", {timeout: 20000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                                } else {
                                    $(".community-box").html("<center>No result found!</center>");
                                }
                            }
                        },
                        data: {
                            param: "search",
                            opt: "mc",
                            uid: readCookie('user_auth')
                        }
                    });
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
                } else {
                    if (current[current.length - 2].toLowerCase() === "communities") {
                        sendData("loadCommunity", {target: "#rightcolumn", loadImage: true, max: true, loadAside: true, comname: current[current.length - 1], start: 0, limit: 10});
                    } else if (current[current.length - 3].toLowerCase() === "communities") {
                        // load Community members
                    } else {
                        sendData("loadCommunity", {target: "#rightcolumn", loadImage: true, max: true, loadAside: true, comname: current[current.length - 1], start: 0, limit: 10});
                    }
                }

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

                            <div class="community-search-box">
                                <form action="tuossog-api-json.php" method="POST" id="searchForm">
                                    <input name="a" class="community-search-field validate[required]" id="searchTerm" placeholder="Search your communities" type="text" value="" >
                                    <input type="submit" class="button" value="Search">
                                </form>
                            </div>
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
                if (($_GET['page'] == "communities" && $_GET['param'] != "") || ($_GET['page'] != "communities" && $_GET['param'] == "")) {
                    include("sample-community-aside.php");
                } else {
                    include("aside.php");
                }
                ?>


            </div>
            <?php
            include("footer.php");
            ?>
        </div>
    </body>
</html>