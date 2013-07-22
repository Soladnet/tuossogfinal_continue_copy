<div class="nav">
    <ul>
            <!-- <img src="images/gossout-logo-image-svg.svg" alt="gossout-logo"> -->
        <li><a href="home"><span class="icon-house"></span><span>Home</span></a></li>
        <li class="last"><a href="communities"><span class="icon-globe"></span><span id="communities">Communities</span></a></li>		
        <li class="nav-login mobile-search last"><a href="index"><span>Search</span><span class="icon-search"></span></a></li>
        <li class="nav-search-container float-right">
            <div class="search-container">
                <label for="nav-search"></label>
                <form action="index-search-results.php" method="GET">
                    <input name="g" class="input-fields" placeholder="Search" type="text" required />
                    <input type="submit" class="button" value="Search"/>
                    <?php
//                    include("search-pop-out.php");
                    ?>
                </form>
            </div>
        </li>
        <div class="clear"></div>
    </ul>
    <div class="clear"></div>
</div>				