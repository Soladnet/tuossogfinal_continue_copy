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
            Gossout - Create Community
        </title>
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>
        <link rel="stylesheet" href="css/jquery-ui-base-1.8.20.css"/>
        <link rel="stylesheet" href="css/tagit-dark-grey.css"/>
        <?php
        include ("head.php");
        ?>
        <script type="text/javascript" src="scripts/jquery-ui.1.8.20.min.js"></script>
        <script type="text/javascript" src="scripts/tagit.js"></script>
        <script src="scripts/humane.min.js"></script>
        <script src="scripts/jquery.timeago.js" type="text/javascript"></script>
        <script src="scripts/test_helpers.js" type="text/javascript"></script>
        <script src="scripts/languages/jquery.validationEngine-en.js" type="text/javascript"></script>
        <script src="scripts/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="scripts/jquery.form.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#communityTag').tagit({select: true});
                jQuery("#creatForm").validationEngine();
                $("#imageSelectBtn").click(function() {
                    $("#comImageField").focus().trigger("click");
                });
                var bar = $('.bar');
                var percent = $('.percent');
                $('#creatForm').ajaxForm({
                    beforeSend: function() {
                        $(".progress").show();
                        var percentVal = '0%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                        $("#creatLoading").html("<img src='images/loading.gif'/>");
                    },
                    uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                    },
                    success: function(responseText, statusText, xhr, $form) {
                        var percentVal = 'Completed!';
                        bar.width(percentVal)
                        percent.html(percentVal);
                        if (responseText.status === "success") {
                            $("#creatForm").resetForm();
                            $("#noCom").hide();
                            $("#cc").html(parseInt($("#cc").html()) + 1);
                            $("#aside-community-list").prepend('<div class="community-listing"><span><a href="' + responseText.unique_name + '">' + responseText.name + '</a></span></div><hr>');
                            humane.log("Community created successfully", {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-success'});
                        } else {
                            if (responseText.status) {
                                humane.log("Community was not created", {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                            } else {
                                humane.log(responseText.error.message, {timeout: 3000, clickToClose: true, addnCls: 'humane-jackedup-error'});
                            }
                        }
                    },
                    complete: function(xhr) {
                        $(".progress").hide();
                        $("#creatLoading").html("");
                    },
                    data: {
                        param: "creatCommunity",
                        uid: readCookie('user_auth')
                    }
                });
                $(".fancybox").fancybox({
                    openEffect: 'none',
                    closeEffect: 'none'

                });
                sendData("loadNotificationCount", {title: document.title});
                var countDesc = 2000;
                $("#desc").keyup(function() {
                    if (!($("#desc").val().length > 2000)) {
                        $("#countDesc").html(countDesc - $("#desc").val().length);
                    } else {
                        $("#desc").val($("#desc").val().substring(0, 2000));
                    }
                });
            });
        </script>
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
                <div class="create-community">
                    <h1>Create Community</h1>
                    <hr>
                    <h3 class="success">Go the best way on gossout! Create and join communities of your interest to make more friends and meet people. It is free!.</h3>
                    <!--                    <h3 class="notice">Please, NOTE that Communities are deleted 
                                            after <strong>XX</strong> days of inactivity.</h3>-->
                    <hr>
                    <form method="POST" action="tuossog-api-json.php" id="creatForm">
                        <div class="individual-detail">
                            <h2>Helve</h2>
                            <p class="desc">Give your community a helve; example: WHO for World Health organizations</p>
                            <input type="text" class="validate[required,ajax[ajaxCommunityNameCallPhp]] text-input input-fields" name="helve"/>
                        </div>

                        <div class="individual-detail">
                            <h2>Name</h2>
                            <p class="desc">This name would be used to identify the community</p>
                            <input type="text" name="name" class="validate[required,maxSize[100]] text-input input-fields">
                        </div>

                        <div class="individual-detail">
                            <h2>About</h2>
                            <p class="desc">Give a short description of the community ( <span id="countDesc">2000</span> )</p>
                            <textarea name="desc" class="input-fields validate[required,maxSize[2000]]" id="desc"></textarea>
                            <p class="desc">Add tags to help other users discover your community more quickly</p>
                            <ul id="communityTag" data-name="comTag[]"></ul>

                        </div>


                        <div class="individual-detail">
                            <h2>Privacy</h2>
                            <p class="desc">Disable Post from Members <input type="checkbox" name="disablePost" value="0" id="enablePost"/></p>
                            <hr/>
                            <p class="desc">Private communities can only be accessed by members that are invited to join</p>
                            <p class="desc">Make community private <input type="checkbox" value="Private" name="privacy"></p>
                        </div>
                        <div class="individual-detail">
                            <h2>Community Photo</h2>
                            <p class="desc">Logo, Badge, whatever image that best represents your Community</p>
                            <p class="desc">Image must be of the following type: .jpg, .png or .jpeg and must not be more than 2MB of size</p>
                            <hr>
                            <label>Select an image: </label>
                            <input type="file" onchange="$('#selectedFile').html('<br/><strong>File Name:</strong> ' + (this.value.substring(this.value.lastIndexOf('\\') + 1)));" name="img" class="input-fields" id="comImageField" style="position: absolute;left: -9999px;"><div class="button" id="imageSelectBtn"><span class="icon-16-camera"></span></div><span id="selectedFile"></span>
                            <!--<p></p>-->
                            <!--<input type="submit" class="button" value="Upload photo">-->
                        </div>
                        <div class="progress" style="display: none">
                            <div class="bar"></div >
                            <div class="percent">0%</div >
                        </div>
                        <div id="status"></div>
                        <hr/>
                        <br>
                        <input type="submit" class="button-big" value="Create"><span id="creatLoading"></span>
                    </form>
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