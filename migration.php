<?php
//define("HOSTNAME", "localhost");
//define("USERNAME", "root");
//define("PASSWORD", "");
//define("OLD_DATABASE_NAME", "gossout");
//define("NEW_DATABASE_NAME", "gossoutdb");
//
//function clean($value) {
//    // If magic quotes not turned on add slashes.
//    if (!get_magic_quotes_gpc()) {
//        // Adds the slashes.
//        $value = addslashes($value);
//    }
//    // Strip any tags from the value.
//    $value = strip_tags($value);
//    // Return the value out of the function.
//    return $value;
//}
//
//function generateUserPersonalInfo($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT id,firstname,lastname,email,gender,dob,`dateJoined`,bio,favquote,location,likes,dislikes,works FROM " . OLD_DATABASE_NAME . ".user_personal_info";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if ($arr != "") {
//                        $arr .=",";
//                    }
//                    $usernameTemp = explode('@', $row['email']);
//                    $username = FALSE;
//                    $count = 0;
//                    do {
//                        if ($count > 0) {
//                            $username = prepareUsername(str_replace(".", "", $usernameTemp[0]) . $count, $arr);
//                        } else {
//                            $username = prepareUsername(str_replace(".", "", $usernameTemp[0]), $arr);
//                        }
//                        $count++;
//                    } while (!$username);
//                    $arr .= "('" . clean($row['id']) . "','$username','" . clean($row['firstname']) . "','" . clean($row['lastname']) . "','" . clean($row['email']) . "','" . clean($row['gender']) . "','" . clean($row['dob']) . "','" . clean($row['dateJoined']) . "','" . clean($row['bio']) . "','" . clean($row['favquote']) . "','" . clean($row['location']) . "','" . clean($row['likes']) . "','" . clean($row['dislikes']) . "','" . clean($row['works']) . "')";
//                }
//            }
//        }
//    }
//    return "INSERT INTO $newTableName(id,username,firstname,lastname,email,gender,dob,`dateJoined`,bio,favquote,location,likes,dislikes,works) VALUES $arr;";
//}
//
//function generateUserLoginDetails($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT id,password,activated,token FROM " . OLD_DATABASE_NAME . ".user_login_details";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if ($arr != "") {
//                        $arr .=",";
//                    }
//                    $arr .= "('" . clean($row['id']) . "','" . clean($row['password']) . "','" . clean($row['activated']) . "','" . clean($row['token']) . "')";
//                }
//            }
//        }
//    }
//    return "INSERT INTO " . NEW_DATABASE_NAME . ".$newTableName(id,password,activated,token) VALUES $arr;";
//}
//
//function generateUserTimeUpdate($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT id,user_id,gossbag FROM " . OLD_DATABASE_NAME . ".user_time_update";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if ($arr != "") {
//                        $arr .=",";
//                    }
//                    $arr .= "('" . clean($row['id']) . "','" . clean($row['user_id']) . "','" . clean($row['gossbag']) . "')";
//                }
//            }
//        }
//    }
//    return "INSERT INTO " . NEW_DATABASE_NAME . ".$newTableName(id,user_id,lastupdate) VALUES $arr;";
//}
//
//function generateUsercontacts($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT id,username1,username2,sender_id,status,`time` FROM gossout.usercontacts";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if ($arr != "") {
//                        $arr .=",";
//                    }
//                    $arr .= "('" . clean($row['id']) . "','" . clean($row['username1']) . "','" . clean($row['username2']) . "','" . clean($row['sender_id']) . "','" . clean($row['status']) . "','" . clean($row['time']) . "')";
//                }
//            }
//        }
//    }
//    return "INSERT INTO " . NEW_DATABASE_NAME . ".$newTableName(id,username1,username2,sender_id,status,`time`) VALUES $arr;";
//}
//
//function generateCommunity($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT id,`name`,description,datecreated FROM gossout.community";
//        $i = 0;
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    $i++;
//                    if ($arr != "") {
//                        $arr .=",";
//                    }
//                    $helve = "helve$i";
//                    $arr .= "('" . clean($row['id']) . "','$helve','images/no-pic.png','images/no-pic.png','images/no-pic.png','" . clean($row['name']) . "','" . clean($row['description']) . "','" . clean($row['datecreated']) . "',47)";
//                }
//            }
//        }
//    }
//    //helve not fixed
//    return "INSERT INTO $newTableName(id,unique_name,pix,`thumbnail100`,`thumbnail150`,`name`,description,datecreated,creator_id) VALUES $arr;";
//}
//
//function generateCommunitySubscribers($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT `user`,community_id,`emailNotif`,datejoined FROM gossout.community_subscribers";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if ($arr != "") {
//                        $arr .=",";
//                    }
//                    $arr .= "('" . clean($row['user']) . "','" . clean($row['community_id']) . "','" . clean($row['emailNotif']) . "','" . clean($row['datejoined']) . "',0)";
//                }
//            }
//        }
//    }
//    //helve not fixed
//    return "INSERT INTO " . NEW_DATABASE_NAME . ".$newTableName(`user`,community_id,`emailNotif`,datejoined,leave_status) VALUES $arr;";
//}
//
//function generatePosts($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT id,post,community_id,sender_id,`time`,status FROM gossout.post";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if (!strstr($row['post'], '<div class="post">')) {
//                        if ($arr != "") {
//                            $arr .=",";
//                        }
//                        $arr .= "('" . clean($row['id']) . "','" . clean($row['post']) . "','" . clean($row['community_id']) . "','" . clean($row['sender_id']) . "','" . clean($row['time']) . "','" . clean($row['status']) . "')";
//                    }
//                }
//            }
//        }
//    }
//    //helve not fixed
//    return "INSERT INTO " . NEW_DATABASE_NAME . ".$newTableName(id,post,community_id,sender_id,`time`,status) VALUES $arr;";
//}
//
//function generatePostsImages($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT community_id,user_id,post_id,original,`100x100`,`date` FROM gossout.community_pix";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if ($arr != "") {
//                        $arr .=",";
//                    }
//                    $arr .= "('" . ($row['post_id']) . "','" . ($row['community_id']) . "','" . ($row['user_id']) . "','" . ($row['original']) . "','" . ($row['100x100']) . "','" . ($row['date']) . "')";
//                }
//            }
//        }
//    }
//    //helve not fixed
//    return "INSERT INTO " . NEW_DATABASE_NAME . ".$newTableName(post_id,community_id,sender_id,original,thumbnail100,`time`) VALUES $arr;";
//}
//
//function getExceptionalPost() {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = array();
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT id,post,community_id,sender_id,`time`,status FROM gossout.post";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if (strstr($row['post'], '<div class="post">')) {
//                        $arr[] = $row['id'];
//                    }
//                }
//            }
//        }
//    }
//    //helve not fixed
//    return $arr;
//}
//
//function generateComment($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $post = getExceptionalPost();
//        $sql = "SELECT comment,post_id,sender_id,`time` FROM gossout.comments";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if (!in_array($row['post_id'], $post)) {
//                        if ($arr != "") {
//                            $arr .=",";
//                        }
//                        $arr .= "('" . clean($row['comment']) . "','" . ($row['post_id']) . "','" . ($row['sender_id']) . "','" . ($row['time']) . "')";
//                    }
//                }
//            }
//        }
//    }
//    //helve not fixed
//    return "INSERT INTO " . NEW_DATABASE_NAME . ".$newTableName(comment,post_id,sender_id,`time`) VALUES $arr;";
//}
//
//function generatePictureUploads($newTableName) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, OLD_DATABASE_NAME);
//    $arr = "";
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT user_id,`35x35`,`50x50`,`100x100`,original,date_added FROM gossout.pictureuploads";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                while ($row = $result->fetch_assoc()) {
//                    if ($arr != "") {
//                        $arr .=",";
//                    }
//                    $arr .= "('" . clean($row['user_id']) . "','" . ($row['original']) . "','" . ($row['35x35']) . "','" . ($row['50x50']) . "','" . ($row['100x100']) . "','" . ($row['100x100']) . "','" . ($row['date_added']) . "')";
//                }
//            }
//        }
//    }
//    //helve not fixed
//    return "INSERT INTO $newTableName(`user_id`, `original`, `thumbnail45`, `thumbnail50`, `thumbnail75`, `thumbnail150`, `date_added`) VALUES $arr;";
//}
//
//function executeQuerry($sql) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, NEW_DATABASE_NAME);
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        if ($mysql->query($sql)) {
//            echo TRUE;
//        } else {
//            echo $mysql->error;
//        }
//    }
//}
//
//function prepareUsername($email, $str) {
//    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, NEW_DATABASE_NAME);
//    if ($mysql->connect_errno > 0) {
//        throw new Exception("Connection to server failed!");
//    } else {
//        $sql = "SELECT * FROM user_personal_info WHERE username='$email'";
//        if ($result = $mysql->query($sql)) {
//            if ($result->num_rows > 0) {
//                $result->free();
//                $mysql->close();
//                return FALSE;
//            } else {
//                $mysql->close();
//                if (strstr($str, "'$email'")) {
//                    return FALSE;
//                } else {
//                    return $email;
//                }
//            }
//        }
//    }
//}
//
//echo executeQuerry(generatePictureUploads("pictureuploads"));
////echo generateUserPersonalInfo("user_personal_info");
////echo generateUserLoginDetails("user_login_details");
////echo generateUserTimeUpdate("user_time_update");
////echo generateUsercontacts("usercontacts");
////echo generateCommunity("community");
////echo generateCommunitySubscribers("community_subscribers");
////echo generatePosts("post");
////echo generatePostsImages("post_image");
////echo generateComment("comments");
//echo generatePictureUploads("pictureuploads");
?>
