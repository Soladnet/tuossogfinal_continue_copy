<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'Config.php';
include_once './GossoutUser.php';
include_once './encryptionClass.php';

/**
 * Description of Communityf
 *
 * @author user
 */
class Community {

    var $uid, $id, $start = 0, $limit = 5, $newuser, $isTimeline = FALSE, $allcom;

    public function __construct() {
        
    }

    function create($unique_name, $comm_name, $comm_desc, $creator_id, $pix = "images/no-pic.png", $thum150 = "images/no-pic.png", $thum100 = "images/no-pic.png", $comm_privacy = "Public") {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO community (creator_id, unique_name, name, description, type,pix,thumbnail150,thumbnail100) values ($creator_id,'$unique_name','$comm_name', '$comm_desc', '$comm_privacy','$pix','$thum150','$thum100')";
            if ($mysql->query($sql)) {
                $id = $mysql->insert_id;
                $mysql->query("INSERT INTO community_subscribers(`user`,community_id) VALUES($creator_id,$id)");
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    /**
     * @param int $userId This is the user's unique ID
     * @param int $start This specifies where the query starts from for pagination
     * @param int $limit This specifies the end of the result for pagination
     * @return Array An associative array is returned with the information for the user's community
     * 
     */
    public function userComm($start, $limit, $max = FALSE, $comname = FALSE) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if ($max) {
                if ($comname) {
                    $sql = "SELECT id,creator_id,unique_name,`name`,category,`pix`,thumbnail100,thumbnail150,thumbnail150,`type`,description,verified,`enableMemberPost` FROM community WHERE unique_name='$comname'";
                } else if ($this->newuser) {
                    $sql = "SELECT DISTINCT cs.`community_id` as id,c.creator_id,c.unique_name,c.`name`,category,c.`pix`,c.thumbnail100,c.thumbnail150,c.thumbnail150,c.`type`,c.description,c.verified,`enableMemberPost` FROM community_subscribers as cs, community as c  WHERE c.id = cs.community_id AND cs.community_id NOT IN (SELECT community_id FROM `community_subscribers` WHERE user = $this->uid AND leave_status = 0) order by c.name asc LIMIT $start, $limit";
                } else if ($this->allcom) {
                    $sql = "SELECT DISTINCT cs.`community_id` as id,c.creator_id,c.unique_name,c.`name`,category,c.`pix`,c.thumbnail100,c.thumbnail150,c.thumbnail150,c.`type`,c.description,c.verified,`enableMemberPost` FROM community_subscribers as cs JOIN community as c ON cs.community_id=c.id WHERE `type`='Public' order by c.name asc LIMIT $start, $limit";
                } else {
                    $sql = "SELECT cs.`community_id` as id,c.creator_id,c.unique_name,c.`name`,category,c.`pix`,c.thumbnail100,c.thumbnail150,c.thumbnail150,c.`type`,c.description,c.verified,`enableMemberPost` FROM community_subscribers as cs JOIN community as c ON cs.community_id=c.id  WHERE cs.`user`=$this->uid AND cs.leave_status=0 order by c.name asc LIMIT $start,$limit";
                }
            } else {
                if ($comname) {
                    $sql = "SELECT id,creator_id,unique_name,`name`,enableMemberPost FROM community WHERE unique_name='$comname'";
                } else {
                    $sql = "SELECT cs.`community_id` as id,c.creator_id,c.unique_name,c.`name`,enableMemberPost FROM community_subscribers as cs JOIN community as c ON cs.community_id=c.id  WHERE cs.`user`=$this->uid AND cs.leave_status=0 order by c.name asc LIMIT $start,$limit";
                }
            }
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $comInfo = new Community();
                    while ($row = $result->fetch_assoc()) {
                        $this->setCommunityId($row['id']);
                        $row['name'] = $this->toSentenceCase($row['name']);
                        $isAm = $this->isAmember($this->uid);
                        if ($isAm['status']) {
                            $row['isAmember'] = "true";
                        } else {
                            $row['isAmember'] = "false";
                        }
                        if ($max) {
                            $comInfo->setCommunityId($row['id']);
                            $mem_count = $comInfo->getMemberCount();
                            if ($mem_count['status']) {
                                $row['mem_count'] = $mem_count['count'];
                            } else {
                                $row['mem_count'] = 0;
                            }
                            $post_count = $comInfo->getPostCount();
                            if ($post_count['status']) {
                                $row['post_count'] = $post_count['count'];
                            } else {
                                $row['post_count'] = 0;
                            }
                        }
                        $row['creator_id'] = $this->encodeData($row['creator_id']);
                        $arr['community_list'][] = $row;
                    }
                    $arr['status'] = true;
                } else {
                    $arr['status'] = false;
                }
                $result->free();
            } else {
                $arr['status'] = false;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function isAmember($uid) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM community_subscribers WHERE `user`=$uid AND leave_status=0 AND community_id=$this->id";
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
        }
        $mysql->close();
        return $arr;
    }

    public function userCommunityCount() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT count(`community_id`) as count FROM `community_subscribers` WHERE `user`=$this->uid AND leave_status=0";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $arr['com_count'] = $row;
                    $arr['status'] = true;
                } else {
                    $arr['status'] = false;
                }
                $result->free();
            } else {
                $arr['status'] = false;
            }
        }
        $mysql->close();
        return $arr;
    }

    /**
     * 
     * @param int start start position for pagination purpose
     * @param int limit limit of result fetch
     * @return Array An array with keys <strong>status</strong> and <strong>com_mem</strong> is returned. <strong>com_mem</strong> contains array of community members with the following keys: id, firstname, lastname, location, and gender while <strong>status</strong> holds the success status of the result i.e FALSE or TRUE
     * @throws Exception 
     */
    public function getMembers($com, $myId, $start, $limit) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT cs.user as id,username,p.firstname, p.lastname,p.location,p.gender FROM community_subscribers AS cs JOIN user_personal_info as p ON p.id=cs.`user` JOIN community as c ON cs.community_id=c.id WHERE (cs.community_id='$com' OR c.unique_name='$com') AND cs.leave_status=0 order by p.firstname LIMIT $start,$limit";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    include_once './encryptionClass.php';
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
                        if (is_numeric($myId)) {
                            $temp = $user->isAfriend($myId);
                            $row['isAfriend'] = $temp['status'];
                        } else {
                            $row['isAfriend'] = FALSE;
                        }
                        $row['ministat'] = $user->getMiniStat();
                        $row['id'] = $this->encodeData($row['id']);
                        $arr['com_mem'][] = $row;
                    }
                    shuffle($arr['com_mem']);
                    $arr['status'] = true;
                } else {
                    $arr['status'] = false;
                }
                $result->free();
            } else {
                $arr['status'] = false;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function getMemberCount() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT count(user) as count FROM community_subscribers WHERE community_id=$this->id AND leave_status=0";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $arr['count'] = $row['count'];
                    $arr['status'] = TRUE;
                } else {
                    $arr['status'] = FALSE;
                }
                $result->free();
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function getPostCount() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT count(`id`) as count from post where community_id=$this->id AND `deleteStatus`=0";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $arr['count'] = $row['count'];
                    $arr['status'] = TRUE;
                } else {
                    $arr['status'] = FALSE;
                }
                $result->free();
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function getCommunityInfo() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * from community WHERE id=$this->id";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $row['sql'] = $sql;
                    $arr['comm'] = $row;
                    $arr['status'] = TRUE;
                } else {
                    $arr['status'] = FALSE;
                }
                $result->free();
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    /**
     * 
     * @param int newUid This id identifies the user to query
     */
    public function setUser($newUid) {
        $this->uid = $newUid;
    }

    public function setStart($newStart) {
        $this->start = $newStart;
    }

    public function setLimit($newLimit) {
        $this->limit = $newLimit;
    }

    public function setNewUser() {
        $this->newuser = true;
    }

    public static function communityExist($comId) {
        $arrFetch = array("status" => FALSE);
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM community WHERE unique_name='" . Community::clean($comId) . "' OR id='" . Community::clean($comId) . "'";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $arrFetch['status'] = $row['id'];
                }
                $result->free();
            }
        }
        $mysql->close();
        return $arrFetch;
    }

    public static function isCreator($comId, $uid) {
        $arrFetch = array("status" => FALSE);
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM community WHERE (unique_name='" . Community::clean($comId) . "' OR id='" . Community::clean($comId) . "') AND creator_id=$uid";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $arrFetch['status'] = TRUE;
                }
                $result->free();
            }
        }
        return $arrFetch;
        $mysql->close();
    }

    public function setIsTimeline($isTimeline) {
        $this->isTimeline = $$isTimeline;
    }

    public function setAllCom() {
        $this->allcom = true;
    }

    /**
     * 
     * @return int The current user defined for this community object
     */
    public function getUser() {
        return $this->uid;
    }

    /**
     * 
     * @param int newId switch the current community id to newId
     */
    public function setCommunityId($newId) {
        $this->id = $newId;
    }

    public function suggest() {
        $response = array();
        $arr = array();
        $user = new GossoutUser($this->getUser());
        $userF = $user->getFriends(0, 1000);
        if ($userF['status']) {
            $com = new Community();
            foreach ($userF['friends'] as $friend) {
                $com->setUser($this->decodeData($friend['id']));
                $userComm = $com->userComm(0, 1000, TRUE);
                if ($userComm['status'])
                    foreach ($userComm['community_list'] as $mem) {
                        if ($mem['type'] != "Private") {
                            $mem['isAmember'] = "false";
                            $arr[$mem['id']] = $mem;
                        }
                    }
            }
            if (count($arr) > 0) {
                $response['status'] = TRUE;
            } else {
                $response['status'] = FALSE;
            }
        } else {
            $response['status'] = FALSE;
        }
//        if ($response['status']) {
//            $myComm = $this->userComm(0, 1000, TRUE);
//            if ($myComm['status']) {
//                foreach ($myComm['community_list'] as $item) {
//                    if (array_key_exists($item['id'], $arr)) {
//                        unset($arr[$item['id']]);
//                    }
//                }
//            }
//            if (count($arr) == 0) {
//                $response['status'] = FALSE;
//            } else {
//                $response['suggest'] = array_values($arr);
//                shuffle($response['suggest']);
//            }
//        } //else {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
//        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT id,unique_name,`name`,category,`type`,description,pix,verified FROM community WHERE `type`='Public'";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $comInfo = new Community();
                    while ($row = $result->fetch_assoc()) {
                        $comInfo->setCommunityId($row['id']);
                        $mem_count = $comInfo->getMemberCount();
                        if ($mem_count['status']) {
                            $row['mem_count'] = $mem_count['count'];
                        } else {
                            $row['mem_count'] = 0;
                        }
                        $post_count = $comInfo->getPostCount();
                        if ($post_count['status']) {
                            $row['post_count'] = $post_count['count'];
                        } else {
                            $row['post_count'] = 0;
                        }
                        $arr[$row['id']] = $row;
                    }
                    $response['status'] = TRUE;
                } else {
                    $response['status'] = FALSE;
                }
            } else {
                $response['status'] = FALSE;
            }
        }
        if ($response['status']) {
            $myComm = $this->userComm(0, 1000);
            if ($myComm['status']) {
                foreach ($myComm['community_list'] as $item) {
                    if (array_key_exists($item['id'], $arr)) {
                        unset($arr[$item['id']]);
                    }
                }
            }
            if (count($arr) == 0) {
                $response['status'] = FALSE;
            } else {
                $response['suggest'] = array_values($arr);
                shuffle($response['suggest']);
            }
        }
