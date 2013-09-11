<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Signup 1/3</title>
        <meta name="description" content="Registrtion is free! Register on gossout today and join more than 200 million of users shring their interest">
        <meta name="keywords" content="Community,Communities,Interest,Interests,Friend,Friends,Connect,Search,Discover,Discoveries,Gossout,Gossout.com,Zuma Communication Nigeria Limited,Soladnet Software,Soladoye Ola Abdulrasheed, Muhammad Kori,Ali Sani Mohammad,Lagos,Nigeria,Nigerian,Africa,Surulere,Pictures,Picture,Video,Videos,Blog,Blogs">
        <meta name="author" content="Soladnet Sofwares, Zuma Communication Nigeria Limited">
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.2.css">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
        <script src="scripts/jquery-1.9.1.min.js"></script>
        <?php
        if (isset($_SESSION['signup_perosnal_error'])) {
            ?>
            <link rel="stylesheet" href="css/bigbox.css">
            <script type="text/javascript" src="scripts/humane.min.js"></script>
            <script>
                $(document).ready(function() {
                    var msg = "<?php echo $_SESSION['signup_perosnal_error']['message']; ?>";
                    humane.log(msg, {timeout: 10000, clickToClose: true, addnCls: 'humane-bigbox-error'});
                });
            </script>
            <?php
        }
        ?>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <script>
            $(document).ready(function() {
                if (Modernizr.inlinesvg) {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.svg" alt="Gossout" /></a>');
                } else {
                    $('#logo').html('<a href="index"><img src="images/gossout-logo-text-and-image-svg.png" alt="Gossout" /></a>');
                }
            });
        </script>
    </head>
    <body>
        <div class="index-page-wrapper">	
            <div class="index-nav">
                <span class="index-login">Already have an account? <a href="login">Login Here!</a></span>
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
                                What if we had a headline so punchy that 
                                no one could refuse to sign-up? 
                            </h1>
                            <hr>
                        </div>	
                        <form action="signup-login" method="POST">
                            <ul>
                                <li>
                                    <label for="first_name">First Name</label>
                                    <input class="input-fields" name="first_name" placeholder="" type="text" value="<?php echo isset($_SESSION['signup_perosnal_error']['data']['first_name']) ? $_SESSION['signup_perosnal_error']['data']['first_name'] : "" ?>" spellcheck="false" required/>
                                </li>
                                <li>
                                    <label for="last_name">Last Name</label>
                                    <input class="input-fields" name="last_name" placeholder="" type="text" value="<?php echo isset($_SESSION['signup_perosnal_error']['data']['last_name']) ? $_SESSION['signup_perosnal_error']['data']['last_name'] : "" ?>" spellcheck="false" required/>
                                </li>
                                <li>
                                    <label for="gender">Gender</label>							

                                    <input name="gender" type="radio" class="radio" value="M" required <?php echo isset($_SESSION['signup_perosnal_error']['data']['gender']) ? $_SESSION['signup_perosnal_error']['data']['gender'] == "M" ? "checked" : ""  : "" ?>/>
                                    <label class="desc" for="" >
                                        Male
                                    </label>

                                    <input name="gender" type="radio" class="radio" value="F" required <?php echo isset($_SESSION['signup_perosnal_error']['data']['gender']) ? $_SESSION['signup_perosnal_error']['data']['gender'] == "F" ? "checked" : ""  : "" ?> />
                                    <label class="desc" for="" >
                                        Female
                                    </label>
                                </li>
                                <li>
                                    <label for="dob">Date of Birth (mm-dd-yyyy)</label>
                                    <select name="dob_month" required>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "" ? "selected" : ""  : "" ?>></option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "1" ? "selected" : ""  : "" ?> value="1">January</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "2" ? "selected" : ""  : "" ?> value="2">February</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "3" ? "selected" : ""  : "" ?> value="3">March</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "4" ? "selected" : ""  : "" ?> value="4">April</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "5" ? "selected" : ""  : "" ?> value="5">May</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "6" ? "selected" : ""  : "" ?> value="6">June</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "7" ? "selected" : ""  : "" ?> value="7">July</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "8" ? "selected" : ""  : "" ?> value="8">August</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "9" ? "selected" : ""  : "" ?> value="9">September</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "10" ? "selected" : ""  : "" ?> value="10">October</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "11" ? "selected" : ""  : "" ?> value="11">November</option>
                                        <option <?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_month']) ? $_SESSION['signup_perosnal_error']['data']['dob_month'] == "12" ? "selected" : ""  : "" ?> value="12">December</option>
                                    </select>
                                    <input type="number"  name="dob_day" min="1" max="31" size="2" required placeholder="DD" value="<?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_day']) ? $_SESSION['signup_perosnal_error']['data']['dob_day'] : "" ?>"/>
                                    <input type="number" max="<?php echo date("Y") - 13 ?>" min="1960" size="4" name="dob_yr" required placeholder="YYYY" value="<?php echo isset($_SESSION['signup_perosnal_error']['data']['dob_yr']) ? $_SESSION['signup_perosnal_error']['data']['dob_yr'] : "" ?>"/>
                                </li>
                            </ul>
                            <br>
                            <input class="button-big" type="submit" value="Next!"/>
                        </form>
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
<?php
unset($_SESSION['signup_perosnal_error']);
?>