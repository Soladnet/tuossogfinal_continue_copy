<?php

/* The Chat class exploses public static methods, used by ajax.php */

class Chat {

    public static function login($uid, $comId) {
        if (!$uid || !$comId) {
            throw new Exception('Please Login first.');
        }
        $user = new ChatUser(array(
            'com_id' => $comId,
            'user_id' => $uid
        ));

        // The save method returns a MySQLi object
        $MySQLiObject = $user->save();
        if ($MySQLiObject->affected_rows <= 0) {
            throw new Exception('This nick is in use.');
        }

        return array(
            'status' => 1,
            'name' => $_SESSION['auth']['firstname'] . " " . $_SESSION['auth']['lastname'],
            'gravatar' => isset($_SESSION['auth']['photo']['thumbnail50']) ? $_SESSION['auth']['photo']['thumbnail50'] : "images/user-no-pic.png"
        );
    }

    public static function checkLogged($uid, $comId) {
        $response = array('logged' => false);
        if ($uid != 0 || $uid != "0") {
            $response['logged'] = true;
            include_once './GossoutUser.php';
            $user = new GossoutUser(0);
            $id = $user->decodeData($uid);
            if (!isset($_SESSION['auth']['id'])) {
                include_once './LoginClass.php';
                if (is_numeric($id)) {
                    $login = new Login();
                    $user->setUserId($id);
                    $user->getProfile();
                    $login->setUsername($user->getEmail());
                    $login->setPassword($user->getPassword(), FALSE);
                    $res = $login->confirmLogin();
                    $response['logged'] = $res['status'];
                    if ($res['status']) {
                        Chat::login($id, $comId);
                    }
                } else {
                    $response['status'] = "uid not numeric";
                    $response['logged'] = FALSE;
                }
            } else {
                Chat::login($id, $comId);
            }
            if ($response['logged']) {
                include_once './Gossout_Community.php';
                $com = new Community();
                $com->setCommunityId($comId);
                if (isset($_COOKIE['cc'])) {
                    $comChaT = $_COOKIE['cc'];
                    $comChat = json_decode($comChaT, TRUE);
                    if (!$comChat[$comId]) {
                        $var = Community::getCommunityInfo($com->getComId());
                        if ($var['status']) {
                            $comChat[$comId] = array("comid" => $comId, "uid" => $id, "comname" => $var['comm']['name'], "pix" => $var['comm']['thumbnail150']);
                        } else {
                            $comChat[$comId] = array("comid" => $comId, "uid" => $id, "comname" => "Community Chat", "pix" => "images/no-pic.png");
                        }
                        setcookie('cc', json_encode($comChat));
                    }
                } else {
                    $var = Community::getCommunityInfo($com->getComId());
                    if ($var['status']) {
                        $comChat[$comId] = array("comid" => $comId, "uid" => $id, "comname" => $var['comm']['name'], "pix" => $var['comm']['thumbnail150']);
                    } else {
                        $comChat[$comId] = array("comid" => $comId, "uid" => $id, "comname" => "Community Chat", "pix" => "images/no-pic.png");
                    }
                    setcookie('cc', json_encode($comChat));
                }
            }
            $response['loggedAs'] = array(
                'name' => $_SESSION['auth']['firstname'] . " " . $_SESSION['auth']['lastname'],
                'gravatar' => isset($_SESSION['auth']['photo']['thumbnail150']) ? $_SESSION['auth']['photo']['thumbnail150'] : "images/user-no-pic.png"
            );
        } else {
            $response['status'] = "uid is $uid";
        }

        return $response;
    }

    public static function logout($comid, $uid) {
        DB::query("UPDATE community_chat_online set `isOnline`=0 WHERE com_id=$comid AND user_id=$uid");
        return array('status' => 1);
    }