//        }
        return $response;
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

    public function inviteFriends() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT if(uc.username1=$this->uid,uc.username2,uc.username1) as id,username,firstname, lastname From user_personal_info, usercontacts as uc Where ((username1 = user_personal_info.id AND username2 = $this->uid) OR (username2 = user_personal_info.id AND username1 = $this->uid)) AND status ='Y' AND (username1 NOT IN(SELECT user FROM community_subscribers WHERE `community_id`=$this->id AND leave_status=0) OR username2 NOT IN(SELECT user FROM community_subscribers WHERE `community_id`=$this->id AND leave_status=0))";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['id'] = $this->encodeData($row['id']);
                        $arrFetch['friends'][] = $row;
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

    public function sendInvitation($values) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO comminvitation (sender_id,receiver_id,comid) VALUES $values";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function respondToInvitation() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE comminvitation SET status =1 WHERE receiver_id=$this->uid AND comid=$this->id";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function updateDescription($desc, $comHelve) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE community SET description='$desc' WHERE unique_name='$comHelve'";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function enablePostStatus($param, $comHelve) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE community SET `enableMemberPost`='$param' WHERE unique_name='$comHelve'";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function updateCommunityTag($param, $comHelve) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE community SET category='$param' WHERE unique_name='$comHelve'";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function updatePix($pix, $thumbnail100, $thumbnail150, $comHelve) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql1 = "SELECT pix,thumbnail100,thumbnail150 FROM community WHERE unique_name='$comHelve'";
            if ($result = $mysql->query($sql1)) {
                if ($result->num_rows > 0) {
                    $arr['com_pix'] = $result->fetch_assoc();
                    $sql = "UPDATE community SET pix='$pix',thumbnail100='$thumbnail100',thumbnail150='$thumbnail150' WHERE unique_name='$comHelve'";
                    if ($mysql->query($sql)) {
                        $arr['status'] = TRUE;
                    } else {
                        $arr['status'] = FALSE;
                    }
                } else {
                    $arr['status'] = FALSE;
                }
                $result->free();
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function updateName($name, $comHelve) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE community SET `name`='$name' WHERE unique_name='$comHelve'";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function updatePrivacy($privacy, $comHelve) {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE community SET `type`='$privacy' WHERE unique_name='$comHelve'";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function leave() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE community_subscribers SET leave_status=1,datejoined=NOW() WHERE `user`=$this->uid AND community_id=$this->id";
            if ($mysql->query($sql)) {
                $arr['status'] = TRUE;
            } else {
                $arr['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arr;
    }

    public function join() {
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        $arr = array();
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM community_subscribers WHERE `user`=$this->uid AND community_id=$this->id";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $sql = "UPDATE community_subscribers SET leave_status=0,datejoined=NOW() WHERE `user`=$this->uid AND community_id=$this->id";
                    if ($mysql->query($sql)) {
//                        $sql = "SELECT `unique_name`,`name` From community WHERE id =$this->id";
//                        if ($result = $mysql->query($sql)){
                        if ($result->num_rows > 0)
                            $arr['status'] = TRUE;
//                             $arr = $mysql->fetch_assoc();
//                        }
                    } else {
                        $arr['status'] = FALSE;
                    }
                } else {
                    $sql = "INSERT INTO community_subscribers(`user`,community_id) VALUES($this->uid,$this->id)";
                    if ($mysql->query($sql)) {
                        $arr['status'] = TRUE;
                    } else {
                        $arr['status'] = FALSE;
                    }
                }
            }
        }
        $mysql->close();
        return $arr;
    }

    public function searchCommunity($term) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT id,unique_name,`name`,`type`,description,thumbnail150,verified FROM community WHERE (`name` LIKE '%$term%' OR unique_name LIKE '%$term%' OR description LIKE '%$term%') AND `type`='Public' LIMIT $this->start,$this->limit";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $this->setCommunityId($row['id']);
                        $mem_count = $this->getMemberCount();
                        if ($mem_count['status']) {
                            $row['mem_count'] = $mem_count['count'];
                        } else {
                            $row['mem_count'] = 0;
                        }
                        $post_count = $this->getPostCount();
                        if ($post_count['status']) {
                            $row['post_count'] = $post_count['count'];
                        } else {
                            $row['post_count'] = 0;
                        }
//                        if ($row['mem_count'] != 0)
                        $arrFetch['community'][] = $row;
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

    function encodeData($param, $useBase64 = TRUE) {
        $encrypt = new Encryption();
        if ($useBase64) {
            return $encrypt->safe_b64encode($param);
        } else {
            return $encrypt->encode($param);
        }
    }

    function decodeData($param, $useBase64 = TRUE) {
        $encrypt = new Encryption();
        if ($useBase64) {
            return $encrypt->safe_b64decode($param);
        } else {
            return $encrypt->decode($param);
        }
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
