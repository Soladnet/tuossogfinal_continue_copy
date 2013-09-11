<?php
header('Content-type: text/html; charset=UTF-8');
include_once './Gossout_Community.php';
$page = $_GET['page'];
$param = $_GET['param'];
$param2 = $_GET['param2'];
if (trim($page) == "community-settings") {
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
}
if (isset($_COOKIE['user_auth'])) {
    include_once './encryptionClass.php';
    include_once './GossoutUser.php';
    include_once './Gossout_Community.php';
    $encrypt = new Encryption();
    $uid = $encrypt->safe_b64decode($_COOKIE['user_auth']);
    $comHelve = $_GET['param'];
    if (trim($comHelve) != "") {
        $isCreator = Community::isCreator($comHelve, $uid);
        if (!$isCreator['status']) {
            header("Location: ../home");
            exit;
        }
    } else {
        header("Location: ../home");
        exit;
    }
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
        <title>Gossout - Settings</title>
        <link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
        <link rel="stylesheet" href="css/chosen.css" />
        <?php
        if (isset($_GET['param']) ? $_GET['param'] != "" ? $_GET['param'] : FALSE  : FALSE) {
            ?>
            <link rel="stylesheet" href="css/jquery-ui-base-1.8.20.css"/>
            <link rel="stylesheet" href="css/tagit-dark-grey.css"/>
            <?php
        }
        include ("head.php");
        ?>
        <script type="text/javascript" src="scripts/humane.min.js"></script>
        <script src="scripts/jquery.timeago.js" type="text/javascript"></script>
        <script src="scripts/test_helpers.js" type="text/javascript"></script>
        <script src="scripts/languages/jquery.validationEngine-en.js" type="text/javascript"></script>
        <script src="scripts/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="scripts/jquery.form.js"></script>
        <script type="text/javascript" src="scripts/jquery.fancybox.pack.js?v=2.1.4"></script>
        <script src="scripts/chosen.jquery.min.js" type="text/javascript"></script>
        <?php
        if (isset($_GET['param']) ? $_GET['param'] != "" ? $_GET['param'] : FALSE  : FALSE) {
            ?>
            <script type="text/javascript" src="scripts/jquery-ui.1.8.20.min.js"></script>
            <script type="text/javascript" src="scripts/tagit.js"></script>
            <script type="text/javascript">
                var current;
                $(document).ready(function() {
                    var currentLocation = window.location + "";
                    var lastChar = currentLocation.substring(currentLocation.length - 1);
                    if (lastChar === "/") {
                        currentLocation = currentLocation.substring(0, currentLocation.length - 1);
                    }
                    current = currentLocation.split("/");
                    var countDesc = 2000;
                    $("#commDescription").keyup(function() {
                        if (!($("#commDescription").val().length > countDesc)) {
                            $("#countDesc").html(countDesc - $("#commDescription").val().length);
                        } else {
                            $("#commDescription").val($("#commDescription").val().substring(0, 2000));
                        }
                    });
                    sendData("loadCommunity", {target: "#rightcolumn", loadImage: true, max: true, loadAside: true, comname: '<?php echo $_GET['param'] ?>', settings: true});
                    sendData("loadNotificationCount", {title: document.title});
                    $("#settingsForm,#imageChangeForm").validationEngine();
                    $("#settingsForm").ajaxForm({
                        beforeSend: function() {
                            $("#profileText").show();
                            var percentVal = '0%';
                            bar.width(percentVal)
                            percent.html(percentVal);
                        }, uploadProgress: function(event, position, total, percentComplete) {
                            var percentVal = percentComplete + '%';
                            bar.width(percentVal)
                            percent.html(percentVal);
                        },
                        success: function(responseText, statusText, xhr, $form) {
                            var percentVal = '100%';
                            bar.width(percentVal)
                            percent.html(percentVal);
                            if (!responseText.error) {
                                $("#commTitle").html(responseText.name);
                                $("#commDesc").html(nl2br(linkify(responseText.desc)));
                                humane.log(responseText.message, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-success'});
                            } else {
                                humane.log(responseText.error.message, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-success'});
                            }
                        },
                        complete: function(xhr) {
                            $("#profileText").hide();
                        }
                    });
                    var bar = $('.bar');
                    var percent = $('.percent');
                    $("#imageChangeForm").ajaxForm({
                        beforeSend: function() {
                            $("#photoProgress").show();
                            var percentVal = '0%';
                            bar.width(percentVal)
                            percent.html(percentVal);
                        }, uploadProgress: function(event, position, total, percentComplete) {
                            var percentVal = percentComplete + '%';
                            bar.width(percentVal)
                            percent.html(percentVal);
                        }, success: function(responseText, statusText, xhr, $form) {
                            var percentVal = '100%';
                            bar.width(percentVal)
                            percent.html(percentVal);
                            if (responseText.status) {
                                $("#imageChangeForm").resetForm();
                                document.getElementById("commPix").src = document.getElementById("com-img").src = responseText.thumb;
                                humane.log(responseText.message, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-success'});
                            } else {
                                if (responseText.error) {
                                    humane.log(responseText.error.message, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                                } else {
                                    humane.log(responseText.message, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                                }
                            }
                        },
                        complete: function(xhr) {
                            $("#photoProgress").hide();
                        },
                        data: {
                            name: ""
                        }
                    });
                    $("#uploadFileBtn").click(function() {
                        $("#fileUpload").trigger('click');
                    });

                });
            </script>
            <?php
        }
        ?>
        <style>
            .progress { position:relative; width:400px; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
            .bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
            .percent { position:absolute; display:inline-block; top:3px; left:48%; }
        </style>
    </head>
    <body>
        <div class="page-wrapper">
            <?php
            include ("nav.php");
            include ("nav-user.php");
            ?>
            <div class="logo" id="logo"><img alt=""></div>

            <div class="content">
                <div class="settings-list create-community">
                    <h1>Community Settings</h1>
                    <hr>
                    <hr>
                    <form action="tuossog-api-json.php" method="POST" id="imageChangeForm" enctype="application/x-www-form-urlencoded">
                        <div class="individual-setting individual-detail">
                            <h2>Photo</h2>
                            <span class="pic-user">
                                <img onload="OnImageLoad(event);" src="images/no-pic.png" id="com-img">
                            </span>
                            <hr>
                            <input type="file" onchange="$('#selectedFile').html('<br/><strong>File Name:</strong> ' + (this.value.substring(this.value.lastIndexOf('\\') + 1)));" name="img" id="fileUpload" class="input-fields validate[required]" style="position: absolute;left: -9999px;">
                            <input type="hidden" name="param" value="Update Community" />
                            <input type="hidden" name="creator" value="" class="creator_field"/> 
                            <input type="hidden" name="helve" readonly="" class="validate[required] helve">
                            <div class="button" id="uploadFileBtn"><span class="icon-16-camera"></span> Choose Photo</div><span id="selectedFile"></span>
                            <p class="desc">Logo, Badge, whatever image that best represents your community
                                Image must be of the following type: .jpg, .png or .jpeg and must not be more than 2MB of size</p>
                            <input type="submit" class="button" value="Upload photo">
                            <div class="progress" id="photoProgress" style="display: none">
                                <div class="bar"></div >
                                <div class="percent">0%</div >
                            </div>
                            <hr>
                        </div>
                    </form>
                    <form method="POST" action="tuossog-api-json.php" id="settingsForm">
                        <div class="individual-setting">
                            <h2>Helve</h2>
                            <input type="hidden" name="creator" value="" id="creator_field" class="creator_field" />
                            <input type="text" name="helve" id="helve" readonly="" class="validate[required] helve">
                        </div>
                        <div class="individual-setting">
                            <h2>Name</h2>
                            <input type="text" name="name" id="commName" class="validate[required,maxSize[100]]">
                        </div>
                        <div class="individual-setting">
                            <h2>Description ( <span id="countDesc">2000</span> )</h2>
                            <textarea name="desc" id="commDescription" rows="5" class="validate[required,maxSize[2000]">
                            </textarea>
                        </div>
                        <div class="individual-setting">
                            <div class="desc">Add more tags to help other users discover your community more quickly</div>
                            <ul id="communityTag" data-name="comTag[]">
                                <!--                                <li data-value="here">here</li>
                                                                <li data-value="are">are</li>
                                                                <li data-value="some...">some</li>
                                                                 notice that this tag is setting a different value :) 
                                                                <li data-value="initial">initial</li>
                                                                <li data-value="tags">tags</li>-->
                            </ul>
                        </div>
                        <div class="individual-setting">
                            <h2>Disable post from members <input type="checkbox" name="disablePost" value="0" id="enablePost"/></h2>
                        </div>
                        <div class="individual-setting">
                            <h2>Privacy</h2>
                            <p> <input type="checkbox" name="privacy" value="Private" id="privacy"> Make this community private</p>
                        </div>
                        <!--                    <div class="individual-setting">
                                                <h2>Notifications</h2>
                                                <p> <input type="checkbox"> Receive notifications through e-mail</p>
                                            </div>-->
                        <div class="button"><a id="setting_cancel">Cancel</a></div>
                        <input type="submit" class="button submit" value="Save Changes"><input type="hidden" name="param" value="Update Community"/>
                        <div class="progress" id="profileText" style="display: none">
                            <div class="bar"></div >
                            <div class="percent">0%</div >
                        </div>
                    </form>
                </div>
                <?php
                include("sample-community-aside.php");
                ?>			
            </div>
            <?php
            include("footer.php");
            ?>
        </div>

    </body>
</html>