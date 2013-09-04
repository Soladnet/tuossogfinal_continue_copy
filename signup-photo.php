<?php
include_once './Config.php';

if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['cpassword'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];
} else {
    $_SESSION['signup_login_error']['message'] = "Invalid Login credentials";
    header("Location: signup-login?signup_login_error=");
    exit();
}

if (isValidEmail($email)) {
    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
    if ($mysql->connect_errno > 0) {
        $arrayToJs[] = TRUE;
//        echo json_encode($arrayToJs);
    } else {
        $sql = "SELECT * FROM `user_personal_info` WHERE email = '" . clean($email) . "'";
        if ($result = $mysql->query($sql)) {
            if ($result->num_rows > 0) {
                if (isset($_COOKIE['user_auth'])) {
                    include_once './GossoutUser.php';
                    $userReg = new GossoutUser(0);
                    $resp['status'] = TRUE;
                    include_once './encryptionClass.php';
                    $encrypt = new Encryption();
                    $id = $encrypt->safe_b64decode($_COOKIE['user_auth']);
                    if (is_numeric($id)) {
                        $resp['id'] = $id;
                        if ($resp['status']) {
                            $userReg->setUserId($resp['id']);
                            $userReg->getProfile();
                        }
                    } else {
                        include_once './LoginClass.php';
                        $login = new Login();
                        $login->logout();
                    }
                } else {
                    $_SESSION['signup_login_error']['message'] = "User already registered";
                    $_SESSION['signup_login_error']['data'] = $_POST;
                    header("Location: signup-login?signup_login_error=");
                    exit();
                }
            } else {
                if ($pass == $cpass) {
                    include_once './GossoutUser.php';
                    $userReg = new GossoutUser(0);
                    if (!isset($_COOKIE['user_auth']) && isset($_SESSION['data'])) {
                        $data = $_SESSION['data'];
                        $resp = $userReg->register(clean($data['first_name']), clean($data['last_name']), clean($email), md5($pass), $data['gender'], "$data[dob_yr]-$data[dob_month]-$data[dob_day]");
                        unset($_SESSION['data']);
                    } else {
                        $resp['status'] = TRUE;
                        include_once './encryptionClass.php';
                        $encrypt = new Encryption();
                        $id = $encrypt->safe_b64decode($_COOKIE['user_auth']);
                        if (is_numeric($id)) {
                            $resp['id'] = $id;
                        } else {
                            include_once './LoginClass.php';
                            $login = new Login();
                            $login->logout();
                        }
                    }
                    if ($resp['status']) {
                        $userReg->setUserId($resp['id']);
                        $result = $userReg->getProfile();
                        if (!$result['status']) {
                            include_once './LoginClass.php';
                            $login = new Login();
                            $login->logout();
                            exit;
                        }
                    } else {
                        $_SESSION['signup_login_error']['message'] = $resp['message'];
                        $_SESSION['signup_login_error']['data'] = $_POST;
                        header("Location: signup-login?signup_login_error=");
                        exit();
                    }
                } else {
                    $_SESSION['signup_login_error']['message'] = "Password fields do not match";
                    $_SESSION['signup_login_error']['data'] = $_POST;
                    header("Location: signup-login?signup_login_error=");
                    exit();
                }
            }
        } else {
            $_SESSION['signup_login_error']['message'] = "Something terrible just went wrong...we will fix this as soon as possible";
            $_SESSION['signup_login_error']['data'] = $_POST;
            header("Location: signup-login?signup_login_error=");
            exit;
        }
    }
} else {

    $_SESSION['signup_login_error']['message'] = "Email is invlaid";
    $_SESSION['signup_login_error']['data'] = $_POST;
    header("Location: signup-login?signup_login_error=");
    exit();
}

function isValidEmail($email) {
    //Perform a basic syntax-Check
    //If this check fails, there's no need to continue
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    //extract host
    list($user, $host) = explode("@", $email);
    //check, if host is accessible
    if (!checkdnsrr($host, "MX") && !checkdnsrr($host, "A")) {
        return false;
    }
    return true;
}

