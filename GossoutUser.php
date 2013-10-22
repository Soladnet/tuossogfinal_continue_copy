<?php

require_once 'Config.php';
include_once './encryptionClass.php';
include_once './Gossout_Community.php';
include_once './Post.php';

class GossoutUser {

    var $id, $fname, $lname, $password, $fullname, $location, $gender, $url, $like, $tel, $email, $screenName = "", $dob, $pix = array(), $tz, $start = 0, $limit = 5;

    /**
     * @author Soladnet Software
     * This class defined a Gossout user with the current supported properties and behaviour. All methods defined in this class that requires server connection implements their connection script
     * @param int $id
     */
    public function GossoutUser($id) {
        $this->id = $id;
    }

    /**
     * @return int The id of $this user is returned
     */
    public function getId() {
        if (isset($this->id) && $this->id != 0) {
            return $this->id;
        } else {
            $response = "";
            if (isset($this->screenName) && $this->screenName != "") {
                $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
                if ($mysql->connect_errno > 0) {
                    throw new Exception("Connection to server failed!");
                } else {
                    $sql = "SELECT id FROM `user_personal_info` WHERE username = '$this->screenName'";
                    if ($result = $mysql->query($sql)) {
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $response = $row['id'];
                            $this->id = $row['id'];
                        }
                    }
                }
            } else if (isset($this->email) && $this->email != NULL) {
                $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
                if ($mysql->connect_errno > 0) {
                    throw new Exception("Connection to server failed!");
                } else {
                    $sql = "SELECT id FROM `user_personal_info` WHERE email = '$this->email'";
                    if ($result = $mysql->query($sql)) {
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $response = $row['id'];
                            $this->id = $row['id'];
                        }
                    }
                }
            }
            return $response;
        }
    }

    public function getPassword() {
        $response['status'] = FALSE;
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if (!empty($this->id)) {
                $sql = "SELECT * FROM `user_login_details` WHERE id=$this->id";
                if ($result = $mysql->query($sql)) {
                    if ($result->num_rows > 0) {
                        $response['status'] = TRUE;
                    }
                }
            }
        }
        return $response;
    }

    public static function verifyUserByEmail($email) {
        $response['status'] = FALSE;
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM `user_personal_info` WHERE email = '$email'";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $response['details'] = $result->fetch_assoc();
                    $response['status'] = TRUE;
                }
            }
        }
        return $response;
    }

    public static function verifyUserById($id) {
        $response['status'] = FALSE;
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if (is_numeric($id)) {
                $sql = "SELECT * FROM `user_personal_info` WHERE id = '$id'";
                if ($result = $mysql->query($sql)) {
                    if ($result->num_rows > 0) {
                        $response['details'] = $result->fetch_assoc();
                        $response['status'] = TRUE;
                    }
                }
            }
        }
        return $response;
    }

    public static function verifyUserByScreenName($screename) {
        $response['status'] = FALSE;
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if (is_numeric($id)) {
                $sql = "SELECT * FROM `user_personal_info` WHERE username = '$screename'";
                if ($result = $mysql->query($sql)) {
                    if ($result->num_rows > 0) {
                        $response['details'] = $result->fetch_assoc();
                        $response['status'] = TRUE;
                    }
                }
            }
        }
        return $response;
    }

    public function isAvalidUser() {
        $response['status'] = FALSE;
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if (!empty($this->id)) {
                $sql = "SELECT * FROM `user_personal_info` WHERE username = '$this->screenName' OR id=$this->id";
                if ($result = $mysql->query($sql)) {
                    if ($result->num_rows > 0) {
                        $response['status'] = TRUE;
                    }
                }
            }
        }
        return $response;
    }

    public function getToken() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $response = "";
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT l.token FROM `user_login_details` as l JOIN user_personal_info as u ON u.id=l.id WHERE u.email= '$this->email' OR l.id=$this->id";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $response = $row['token'];
                }
                $result->free();
            }
            $mysql->close();
        }
        return $response;
    }

    public function getUnExpiredPasswordResetInfo() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $response = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT token,user_id,`time`,responded,TIMESTAMPDIFF(MONTH,password_recovery.time,NOW()) as monthOld,TIMESTAMPDIFF(YEAR,password_recovery.time,NOW()) as yrOld,TIMESTAMPDIFF(DAY,password_recovery.time,NOW()) as dayOld,TIMESTAMPDIFF(HOUR,password_recovery.time,NOW()) as hrOld,TIMESTAMPDIFF(MINUTE,password_recovery.time,NOW()) as minOld,TIMESTAMPDIFF(SECOND,password_recovery.time,NOW()) as secOld FROM password_recovery WHERE user_id=$this->id AND responded=0";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $response = $row;
                    }
                    if ($response['yrOld'] == 0 && $response['monthOld'] == 0 && $response['dayOld'] == 0 && $response['hrOld'] < 24) {
                        $response['status'] = TRUE;
                    } else {
                        $response['status'] = FALSE;
                    }
                } else {
                    $response['status'] = FALSE;
                }
                $result->free();
            }
            $mysql->close();
        }
        return $response;
    }

    public function makePasswordTokenExpire($token) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $response = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE password_recovery SET responded=1 WHERE user_id=$this->id AND token='$token'";
            if ($mysql->query($sql)) {
                $response['status'] = TRUE;
            }
        }
    }

    public function addResetInfor($token) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $response = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO password_recovery(user_id,token) VALUES($this->id,'$token')";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $response['status'] = TRUE;
                } else {
                    $response['status'] = FALSE;
                    $response['msg'] = $mysql->error;
                }
            }
            $mysql->close();
        }
        return $response;
    }

    public function isActivated() {
        return FALSE;
    }

    /**
     * @return String The first name of $this user is returned
     */
    public function getFirstname() {
        return $this->fname;
    }

    /**
     * @return String The last name of $this user is returned
     */
    public function getLastname() {
        return $this->lname;
    }

    /**
     * @return String The fullname of $this user is returned by calling getLastName() . " " . getFirstname(). " "
     */
    public function getFullname() {
        return $this->fname . " " . $this->lname . " ";
    }

    /**
     * @return String The location of $this user is returned
     */
    public function getLocation() {
        return $this->location;
    }

    /**
     * @return String The gender of $this user is returned
     */
    public function getGender() {
        return $this->gender;
    }

    /**
     * @return String The url of $this user is returned
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * @return String The url of $this user is returned
     */
    public function getInterestTag() {
        return $this->like;
    }

    /**
     * @return String The Phone number of $this user is returned
     */
    public function getTel() {
        return $this->tel;
    }

    /**
     * @return String The email of $this user is returned
     */
    public function getEmail() {
        return $this->email;
    }

    public function getScreenName() {
        return $this->screenName;
    }

    /**
     * @return String The date of birth of $this user is returned
     */
    public function getDOB() {
        return $this->dateToString($this->dob);
    }

    public function getPix() {
        return $this->pix;
    }

    public function setUserId($newUid) {
        if (is_null($newUid)) {
            unset($this->id, $this->fname, $this->lname, $this->password, $this->fullname, $this->location, $this->gender, $this->url, $this->tel, $this->email, $this->dob, $this->pix, $this->tz);
            $this->screenName = "";
            $this->pix = array();
            $this->start = 0;
            $this->limit = 5;
        } else {
            $this->id = $newUid;
        }
    }

    public function setEmail($newEmail) {
        if (is_null($newEmail)) {
            unset($this->email);
        } else {
            $this->email = $this->clean($newEmail);
        }
    }

    public function setTimezone($newTimezone) {
        $this->tz = $newTimezone;
    }

    public function setStart($newStart) {
        $this->start = $newStart;
    }

    public function setLimit($newLimit) {
        $this->limit = $newLimit;
    }

    public function setScreenName($user) {
        $this->screenName = $user;
    }

    public function updateFirstname($newName) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE user_personal_info SET firstname='$newName' WHERE id=$this->id";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arrFetch['status'] = TRUE;
                } else {
                    $arrFetch['status'] = FALSE;
                }
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function updateLastname($lname) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE user_personal_info SET lastname='$lname' WHERE id=$this->id";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arrFetch['status'] = TRUE;
                } else {
                    $arrFetch['status'] = FALSE;
                }
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function updateInterestTag($param) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE user_personal_info SET likes='$param' WHERE id=$this->id";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function getLastUpdate() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT lastupdate FROM user_time_update WHERE user_id=$this->id";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $arrFetch['time'] = $this->convert_time_zone($row['lastupdate'], $this->tz);
                    $arrFetch['status'] = TRUE;
                } else {
                    $sql = "SELECT NOW() as lastupdate";
                    if ($result = $mysql->query($sql)) {
                        if ($result->num_rows > 0) {
                            $arrFetch['status'] = TRUE;
                            $row = $result->fetch_assoc();
                            $arrFetch['time'] = $this->convert_time_zone($row['lastupdate'], $this->tz);
                            $arrFetch['status'] = TRUE;
                        } else {
                            $arrFetch['status'] = FALSE;
                        }
                    } else {
                        $arrFetch['status'] = FALSE;
                    }
                }
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function updateTime() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO user_time_update(user_id) VALUES('$this->id') ON DUPLICATE KEY UPDATE lastupdate = NOW()";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arrFetch['status'] = TRUE;
                } else {
                    $arrFetch['status'] = FALSE;
                }
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
    }

    public function updatePassword($pass) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE user_login_details SET password='$pass' WHERE id=$this->id";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arrFetch['status'] = TRUE;
                } else {
                    $arrFetch['status'] = FALSE;
                }
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    /**
     * Get the profile of the current user if a valid user id was specified
     * @return Array An array containing $this user's profile information would be returned
     * @throws Exception is thrown when the connection to the server fails
     */
    function getProfile() {
        $arr = array();
        $response = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            // Make the variable a sql statement if the visitor is a registered and logged in user else make the variable hold the vlaue geust
            $sql = "SELECT id,firstname,lastname,email,username,gender,dob,phone,url,location,likes FROM `user_personal_info` WHERE id = $this->id OR username = '$this->screenName'";

            //the condition will return true. if the id is not zero, then run query and enter block else enter block
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $arr = $result->fetch_assoc();
                    $pix = $this->getProfilePix();
                    if ($pix['status']) {
                        $arr['photo'] = $pix['pix'];
                    } else {
                        $arr['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                    }
                    $response['user'] = $arr;
                    $this->fname = $this->toSentenceCase($arr['firstname']);
                    $this->lname = $this->toSentenceCase($arr['lastname']);
                    $this->gender = $arr['gender'];
                    $this->location = $arr['location'];
                    $this->url = $arr['url'];
                    $this->like = $arr['likes'];
                    $this->tel = $arr['phone'];
                    $this->email = $arr['email'];
                    $this->screenName = $arr['username'];
                    $this->dob = $arr['dob'];
                    $this->pix = $arr['photo'];
                    $response['status'] = true;
                } else {
                    $response['status'] = false;
                }
                $result->free();
            } else {
                $response['status'] = false;
            }
        }
        $mysql->close();
        return $response;
    }

    public function updateProfilePix($pix_id) {
        $response = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM profile_pix WHERE user_id=$this->id AND $pix_id=$pix_id";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows == 0) {
                    $sql = "INSERT INTO profile_pix (pix_id,user_id) VALUES($pix_id,$this->id)";
                } else {
                    $sql = "UPDATE profile_pix SET `pix_id`=$pix_id WHERE user_id=$this->id";
                }
                $mysql->query($sql);
                if ($mysql->affected_rows > 0) {
                    $response['status'] = TRUE;
                } else {
                    $response['status'] = FALSE;
                }
                $result->free();
            }
        }
        $mysql->close();
        return $response;
    }

    public function getProfilePix($val = array("id", "original", "thumbnail45", "thumbnail50", "thumbnail75", "thumbnail150", "date_added")) {
        $response = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $colmn = implode(",", $val);
            $sql = "SELECT $colmn FROM pictureuploads WHERE user_id=$this->id";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $response['status'] = TRUE;
                    while ($row = $result->fetch_assoc()) {
                        $response['pix'] = $row;
                    }
                } else {
                    $response['status'] = FALSE;
                    $response['alt'] = "images/user-no-pic.png";
                }
                $result->free();
            } else {
                $response['status'] = FALSE;
            }
        }
        $mysql->close();
        return $response;
    }

    public function newPictureUpload($param) {
        $response = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO pictureuploads (user_id,original) VALUES($this->id,'$param')";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $response['status'] = TRUE;
                    $response['id'] = $mysql->insert_id;
                } else {
                    $response['status'] = FALSE;
                }
            } else {
                $response['status'] = FALSE;
            }
        }
        $mysql->close();
        return $response;
    }

    public function updateThumbnail($pix_id, $thumbnail45, $thumbnail50, $thumbnail75, $thumbnail150) {
        $response = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE pictureuploads SET thumbnail45='$thumbnail45',thumbnail50='$thumbnail50',thumbnail75='$thumbnail75',thumbnail150='$thumbnail150' WHERE id=$pix_id";
            $mysql->query($sql);
            if ($mysql->affected_rows > 0) {
                $response['status'] = TRUE;
            } else {
                $response['status'] = FALSE;
            }
        }
        $mysql->close();
        return $response;
    }

    /**
     * 
     * @param int $start This specifies where the query starts from for pagination
     * @param int $limit This specifies the end of the result for pagination
     * @param String $status Either 'Y' or 'N' defualt is 'Y'
     * @return Array This method fetches this user's friends with fetch limit of 20
     * @throws Exception is thrown when the connection to the server fails
     */
    public function getFriends($start, $limit, $status = "Y", $shuffle = FALSE) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $str = "Select if(uc.username1=$this->id,uc.username2,uc.username1) as id,username,firstname, lastname,location,gender From user_personal_info, usercontacts as uc Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='$status' LIMIT $start,$limit";
            if ($result = $mysql->query($str)) {
                if ($result->num_rows > 0) {
                    $user = new GossoutUser(0);
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $user->setUserId($row['id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['ministat'] = $user->getMiniStat();
                        $row['id'] = GossoutUser::encodeData($row['id']);
                        $arrFetch['friends'][] = $row;
                    }
                    if ($shuffle) {
                        shuffle($arrFetch['friends']);
                    }
                    $arrFetch['status'] = TRUE;
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

    public function isAfriend($uid) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if ($this->id == $uid) {
                $arr['status'] = "me";
            } else {
                $sql = "SELECT * FROM usercontacts WHERE ((username1=$uid AND username2=$this->id) OR (username2='$uid' AND username1='$this->id')) AND status='Y'";
                if ($result = $mysql->query($sql)) {
                    if ($result->num_rows > 0) {
                        $arr['status'] = TRUE;
                    } else {
                        $arr['status'] = FALSE;
                    }
                    $result->free();
                } else {
                    $arr['status'] = FALSE;
                }
                $mysql->close();
            }
        }

        return $arr;
    }

    public function countUserFriends() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $str = "Select count(if(uc.username1=$this->id,uc.username2,uc.username1)) as count From usercontacts as uc Where if(uc.username1<>$this->id,uc.username2,uc.username1) = $this->id AND status ='Y'";
            if ($result = $mysql->query($str)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $arrFetch['friends_count'] = $row;

                    $arrFetch['status'] = TRUE;
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

    public function suggestFriend() {
        $arrfetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            //suggest frinds of my friends
            $my = $this->getFriends(0, 1000);
            $arr = array();
            if ($my['status']) {
                $user = new GossoutUser(0);
                foreach ($my['friends'] as $friend) {
                    $fid = GossoutUser::decodeData($friend['id']);
                    $user->setUserId($fid);
                    $userFriend = $user->getFriends(0, 1000);
                    if ($userFriend['status']) {
                        foreach ($userFriend['friends'] as $userFrnd) {
                            $arr[$userFrnd['id']] = $userFrnd;
                        }
                    }
                }
                $arrfetch['status'] = TRUE;
            } else {
                $arrfetch['status'] = FALSE;
            }
            $com = new Community();
            $com->setUser($this->id);
            //suggest people from community i belong
            $myCom = $com->userComm(0, 1000);
            if ($myCom['status']) {
                foreach ($myCom['community_list'] as $userComm) {
                    $comMem = $com->getMembers($userComm['id'], $this->id, 0, 1000);
                    foreach ($comMem['com_mem'] as $mem) {
                        $arr[$mem['id']] = $mem;
                    }
                }
                $arrfetch['status'] = TRUE;
            } else {
                if (!$arrfetch['status']) {
                    $arrfetch['status'] = FALSE;
                }
            }
            if ($arrfetch['status']) {
                unset($arr[GossoutUser::encodeData($this->id)]);
//                unset($arr[$this->id]);
                if ($my['status']) {
                    foreach ($my['friends'] as $friend) {
                        if (array_key_exists($friend['id'], $arr)) {
                            unset($arr[$friend['id']]);
                        }
                    }
                }
                if (count($arr) == 0) {
                    $arrfetch['status'] = FALSE;
                } else {
                    $arrfetch['suggest'] = array_values($arr);
                    shuffle($arrfetch['suggest']);
                }
            } else {
                $arrfetch['status'] = FALSE;
            }
        }

        return $arrfetch;
    }

    public function getPrivateMessageSummary() {
        $arrFetch = array('status' => FALSE);
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT (SELECT count(id) FROM privatemessae WHERE status='N' AND receiver_id=$this->id) as undeli,(SELECT count(id) FROM privatemessae WHERE status='D' AND receiver_id=$this->id) as deli,(SELECT count(id) FROM privatemessae WHERE status='R' AND receiver_id=$this->id) as rd,(SELECT count(id) FROM privatemessae WHERE sender_id=$this->id) as sent,(SELECT count(id) FROM privatemessae WHERE receiver_id=$this->id) as rec";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $arrFetch['summary'] = $result->fetch_assoc();
                    $arrFetch['status'] = TRUE;
                }
                $result->free();
            }
        }
        $mysql->close();
        return $arrFetch;
    }

    /**
     * 
     * @param int $start This specifies where the query starts from for pagination
     * @param int $limit This specifies the end of the result for pagination
     * @param String $status
     * @return Array
     * @throws Exception is thrown when the connection to the server fails
     */
    public function getMessages($flag = TRUE) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT MAX(pm.`id`) as id, MAX(pm.`sender_id`) as sender_id,MAX(u.username) as username,
