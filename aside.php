<?php
$countStat = isset($user) ? $user->getMiniStat() : array("fc" => 0, "cc" => 0, "pc" => 0);
if (isset($user)) {
    $pix = $user->getPix();
}
?>
<div class="aside">
    <div class="aside-wrapper">
        <a href="<?php echo isset($pix['original']) ? $pix['original'] : "images/user-no-pic.png" ?>" class= "fancybox " rel="profile">
            <span class="profile-pic">
                <img onload="OnImageLoad(event);" class="holdam" id="profile-pic" src="<?php
                echo isset($pix['thumbnail150']) ? $pix['thumbnail150'] : "images/user-no-pic.png"
                ?>">

            </span>
        </a>
        <table>
            <tr><td></td><td><h3><a href="user/<?php echo isset($user) ? $user->getScreenName() : "" ?>" id="asideName"><?php echo isset($user) ? $user->getId() == 0 ? "GUEST" : $user->getFullname()  : "GUEST"; ?></a></h3></td></tr>
            <!--<tr><td><span class="icon-16-location"></span></td><td class="profile-meta"><?php echo isset($user) ? $user->getLocation() != "" ? $user->getLocation() : "Set your location"  : ""; ?></td></tr>-->
            <!--<tr><td><span class="icon-16-calendar"></span></td><td class="profile-meta">Joined on Feb 18, 2013 </td></tr>-->
            <tr><td><span class="icon-16-male"></span></td><td class="profile-meta"><?php echo isset($user) ? $user->getId() == 0 ? "GUEST" : ($user->getGender() == "M" ? "Male" : "Female")  : "GUEST"; ?></td></tr>
            <tr><td><span class="icon-16-dot nouser"></span></td><td class="profile-meta"><a id="show-full-profile" class="nouser"> View Full Profile</a> </td></tr>
        </table>
        <div class="clear"></div>
        <div class="profile-summary">
            <div class="profile-summary-wrapper"><a href="user/<?php echo isset($user) ? $user->getScreenName() : "" ?>"><p class="number"><?php echo isset($user) ? $user->getMiniStat("pc") : "0" ?></p> <p class="type">Posts</p></a></div>
            <div class="profile-summary-wrapper"><a href="communities"><p class="number" id="cc"><?php echo isset($user) ? $user->getMiniStat("cc") : "0" ?></p> <p class="type">Communities</p></a></div>
            <div class="profile-summary-wrapper"><a href="friends"><p class="number"><?php echo isset($user) ? $user->getMiniStat("fc") : "0" ?></p> <p class="type">Friends</p></a></div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
        <div id="full-profile-data" class="no-display">
            <hr>
            <b>Personal Information</b>
            <table class="profile-meta " colspan="1">
                <tr><td><strong>Name</strong></td><td id="more-fullname"><?php echo isset($user) ? $user->getFullname() : "GUEST"; ?><td></tr>
                <tr><td><strong>Username</strong></td><td><?php echo isset($user) ? $user->getScreenName() : ""; ?><td></tr>
                <?php
                if (isset($_COOKIE['user_auth'])) {
                    ?>
                    <tr><td><strong>Email</strong></td><td><?php echo isset($user) ? $user->getEmail() : ""; ?><td></tr>
                    <?php
                }
                ?>
                <tr><td><strong>Gender</strong></td><td><?php echo isset($user) ? $user->getGender() == "M" ? "Male" : "Female"  : "N/A"; ?><td></tr>
                <?php
                if (isset($_COOKIE['user_auth'])) {
                    ?>
                    <tr><td><strong>Birthday</strong></td><td> <?php echo isset($user) ? $user->getDOB() : ""; ?><td></tr>
                    <!--<tr><td><strong>Relationship</strong></td><td>Single<td></tr>-->
                    <!--<tr><td><strong>Phone</strong></td><td><?php echo isset($user) ? $user->getTel() != "" ? $user->getTel() : "Not Set"  : ""; ?><td></tr>-->
                    <?php
                }
                ?>
                <tr><td><strong>Website</strong></td><td><?php echo isset($user) ? $user->getUrl() != "" ? $user->getUrl() : "Not set"  : ""; ?><td></tr>
            </table>
            <!--<hr>-->
