<?php

include_once './Config.php';
include_once './encryptionClass.php';
include_once './GossoutUser.php';

/**
 * Description of Post
 *
 * @author user
 */
class Post {

    var $uid, $comId, $postId, $tz, $start = 0, $limit = 5;

    public function __construct() {
        
    }

    public function countUserPosts() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT count(`id`) as count FROM `post` WHERE `sender_id` = $this->uid AND `deleteStatus`=0";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $arrFetch['post_count'] = $row;

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

    public function post($values) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO post(post,community_id,sender_id) VALUES $values";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arrFetch['post']['id'][] = $mysql->insert_id;
                    $result = $mysql->query("SELECT NOW() as time");
                    $row = $result->fetch_assoc();
                    $result->free();
                    $arrFetch['post']['time'] = $this->convert_time_zone($row['time'], $this->tz);
                } else {
                    $arrFetch['post']['id'] = 0;
                }
                $arrFetch['status'] = TRUE;
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arrFetch;
    }

    public function postImage($values) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO post_image(post_id,community_id,sender_id,original,thumbnail100) VALUES $values";
            if ($mysql->query($sql)) {
                $arrFetch['status'] = TRUE;
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arrFetch;
    }

    public function loadPost() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $encrypt = new Encryption();
            $sql = "SELECT p.`id`, p.`post`, p.`sender_id`,u.username,u.firstname,u.lastname, p.`time`, p.`status` FROM `post` as p JOIN user_personal_info as u ON p.sender_id=u.id WHERE p.`community_id`=$this->comId  AND p.deleteStatus=0 order by p.`id` desc LIMIT $this->start,$this->limit";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $comCount = $this->getCommentCountFor($row['id']);
                        if ($comCount['status']) {
                            $row['numComnt'] = $comCount['count'];
                        } else {
                            $row['numComnt'] = 0;
                        }
                        $user = new GossoutUser($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $post_image = $this->loadPostImage($row['id']);
                        if ($post_image['status']) {
                            $row['post_photo'] = $post_image['photo'];
                        }
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $row['isLike'] = $this->isLike($row['id'], $this->uid);
                        $row['likeCount'] = $this->getLikeCount($row['id']);
                        $row['sender_id'] = $encrypt->safe_b64encode($row['sender_id']);



                        $arrFetch['post'][] = $row;
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

    public function getLikeCount($post_id) {
        $likeCount = 0;
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT `time` FROM `like` WHERE `post_id` = '$post_id'";
            if ($result = $mysql->query($sql)) {
                $likeCount = $result->num_rows;
//                     $likeCount = $row['likeCount'];
            } else {
                
            }
        }
        $mysql->close();
        return $likeCount;
    }

    public function manageLikePost($action, $post_id, $uid) {
        $arr = array();
        $status = false;
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            if ($action == 'Like')
                $sql = "INSERT INTO `like` (`post_id`,`user_id`) VALUES ('$post_id', '$uid')";
            if ($action == 'Unlike')
                $sql = "Delete From `like` WHERE `post_id` = '$post_id' AND user_id = '$uid'";
            if ($result = $mysql->query($sql)) {
                ($mysql->affected_rows != 0) ? $status = true : '';
            } else {
//                $arr['sql'] = $sql;
            }
        }
        $mysql->close();
        $arr['status'] = $status;
        $arr['action'] = $action;
