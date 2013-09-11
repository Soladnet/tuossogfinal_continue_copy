<?php

require_once './Config.php';
require_once './Gossout_Community.php';
require_once './GossoutUser.php';

class Search {

    var $term;
    var $result;
    var $uid;

    public function __construct() {
        
    }

    public function setUid($id) {
        $this->uid = $id;
    }

    public function search($term, $start, $limit, $opt = "both") {
        $response = array('status' => FALSE);
        $comInfo = new Community();
        $user = new GossoutUser(0);
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $arr = explode(' ', $term);
            if ($opt == "people" || $opt == "both") {
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
                    //first combination
                    $searchCombination = "((`firstname` LIKE '%$arr[0]%' OR `firstname` LIKE '%$arr[1]%') AND (`lastname` LIKE '%$arr[1]%' OR `lastname` LIKE '%$arr[0]%') OR email = '$term')";
                }
                $sql = "SELECT id,firstname,lastname,location,gender FROM `user_personal_info` WHERE $searchCombination Limit $start,$limit";
                if ($result = $mysql->query($sql)) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $row['firstname'] = $this->toSentenceCase($row['firstname']);
                            $row['lastname'] = $this->toSentenceCase($row['lastname']);
                            $response['people'][] = $row;
                        }
                        $response['status'] = TRUE;
                    }
                    $result->free();
                }
            }
            if ($opt == "community" || $opt == "both") {
                $sqlCommunity = "SELECT `id`,unique_name, `name`, `category`, `description` FROM `community` WHERE (`name` LIKE '%$term%' OR unique_name LIKE '%$term%' OR description LIKE '%$term%') Limit $start,$limit";
                if ($result = $mysql->query($sqlCommunity)) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $response['community'][] = $row;
                        }
                        $response['status'] = TRUE;
                    }
                    $result->free();
                }
            }
            if ($opt == "mc") {
                $sqlCommunity = "SELECT c.`id`, c.`type`,c.unique_name,c.`name`,c.thumbnail100,c.thumbnail150, c.`description` FROM `community` as c JOIN community_subscribers as cs ON c.id=cs.community_id WHERE (c.`name` LIKE '%$term%' OR c.unique_name LIKE '%$term%' OR description LIKE '%$term%') AND cs.`user`=$this->uid AND cs.leave_status=0 Limit $start,$limit";
                if ($result = $mysql->query($sqlCommunity)) {
                    if ($result->num_rows > 0) {
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
                            $response['community'][] = $row;
                        }
                        $response['status'] = TRUE;
                    }
                    $result->free();
                }
            }
            if ($opt == "mf") {
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
                    //first combination
                    $searchCombination = "((`firstname` = '$arr[0]' OR `firstname` = '$arr[1]') OR (`lastname` = '$arr[1]' OR `lastname` = '$arr[0]') OR email = '$term')";
                }
                $sql = "SELECT * FROM (SELECT if(uc.username1=$this->uid,uc.username2,uc.username1) as id,username,email,firstname, lastname,location,gender From user_personal_info, usercontacts as uc Where ((username1 = user_personal_info.id AND username2 = $this->uid) OR (username2 = user_personal_info.id AND username1 = $this->uid)) AND status ='Y') as temp WHERE $searchCombination";
                if ($result = $mysql->query($sql)) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $row['firstname'] = $this->toSentenceCase($row['firstname']);
                            $row['lastname'] = $this->toSentenceCase($row['lastname']);
                            $row['sql'] = $sql;
                            $user->setUserId($row['id']);
                            $pix = $user->getProfilePix();
                            if ($pix['status']) {
                                $row['photo'] = $pix['pix'];
                            } else {
                                $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                            }
                            $response['people'][] = $row;
                        }
                        $response['status'] = TRUE;
                    } 
                    $result->free();
                }
            }
        }
        $mysql->close();
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

}

?>
