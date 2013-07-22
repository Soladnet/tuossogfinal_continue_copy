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
        <title>
            Gossout - Messages
        </title>

        <link rel="stylesheet" href="css/chosen.css" />
        <?php
        include ("head.php");
        ?>
        <script src="scripts/jquery.timeago.js" type="text/javascript"></script>
        <script src="scripts/test_helpers.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.4"></script>
        <script type="text/javascript" src="scripts/humane.min.js"></script>
        <script type="text/javascript" src="scripts/jquery.form.js"></script>
        <script src="scripts/chosen.jquery.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                sendData("loadNotificationCount", {title: document.title});
                sendData("loadNavMessages", {target: "#individual-message-box", loadImage: true <?php echo isset($_GET['param']) ? $_GET['param'] != "" ? ",cw:'" . $_GET['param'] . "'" : ""  : "" ?>});
                $(".fancybox").fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    minWidth: 250
                });
                $(".newMessage").fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    minWidth: 500,
                    afterClose: function() {
                        $("#new-message-btn").removeClass("Open");
                    }
                });

            });
            $('#newMsgForm').ajaxForm({
                beforeSubmit: function() {
                    $("#messageStatus").html("<img src='images/loading.gif' />");
                },
                success: function(responseText, statusText, xhr, $form) {
                    $("#messageStatus").html("");
                    $("#newMsgForm").clearForm();
                    $('select').trigger('liszt:updated');
                    $("#msg").val("");
                    $.fancybox.close();
                    if (!responseText.error) {
                        if (responseText.status) {
                            humane.log("Message sent successfully!", {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-success'});
                        } else {
                            humane.log("Message was not sent!", {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                        }
                    } else {
                        humane.log(responseText.error.message, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                    }
                },
                complete: function(response, statusText, xhr, $form) {
                    if (response.error) {
                        $("#messageStatus").html(response.error.message);
                    } else {
                        $("#messageStatus").html("");
                    }
                },
                data: {
                    uid: readCookie("user_auth")
                }
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
                <div class="all-messages-list">
                    <h1 id="messageTitle">Messages</h1>
                    <span id="msgHeader">
                        <a href="#newMsg" class="newMessage" id="new-message-btn"><input type="submit" class="button submit float-right" value="New Message">
                            <div style="display:none">
                                <div id="newMsg" class="registration" style="width: 800">
                                    <h3>Message</h3>
                                    <hr/>
                                    <form id="newMsgForm" method="POST" action="tuossog-api-json.php" name="Ola">
                                        <ul>
                                            <li>
                                                <label for="To" id="toLabel">To</label>
                                                <span id="toUserInput">
                                                </span>
                                                                                    <!--<input type="text" class="input-fields" name="user" placeholder="Enter user's username"/>-->
                                            </li>
                                            <li>
                                                <label for="To" id="msgLabel">Message</label>
                                                <textarea class="input-fields" id="msg" placeholder="Enter message here" name="message"></textarea>
                                            </li>
                                            <li>
                                                <input id="sendBtn" type="submit" class="button submit" name="param" value="Send Message" /><span id="messageStatus"></span>
                                            </li>
                                        </ul>
    <!--                                    <script src="scripts/chosen.jquery.js" type="text/javascript"></script>
                                        <script type="text/javascript">
                                            $(".chzn-select").chosen();
                                            $(".chzn-select-deselect").chosen({allow_single_deselect: true});
                                        </script>-->
                                    </form>

                                </div>
                            </div>
                        </a>
                    </span>
                    <span id="individual-message-box">
                    </span>
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