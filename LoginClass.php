<?php

//if (session_status() == PHP_SESSION_NONE) {
if (session_id() == "") {
    session_name('GSID');
    session_start();
}
include_once './Config.php';
include_once './encryptionClass.php';
include_once './GossoutUser.php';

class Login extends Encryption {

    var $user, $pass, $rem, $uid, $timezone;

    public function __construct() {
        
    }

    public function setUsername($username) {
        $this->user = Login::clean($username);
    }

    public function setPassword($password, $encrypt = TRUE) {
        if ($encrypt) {
            $this->pass = md5($password);
        } else {
            $this->pass = $password;
        }
    }

    public function setRememberStatus($remember = FALSE) {
        $this->rem = $remember;
    }

    public function setTimezone($zone) {
        $this->timezone = $zone;
    }

    public function setUid($uid) {
        $this->uid = $uid;
    }

    public function getUser() {
        return $this->user;
    }

    public function confirmLogin() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $str = "SELECT l.id, p.email, l.activated,p.dateJoined,  p.firstname, p.lastname, p.gender, p.dob,p.relationship_status,p.phone,p.url,p.bio,p.favquote,p.location,p.likes,p.works FROM user_login_details AS l JOIN user_personal_info AS p ON p.id = l.id WHERE p.email = '$this->user' AND l.password = '$this->pass' AND l.id=p.id";

            if ($result = $mysql->query($str)) {
                if ($result->num_rows > 0 && $result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $row['firstname'] = $this->toSentenceCase($row['firstname']);
                    $row['lastname'] = $this->toSentenceCase($row['lastname']);
                    $arrFetch['status'] = TRUE;
                    $arrFetch['user'] = $row['id'];
                    if ($this->rem) {
                        $expire = time() + 60 * 60 * 24 * 30 * 1;
                        setcookie("user_auth", $this->safe_b64encode($row['id']), $expire);
                        setcookie("ro", $this->safe_b64encode($this->encode(md5(sha1($this->safe_b64encode($row['id']))))), $expire);
                        setcookie("tz", $this->safe_b64encode($this->timezone), $expire);
                    } else {
                        setcookie("user_auth", $this->safe_b64encode($row['id']), 0);
                        setcookie("ro", $this->safe_b64encode($this->encode(md5(sha1($this->safe_b64encode($row['id']))))), 0);
                        setcookie("tz", $this->safe_b64encode($this->timezone), 0);
                    }
                    $user = new GossoutUser($row['id']);
                    $user->getProfile();
                    $row['photo'] = $user->getPix();
                    session_regenerate_id();
                    $row['tz'] = $this->safe_b64encode($this->timezone);
                    $_SESSION['auth'] = $row;
                } else {
                    $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arrFetch;
    }

    public function isValidPassword() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT count(*) as count FROM user_personal_info as u JOIN user_login_details as ul ON u.id=ul.id AND u.id='$this->uid' AND ul.password='$this->pass'";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row['count'] == 1) {
                        $arrFetch['status'] = TRUE;
                        $arrFetch['sql'] = $sql;
                    } else {
                        $arrFetch['status'] = FALSE;
                    }
                } else {
                    $arrFetch['status'] = FALSE;
                }
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function isValidCredential() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT count(*) as count FROM user_personal_info as u JOIN user_login_details as ul ON u.id=ul.id AND u.email='$this->user' AND ul.password='$this->pass'";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    if ($row['count'] == 1) {
                        $arrFetch['status'] = TRUE;
                    } else {
                        $arrFetch['status'] = FALSE;
                    }
                } else {
                    $arrFetch['status'] = FALSE;
                }
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function logout() {
//        if (isset($_SESSION['auth'])) {
        unset($_SESSION['auth']);
        unset($_SESSION['data']);
        setcookie("user_auth", "", time() - 3600);
        setcookie("m_t", "", time() - 3600);
        setcookie("tz", "", time() - 3600);
        setcookie(session_id(), "", time() - 3600);
        session_destroy();
        session_write_close();
//        }
        header("Location:login");
        exit;
    }

    public function isLoggedIn() {
        if (isset($_COOKIE['user_auth'])) {
            $user_auth_id = $this->safe_b64decode($_COOKIE['user_auth']);
            $ro = $_COOKIE['ro'];
            $val = $this->safe_b64encode($this->encode(md5(sha1($this->safe_b64encode($user_auth_id)))));
            if ($ro != $val) {
                $this->logout();
            } else {
                if (!isset($_SESSION['auth'])) {
                    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
                    if ($mysql->connect_errno > 0) {
                        throw new Exception("Connection to server failed!");
                    } else {
                        $str = "SELECT l.id, p.email, l.activated,p.dateJoined,  p.firstname, p.lastname, p.gender, p.dob,p.relationship_status,p.phone,p.url,p.bio,p.favquote,p.location,p.likes,p.works FROM user_login_details AS l JOIN user_personal_info AS p ON p.id = l.id WHERE l.id=$user_auth_id";
                        if ($result = $mysql->query($str)) {
                            if ($result->num_rows > 0 && $result->num_rows == 1) {
                                $row = $result->fetch_assoc();

                                $user = new GossoutUser($row['id']);
                                $pix = $user->getProfilePix();
                                if ($pix['status']) {
                                    $row['photo'] = $pix['pix'];
                                } else {
                                    $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                                }
                                $_SESSION['auth'] = $row;
                            }
                        }
                    }
                }
            }
        }
    }

    public function toSentenceCase($str) {
        $arr = explode(' ', $str);
        $exp = array();
        foreach ($arr as $x) {
            if (strtolower($x) == "of") {
                $exp[] = strtolower($x);
            } else {
                if (strlen($x) > 0) {
                    $exp[] = strtoupper($x[0]) . substr($x, 1);
                } else {
                    $exp[] = strtoupper($x);
                }
            }
        }
        return implode(' ', $exp);
    }

    public static function clean($value) {
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

}

?>
