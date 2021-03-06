<?php

function decodeText($param) {
    include_once '../encryptionClass.php';
    $encrypt = new Encryption();
    return $encrypt->safe_b64decode($param);
}
print_r($_POST);
print_r($_FILES);

exit;
if (session_id() == "")
    @session_start();
if (isset($_POST['us'])) {
    ini_set('max_execution_time', 600); //300 seconds = 5 minutes
    $uploadDir = 'users/';
    $arrRes = array('status' => FALSE);
    $errors = array();
    if (isset($_POST['us'])) {
        $fileTypes = array('xls', 'xlsx'); // Allowed file extensions
        if (!empty($_FILES)) {
            $arrRes['file'] = $_FILES;
            $tempFile = $_FILES['user-file']['tmp_name'];
            list($namepart, $ext) = explode(".", $_FILES['user-file']['name']);
            $ext = strtolower($ext);
            $cId = explode('-', $_POST['upComId']);
            $commId = end($cId);
            $cAdmin = explode('-', $_POST['commAdmin']);
            $commAdmin = decodeText(end($cAdmin));
            $target = $uploadDir . $commId . time() . $ext;
            if (in_array($ext, $fileTypes)) {
                if (@move_uploaded_file($tempFile, $target)) {
                    include_once '../Config.php';
                    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
                    $arrValues = array(); //to hold all uploaded values as a 2-dimensional array with a row representing a full user's record irrespective of the input status (valid or not valid)
                    $emails = array(); //will  hold all emails to be uploaded as a array
                    $f_emails = array(); //will hold all emails to be uploaded as a array with quotes suitable for query processing
                    $arrUnames = array(); // will hold array of usernames with emails as keys
                    $arrHeadings = array(1 => 'First_Name', 'Last_Name', 'Gender', 'DoB', 'Email'); //array of headings to be used as indexes during query preparations
                    $arrKeys = array(1 => 'A', 'B', 'C', 'D', 'E');
                    $rejectedEmails = array();
                    $emailProblems = array();
                    $i = $m = $p = 2; // start index is 2 since we want to start reding from the second row of the excel file. The first row is just headings and is not needed. 
                    $countVal = 0;
                    $minimumRows = 1;
                    $uploadId = 0;

                    /**
                      function definitions begins here
                     */
                    function filter($data) {
                        $data = trim(htmlentities(strip_tags($data)));
                        if (get_magic_quotes_runtime())
                            $data = stripslashes($data);
                        $data = mysql_real_escape_string($data);
                        return $data;
                    }

                    function hasHost($e) {
                        $result = array('status' => FALSE);
                        list($namepart, $domain) = explode('@', $e);
                        if (!checkdnsrr($domain, "MX")) {
                            $result['email'] = $e;
                            $result['problem'] = "Email host could not be verified";
                        } else {
                            $result['status'] = TRUE;
                        }
                        return $result;
                    }

                    function isDate($date) {//parameter are $date and the corresponding email corresponding date to be checked
                        if (!strstr($date, '-')) {
                            return false;
                        }
                        $vals = explode('-', $date);
                        foreach ($vals as $v) {
                            if (strlen($v) > 2)
                                return false;
                        }
                        if ($vals[0] == 0 || $vals[0] > 12 || $vals[0] < 0 || $vals[1] == 0 || $vals[1] > 31 || $vals[1] < 0)
                            return false;
                        return true;
                    }

                    function genPass($length = 8) {
                        $password = "";
                        $possible = "0bc1df2gh3jk4mn5pqrs5tvwx78yz9"; //no vowels
                        $i = 0;
                        while ($i < $length) {
                            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
                            if (!strstr($password, $char)) {
                                $password .= $char;
                                $i++;
                            }
                        }
                        return $password;
                    }

                    function formatDate($date) {//formats date into acceptable format by the db
                        if (strstr($date, '-')) {
                            $date_data = explode('-', $date);
                            $year = $date_data[2];
                            $month = $date_data[0];
                            $date_data[2] = $date_data[1];
                            $date_data[0] = $year; // :  ($date_data[0] <= 13 && $date_data[0] >= 0) ? $date_data[0] = "20" . $date_data[0] : $date_data[0] = "19" . $date_data[0];
                            if ($date_data[0] >= 60)
                                $date_data[0] = '19' . $date_data[0];
                            if ($date_data[0] < 60 && $date_data[0] >= 13)
                                $date_data[0] = '19' . $date_data[0];
                            if ($date_data[0] <= 13 && $date_data[0] >= 0)
                                $date_data[0] = '20' . $date_data[0];
                            $date_data[1] = $month;
                            return implode('-', $date_data);
                        } else {
                            return $date;
                        }
                    }

                    function prepareUsername($uname) {
                        $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
                        if ($mysql->connect_errno > 0) {
                            throw new Exception("Connection to server failed!");
                        } else {
                            $sql = "SELECT * FROM user_personal_info WHERE username='$uname'";
                            if ($result = $mysql->query($sql)) {
                                if ($result->num_rows > 0) {
                                    $result->free();
                                    $mysql->close();
                                    return FALSE;
                                } else {
                                    $mysql->close();
                                    return $uname;
                                }
                            }
                        }
                    }

                    //prepared statement allow for a single parsing of query in multuple use
                    $insert_string = "INSERT INTO `user_personal_info` (`firstname`, `lastname`, `email`, `username`, `gender`, `dob`, `phone`, `url`, `relationship_status`, `bio`, `favquote`, `location`, `likes`, `dislikes`, `works`, `agreement`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($ext === "xls" || $ext === "xlsx") {
                        include 'PHPExcel/IOFactory.php';
                        $inputFileName = $target;
                        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
                        $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);
                        $arr = array();
                        $countVal = count($sheetData) - 1;
                        for ($i; $i <= count($sheetData); $i++) {//start from the second row of the excel file
                            $em = filter($sheetData[$i]['E']);
                            if (preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $em)) {//quick run of a regex validatory check for email
                                $mailStatus = hasHost($em);
                                if (!empty($sheetData[$i]['A']) && !empty($sheetData[$i]['B'])) {
                                    if ($mailStatus['status']) {
                                        if (strlen($sheetData[$i]['D']) === 8 && isdate($sheetData[$i]['D'])) {
                                            try {
                                                $date1 = new DateTime(formatDate($sheetData[$i]['D']));
                                                $date2 = new DateTime("now");
                                                $interval = $date1->diff($date2);
                                                $years = $interval->format('%y');
                                                if ($years >= 13) {
                                                    for ($k = 1; $k <= 5; $k++) {
                                                        if ($k == 4)
                                                            $arr[$arrHeadings[$k]] = formatDate($sheetData[$i][$arrKeys[$k]]);
                                                        else
                                                            $arr[$arrHeadings[$k]] = filter($sheetData[$i][$arrKeys[$k]]);
                                                    }
                                                    $arrValues[filter($sheetData[$i]['E'])] = $arr;
                                                    $emails[$em] = $em;
                                                    $f_emails[] = "'" . $em . "'";
                                                }else {
                                                    $rejectedEmails[] = $em;
                                                    $emailProblems[$em] = 'Input date not in acceptable format/figure';
                                                }
                                            } catch (Exception $exc) {
                                                $rejectedEmails[] = $em;
                                                $emailProblems[$em] = 'Input date not in acceptable format/figure';
                                            }
                                        } else {
                                            $rejectedEmails[] = $em;
                                            $emailProblems[$em] = 'Input date not in acceptable format/figure';
                                        }
                                    } else {//email does not have a verifiable host
                                        $rejectedEmails[] = $mailStatus['email'];
                                        $emailProblems[$em] = $mailStatus['problem'];
                                    }
                                } else {
                                    $rejectedEmails[] = $em;
                                    $emailProblems[$em] = 'Empty name column founnd';
                                }
                            } else {//email is not in valid email format so add it to blacklist before next logic
                                $rejectedEmails[] = $em;
                                $emailProblems[$em] = 'Input email is not valid';
                            }
                        }
                    }
                    //remember that an upload record exist in the db on when number valid rows is greater than set value.
                    //$email_str is a string version of all emails to register. We check for existing emails here
                    $p = "mysql:dbname=gossoutdb;host=localhost";
                    $pdo = new PDO($p, "root", "");
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $pdo->beginTransaction();
                    $UploadInforQ = "INSERT INTO `bulk_registration` (`filename`, `commId`,`report`,`adminId`) VALUES (?, ?,?,?)";

                    $logInfoSql = "INSERT INTO `user_login_details`(`id`, `password`, `token`) VALUES (? , ?, ?)";
                    $lastId = 0;

                    $report = $commId . '_' . time();
                    $filename = $report . '.' . $ext;
                    $successUserQ = "INSERT INTO `success_uploaded_users` (`uploadId`, `userId`) VALUES (?, ?)";
                    $arrSuccess = array();
                    $arrIds = array();
                    $stmtUpldInfo = $pdo->prepare($UploadInforQ);
                    $runUploadInfo = $stmtUpldInfo->execute(array("$filename", "$commId", "$report", "$commAdmin"));
                    if ($runUploadInfo) {
                        if ($countVal >= $minimumRows) {
                            $emails_str = implode(",", $f_emails);
                            if ($mysql->connect_errno > 0) {
                                throw new Exception("Connection to server failed!");
                            } else {
                                $sql = "Select email From user_personal_info Where email IN ($emails_str)";
                                if ($result = $mysql->query($sql)) {
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {//using a single loop without foreach creates an embedded array. Use foreach to make the resulting array  a 1-dimensional array
                                            foreach ($row AS $value) {
                                                $rejectedEmails[] = $value;
                                                unset($emails[$value]); //should there exist any email that passed the above test but allready existing, delete such an email 
                                                $emailProblems[$value] = 'Email already registered';
                                            }
                                        }
                                    }
                                }
                            }
                            foreach ($emails as $value) {
                                $usernameTemp = explode('@', $value);
                                $username = FALSE;
                                $count = 0;
                                do {
                                    if ($count > 0) {
                                        $username = prepareUsername(str_replace(".", "", $usernameTemp[0]) . $count);
                                    } else {
                                        $username = prepareUsername(str_replace(".", "", $usernameTemp[0]));
                                    }
                                    $count++;
                                } while (!$username);
                                $arrUnames[$value] = $username;
                            }
                            //insert upload info first to provide for upload id to be used in subseguent queries

                            $uploadId = $pdo->lastInsertId();
                            foreach ($arrValues as $email => $e) {
                                if (!in_array($email, $rejectedEmails)) {
                                    $j = $email; //key email of each array element (which is also an array)
                                    $g = strtolower($arrValues[$j]['Gender']);
                                    $arrSex = array('f', 'm');
                                    if (in_array($g, $arrSex)) {
                                        $stmt = $pdo->prepare($insert_string);
                                        $arrv = array("" . $arrValues[$j]['First_Name'] . "", "" . $arrValues[$j]['Last_Name'] . "", "" . $arrValues[$j]['Email'] . "", "" . $arrUnames[$arrValues[$j]['Email']] . "", "" . strtoupper($arrValues[$j]['Gender']) . "", "" . $arrValues[$j]['DoB'] . "", "", "", "", "", "", "", "", "", "", "");
                                        try {
                                            $runner = $stmt->execute($arrv);
                                            if ($runner) {
                                                $lastId = $pdo->lastInsertId();
                                                $passw = genPass(); //to be emailed to user
                                                $password = md5($passw);
                                                $token = md5(strtolower($arrValues[$j]['Email'] . $arrValues[$j]['Last_Name'] . $password));
                                                $stmtLogIn = $pdo->prepare($logInfoSql);
                                                try {
                                                    $runLogInfo = $stmtLogIn->execute(array("$lastId", "$password", "$token"));
                                                    if ($runLogInfo) {
                                                        try {
                                                            $stmtSucUpld = $pdo->prepare($successUserQ);
                                                            $runSucUpld = $stmtSucUpld->execute(array("$uploadId", "$lastId"));
                                                            if ($runSucUpld) {
                                                                $arrSuccess[$arrValues[$j]['Email']] = "Successfully registered";
                                                                $arrIds[$j] = $lastId;
                                                                //send email to the user here
                                                                if (FALSE) {
                                                                    $fullname = $arrValues[$j]['First_Name'] .', '. $arrValues[$j]['Last_Name'];
                                                                    $email = $j;
                                                                    $headers = "From: Email verification<no-reply-email-verification@gossout.com>\r\n";
                                                                    $headers .= "MIME-Version: 1.0\r\n";
                                                                    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                                                                    $to = "$fullname<$email>";
                                                                    $subject = "Email verification";
                                                                    $htmlHead = "<!doctype html><html><head><meta charset='utf-8'><style>a:hover{color: #000;}a:active , a:focus{color: green;}.index-functions:hover{/*cursor: pointer;*/ color: #99C43D !important;-webkit-box-shadow: inset 0px 0px 1px 1px #ddd;box-shadow: inset 0px 0px 1px 1px #ddd;}.index-functions:active{color: #C4953D !important;-webkit-box-shadow: inset 0px 0px 1px 2px #ddd;box-shadow: inset 0px 0px 1px 2px #ddd;}</style></head><body style='font-family: 'Segoe UI',sans-serif;background-color: #f9f9f9;color: #000000;line-height: 2em;'><div style='max-width: 800px;margin: 0 auto;background-color: #fff;border: 1px solid #f2f2f2;padding: 10px'><div class='header'><img style='float: right;top: 0px;' src='http://service.gossout.com/images/gossout-logo-text-and-image-Copy.png'/><br><h2>Email verification, </h2><p style='margin: 3px;'><span class='user-name'>Hi, <a style='color: #62a70f;text-decoration: none;'>$fullname</a></span>, Welcome to Gossout. As part of our measures to keep you safe, we request that you verify your account by clicking on the link below. <br><br> <a href='http://gossout.com/signup-agreement/$token'>Click here to verify your email</a></p><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'></div><div style='background-color: #fff;padding: 1em;'><p style='margin: 3px;font-size: .9em;'></p><p style='margin: 3px;font-size: .9em;'><a style='color: #62a70f;text-decoration: none;' href='http://gossout.com/signup-agreement/$token'></a></p><p>OR</p><p style='margin: 3px;font-size: .9em;'><strong>Copy the link below and paste into your browser address bar</strong></p><p style='margin: 3px;font-size: .9em;'>http://gossout.com/password-reset/$token</p></div><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'><div style='background-color: #f9f9f9;padding: 10px;font-size: .8em;'><center><div class='index-intro-2'><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/community-resize.png'/></span></div><h3 style='text-align: center;height: 1em;'>Discover</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Communities &Friends</p></div><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/connect-pple.png'/></span></div><h3 style='text-align: center;height: 1em;'>Connect</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Meet People, Share Interests</p></div><div style='display: block;display: inline-block;padding: 1em;max-width: 200px;' class='index-functions'><div style='margin: 0 auto;width: 24px;height:1em'><span style='margin-right: .15em;display: inline-block;width: 24px;height: 24px;'><img src='http://service.gossout.com/images/search.png'/></span></div><h3 style='text-align: center;height: 1em;'>Search</h3><p style='margin: 3px;color: #777;line-height: 1.5;margin-bottom: 1em;padding-top: 1em;font-size: .8em;padding-top: 0;'>Communities, People and Posts</p></div></div></center><hr style='margin: .3em 0;width: 100%;height: 1px;border-width:0;color: #ddd;background-color: #ddd;'><table cellspacing='5px'><tr ><td colspan='3'> ©<?php echo date('Y');?><a style='color: #62a70f;text-decoration: none;' href='http://www.gossout.com'>Gossout</a></td></tr></table></div></div></body></html>";
                                                                    @mail($to, $subject, $htmlHead, $headers);
                                                                }
                                                                //send email end
                                                                $pdo->commit();
                                                            } else {
                                                                $pdo->rollBack();
                                                            }
                                                        } catch (Exception $exc) {
                                                            $emailProblems[$j] = "System error or User already exist!";
                                                        }
                                                    } else {
                                                        $emailProblems[$j] = "System error or user already exist!";
                                                    }
                                                } catch (Exception $exc) {
                                                    $emailProblems[$j] = "System error or user already exist!";
                                                }
                                            } else {
                                                //user cannot be inserted
                                            }
                                        } catch (Exception $exc) {
                                            $emailProblems[$j] = "System error or user already exist!";
                                        }
                                    } else {
                                        $emailProblems[$j] = 'Input gender value not accepptable';
                                    }
                                }
                            }
                        } else {
                            $errors[] = "Valid rows less than acceptable minimum records ($minimumRows)";
                        }
                    } else {
                        $errors[] = 'General error!';
                    }
                } else {
                    $errors[] = ' File could not be moved';
                }
            } else {
                $errors[] = 'File not in acceptable format';
            }
        } else {
            $errors[] = 'No file selected';
        }
    } else {
        $errors[] = 'Submission not successful';
    }

    $registered = count($arrSuccess);
    if (empty($errors) && $registered > 0) {
//        $rejectedArr = array_diff($arrValues, $arrSuccess);
        $rejected = $countVal - $registered;
//        $data__table1 = "";
        $data__table = "<center><div style='width:960px;'><rabiusal><img src='images/gossout-logo-image-svg.png' height='62' align='left'/><rabiusal><center><div style='width:100%;border-bottom:1px #99c43d solid;'><h3 style='color:#99c43d;margin-bottom:-12px;'><strong>Success -Your information upload was successful!</strong></h3><br><h4>The statistics of your upload are given below:</h4></div></center>";
        $data__table.= "<p align='left'>Total records(rows) found: $countVal <br>Registered records: $registered <br>Unregistered records: $rejected</p><br>";
        if ($registered > 0) {
            $data__table.= "<div style='width:100%;border-bottom:1px #99c43d solid;margin-bottom:5px;font-weight:bold;color:#99c43d;' align='left'>Registered Records</div><table width='960px' border='1' cellspacing='0'><tr><th colspan='2' class='bold'>Email</th><th colspan='2' class='bold'>Fullname</th><th colspan='2' class='bold'>Username</th><th colspan='2' class='bold'>Status</th></tr>";
            foreach ($arrSuccess as $key => $v) {
                $data__table .= "<tr><th colspan='2' style='font-weight:normal'>" . $arrValues[$key]['Email'] . "</th>";
                $data__table .= "<th colspan='2' style='font-weight:normal'>" . $arrValues[$key]['First_Name'] . ", " . $arrValues[$key]['Last_Name'] . "</th>";
                $data__table .= "<th colspan='2' style='font-weight:normal'>" . $arrUnames[$key] . "</th>";
                $data__table .= "<th colspan='2' style='font-weight:normal;'>" . $arrSuccess[$key] . "</th></tr>";
                unset($emailProblems[$key]);
            }$data__table.="</table><br>";
        }

        if ($rejected > 0) {
            $data__table .= "<div style='width:100%;border-bottom:1px red solid;margin-bottom:5px;color:red;font-weight:bold;' align='left'>Unregistered Records</div><table width='960px' border='1' cellspacing='0'><tr><th colspan='2' class='bold'>Email</th><th colspan='2' class='bold'>Status</th></tr>";
            foreach ($emailProblems as $key => $v) {
                $data__table .= "<tr><th colspan='2' style='font-weight:normal'>" . $key . "</th>";
//                $data__table .= "<th colspan='2' style='font-weight:normal'>" . $arrValues[$key]['First_Name'] . ", " . $arrValues[$key]['Last_Name'] . "</th>";
                $data__table .= "<th colspan='2' style='font-weight:normal;'>" . $emailProblems[$key] . "</th></tr>";
            }
            $data__table.="</table>";
        }
        $data__table.="</div></center>";
        $arrRes['data'] = $data__table;
        $arrRes['status'] = TRUE;
        $arrRes['count'] = $countVal;
        $arrRes['uploadId'] = $uploadId;
        $arrRes['registered'] = $registered;
        file_put_contents("../bulkRegReport/$report.txt", $data__table);
    } else {
        if (empty($errors))
            $arrRes['Error'] = "No valid rows found";
        else
            $arrRes['Error'] = $errors[0];

        $arrRes['data'] = "";
        $arrRes['status'] = FALSE;
    }
    echo json_encode($arrRes);
} else {
    exit(0);
}
?>