function clean($value) {
    // If magic quotes not turned on add slashes.
    if (!get_magic_quotes_gpc()) {
        // Adds the slashes.
        $value = addslashes($value);
    }
    // Strip any tags from the value.
    $value = strip_tags($value);
    // Return the value out of the function.
    return $value;
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Signup 3/3</title>
        <meta name="description" content="Start or join existing communities/interests on Gossout and start sharing pictures and videos. People use Gossout search, Discover and connect with communities">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <link rel="shortcut icon" href="favicon.ico">

        <link rel="stylesheet" media="screen" href="css/style.min.1.0.2.css">
        <!--<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css"/>-->
        <link rel="stylesheet" href="css/jquery-ui-base-1.8.20.css"/>
        <link rel="stylesheet" href="css/tagit-dark-grey.css"/>

        <script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="scripts/jquery-ui.1.8.20.min.js"></script>
        <script type="text/javascript" src="scripts/tagit.js"></script>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <script type="text/javascript" src="scripts/jquery.form.js"></script>
        <script>
            function showTags(tags) {
                var string = "";
                for (var i in tags) {
                    if (string.length > 0) {
                        string += ",";
                    }
                    string += tags[i].value;
                }
                return string;
            }
            function readCookie(name) {
                var nameEQ = name + "=";
                var ca = document.cookie.split(';');
                for (var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) === ' ')
                        c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0)
                        return c.substring(nameEQ.length, c.length);
                }
                return 0;
            }
            $(document).ready(function() {
                $('#communityTag').tagit({select: true});
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
                var bar = $('.bar');
                var percent = $('.percent');
                $("#fileChookseBtn").click(function() {
                    $("#fileInput").focus().trigger('click');
                });
                $("#uploadForm").ajaxForm({
                    beforeSubmit: function() {
                        $(".progress").show();
                        var percentVal = '0%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                        var tags = showTags($('#communityTag').tagit("tags"));
                        var fileSelect = $("#selectedFile").html();
                        if (tags.length === 0 && (fileSelect === "")) {
                            $(".progress").hide();
                            return false;
                        }
                    }, uploadProgress: function(event, position, total, percentComplete) {
                        var percentVal = percentComplete + '%';
                        bar.width(percentVal);
                        var value = percentVal;
                        if (percentComplete > 99) {
                            percent.html("Finalizing...");
                        } else {
                            percent.html(percentVal);
                        }
                    }, success: function(responseText, statusText, xhr, $form) {
                        var percentVal = '100%';
                        bar.width(percentVal)
                        percent.html(percentVal);
                        if (!responseText.error) {
                            document.getElementById("target").src = responseText.thumb;
                        }
                    }, complete: function(xhr) {
                        var response = JSON.parse(xhr.responseText);
                        $("#status").show();
                        if (!response.error) {
                            $("#status").removeClass("error").addClass("success");
                            $("#status").html("Upload Successful");
                        } else {
                            $("#status").html("Upload Failed. " + response.error.message);
                        }
                        $(".progress").hide();
                    },
                    data: {
                        uid: readCookie("user_auth")
                    }
                });

            });
            function showImageName(name) {
                var last_backslash = name.lastIndexOf('\\');
                //var values = name.split('\\');
                var value = name.substring(last_backslash + 1);
                return value;
            }
        </script>
        <style>
            .progress { position:relative; width:400px; border: 1px solid #ddd; padding: 1px; border-radius: 3px; }
            .bar { background-color: #B4F5B4; width:0%; height:20px; border-radius: 3px; }
            .percent { position:absolute; display:inline-block; top:3px; left:48%; }
        </style>
    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login"><?php echo "Welcome " . $userReg->getFullname() ?></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="logo" id="logo"><img alt=""></div>
            </div>
            <div class="index-intro">	
                <div class="index-intro-2">
                    <div class="registration">
                        <div class="index-intro-1">
                            <h1>
                                Ahaa... That's it! 
                            </h1>
                        </div>	
                        <progress max="100" value="85" >3 of 3 Completed</progress>
                        <hr>
                        <form id="uploadForm" method="POST" action="files-raw.php" enctype="multipart/form-data">
                            <ul>
                                <li><center>
                                    <label>Select an image: </label>
                                    <div class="profile-pic">
                                        <img src="<?php
                                        $pix = $userReg->getPix();
                                        echo isset($pix['thumbnail150']) ? $pix['thumbnail150'] : "images/user-no-pic.png"
                                        ?>" id="target">
                                    </div>
                                    <hr>
                                    <input type="file" onchange="$('#selectedFile').html('<br/><strong>File Name:</strong> ' + showImageName(this.value));" id="fileInput" name="myfile" class="" style="position: absolute;left: -9999px;"/>
                                    <div id="fileChookseBtn" class="button"><span class="icon-16-camera"></span> Click to choose image</div>
                                    <span id="selectedFile"></span>
                                    <p><strong>Maximum file size:</strong> 5MB<br/><strong>Image types:</strong> .jpg, .jpeg, .gif, and .png</p>
                                </center>
                                <hr>
                                <h2>Interests and Tags</h2>
                                <p class="desc">Add tags to help other users discover you more quickly. Separate each tags with coma(,) or space( )</p>
                                <ul id="communityTag" data-name="comTag[]">
                                    <?php
                                    if (isset($userReg)) {
                                        $tags = explode(',', $userReg->getInterestTag());
                                        foreach ($tags as $tag) {
                                            echo "<li data-value='$tag'>$tag</li>";
                                        }
                                    }
                                    ?>
                                </ul>
                                <hr/>
                                <center><input type="submit" class="button" value="Save Changes"></center>
                                <hr/>
                                <div class="progress" style="display: none">
                                    <div class="bar"></div >
                                    <div class="percent">0%</div >
                                </div>
                                <div id="status" class="error" style="display: none"></div>
                                </li>
                            </ul>
                            <br>
                        </form>
                        <center><div class="button"><a href="validate-email">Skip</a></div>
                            <div class="button"><a href="validate-email">Next!</a></div>
                        </center>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="index-shadow-bottom"></div>
            <div class="index-content-wrapper">
                <?php
//                include("footer.php");
                ?>
            </div>

        </div>
    </body>
</html>