<!--            <p><b>Location</b></p>
 <iframe width="300" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com.ng/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=Abuja,+Abuja+Capital+Territory,+Nigeria&amp;aq=0&amp;oq=abuja,+niger&amp;sll=9.033872,8.677457&amp;sspn=11.762511,21.643066&amp;ie=UTF8&amp;hq=&amp;hnear=Abuja,+Abuja+Capital+Territory&amp;t=m&amp;ll=9.066839,7.482376&amp;spn=0.20342,0.136642&amp;z=11&amp;iwloc=A&amp;output=embed"></iframe><br /><small><a href="https://maps.google.com.ng/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=Abuja,+Abuja+Capital+Territory,+Nigeria&amp;aq=0&amp;oq=abuja,+niger&amp;sll=9.033872,8.677457&amp;sspn=11.762511,21.643066&amp;ie=UTF8&amp;hq=&amp;hnear=Abuja,+Abuja+Capital+Territory&amp;t=m&amp;ll=9.066839,7.482376&amp;spn=0.20342,0.136642&amp;z=11&amp;iwloc=A" style="color:#0000FF;text-align:left">View Larger Map</a></small>
            <hr>
            <p><b>College</b></p>-->
        </div>
    </div>
    <?php if ($user->getId() != 0) { ?>
        <div class="aside-wrapper" id="aside-wrapper-comm">
            <h3><a href="communities">My Communities</a></h3>
            <span id="aside-community-list"></span>
            <p class="community-listing">
                <span>
                    <?php
                    if ($countStat['cc'] > 5) {
                        ?>
                        <span><span class="icon-16-dot"></span><a href="communities">Show all</a></span>
                        <?php
                    }
                    ?>
            <!--<span><span class="icon-16-dot"></span><a id="show-suggested-community" href="communities">Explore all Communities</a></span>-->
                </span>
            </p>
        </div>
        <div id= "suggested-community" class="no-display aside-wrapper" > 
            <h3>Suggested Community </h3>
            <span id="aside-suggest-community">
            </span>
        </div>
        <div class="aside-wrapper" id="aside-wrapper-frnd"><h3><a href="friends">Friends</a></h3>
            <script>
                        $(document).ready(function() {
                            var user = readCookie('user_auth');
                            if (user !== 0) {
                                sendData("loadFriends", {target: "#aside-friends-list", start: 0, limit: 18, loadImage: true <?php echo isset($_GET['page']) ? $_GET['page'] == "friends" ? ",friendPage:'#individual-friend-box'" : ""  : "" ?>});
                                sendData("loadCommunity", {target: "#aside-community-list", loadImage: true, max: "", start: 0, limit: 5});
                            }
                        });
            </script>
            <span id="aside-friends-list"></span>
            <p class="community-listing">
            <div class="clear"></div>
            <span>
                <?php
                if ($countStat['fc'] > 6) {
                    ?>
                    <span><span class="icon-16-dot"></span><a href="friends">Show all</a></span>
                    <?php
                }
                ?>
                <span><span class="icon-16-dot"></span><a id="show-suggested-friends">Suggested Friends</a></span>

            </span>
            </p>
            <div class="clear"></div>
        </div> 
        <?php
    }
    include("suggested-friends.php");
    ?>
    <!--    <div class="aside-wrapper">
            <h3>Trends</h3>
            <p><a>#newGossout</a></p>
            <p><a>#newGossout</a></p>
            <p><a>#newGossout</a></p>
            <p><a>#newGossout</a></p>
            <p><a>#newGossout</a></p>
            <p class="community-listing">
                <span>
                    <span><span class="icon-16-dot"></span><a href="">Show all</a></span>
                </span>
            </p>
        </div>-->

</div>	
<div class="clear"></div>