//        $arr['sql'] = $sql;
        $arr['countLike'] = $this->getLikeCount($post_id);
        return $arr;
    }

    public function isLike($post_id, $user_id) {
        $isLike = false;
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT * FROM `like` WHERE `post_id` =  '$post_id' AND user_id = '$user_id'";
            if ($result = $mysql->query($sql)) {
                $num = $result->num_rows;
                ($num == 1) ? $isLike = true : '';
//                $isLike = $sql;
            }
        }
        $mysql->close();
        return $isLike;
    }

    public function loadPostImage($postId) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT original,thumbnail100 as thumbnail FROM `post_image` WHERE `post_id` =$postId";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $arrFetch['photo'][] = $row;
                    }
                    $arrFetch['status'] = TRUE;
                } else {
                    $arrFetch['status'] = FALSE;
                }
                $result->free();
            }
        }
        $mysql->close();
        return $arrFetch;
    }

    public function getCommentCountFor($postId) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "SELECT count(`id`) as count FROM `comments` WHERE `post_id` =$postId AND `deleteStatus`=0";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $arrFetch['count'] = $row['count'];
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

    public function loadComment($postId) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        //$count = 0;
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $encrypt = new Encryption();
            $sql = "SELECT c.`id`, c.`comment`, c.`post_id`, c.`sender_id`,u.username,u.firstname,u.lastname, c.`time`, c.`status` FROM `comments` as c JOIN user_personal_info as u ON c.`sender_id`=u.id WHERE c.`post_id`=$postId AND c.`deleteStatus`=0 order by c.id ASC";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $row['comment'] = nl2br($row['comment']);
                        $user = new GossoutUser($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $row['sender_id'] = $encrypt->safe_b64encode($row['sender_id']);
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['comment'][] = $row;
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

    public function comment($pid, $uid, $comment) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "INSERT INTO comments(comment,post_id,sender_id) VALUES('$comment','$pid','$uid')";
            if ($mysql->query($sql)) {
                if ($mysql->affected_rows > 0) {
                    $arrFetch['comment']['id'] = $mysql->insert_id;
                    $user = new GossoutUser($uid);
                    $user->getProfile();
                    $arrFetch['comment']['name'] = $user->getFullname();
                    $pix = $user->getProfilePix();
                    if ($pix['status']) {
                        $arrFetch['comment']['photo'] = $pix['pix'];
                    } else {
                        $arrFetch['comment']['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                    }
                    $result = $mysql->query("SELECT NOW() as time");
                    $row = $result->fetch_assoc();
                    $result->free();
                    $arrFetch['comment']['time'] = $this->convert_time_zone($row['time'], $this->tz);
                } else {
                    $arrFetch['comment']['id'] = 0;
                }
                $arrFetch['status'] = TRUE;
            } else {
                $arrFetch['status'] = FALSE;
            }
        }
        $mysql->close();
        return $arrFetch;
    }

    public function getUserId() {
        return $this->uid;
    }

    public function getCommunityId() {
        $this->comId;
    }

    public function setUserId($newUid) {
        $this->uid = $newUid;
    }

    public function setCommunity($newComId) {
        $this->comId = $newComId;
    }

    public function setPostId($newPost) {
        $this->postId = $newPost;
    }

    public function setStart($newStart) {
        $this->start = $newStart;
    }

    public function setLimit($newLimit) {
        $this->limit = $newLimit;
    }

    public function setTimezone($newTimeZone) {
        $this->tz = $newTimeZone;
    }

    public function deletePost() {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE post SET `deleteStatus`=1 WHERE id=$this->postId AND sender_id=$this->uid";
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
        $mysql->close();
        return $arrFetch;
    }

    public function deleteComment($cid) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $sql = "UPDATE comments SET `deleteStatus`=1 WHERE id=$cid AND sender_id=$this->uid";
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
        $mysql->close();
        return $arrFetch;
    }

    public function searchPost($term) {
        $arrFetch = array();
        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
        if ($mysql->connect_errno > 0) {
            throw new Exception("Connection to server failed!");
        } else {
            $encrypt = new Encryption();
            $sql = "SELECT p.id,p.post,p.`time`,p.status,p.sender_id,u.username,u.firstname,u.lastname FROM post as p JOIN user_personal_info as u ON p.sender_id=u.id JOIN community as c ON p.community_id=c.id WHERE p.post LIKE '%$term%' AND c.`type`='Public' AND p.`deleteStatus`=0 LIMIT $this->start,$this->limit";
            if ($result = $mysql->query($sql)) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $row['firstname'] = $this->toSentenceCase($row['firstname']);
                        $row['lastname'] = $this->toSentenceCase($row['lastname']);
                        $user = new GossoutUser($row['sender_id']);
                        $pix = $user->getProfilePix();
                        if ($pix['status']) {
                            $row['photo'] = $pix['pix'];
                        } else {
                            $row['photo'] = array("nophoto" => TRUE, "alt" => $pix['alt']);
                        }
                        $comCount = $this->getCommentCountFor($row['id']);
                        if ($comCount['status']) {
                            $row['numComnt'] = $comCount['count'];
                        } else {
                            $row['numComnt'] = 0;
                        }
                        $post_image = $this->loadPostImage($row['id']);
                        if ($post_image['status']) {
                            $row['post_photo'] = $post_image['photo'];
                        }
                        $row['sender_id'] = $encrypt->safe_b64encode($row['sender_id']);
                        $row['time'] = $this->convert_time_zone($row['time'], $this->tz);
                        $arrFetch['post'][] = $row;
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

}

?>