MAX(CONCAT(u.firstname,' ',u.lastname)) as fullname, 
MAX(pm.`message`) as message, MAX(pm.`time`) as time,
MAX(pm.`status`) as status,(SELECT MAX(thumbnail50) FROM pictureuploads WHERE user_id=$this->id) as photo,(SELECT 'M') as type,
(SELECT 'U') as creator FROM `privatemessae` as pm 
JOIN user_personal_info as u ON pm.sender_id=u.id 
WHERE pm.`receiver_id` = $this->id group by pm.sender_id
UNION SELECT MAX(cm.id) as id,MAX(cm.sender_id) as sender_id,MAX(c.unique_name) as username,
MAX(c.`name`) as fullname,MAX(cm.message_title) as message,
MAX(cm.`time`) as time,MAX(cm.status) as status,c.thumbnail150 as photo,
(SELECT 'C') as type,MAX(c.creator_id) as creator 
FROM community_message  as cm JOIN community as c 
ON cm.com_id=c.id WHERE c.creator_id=$this->id group by c.`name`
UNION SELECT MAX(cmc.parent_id) as id,MAX(cmc.sender_id) as sender_id,MAX(c.unique_name) as username,
MAX(c.`name`) as fullname,MAX(cmc.reply) as message,
MAX(cmc.`time`) as time,MAX(cmc.status) as status,c.thumbnail150 as photo,
(SELECT 'CR') as type,MAX(c.creator_id) as creator 
FROM community_message_child as cmc JOIN 
community_message as cm ON cmc.parent_id=cm.id 
JOIN community as c ON cm.com_id=c.id 
WHERE cmc.receiver_id=$this->id group by c.`name`";
//            $sql = "SELECT pm.`id`, pm.`sender_id`,u.username,CONCAT(u.firstname,' ',u.lastname) as fullname, MAX(pm.`message`) as message, MAX(pm.`time`) as time, MAX(pm.`status`) as status,(SELECT 'M') as type,(SELECT 'U') as creator FROM `privatemessae` as pm JOIN user_personal_info as u ON pm.sender_id=u.id WHERE pm.`receiver_id` = $this->id group by u.username UNION SELECT cm.id,cm.sender_id,c.unique_name as username,c.`name` as fullname,MAX(cm.message_title) as message,MAX(cm.`time`) as time,MAX(cm.status) as status,(SELECT 'C') as type,c.creator_id as creator FROM community_message  as cm JOIN community as c ON cm.com_id=c.id WHERE c.creator_id=$this->id group by c.unique_name UNION SELECT cmc.parent_id,cmc.sender_id,MAX(c.unique_name) as username,MAX(c.`name`) as fullname,MAX(cmc.reply) as message,MAX(cmc.`time`) as time,MAX(cmc.status) as status,(SELECT 'C') as type,c.creator_id as creator FROM community_message_child as cmc JOIN community_message as cm ON cmc.parent_id=cm.id JOIN community as c ON cm.com_id=c.id WHERE cmc.receiver_id=$this->id group by c.unique_name";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['fullname'] = ucwords($row['fullname']);
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $row['sender_id'] = GossoutUser::encodeData($row['sender_id']);
                        $row['creator'] = $row['creator'] == 'U' ? $row['creator'] : GossoutUser::encodeData($row['creator']);
                        $arrFetch['message'][] = $row;
                        if ($row['status'] == "N" && $flag) {
                            $mysql->query("UPDATE `privatemessae` SET `status`='D' WHERE `id`=$row[id]");
                        }
                    }
                    $arrFetch['status'] = TRUE;
                    $result = $mysql->query("SELECT NOW() as time");
                    $row = $result->fetch_assoc();
                    $arrFetch['m_t'] = $this->convert_time_zone($row['time'], $this->tz);
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

    public function sendMessage($sender_id, $msg) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO privatemessae (sender_id,receiver_id,message) VALUES('$sender_id','$this->id','$msg')";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arrFetch['status'] = TRUE;
                    $arrFetch['response']['msg_id'] = $mysql->insert_id;
                    $arrFetch['response']['sender_id'] = $sender_id;
                    $arrFetch['response']['receiver_id'] = $this->id;
                    $this->getProfile();
                    $arrFetch['response']['receiver_name'] = $this->getFullname();
                    $arrFetch['response']['status'] = TRUE;
                    $user = new GossoutUser($sender_id);
                    $user->getProfile();
                    $pix = $user->getProfilePix();
                    if ($pix['status']) {
                        $arrFetch['response']['photo'] = $pix['pix'];
                    } else {
                        $arrFetch['response']['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                    }
                    $arrFetch['response']['sender_name'] = $user->getFullname();
                    $result = $mysql->query("SELECT NOW() as time");
                    $row = $result->fetch_assoc();
                    $result->free();
                    $arrFetch['response']['m_t'] = $this->convert_time_zone($row['time'], $this->tz);
                } else {
                    $arrFetch['status'] = FALSE;
                    $arrFetch['sql'] = $sql;
                }
                $mysql->close();
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function getConversation($me, $userCon, $fetchfromCommunity = FALSE) {
        $arrFetch = array("status" => FALSE);
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if ($fetchfromCommunity) {
                $comm = Community::getCommunityInfo($userCon);
                if ($comm['status']) {
                    $arrFetch['message']["cwn"] = $comm['comm']['name'];
                    $sql = "SELECT cmc.parent_id as id,cmc.sender_id,c.unique_name,c.`name`,c.thumbnail150,cmc.reply as message,cmc.`time`,(SELECT MAX(thumbnail50) FROM pictureuploads WHERE user_id=$this->id) as s_pix,cmc.status FROM community_message_child as cmc JOIN community_message as cm ON cmc.parent_id=cm.id JOIN community as c ON cm.com_id=c.id WHERE (cmc.receiver_id=$this->id OR cmc.sender_id=$this->id) AND cmc.parent_id=$me";
                    
                    if ($result = $mysql->query($sql)) {
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $row['sender_id'] = GossoutUser::encodeData($row['sender_id']);
                                $arrFetch['message']['conversation'][] = $row;
                            }
                            $arrFetch['status'] = TRUE;
                        }
                    }
                }
            } else {
                $user = new GossoutUser(0);
                $user->setScreenName($userCon);
                $user->getProfile();
                $arrFetch['message']["cwn"] = $this->toSentenceCase(trim($user->getFullname()));
                $sql = "SELECT p.id, p.sender_id, p.receiver_id, p.message, p.time, p.status,(SELECT MAX(thumbnail50) FROM pictureuploads WHERE user_id=p.sender_id) as s_photo,(SELECT MAX(thumbnail50) FROM pictureuploads WHERE user_id=p.receiver_id) as r_photo,u.username as s_username, u.firstname as s_firstname, u.lastname as s_lastname,r.username as r_username, r.firstname as r_firstname, r.lastname as r_lastname FROM `privatemessae` as p JOIN user_personal_info as u ON u.id=p.sender_id JOIN user_personal_info as r ON r.id=p.receiver_id WHERE u.username ='$me' AND r.username='$userCon' OR u.username='$userCon' AND r.username='$me' LIMIT $this->start,$this->limit";
                if ($result = $mysql->query($sql)) {
                    if ($result->num_rows > 0) {
                        $user->setScreenName("");
                        $i = 0;
                        while ($row = $result->fetch_assoc()) {
                            $row['s_firstname'] = $this->toSentenceCase($row['s_firstname']);
                            $row['s_lastname'] = $this->toSentenceCase($row['s_lastname']);
                            $row['r_firstname'] = $this->toSentenceCase($row['r_firstname']);
                            $row['r_lastname'] = $this->toSentenceCase($row['r_lastname']);
                            if ($i == 0) {
                                $user->setUserId($row['sender_id']);
                                $user->getProfile();
                                $pix = $user->getProfilePix();
                                if ($pix['status']) {
                                    $arrFetch['message']['photo'][$user->getScreenName()] = $pix['pix'];
                                } else {
                                    $arrFetch['message']['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                                }
                                $user->setScreenName("");
                                $user->setUserId($row['receiver_id']);
                                $user->getProfile();
                                $pix2 = $user->getProfilePix();
                                if ($pix2['status']) {
                                    $arrFetch['message']['photo'][$user->getScreenName()] = $pix2['pix'];
                                } else {
                                    $arrFetch['message']['photo'] = array("nophoto" => TRUE, "alt" => $pix2['alt']);
                                }
                                $i++;
                            }
                            if ($row['status'] == "N" || $row['status'] == "D") {
                                $mysql->query("UPDATE `privatemessae` SET `status`='R' WHERE (sender_id='$row[sender_id]' AND receiver_id='$row[receiver_id]')");
                            }
                            $row['message'] = nl2br($row['message']);
                            $row['id'] = GossoutUser::encodeData($row['id']);
                            $row['sender_id'] = GossoutUser::encodeData($row['sender_id']);
                            $row['receiver_id'] = GossoutUser::encodeData($row['receiver_id']);
                            $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                            $arrFetch['message']['conversation'][] = $row;
                        }
                        $arrFetch['status'] = TRUE;
                    } else {
                        $arrFetch['status'] = TRUE;
                    }
                }
            }
            $result->free();
            $result = $mysql->query("SELECT NOW() as time");
            $row = $result->fetch_assoc();
            $result->free();
            $arrFetch['m_t'] = $this->convert_time_zone($row['time'], $this->tz);
        }
        $mysql->close();
        return $arrFetch;
    }

    /**
     * Fetches user’s gossbag combining post,comment, and tweak and wink
     * @return Array
     * @throws Exception is thrown when the connection to the server fails
     */
    public function getGossbag($checkTime = FALSE) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $user = new GossoutUser(0);
            //post notiif
            if ($checkTime) {//get posts for this user from the last updated time
                $sql1 = "Select p.id,p.post, c.unique_name,p.sender_id,c.name,u.username,u.firstname,u.lastname, p.time From post as p JOIN user_personal_info as u ON p.sender_id=u.id JOIN community as c ON p.community_id=c.id Where p.sender_id IN(select user from community_subscribers where community_id IN (Select community_id from community_subscribers where user = $this->id AND leave_status=0)) AND p.sender_id IN (Select if(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y') AND p.time>=(SELECT `lastupdate` FROM user_time_update WHERE `user_id`=$this->id) AND p.`deleteStatus`=0 order by p.id desc";
            } else {//get posts irrespective of time!
                $sql1 = "Select p.id,p.post, c.unique_name,p.sender_id,c.name,u.username,u.firstname,u.lastname, p.time From post as p JOIN user_personal_info as u ON p.sender_id=u.id JOIN community as c ON p.community_id=c.id Where p.sender_id IN(select user from community_subscribers where community_id IN (Select community_id from community_subscribers where user = $this->id AND leave_status=0)) AND p.sender_id IN (Select if(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y') AND p.`deleteStatus`=0 order by p.id desc";
            }
            $arrFetch['sql1'] = $sql1;
            if ($result = $mysql->query($sql1)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "post";
                        $user->setUserId($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $row['sender_id'] = GossoutUser::encodeData($row['sender_id']);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                $arrFetch['status'] = FALSE;
            }
            //comment notif
            if ($checkTime) {//get comments for this user from the last updated time
                $sql2 = "SELECT c.id,c.comment, c.post_id,c.sender_id,com.unique_name,com.`name`,u.username,u.firstname,u.lastname,p.sender_id as post_sender_id, c.`time`
FROM comments as c 
JOIN user_personal_info as u ON c.sender_id=u.id 
JOIN post as p ON c.post_id=p.id 
JOIN community as com ON p.community_id=com.id 
WHERE c.sender_id IN(SELECT `user` FROM community_subscribers WHERE community_id IN (SELECT community_id from community_subscribers WHERE `user` = $this->id AND leave_status=0)) 
AND c.sender_id IN (SELECT IF(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y')
AND c.time >= (SELECT `lastupdate` FROM user_time_update WHERE `user_id`=$this->id)
UNION (SELECT c.id,c.comment, c.post_id,c.sender_id,com.unique_name,com.`name`,u.username,u.firstname,u.lastname,p.sender_id as post_sender_id, c.`time` 
FROM comments as c 
JOIN post as p ON c.post_id=p.id 
JOIN user_personal_info as u ON c.sender_id=u.id 
JOIN community as com ON p.community_id=com.id 
WHERE p.sender_id=$this->id AND c.sender_id<>$this->id
AND c.time >= (SELECT `lastupdate` FROM user_time_update WHERE `user_id`=$this->id))
ORDER by `time`DESC";
            } else {//get comments irrespective of time!
                $sql2 = "SELECT c.id,c.comment, c.post_id,c.sender_id,com.unique_name, com.name, u.username, u.firstname,u.lastname,p.sender_id as post_sender_id,c.time 
FROM comments as c 
JOIN post as p ON c.post_id=p.id 
JOIN user_personal_info as u ON c.sender_id=u.id 
JOIN community as com ON p.community_id=com.id 
WHERE c.sender_id IN(SELECT `user` FROM community_subscribers WHERE community_id IN (SELECT community_id from community_subscribers WHERE `user` = $this->id AND leave_status=0)) 
AND c.sender_id IN (SELECT IF(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y')
UNION (SELECT c.id,c.comment, c.post_id,c.sender_id,com.unique_name,com.`name`,u.username,u.firstname,u.lastname,p.sender_id as post_sender_id, c.`time` 
FROM comments as c 
JOIN post as p ON c.post_id=p.id 
JOIN user_personal_info as u ON c.sender_id=u.id 
JOIN community as com ON p.community_id=com.id 
WHERE p.sender_id=$this->id AND c.sender_id<>$this->id)";
            }
            $arrFetch['sql2'] = $sql2;
            if ($result = $mysql->query($sql2)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "comment";
                        $row['isMyPost'] = $row['post_sender_id'] == $this->id ? TRUE : FALSE;
                        $user->setUserId($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    if (!$arrFetch['status'])
                        $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                if (!$arrFetch['status'])
                    $arrFetch['status'] = FALSE;
            }
            //wink notif
            $sql3 = "SELECT t.`id`, t.`sender_id`,u.username,u.firstname,u.lastname, t.`type`, t.`time`, t.`status` FROM `tweakwink` as t JOIN user_personal_info as u ON t.sender_id=u.id  WHERE t.`receiver_id` =$this->id AND status='N' order by t.id desc";
            $arrFetch['sql3'] = $sql3;
            if ($result = $mysql->query($sql3)) {
                if ($result->num_rows > 0) {
                    $user = new GossoutUser(0);
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "TW";
                        $user->setUserId($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['id'] = GossoutUser::encodeData($row['id']);
                        $row['sender_id'] = GossoutUser::encodeData($row['sender_id']);
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    if (!$arrFetch['status'])
                        $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                if (!$arrFetch['status'])
                    $arrFetch['status'] = FALSE;
            }
            //invitation notif
            $sql4 = "SELECT u.id,u.username,u.firstname,u.lastname,c.id as comid, c.`name`,c.unique_name,ci.`time`,ci.status FROM comminvitation as ci JOIN user_personal_info as u ON ci.sender_id=u.id JOIN community as c ON ci.comid=c.id WHERE ci.receiver_id=$this->id AND ci.status=0 order by ci.id desc";
            $arrFetch['sql4'] = $sql4;
            if ($result = $mysql->query($sql4)) {
                if ($result->num_rows > 0) {
                    $user = new GossoutUser(0);
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "IV";
                        $user->setUserId($row['id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['id'] = GossoutUser::encodeData($row['id']);
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    if (!$arrFetch['status'])
                        $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                if (!$arrFetch['status'])
                    $arrFetch['status'] = FALSE;
            }
            //friends request notif
            $sql5 = "SELECT uc.username1,uc.`time`,u.username,u.firstname,u.lastname FROM usercontacts as uc JOIN user_personal_info as u ON uc.username1=u.id WHERE username2=$this->id AND status='N' order by uc.id desc";
            $arrFetch['sql5'] = $sql5;
            if ($result = $mysql->query($sql5)) {
                if ($result->num_rows > 0) {
                    $user = new GossoutUser(0);
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "frq";
                        $user->setUserId($row['username1']);
                        $row['username1'] = GossoutUser::encodeData($row['username1']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    if (!$arrFetch['status'])
                        $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                if (!$arrFetch['status'])
                    $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function getNotificationSummary() {
        $gb = $this->getGossbag(TRUE);
        $msg = $this->getPrivateMessageSummary();
        $comNoitif = Community::getComMsgNotif($this->id);
        $response['msg'] = $msg['status'] ? ((int) $msg['summary']['undeli']) : 0;
        $response['gb'] = $gb['status'] ? count($gb['bag']) : 0;
        $response['cn'] = $comNoitif['status'] ? $comNoitif['count'] : 0;
        return $response;
    }

    public function getTimeline($userTimeline = FALSE) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $user = new GossoutUser(0);
            $com = new Community();
            //post notiif
            if ($userTimeline) {
                $sql1 = "Select p.id,p.post, c.unique_name,p.sender_id,c.name,u.username,u.firstname,u.lastname, p.time From post as p JOIN user_personal_info as u ON p.sender_id=u.id JOIN community as c ON p.community_id=c.id Where (p.sender_id=$this->id AND p.`deleteStatus`=0 AND c.`type`='Public') order by p.id desc";
            } else {
                $sql1 = "Select p.id,p.post, c.unique_name,p.sender_id,c.name,u.username,u.firstname,u.lastname, p.time From post as p JOIN user_personal_info as u ON p.sender_id=u.id JOIN community as c ON p.community_id=c.id Where (p.sender_id=$this->id AND p.`deleteStatus`=0) OR p.sender_id IN(select user from community_subscribers where community_id IN (Select community_id from community_subscribers where user = $this->id AND leave_status=0)) AND p.sender_id IN (Select if(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y') AND p.`deleteStatus`=0 order by p.id desc";
            }
            if ($result = $mysql->query($sql1)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "post";
                        $user->setUserId($row['sender_id']);
                        $pix = $user->getProfilePix(array("original", "thumbnail50"));
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $post = new Post();
                        $comCount = $post->getCommentCountFor($row['id']);
                        if ($comCount['status']) {
                            $row['numComnt'] = $comCount['count'];
                        } else {
                            $row['numComnt'] = 0;
                        }
                        $post_image = $post->loadPostImage($row['id']);
                        if ($post_image['status']) {
                            $row['post_photo'] = $post_image['photo'];
                        }
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $row['sender_id'] = GossoutUser::encodeData($row['sender_id']);
                        $arrFetch['timeline'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                $arrFetch['status'] = FALSE;
            }
            //community creation by friend
            if ($userTimeline) {
                $sql2 = "SELECT c.`id`, c.`unique_name`,u.username,u.firstname,u.lastname, c.`name`, c.`category`, c.`type`, c.`description`,c.pix,c.`thumbnail100`, c.`datecreated` as time, c.`creator_id` FROM `community` as c JOIN user_personal_info as u ON c.creator_id=u.id WHERE `creator_id` =$this->id";
            } else {
                $sql2 = "SELECT c.`id`, c.`unique_name`,u.username,u.firstname,u.lastname, c.`name`, c.`category`, c.`type`, c.`description`,c.pix,c.`thumbnail100`, c.`datecreated` as time, c.`creator_id` FROM `community` as c JOIN user_personal_info as u ON c.creator_id=u.id WHERE `creator_id` IN(Select if(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y')";
            }
            if ($result = $mysql->query($sql2)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "comcrea";
                        $user->setUserId($row['creator_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $com->setCommunityId($row['id']);
                        $isAmember = Community::isAmember($row['id'], $this->id);
                        $row['isAmember'] = $isAmember['status'];
                        $row['creator_id'] = GossoutUser::encodeData($row['creator_id']);
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['timeline'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    if (!$arrFetch['status'])
                        $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                if (!$arrFetch['status'])
                    $arrFetch['status'] = FALSE;
            }
            //friends who joined community
            if ($userTimeline) {
                $sql3 = "SELECT c.name,cs.community_id,cs.`user`, cs.datejoined as time,cs.leave_status FROM community_subscribers as cs JOIN community as c ON cs.community_id=c.id WHERE cs.leave_status=0 cs.`user`=$this->id ";
            } else {
                $sql3 = "SELECT c.name,cs.community_id,cs.`user`, cs.datejoined as time,cs.leave_status FROM community_subscribers as cs JOIN community as c ON cs.community_id=c.id WHERE cs.leave_status=0 cs.`user` IN(Select if(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y')";
            }
            if ($result = $mysql->query($sql3)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['type'] = "joinCom";
                        $user->setUserId($row['user']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['timeline'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    if (!$arrFetch['status'])
                        $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                if (!$arrFetch['status'])
                    $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function register($firstname, $lastname, $email, $password, $gender, $dob) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $usernameTemp = explode('@', $email);
            $username = FALSE;
            $count = 0;
            do {
                if ($count > 0) {
                    $username = $this->prepareUsername(str_replace(".", "", $usernameTemp[0]) . $count);
                } else {
                    $username = $this->prepareUsername(str_replace(".", "", $usernameTemp[0]));
                }
                $count++;
            } while (!$username);
            $sql = "INSERT INTO `user_personal_info`(`firstname`, `lastname`, `email`,`username`, `gender`, `dob`) VALUES ('$firstname','$lastname','$email','$username','$gender','$dob')";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $newUid = $mysql->insert_id;
                    $token = md5(strtolower($email . $lastname . $password));
                    $sql = "INSERT INTO `user_login_details`(`id`, `password`, `token`) VALUES ('$newUid','$password','$token')";
                    $mysql->query($sql);
                    if ($mysql->affected_rows > 0) {
                        $e = new Encryption();
                        setcookie("user_auth", GossoutUser::encodeData($newUid), 0);
                        setcookie("ro", GossoutUser::encodeData($e->encode(md5(sha1(GossoutUser::encodeData($newUid))))), 0);
                        $this->setUserId($newUid);
                        $user = $this->getProfile();
                        $_SESSION['auth'] = $user['user'];
                        $_SESSION['auth']['token'] = $token;
                        $_SESSION['newuser'] = TRUE;
                        $arrFetch['status'] = TRUE;
                        $arrFetch['id'] = $newUid;
                    } else {
                        $sql = "DELETE FROM `user_personal_info` WHERE `id`=$newUid";
                        $mysql->query($sql);
                        $arrFetch['status'] = FALSE;
                        $arrFetch['message'] = "An unexpected error just occured. Please try again some minutes later.";
                    }
                } else {
                    $arrFetch['status'] = FALSE;
                    $arrFetch['message'] = "An unexpected error just occured. Please try again some minutes later.";
                }
            } else {
                $arrFetch['status'] = FALSE;
                $arrFetch['message'] = "An unexpected error just occured. Please try again some minutes later.";
            }
        }
        $mysql->close();
        return $arrFetch;
    }

    private function prepareUsername($email) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM user_personal_info WHERE username='$email'";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $result->free();
                    $mysql->close();
                    return FALSE;
                } else {
                    $mysql->close();
                    return $email;
                }
            }
        }
    }

    public function unfriend($userid, $opt = "UNFRIEND") {
        $arr = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if ($opt == "UNFRIEND") {
                $sql = "UPDATE usercontacts SET status='R' WHERE ((username1='$this->id' AND username2='$userid') OR (username1='$userid' AND username2='$this->id')) AND (status='Y' OR status ='N')";
            } else if ($opt == "Cancel Request" || $opt == "Ignore") {
                $sql = "UPDATE usercontacts SET status='R' WHERE ((username1='$userid' AND username2='$this->id')) AND (status='Y' OR status ='N')";
            }
            $arr['sql'] = $sql;
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arr['status'] = TRUE;
                } else {
                    $arr['status'] = FALSE;
                }
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function acceptFriendRequest($userid) {
        $arr = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE usercontacts SET status='Y' WHERE username1='$userid' AND username2='$this->id' AND status='N'";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arr['status'] = TRUE;
                } else {
                    $arr['status'] = FALSE;
                }
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function sendFriendRequest($userid) {
        $arr = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM usercontacts WHERE ((username1='$this->id' AND username2='$userid') OR (username1='$userid' AND username2='$this->id')) AND (status='N' OR status='Y')";

            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $arr['status'] = TRUE;
                } else {
                    $sql = "INSERT INTO usercontacts(username1,username2,sender_id) VALUES($this->id,$userid,$this->id)";
                    if ($mysql->query($sql)) {
                        if ($mysql->affected_rows > 0) {
                            $arr['status'] = TRUE;
                        } else {
                            $arr['status'] = FALSE;
                        }
                    } else {
                        $arr['status'] = FALSE;
                    }
                }
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function wink($userid, $winkBack = FALSE) {
        if ($winkBack) {
            $this->responseToWink($userid);
        }
        $arr = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM tweakwink WHERE sender_id=$this->id AND receiver_id=$userid AND status='N'";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $arr['status'] = FALSE;
                } else {
                    $sql = "INSERT INTO tweakwink(sender_id,receiver_id,`type`) VALUES('$this->id','$userid','W')";
                    if ($mysql->query($sql)) {
                        if ($mysql->affected_rows > 0) {
                            $arr['status'] = TRUE;
                        } else {
                            $arr['status'] = FALSE;
                        }
                    } else {
                        $arr['status'] = FALSE;
                    }
                }
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function responseToWink($userid, $response = "R") {
        $arr = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE tweakwink SET status ='$response' WHERE sender_id=$userid AND receiver_id=$this->id AND status='N'";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arr['status'] = TRUE;
                } else {
                    $arr['status'] = FALSE;
                }
            } else {
                $arr['status'] = FALSE;
                $arr['sql'] = $sql;
                echo json_encode($arr);
                exit;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function getMiniStat($param = "ALL") {
        $userCom = new Community();
        $post = new Post();
        $post->setUserId($this->getId());
        $userCom->setUser($this->getId());

        $com_count = 0;
        $user_count = 0;
        $post_count = 0;

        $comCount = $userCom->userCommunityCount();
        if ($comCount['status']) {
            $com_count = $comCount['com_count']['count'];
        }

        $userCount = $this->countUserFriends();
        if ($userCount['status']) {
            $user_count = $userCount['friends_count']['count'];
        }

        $postCount = $post->countUserPosts();
        if ($postCount['status']) {
            $post_count = $postCount['post_count']['count'];
        }
        if ($param == "ALL") {
            return array("fc" => $user_count, "cc" => $com_count, "pc" => $post_count);
        } else if ($param == "fc") {
            return $user_count;
        } else if ($param == "cc") {
            return $com_count;
        } else if ($param == "pc") {
            return $post_count;
        }
    }

    /**
     * 
     * @param String $date The string date format from the database in the format yyyy-mm-dd
     * @param boolean $withYear specify whether the year should be displayed or not. DEAFULT value is 'FALSE'
     * @return String This returns the formated string in the form  13 January 2014
     */
    private function dateToString($date, $withYear = false) {
        if (trim($date) == "") {
            return "";
        } else {
            $month = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
            $arr1 = explode(' ', $date);
            $arr = explode('-', $arr1[0]);
            if ($withYear) {
                $str = $month[$arr[1] - 1] . " " . intval($arr[2]) . ", " . $arr[0];
            } else {
                $str = $month[$arr[1] - 1] . " " . intval($arr[2]);
            }
            return $str;
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

    private function convert_time_zone($timeFromDatabase_time, $tz) {
        $date = new DateTime($timeFromDatabase_time, new DateTimeZone(date_default_timezone_get()));
        $date->setTimezone(new DateTimeZone($tz));
        return $date->format('Y-m-d H:i:s');
        // or return $userTime; // if you want to return a DateTime object.
    }

    public function clean($value) {
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

    public function searchPeople($term) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $arr = explode(' ', $term);
            $searchCombination = "";
            if (count($arr) == 1) {
                $searchCombination = "`firstname` LIKE '%$arr[0]%' OR `lastname` LIKE '%$arr[0]%' OR email = '$arr[0]'";
            } else if (count($arr) > 1) {
                $merg = "";
                for ($i = 1; $i < count($arr); $i++) {
                    if ($i > 1) {
                        $merg.=" ";
                    }
                    $merg.=$arr[$i];
                }
                $arr = array($arr[0], $merg);
                $searchCombination = "((`firstname` LIKE '%$arr[0]%' OR `firstname` LIKE '%$arr[1]%') OR (`lastname` LIKE '%$arr[0]%' OR `lastname` LIKE '%$arr[1]%'))";
            }
            $sql = "SELECT id,username,firstname,lastname,location,gender,`dateJoined` FROM `user_personal_info` WHERE $searchCombination LIMIT $this->start,$this->limit";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $this->setUserId($row['id']);
                        $pix = $this->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['dateJoined'] = $this->dateToString($row['dateJoined'], TRUE);
                        $row['id'] = GossoutUser::encodeData($row['id']);
                        $arrFetch['people'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
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

    public function loaWink() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $user = new GossoutUser(0);
            $sql = "SELECT t.`id`, t.`sender_id`,u.username,u.firstname,u.lastname, t.`type`, t.`time`, t.`status` FROM `tweakwink` as t JOIN user_personal_info as u ON t.sender_id=u.id  WHERE t.`receiver_id` =$this->id AND status='N' order by t.id desc Limit $this->start, $this->limit";

            if ($result = $mysql->query($sql)) {
                $arrFetch['status'] = FALSE;

                if ($result->num_rows > 0) {
                    $_SESSION['status_string'] = $result->num_rows;
                    $user = new GossoutUser(0);
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "TW";
                        $user->setUserId($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['id'] = GossoutUser::encodeData($row['id']);
                        $row['sender_id'] = GossoutUser::encodeData($row['sender_id']);
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                    return $arrFetch;
                } else {
                    if (isset($arrFetch['status']) && !$arrFetch['status'])
                        $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
    }

    public function loadGossComment() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $user = new GossoutUser(0);
            $sql2 = "SELECT c.id,c.comment, c.post_id,c.sender_id,com.unique_name, com.name, u.username, u.firstname,u.lastname,p.sender_id as post_sender_id,c.time 
FROM comments as c 
JOIN post as p ON c.post_id=p.id 
JOIN user_personal_info as u ON c.sender_id=u.id 
JOIN community as com ON p.community_id=com.id 
WHERE c.sender_id IN(SELECT `user` FROM community_subscribers WHERE community_id IN (SELECT community_id from community_subscribers WHERE `user` = $this->id AND leave_status=0)) 
AND c.sender_id IN (SELECT IF(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y')
UNION (SELECT c.id,c.comment, c.post_id,c.sender_id,com.unique_name,com.`name`,u.username,u.firstname,u.lastname,p.sender_id as post_sender_id, c.`time` 
FROM comments as c 
JOIN post as p ON c.post_id=p.id 
JOIN user_personal_info as u ON c.sender_id=u.id 
JOIN community as com ON p.community_id=com.id 
WHERE p.sender_id=$this->id AND c.sender_id<>$this->id) 
ORDER by `time` DESC LIMIT $this->start, $this->limit";

//            $arrFetch['sql2'] = $sql2;
            if ($result = $mysql->query($sql2)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "comment";
                        $row['isMyPost'] = $row['post_sender_id'] == $this->id ? TRUE : FALSE;
                        $user->setUserId($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                    return $arrFetch;
                } else {
                    $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
    }

    public function loadGossFrq() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $user = new GossoutUser(0);
            $sql3 = "SELECT uc.username1,uc.`time`,u.username,u.firstname,u.lastname FROM usercontacts as uc JOIN user_personal_info as u ON uc.username1=u.id WHERE username2=$this->id AND status='N' order by uc.id desc LIMIT $this->start,$this->limit";
            if ($result = $mysql->query($sql3)) {
                if ($result->num_rows > 0) {
                    $user = new GossoutUser(0);
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "frq";
                        $user->setUserId($row['username1']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['username1'] = GossoutUser::encodeData($row['username1']);
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                    return $arrFetch;
                } else {
                    $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        return $arrFetch;
    }

    public function loadGossPost() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $user = new GossoutUser(0);
            $sql1 = "Select p.id,p.post, c.unique_name,p.sender_id,c.name,u.username,u.firstname,u.lastname, p.time From post as p JOIN user_personal_info as u ON p.sender_id=u.id JOIN community as c ON p.community_id=c.id Where p.sender_id IN(select user from community_subscribers where community_id IN (Select community_id from community_subscribers where user = $this->id AND leave_status=0)) AND p.sender_id IN (Select if(uc.username1=$this->id,uc.username2,uc.username1) as id From usercontacts as uc, user_personal_info Where ((username1 = user_personal_info.id AND username2 = $this->id) OR (username2 = user_personal_info.id AND username1 = $this->id)) AND status ='Y') AND p.`deleteStatus`=0 order by p.id desc Limit $this->start, $this->limit";

            $arrFetch['sql1'] = $sql1;
            if ($result = $mysql->query($sql1)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['type'] = "post";
                        $user->setUserId($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $row['sender_id'] = GossoutUser::encodeData($row['sender_id']);
                        $arrFetch['bag'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                    return $arrFetch;
                } else {
                    $arrFetch['status'] = FALSE;
                }
                $result->free();
            } else {
                $arrFetch['status'] = FALSE;
            }
//                $result->free();
        }
    }

    public static function encodeData($param, $useBase64 = TRUE) {
        $encrypt = new Encryption();
        if ($useBase64) {
            return $encrypt->safe_b64encode($param);
        } else {
            return $encrypt->encode($param);
        }
    }

    public static function decodeData($param, $useBase64 = TRUE) {
        $encrypt = new Encryption();
        if ($useBase64) {
            return $encrypt->safe_b64decode($param);
        } else {
            return $encrypt->decode($param);
        }
    }

}

?>
