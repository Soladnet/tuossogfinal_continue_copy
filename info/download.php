<?php

$track = "";
if (isset($_GET['f'])) {
    if ($_GET['f'] == "y") {
        $track = "gossout_yor.mp3";
        header('Content-type: audio/mpeg');
        header('Content-length: ' . filesize($track));
        header('Content-Disposition: attachment; filename="gossoutDotCom_y.mp3"');
        header('X-Pad: avoid browser bug');
        header('Cache-Control: no-cache');
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3");
        readfile($track);
    } else if ($_GET['f'] == "p") {
        $track = "gossout_pidgin.mp3";
        header('Content-type: audio/mpeg');
        header('Content-length: ' . filesize($track));
        header('Content-Disposition: attachment; filename="gossoutDotCom_p.mp3"');
        header('X-Pad: avoid browser bug');
        header('Cache-Control: no-cache');
        header("Content-Transfer-Encoding: binary");
        header("Content-Type: audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3");
        readfile($track);
        
    }
}
if($track==""){
    echo "File name empty and cannot be loaded!";
}
?>
