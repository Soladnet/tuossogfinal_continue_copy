<div class="nav-user" id="nav-user">
    <?php
    if (isset($_COOKIE['user_auth'])) {
        ?>
        <ul>
            <li>
                <a id="gossbag-text">
                    <span class="icon-16-asterisk"></span>
                    <span id="gossbag">Gossbag</span>
                </a>
                <span class="notification-number" id="gb-number">&nbsp;</span>
                <div class="notifications-container" id="pop-up-gossbag">
                    <div class="notifications">
                        <a id="gossbag-close" class="float-right">
                            <span class="icon-16-cross"></span>Close 
                        </a>
                        <div class="clear"></div>
                        <span id="gossbag-individual-notification"></span>
                        <a href="notifications"> See all</a>
                    </div>
                </div>
                <div class="messages-container" id="pop-up-message">
                    <div class="messages">
                        <a id="messages-close" class="float-right">
                            <span class="icon-16-cross"></span>Close 
                        </a>
                        <div class="clear"></div>
                        <span id="message-individual-notification"></span><a href="messages">Go to messages</a>
                    </div>
                </div>
            </li>

            <li class="last">
                <a id="messages-text" >
                    <span class="icon-16-mail"></span>
                    <span>Messages</span>
                </a>
                <span class="notification-number" id="msg-number">&nbsp;</span>
            </li>
            <?php
            if (isset($_GET['page'])) {
                if ($_GET['page'] == "home") {
                    ?>
                    <li>
                        <a>
                            <span onclick='javascript:callTour();
                                                return false;'>Take a Tour</span>
                        </a>

                    </li>
                    <?php
                }
            } else {
                ?>
                <li>
                    <a>
                        <span onclick='javascript:callTour();
                                            return false;'>Take a Tour</span>
                    </a>
                </li>
                <?php
            }
            ?>
            <!--<div id='settings_profile'></div>-->
            <li class="nav-user-profile last hint hint--left  float-right" data-hint="Profile Settings"  id="user-actions">
                <a><span>[<?php echo $user->getFullname() ?>]</span></a>
                <a><span class="icon-16-user"></span></a>
                <div class="user-actions-container" id="pop-up-user-actions">
                    <div class="user-actions">
                        <ul>
                            <!--<li><a href="home"><span class="icon-16-vcard"></span> My Profile</a></li>-->
                            <li><a href="settings"><span class="icon-16-cog"></span> Profile &AMP; Settings</a></li>
                            <hr>
                            <li><a href="login_exec"><span class="icon-16-logout"></span> Log Out</a></li>
                        </ul>
                    </div>
                </div>

            </li>
        </ul>
        <?php
    } else {
        ?>
        <ul>
            <li class="nav-user-profile last hint hint--left  float-right" data-hint="Click to login"  id="user-actions">
                <a href="login">Login <span class="icon-16-logout"></span></a>

            </li>
        </ul>
        <?php
    }
    ?>
    <div class="clear"></div>
</div>