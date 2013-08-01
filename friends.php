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
    } else {
        include_once './LoginClass.php';
        $login = new Login();
        $login->logout();
        exit;
    }
} else {
    include_once './LoginClass.php';
    $login = new Login();
    $login->logout();
    exit;
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Friends</title>
        <link rel="stylesheet" href="css/validationEngine.jquery.css">
        <?php
        include ("head.php");
        ?>
        <script type="text/javascript" src="scripts/humane.min.js"></script>
        <script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.4"></script>
        <script src="scripts/jquery.timeago.js" type="text/javascript"></script>
        <script src="scripts/test_helpers.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/jquery.form.js"></script>
        <script type="text/javascript" src="scripts/languages/jquery.validationEngine-en.js"></script>
        <script type="text/javascript" src="scripts/jquery.validationEngine.js"></script>
        <script type="text/javascript">
            
            $(document).ready(function() {
                $(".fancybox").fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    minWidth: 250

                });
                $('.noResult,#loader1,#loadMoreFrndDiv').hide();
                var start, limit;
                $('#loadMoreFrnd').click(function(){
                    $('#loader1').show();
                    start = parseInt($(this).attr('frnd'));
                    limit = 10;
                    //                    limit = 1;
                    sendData("loadFriends", {target: "#aside-friends-list", friendPage:'#individual-friend-box',individualFriend:true,start:start,limit:limit,targetLoader:'#individual-friend-box'});
                    return false;
                });
                $("#searchForm").validationEngine();
                $("#searchForm").ajaxForm({
                    beforeSend: function() {
                        if (!($('#searchTerm').val().length > 2) && $('#searchTerm').val() !== "*") {
                            return false;
                        } else {
                            $("#individual-friend-box").html("<center><img src='images/loading.gif'/></center>");
                        }
                    },
                    success: function(responseText, statusText, xhr, $form) {
                        var htmlstr = "";
                        if (responseText.status === true) {
                            if (responseText.people) {
                                $.each(responseText.people, function(i, response) {
                                    htmlstr += '<div class="individual-friend-box"><a class= "fancybox " id="inline" href="#' + response.username + '">' +
                                        '<div class="friend-image"><img src="' + (response.photo.nophoto ? response.photo.alt : response.photo.thumbnail50) + '"></div><div class="friend-text">' +
                                        '<div class="friend-name">' + response.firstname.concat(" ", response.lastname) + '</div>' +
                                        '<div class="friend-location">' + response.location + '</div></div>' +
                                        '<div style="display:none"><div id="' + response.username + '"><div class="aside-wrapper"><div class="profile-pic"><img class="holdam" src="' + (response.photo.nophoto ? response.photo.alt : response.photo.thumbnail150) + '"></div>' +
                                        '<table><tr><td></td><td><h3>' + response.firstname.concat(" ", response.lastname) + '</h3></td></tr>' +
                                        '<tr><td><span class="icon-16-map"></span></td><td class="profile-meta"> ' + response.location + '</td></tr>' +
                                        '<tr><td><span class="icon-16-' + (response.gender === "M" ? "male" : "female") + '"></span></td><td class="profile-meta">' + (response.gender === "M" ? "Male" : "Female") + '</td></tr>' +
                                        '</table><div class="clear"></div>' +
                                        '<div class="profile-meta-functions button" id="wink-f-' + response.id + '"><span class="icon-16-eye"></span> Wink</div>' +
                                        '<div class="profile-meta-functions button"><a href="messages/' + response.username + '"><span class="icon-16-mail"></span> Send Message</a></div>' +
                                        '<div class="profile-meta-functions button" id="unfriend-f-' + response.id + '"><span class="icon-16-checkmark"></span> <span id="unfriend-f-' + response.id + '-text">Unfriend</a></div><span id="friend-action-loading"></span>' +
                                        '<div class="clear"></div></div></div></div></a></div>';
                                });
                                $("#individual-friend-box").html(htmlstr);
                            } else {
                            }
                        } else {
                            if (responseText.status) {
                                humane.log("Community was not created", {timeout: 20000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                            } else {
                                $("#individual-friend-box").html("<center>Oops! Your search critaria produce no result.</center>");
                            }
                        }
                    },
                    data: {
                        param: "search",
                        opt: "mf",
                        uid: readCookie('user_auth')
                    }
                });
                sendData("loadNotificationCount", {title: document.title});
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
                <div class="all-friends-list">
                    <h1>All Friends</h1>

                    <div class="friend-search-box">
                        <form action="tuossog-api-json.php" method="POST" id="searchForm">
                            <input name="a" class="friend-search-field validate[required]" placeholder="Search Friends" type="text" value="" id="searchTerm">
                            <input type="submit" class="button" value="Search">
                        </form>
                    </div>
                    <div class="clear"></div>
                    <span id="individual-friend-box"></span>
                    <!--<div class="clear">&nbsp;</div>-->
                    <div class="clear" style="height:5px;"></div>
                    <div class="button" style="float:left;" id="loadMoreFrndDiv">
                        <a href="" frnd="20" class="loadMoreFrnd" id="loadMoreFrnd">Load more > ></a>
                    </div>&nbsp;<img src='images/loading.gif' style='border:none' id="loader1"/>
                </div>

                <?php
                include("aside.php");
                ?>
                <div class="clear"></div>		
            </div>
            <?php
            include("footer.php");
            ?>
        </div>

    </body>
</html>