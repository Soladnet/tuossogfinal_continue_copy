<?php
@session_start();
include_once './Config.php';
if (isset($_COOKIE['user_auth'])) {
    include_once './encryptionClass.php';
    include_once './GossoutUser.php';
    $encrypt = new Encryption();
    $user = new GossoutUser(0);
    $id = $encrypt->safe_b64decode($_COOKIE['user_auth']);
    if (is_numeric($id)) {
        $user->setUserId($id);
        $user->getProfile();
    } else {
        include_once './LoginClass.php';
        $login = new Login();
        $login->logout();
    }
    $param = trim($_GET['param']);
    if (!is_numeric($param) || empty($param)) {
        include_once './404.php';
        exit;
    } else {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array('status' => FALSE);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT report From bulk_registration WHERE report = $param";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows == 0) {
                    include_once './404.php';
                    exit;
                } else {
                    $sql = "SELECT name FROM community WHERE id =(SELECT commId FROM bulk_registration WHERE report =  $param)";
                    if ($result = $mysql->query($sql)) {
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_row();
                            $comName = $row[0];
                        }
                    }
                }
            }
        }
    }
} else {
    include_once './LoginClass.php';
    $login = new Login();
    $login->logout();
}
?>
<!doctype html>
<html lang="en">
    <head>
        <?php include_once './webbase.php'; ?>
        <title><?php echo "$comName -Users Information Upload Report [$param]"; ?></title>
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.css">
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script>
            $(function() {
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
                $("#searchField").focus();
            });
        </script>
        <style>
            ul li{
                margin-top: .5em;
            }
        </style>
    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login" id="name-login-cont"><?php
        echo isset($user) ? "Welcome <a href='home'>" . $user->getFullname() . "</a> [ <a href='login_exec'>Logout</a> ]" :
                'Already have an account? <a href="login">Login Here</a> | <a href="signup-personal">Sign up</a>'
        ?></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="logo" id="logo"><img alt=""></span></div>
            </div>
            <center><h3>Users Information Upload Report</h3></center>
            <p>&nbsp;</p> 
            <div class="index-intro">

                <?php ?>

                <?php ?>
                <p>&nbsp;</p>
                <center>
                    <div style='min-width: 1021px;border-radius:5px;border:1px #ccc solid;background: white;width: 90%;height: auto;text-align: left;'>
                        <div style="margin: 2em;margin-top:1em;text-align:justify">
                            <?php
                            $file = "bulkRegReport/$param.txt";
                            $h = file_get_contents($file);
                            $arry = explode('<rabiusal>', $h);
                            echo "<center>$arry[0].$arry[2]</center>";
                            ?>
                        </div> 
                    </div>
                </center>
                <p>&nbsp;</p>

                <?php ?>
            </div>
            <div class="index-shadow-bottom"></div>
            <div class="index-content-wrapper">
                <span id="footer-links">
                    <?php
                    include("footer.php");
                    ?>
                </span>
            </div>


        </div>

    </body>
</html>
<?php
//require_once("phpExcelReader/dompdf/dompdf_config.inc.php");
//// We check wether the user is accessing the demo locally
//$local = array("::1", "127.0.0.1");
//$is_local = in_array($_SERVER['REMOTE_ADDR'], $local);
//$file = 'userReport.txt';
//if ($is_local) {
//    $h = file_get_contents($file);
//    $dompdf = new DOMPDF();
//    $dompdf->load_html($h);
//    $dompdf->set_paper("letter", "landscape");
//    $dompdf->render();
//    $dompdf->stream("User-ulpoad-report.pdf", array("Attachment" => TRUE));
//}
?>
