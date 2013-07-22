<?php
if (session_id() == ""){
    session_name('GSID');
    session_start();
}
//UploadiFive
//Copyright (c) 2012 Reactive Apps, Ronnie Garcia

function myResizeFunction($target, $newcopy, $w, $h, $ext) {

    list($w_orig, $h_orig) = getimagesize($target);
    $scale_ratio = $w_orig / $h_orig;
    if (($w / $h) > $scale_ratio) {
        $w = $h * $scale_ratio;
    } else {
        $h = $w / $scale_ratio;
    }
    $img = "";
    $ext = strtolower($ext);
    if ($ext == "gif") {
        $img = imagecreatefromgif($target);
    } else if ($ext == "png") {
        $img = imagecreatefrompng($target);
    } else {
        $img = imagecreatefromjpeg($target);
    }
    $tci = imagecreatetruecolor($w, $h);
//    imagecopyresampled(dst_img, src_img, dst_x, dst_y, src_x, src_y, dst_w, dst_h, src_w, src_h);

    imagecopyresampled($tci, $img, 0, 0, 0, 0, $w, $h, $w_orig, $h_orig);
    imagejpeg($tci, $newcopy, 100);
}

function cropResize($target, $newcopy, $e, $x, $y, $w, $h) {

	//$e = $ext;
    $targ_h = 180;
    $targ_w = 180;

    $ext = strtolower($e);
    if ($ext == "gif"){ 
      $img = imagecreatefromgif($target);
    } else if($ext =="png"){ 
      $img = imagecreatefrompng($target);
    } else { 
      $img = imagecreatefromjpeg($target);
    }
//    $y-+20;
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
	imagecopyresampled($dst_r,$img,0,0,$x,$y,$targ_w,$targ_h,$w,$h);
    imagejpeg($dst_r, $newcopy, 100);
}

function cropFunction($target, $newcopy, $e) {

	//$e = $ext;
    $targ_h = 300;
    $targ_w = 300;

    $ext = strtolower($e);
    if ($ext == "gif"){ 
      $img = imagecreatefromgif($target);
    } else if($ext =="png"){ 
      $img = imagecreatefrompng($target);
    } else { 
      $img = imagecreatefromjpeg($target);
    }
	$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
	imagecopyresampled($dst_r,$img,0,0,$_POST['x'],$_POST['y'],
	$targ_w,$targ_h,$_POST['w'],$_POST['h']);
    imagejpeg($dst_r, $newcopy, 100);
}



    ?>