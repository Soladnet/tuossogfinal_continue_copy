<div class="footer">
    <hr>
    <ul>      
        <!--<li> <a href="">About</a> </li>-->
        <li> <a href="tos">Terms</a> </li>
        <li> <a href="privacy">Privacy</a> </li>
        <!--<li> <a href="">Help</a> </li>-->
        <div class="clear"></div>
        <li class="float-none"> &copy; <?php echo date("Y"); ?> <a href="http://www.gossout.com">Gossout</a></li>		
    </ul> 
    <div class="clear"></div>
</div>
<span id="chatHolder"></span>
<script>
    $(document).ready(function() {
        
<?php
if (isset($_COOKIE['cc'])) {
    $cookieString = $_COOKIE['cc'];
    $cookieArray = json_decode($cookieString, TRUE);
//    foreach ($cookieArray as $key => $value) {
//        echo "chat.init('$value[comid]', '$value[comname]', '$value[pix]');";
//    }
//    echo '$(".chatContainer").show();';
}
?>
    });
</script>
<?php
//include_once './googleanalytics.html';
?>