<!doctype html>
<html lang="en">
    <head>
        <?php
        include_once './webbase.php';
        ?>
        <title>Gossout - Password Reset</title>
        <link rel="shortcut icon" href="favicon.ico">
        <link rel="stylesheet" media="screen" href="css/style.min.1.0.css">
        <script type="text/javascript" src="scripts/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="scripts/modernizr.custom.77319.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" > 
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
                <span class="index-login">No account? <a href="signup-personal">Signup Here!</a> | <a href="login">Login here</a></span>
                <div class="clear"></div>
            </div>
            <div class="index-banner">
                <div class="index-logo">
                    <div class="logo" id="logo"><img alt=""></div>
                </div>
            </div>
            <div class="index-intro">


                <div class="index-intro-2">
                    <div class="registration">
                        <div class="index-intro-1">
                            <h1>
                                Password Recovery!
                            </h1>
                            <hr>
                        </div>
                        <form autocomplete="off" method="POST" action="./password-recovery-confirm" id="resetPasswordForm">
                            <ul>
                                <li>
                                    <p class="info">
                                        Enter the email address you used to register with us and you'll 
                                        revieve an email from us with instructions on how to reset your password!
                                    </p>
                                    <label for="email">e-mail Address</label>
                                    <input class="input-fields" name="email" placeholder="email@awesome.com" type="email" spellcheck="false" required/>
                                </li>
                            </ul>
                            <input class="button-big" type="submit" value="Send">
                        </form>
                        <div class="clear"></div>
                    </div>
                </div>
            </div>
            <div class="index-content-wrapper">
                <?php
                include("footer.php");
                ?>
            </div>

        </div>
    </body>
</html>