    public static function submitChat($chatText, $comid, $uid) {
        if (!$_SESSION['auth']) {
            throw new Exception('You are not logged in');
        }

        if (!$chatText || !$comid || !$uid) {
            throw new Exception('You haven\'t entered a chat message.');
        }
        include_once './GossoutUser.php';
        $g = new GossoutUser(0);
        $id = $g->decodeData($uid);

        $chat = new ChatLine(array(
            'text' => $chatText,
            'comid' => $comid,
            'user_id' => $id
        ));

        // The save method returns a MySQLi object
        $MySQLiObject = $chat->save();
        $insertID = $MySQLiObject->insert_id;

        return array(
            'status' => 1,
            'insertID' => $insertID,
            'error' => $MySQLiObject->error
        );
    }

    public static function getUsers($uid, $comid) {
        include_once './GossoutUser.php';
        $u = new GossoutUser(0);
        $id = $u->decodeData($uid);
        $user = new ChatUser(array('user_id' => $id, 'com_id' => $comid));
        $user->update();
        DB::query("UPDATE community_chat_online set `isOnline`=0 WHERE time < SUBTIME(NOW(),'0:0:30')");

        $result = DB::query("SELECT cco.`com_id`, cco.`user_id`,concat(u.firstname,u.lastname) as name, cco.`isOnline` FROM `community_chat_online` as cco JOIN user_personal_info as u ON cco.user_id=u.id WHERE cco.`isOnline`=1 AND cco.com_id=$comid ORDER BY cco.user_id ASC");
        $users = array();
        while ($user = $result->fetch_object()) {
            $u->setUserId($user->user_id);
            $u->getProfile();
            $photo = $u->getPix();
            $user->gravatar = isset($photo['thumbnail50']) ? $photo['thumbnail50'] : "images/user-no-pic.png";
            $users[] = $user;
        }

        return array(
            'users' => $users,
            'total' => DB::query("SELECT COUNT(*) as cnt FROM community_chat_online WHERE `isOnline`=1 AND com_id=$comid")->fetch_object()->cnt
        );
    }

    public static function getChats($lastID, $comid) {
        $lastID = (int) $lastID;

        $result = DB::query("SELECT cc.`id`, cc.`user_id`,concat(u.firstname,' ',u.lastname) as name, cc.`text`, cc.`time` FROM `community_chat` as cc JOIN user_personal_info as u ON cc.user_id=u.id WHERE  cc.id > $lastID AND cc.com_id=$comid ORDER BY cc.id ASC");
        include_once './GossoutUser.php';
        $user = new GossoutUser(0);
        $chats = array();
        while ($chat = $result->fetch_object()) {
            $user->setUserId($chat->user_id);
            $user->getProfile();

            if (isset($_COOKIE['tz'])) {
                $tz = Chat::decodeText($_COOKIE['tz']);
            } else if (isset($_SESSION['auth']['tz'])) {
                $tz = Chat::decodeText($_SESSION['auth']['tz']);
            } else {
                $tz = "Africa/Lagos";
            }
            $chat->time = Chat::convert_time_zone($chat->time, $tz);
            $photo = $user->getPix();
            if ($photo['thumbnail150']) {
                $chat->gravatar = $photo['thumbnail50'];
            } else {
                $chat->gravatar = $photo['alt'];
            }
            $chats[] = $chat;
        }

        return array('chats' => $chats);
    }

    public static function convert_time_zone($timeFromDatabase_time, $tz) {
        $date = new DateTime($timeFromDatabase_time, new DateTimeZone(date_default_timezone_get()));
        $date->setTimezone(new DateTimeZone($tz));
        return $date->format('Y-m-d H:i:s');
        // or return $userTime; // if you want to return a DateTime object.
    }

    public static function decodeText($param) {
        include_once './encryptionClass.php';
        $encrypt = new Encryption();
        return $encrypt->safe_b64decode($param);
    }

    public static function gravatarFromHash($hash, $size = 23) {
        return isset($_SESSION['auth']['photo']['thumbnail150']) ? $_SESSION['auth']['photo']['thumbnail150'] : "images/user-no-pic.png";
    }

}

?>