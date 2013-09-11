<?php
if (session_id() == ""){
    session_name('GSID');
    session_start();
}
$type = 'json'; //$_POST['mimetype'];
$xhr = true;
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
    $xhr = $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

if ($type == 'xml') {
    header('Content-type: text/xml');
    ?> 
    <address attr1="value1" attr2="value2"> 
        <street attr="value">A &amp; B</street> 
        <city>Palmyra</city> 
    </address> 
    <?php
} else if ($type == 'json') {
    if (isset($_FILES['myfile'])) {
        if (isset($_FILES['myfile']['tmp_name'])) {
            $data = array();
            $allowedExts = array("jpg", "jpeg", "gif", "png");
            $ext = explode(".", $_FILES["myfile"]["name"]);
            $data['extension'] = strtolower(end($ext));
            $data['size'] = $_FILES['myfile']['size'] / 1024;
            if ((($_FILES["myfile"]["type"] == "image/gif") || ($_FILES["myfile"]["type"] == "image/jpeg") || ($_FILES["myfile"]["type"] == "image/png") || ($_FILES["myfile"]["type"] == "image/pjpeg")) && ($data['size'] < 5120 && $data['size'] > 1) && in_array($data['extension'], $allowedExts) && !($_FILES["myfile"]["error"] > 0)) {
                $newPath = "upload/images/" . time() . $_COOKIE['user_auth'] . ".$data[extension]";
                $thumbnail45 = "upload/images/" . time() . $_COOKIE['user_auth'] . "-1.$data[extension]";
                $thumbnail50 = "upload/images/" . time() . $_COOKIE['user_auth'] . "-2.$data[extension]";
                $thumbnail75 = "upload/images/" . time() . $_COOKIE['user_auth'] . "-3.$data[extension]";
                $thumbnail150 = "upload/images/" . time() . $_COOKIE['user_auth'] . "-4.$data[extension]";
                include_once './SimpleImage.php';
                $image = new SimpleImage();
                $image->load($_FILES["myfile"]["tmp_name"]);
                list($width, $height) = getimagesize($_FILES["myfile"]["tmp_name"]);
                if ($width > $height) {
                    if ($width > 200) {
                        $image->resizeToWidth(200);
                        $image->save($thumbnail150);
                    } else {
                        copy($_FILES["myfile"]["tmp_name"], $thumbnail150);
                    }
                    if ($width > 100) {
                        $image->resizeToWidth(100);
                        $image->save($thumbnail75);
                    } else {
                        copy($_FILES["myfile"]["tmp_name"], $thumbnail75);
                    }
                    if ($width > 75) {
                        $image->resizeToWidth(75);
                        $image->save($thumbnail50);
                    } else {
                        copy($_FILES["myfile"]["tmp_name"], $thumbnail50);
                    }
                    if ($width > 50) {
                        $image->resizeToWidth(50);
                        $image->save($thumbnail45);
                    } else {
                        copy($_FILES["myfile"]["tmp_name"], $thumbnail45);
                    }
                } else {
                    if ($height > 200) {
                        $image->resizeToHeight(200);
                        $image->save($thumbnail150);
                    } else {
                        copy($_FILES["myfile"]["tmp_name"], $thumbnail150);
                    }
                    if ($height > 100) {
                        $image->resizeToHeight(100);
                        $image->save($thumbnail75);
                    } else {
                        copy($_FILES["myfile"]["tmp_name"], $thumbnail75);
                    }
                    if ($height > 75) {
                        $image->resizeToHeight(75);
                        $image->save($thumbnail50);
                    } else {
                        copy($_FILES["myfile"]["tmp_name"], $thumbnail50);
                    }
                    if ($height > 50) {
                        $image->resizeToHeight(50);
                        $image->save($thumbnail45);
                    } else {
                        copy($_FILES["myfile"]["tmp_name"], $thumbnail45);
                    }
                }
                move_uploaded_file($_FILES["myfile"]["tmp_name"], $newPath);
                include_once './GossoutUser.php';
                $userUploader = new GossoutUser($_SESSION['auth']['id']);
                if (isset($_POST['comTag'])) {
                    if (count($_POST['comTag'])) {
                        $tagStatus = $userUploader->updateInterestTag(clean(implode(',', $_POST['comTag'])));
                    }
                }
                $response = $userUploader->newPictureUpload($newPath);
                if ($response['status']) {
                    $userUploader->updateThumbnail($response['id'], $thumbnail45, $thumbnail50, $thumbnail75, $thumbnail150);
                    header('Content-type: application/json');
                    echo json_encode(array('thumb' => $thumbnail150, "status" => TRUE));
                } else {
                    if (file_exists($newPath))
                        unlink($newPath);
                    if (file_exists($thumbnail150))
                        unlink($thumbnail150);
                    if (file_exists($thumbnail50))
                        unlink($thumbnail50);
                    if (file_exists($thumbnail45))
                        unlink($thumbnail45);
                    if (file_exists($thumbnail75))
                        unlink($thumbnail75);
                    if (isset($tagStatus)) {
                        if ($tagStatus['status']) {
                            echo json_encode(array("status" => TRUE));
                        } else {
                            displayError(404, "The request cannot be fulfilled due to bad syntax");
                        }
                    } else {
                        displayError(404, "The request cannot be fulfilled due to bad syntax");
                    }
                }
            } else {
                displayError(404, "The request cannot be fulfilled due to unacceptable image format or size");
            }
        } else {
            include_once './GossoutUser.php';
            $userUploader = new GossoutUser($_SESSION['auth']['id']);
            if (isset($_POST['comTag'])) {
                if (count($_POST['comTag'])) {
                    $tagStatus = $userUploader->updateInterestTag(clean(implode(',', $_POST['comTag'])));
                    if ($tagStatus['status']) {
                        echo json_encode(array("status" => TRUE));
                    } else {
                        displayError(404, "The request cannot be fulfilled due to bad syntax");
                    }
                } else {
                    displayError(404, "The request cannot be fulfilled due to bad syntax");
                }
            } else {
                displayError(404, "The request cannot be fulfilled due to bad syntax");
            }
        }
    } else {
        include_once './GossoutUser.php';
        $userUploader = new GossoutUser($_SESSION['auth']['id']);
        if (isset($_POST['comTag'])) {
            if (count($_POST['comTag'])) {
                $tagStatus = $userUploader->updateInterestTag(clean(implode(',', $_POST['comTag'])));
                if ($tagStatus['status']) {
                    echo json_encode(array("status" => TRUE));
                } else {
                    displayError(404, "The request cannot be fulfilled due to bad syntax");
                }
            } else {
                displayError(404, "The request cannot be fulfilled due to bad syntax");
            }
        } else {
            displayError(404, "The request cannot be fulfilled due to bad syntax");
        }
    }
} else if ($type == 'script') {
    // wrap script in a textarea if the request did not come from xhr 
    if (!$xhr)
        echo '<textarea>';
    ?> 

    for (var i=0; i < 2; i++) 
    <!--alert('Script evaluated!');--> 

    <?php
    if (!$xhr)
        echo '</textarea>';
}
else {
    // return text var_dump for the html request 
    echo "VAR DUMP:<p />";
    var_dump($_POST);
    foreach ($_FILES as $file) {
        $n = $file['name'];
        $s = $file['size'];
        if (!$n)
            continue;
        echo "File: $n ($s bytes)";
    }
}

function displayError($code, $meesage) {
    $response_arr = array();
    $response_arr['error']['code'] = $code;
    $response_arr['error']['message'] = $meesage;
    $data = $_POST;
    $data['userAgent'] = $_SERVER['HTTP_USER_AGENT'];
    mail("soladnet@gmail.com", "bad syntax from user " . $_SERVER['HTTP_REFERER'], json_encode($data));
    header('Content-type: application/json');
    echo json_encode($response_arr);
}

function clean($value) {
    // If magic quotes not turned on add slashes.
    if (!get_magic_quotes_gpc()) {
        // Adds the slashes.
        $value = addslashes($value);
    }
    // Strip any tags from the value.
    $value = strip_tags($value);
    // Return the value out of the function.
    return $value;
}
?>