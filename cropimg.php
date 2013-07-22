<?php

if (isset($_COOKIE['user_auth'])) {
    include_once './GossoutUser.php';
    $user = new GossoutUser(0);
    include_once './encryptionClass.php';
    $encrypt = new Encryption();
    $id = $encrypt->safe_b64decode($_COOKIE['user_auth']);
    if (is_numeric($id)) {
        $user->setUserId($id);
        $user->getProfile();
        $pix = $user->getProfilePix();
        if ($pix['status']) {
            $photo = $pix['pix']['original'];
            $ext = explode(".", $photo);
            $data['extension'] = strtolower(end($ext));
            
            $pix_quality = 90;
            if ($data['extension'] == "jpeg" || $data['extension'] == "jpg") {
                $path = "upload/images/" . time() . "$id." . $data['extension'];
                
                $jpeg_quality = 100;
                $size = getimagesize($photo);
                $targ_w = $targ_h = $_POST['h'];
                $img_r = imagecreatefromjpeg($photo);
                $dst_r = ImageCreateTrueColor($targ_w, $targ_h);

                imagecopyresampled($dst_r, $img_r, 0, 0, $_POST['x'], $_POST['y'], $targ_w, $targ_h, $_POST['w'], $_POST['h']);
//                header('Content-type: image/jpeg');
                imagejpeg($dst_r, $path, $jpeg_quality);
                $user->updateThumbnail($pix['pix']['id'], $path);
            }
        } else {
            echo "Failed!";
        }
    } else {
        echo "Failed!";
    }
} else {
    echo "Failed!";
}
?>