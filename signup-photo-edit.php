<?php
if (session_id() == ""){
    session_name('GSID');
    session_start();
}
if (isset($_GET['skip'])) {
    header("Location: signup-agreement");
    exit;
} else {
    include_once './GossoutUser.php';
    $user = new GossoutUser(0);
    $photo = "";
    if (isset($_COOKIE['user_auth'])) {
        include_once './encryptionClass.php';
        $encrypt = new Encryption();
        $id = $encrypt->safe_b64decode($_COOKIE['user_auth']);
        if (is_numeric($id)) {
            $user->setUserId($id);
            $user->getProfile();
            $pix = $user->getProfilePix();
            if ($pix['status']) {
                $photo = $pix['pix'];
            } else {
                header("Location: signup-agreement");
                exit;
            }
        } else {
            include_once './LoginClass.php';
            $login = new Login();
            $login->logout();
        }
    } else {
        include_once './LoginClass.php';
        $login = new Login();
        $login->logout();
    }
}
?>
<!doctype html>
<html>
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <link rel="stylesheet" href="css/jquery.Jcrop.min.css" type="text/css" />
        <?php
        include ("head.php");
        ?>
        <script src="scripts/jquery.min.js"></script>
        <script src="scripts/jquery.Jcrop.min.js"></script>
        <script src="scripts/jquery.form.js"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                var options = {
                    target: '#output', // target element(s) to be updated with server response 
                    beforeSubmit: showLoading, // pre-submit callback 
                    success: showResponse, // post-submit callback 

                    // other available options: 
                    //url:       url         // override for form's 'action' attribute 
                    //type:      type        // 'get' or 'post', override for form's 'method' attribute 
                    //dataType:  null        // 'xml', 'script', or 'json' (expected server response type) 
                    //clearForm: true        // clear all form fields after successful submit 
                    //resetForm: true        // reset the form after successful submit 

                    // $.ajax options can be used here too, for example: 
                    timeout: 30000
                };
                $('#jcropform').ajaxForm(options);
            });

            function showLoading(formData, jqForm, options) {
                $('#doneCroping').attr("disabled", "disabled");
                $('#output').html("<img src='images/loading.gif'/>");
                return true;
            }
            function showResponse(responseText, statusText, xhr, $form) {
                $('#doneCroping').removeAttr("disabled");
            }
            var boundx,
                    boundy,
                    $pcnt = $('.profile-pic'),
                    $pimg = $('.profile-pic img'),
                    xsize = $pcnt.width(),
                    ysize = $pcnt.height();
            $(function() {


                $('#target').Jcrop({
                    setSelect: [0, 0, 150, 150],
                    minSize: [150, 150],
                    maxSize: [150, 150],
                    aspectRatio: 1,
                    onSelect: updateCoords
                });

            });

            function updateCoords(c)
            {
                console.log('init', [boundx, boundy]);
                $('#x').val(c.x);
                $('#y').val(c.y);
                $('#w').val(c.w);
                $('#h').val(c.h);
            }
            ;
            function checkCoords() {
                if (parseInt($('#w').val()))
                    return true;
                alert('Please select a crop region then press submit.');
                return false;
            }
        </script>

    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login"><?php echo "Welcome " . $user->getFullname() ?></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="index-logo">
                    <img src="images/gossout-logo-text-and-image-svg.svg" alt="logo" >
                </div>
            </div>
            <div class="index-intro">	
                <div class="index-intro-2">
                    <div class="registration">
                        <div class="index-intro-1">
                            <h1>
                                Ahaa... That's it! 
                            </h1>
                        </div>	
                        <progress max="100" value="75" >4 of 4 completed!</progress>
                        <hr>
                        <form method="POST" action="cropimg.php" id="jcropform" onsubmit="return checkCoords()">
                            <ul>
                                <li>
                                    <label for="profile-pic">Get a thumbnail and you are set!!!</label>
                                    <div class="profile-pic">
                                        <img src="<?php echo $photo['original'] ?>" id="target">
                                    </div>
<!--<img src="<?php
                                    $size = getimagesize($photo['original']);
                                    echo $photo['original']
                                    ?>" <?php echo $size[3] ?>id="target">-->

                                    <input type="text" name="x" id="x"/>   
                                    <input type="text" name="y" id="y"/>
                                    <input type="text" name="w" id="w"/>
                                    <input type="text" name="h" id="h"/>
                                    <hr>
                                    <p>Drag and adjust the image cropping tool to get the required thumbnail.</p>
                                    <input type="submit" class="button" id="doneCroping" value="Done"><span id="output"></span>
                                    <hr>
                                </li>
                            </ul>
                            <br>
                        </form>
                        <div class="button"><a href="signup-agreement">Finish!</a></div>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="index-shadow-bottom"></div>
            <div class="index-content-wrapper">
                <?php
                include("footer.php");
                ?>
            </div>

        </div>
    </body>
</html>