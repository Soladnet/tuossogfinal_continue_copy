<?php

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
            $target = $uploadDir . $_FILES['user-file']['name'];
            if (in_array($ext, $fileTypes)) {
                if (@move_uploaded_file($tempFile, $target)) {
                   include_once '../Config.php';
                    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
                    $arrValues = array(); //to hold all uploaded values as a 2-dimensional array with a row representing a full user's record irrespective of the input status (valid or not valid)
                    $emails = array(); //will  hold all emails to be uploaded as a array
                    $f_emails = array(); //will hold all emails to be uploaded as a array with quotes suitable for query processing
                    $arrUnames = array(); // will hold array of usernames with emails as keys
                    $arrHeadings = array(1 => 'First_Name', 'Last_Name', 'Gender', 'DoB', 'Email'); //array of headings to be used as indexes during query preparations
                    $rejectedEmails = array();
                    $emailProblems = array();
                    $i = $m = $p = 2; // start index is 2 since we want to start reding from the second row of the excel file. The first row is just headings and is not needed. 
                    $countVal = 0;
                    $minimumRows = 1;

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
                        $result = array('status' => FALSE);
                        if (!strstr($date, '/')) {
                            $result['problem'] = 'Input date contains an invalid character';
                            return $result;
                        }
                        $date_data = explode('/', filter($date));
                        if (is_numeric($date_data[0]) && ($date_data[0] > 0 && ($date_data[0]) <=31) &&  is_numeric($date_data[1]) && ($date_data[1]) && ($date_data[1] <= 12) && ($date_data[1] > 0)  && is_numeric($date_data[2]) && ($date_data[2]) && ($date_data[2] >= 1960) && ($date_data[2] < 2000)) {
                            $result['status'] = TRUE;
                            return $result;
                        } else {
                            if (!is_numeric($date_data[0]) || !is_numeric($date_data[1]) || !is_numeric($date_data[2]))
                                $result['problem'] = 'Input date contains an invalid character';
                            elseif ($date_data[2] < 1960 || $date_data[2] > 2000) {
                                $result['problem'] = 'Input year not in acceptable range (1960 - 2000)';
                            } else {
                                $result['problem'] = 'Input date not in correct format';
                            }
                            return $result;
                        }
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
                        $date_data = explode('/', $date);
                        $last = $date_data[2];
                        $date_data[2] = $date_data[0];
                        $date_data[0] = $last;
                        return implode('-', $date_data);
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

                    function convertDate($date) {//date converstion form xlsx format into standard format
                        $UNIX_DATE = ($date - 25569) * 86400;
                        $dt = gmdate("m/d/Y", $UNIX_DATE);
                        $arrdt = explode('/', $dt);
                        $x = $arrdt[0];
                        $arrdt[0] = $arrdt[1];
                        $arrdt[1] = $x;
                        return implode('/', $arrdt);
                    }

                    //prepared statement allow for a single parsing of query in multuple use
                    $insert_string = "INSERT INTO `user_personal_info` (`firstname`, `lastname`, `email`, `username`, `gender`, `dob`, `phone`, `url`, `relationship_status`, `bio`, `favquote`, `location`, `likes`, `dislikes`, `works`, `agreement`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($ext === "xls") {
                        require_once 'Excel/reader.php';
                        $data = new Spreadsheet_Excel_Reader();
                        $data->setOutputEncoding('CP1251');
                        $data->read($target);
                        error_reporting(E_ALL ^ E_NOTICE);
                        //this loop takes the rows of the uploaded excel file one after other. It performs series of checks at every instance of operation. In the end,
                        //any rejected email for any reason is added to rejected email array and the reason for such rejection is included in another array: $emailProblems. Though thses two array may be merged into one
                        //$emailProblems takes an email as key and the problem string as value. Both the $emailProblems and $rejectedEmails arrays would be of use later for user's feedback.
                        for ($i; $i <= $data->sheets[0]['numRows']; $i++) {//start from the second row of the excel file
                            for ($k = 1; $k < $data->sheets[0]['numCols']; $k++) {//make the new array of values to be associative using $arrHeadings values as indexes
                                if (filter($data->sheets[0]['cells'][$i][1]) !== "") {
                                    $arrValues[filter($data->sheets[0]['cells'][$i][5])][$arrHeadings[$k]] = filter($data->sheets[0]['cells'][$i][$k]);
                                    $date = filter($data->sheets[0]['cells'][$i][4]);
                                    if ($k === 5) {//reduce overhead by making all checks @ once when you get to the email column
                                        $em = filter($data->sheets[0]['cells'][$i][5]);
                                        if (preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $em)) {//quick run of a regex validatory check for email
                                            $mailStatus = hasHost($em);
                                            if ($mailStatus['status']) {//email has a valid host, proceed to check the date
                                                $isDate = isDate($date);
                                                if ($isDate['status']) {//date is in acceptable format and figures
                                                    $emails[$em] = $em;
                                                    $f_emails[] = "'" . $em . "'";
                                                } else {//date is not in acceptable format and figures
                                                    unset($arrValues[filter($data->sheets[0]['cells'][$i][5])][$arrHeadings[$k]]);
                                                    $rejectedEmails[] = $em;
                                                    $emailProblems[$em] = $isDate['problem'];
                                                }
                                            } else {//email does not have a verifiable host
                                                $rejectedEmails[] = $mailStatus['email'];
                                                $emailProblems[$em] = $mailStatus['problem'];
                                            }
                                        } else {//email is not in valid email format so add it to blacklist before next logic
                                            $rejectedEmails[] = $em;
                                            $emailProblems[$em] = 'Input email is not valid';
                                        }
                                    }
                                }
                            }
                            if (filter($data->sheets[0]['cells'][$i][1]) !== "" && filter($data->sheets[0]['cells'][$i][2]) !== "" && filter($data->sheets[0]['cells'][$i][3]) !== "" && filter($data->sheets[0]['cells'][$i][4]) !== "" && filter($data->sheets[0]['cells'][$i][5]) !== "")
                                $countVal++;
                        }
                    } else {//file is in xlsx format (latest excel format).Same logic as above goes below but for .xlsx formats
                        require_once "simplexlsx.class.php";
                        $xlsx = new SimpleXLSX($target);
                        $countVal = count($xlsx->rows());
                        foreach ($xlsx->rows() as $k => $r) {//start from the second row of the excel file
                            if ($k == 0)
                                continue; // skip first row
                            $arr = $r;
                            $arrValues[filter($arr[4])][$arrHeadings[1]] = $arr[0];
                            $arrValues[filter($arr[4])][$arrHeadings[2]] = $arr[1];
                            $arrValues[filter($arr[4])][$arrHeadings[3]] = $arr[2];
                            $arrValues[filter($arr[4])][$arrHeadings[4]] = convertDate($arr[3]);
                            $arrValues[filter($arr[4])][$arrHeadings[5]] = $arr[4];
                            $em = $arr[4];
                            if (preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $em)) {//quick run of a regex validatory check for email
                                $mailStatus = hasHost($em);
                                if ($mailStatus['status']) {//email has a valid host, proceed to check the date
                                    $date = convertDate($arr[3]);
                                    $isDate = isDate($date);
                                    if ($isDate['status']) {//date is in acceptable format and figures
                                        $emails[$em] = $em;
                                        $f_emails[] = "'" . $em . "'";
                                    } else {//date is not in acceptable format and figures
//                                    unset($arrValues[$em]);
                                        $rejectedEmails[] = $em;
                                        $emailProblems[$em] = $isDate['problem'];
                                    }
                                } else {//email does not have a verifiable host
                                    $rejectedEmails[] = $mailStatus['email'];
                                    $emailProblems[$em] = $mailStatus['problem'];
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
                    $UploadInforQ = "INSERT INTO `bulk_registration` (`filename`, `commId`,`report`) VALUES (?, ?,?)";

                    $logInfoSql = "INSERT INTO `user_login_details`(`id`, `password`, `token`) VALUES (? , ?, ?)";
                    $lastId = 0;
                    $filename = $_FILES['user-file']['name'];
                    $cId = explode('-', $_POST['upComId']);
                    $commId = end($cId);
                    $report = $commId . time();
                    $successUserQ = "INSERT INTO `success_uploaded_users` (`uploadId`, `userId`) VALUES (?, ?)";
                    $arrSuccess = array();
                    $arrIds = array();
                    $stmtUpldInfo = $pdo->prepare($UploadInforQ);
                    $runUploadInfo = $stmtUpldInfo->execute(array("$filename", "$commId", "$report"));
                    if ($runUploadInfo){
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
                                        if ($ext === 'xls') {
                                            $date1 = new DateTime(formatDate($arrValues[$j]['DoB']));
                                            $date1->sub(new DateInterval('P1D'));
                                            $arrValues[$j]['DoB'] = $date1->format('Y-m-d');
                                            $arrv = array("" . $arrValues[$j]['First_Name'] . "", "" . $arrValues[$j]['Last_Name'] . "", "" . $arrValues[$j]['Email'] . "", "" . $arrUnames[$arrValues[$j]['Email']] . "", "" . $arrValues[$j]['Gender'] . "", "" . $arrValues[$j]['DoB'] . "", "", "", "", "", "", "", "", "", "", "");
                                        } else{
                                            $arrv = array("" . $arrValues[$j]['First_Name'] . "", "" . $arrValues[$j]['Last_Name'] . "", "" . $arrValues[$j]['Email'] . "", "" . $arrUnames[$arrValues[$j]['Email']] . "", "" . $arrValues[$j]['Gender'] . "", "" . formatDate($arrValues[$j]['DoB']) . "", "", "", "", "", "", "", "", "", "", "");
                                        }try {
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
    if (empty($errors) && $registered > 0){
        $countVal = count($arrValues);
        $rejected = count($emailProblems);
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
            $data__table .= "<div style='width:100%;border-bottom:1px red solid;margin-bottom:5px;color:red;font-weight:bold;' align='left'>Unregistered Records</div><table width='960px' border='1' cellspacing='0'><tr><th colspan='2' class='bold'>Email</th><th colspan='2' class='bold'>Fullname</th><th colspan='2' class='bold'>Status</th></tr>";
            foreach ($emailProblems as $key => $v) {
                $data__table .= "<tr><th colspan='2' style='font-weight:normal'>" . $key . "</th>";
                $data__table .= "<th colspan='2' style='font-weight:normal'>" . $arrValues[$key]['First_Name'] . ", " . $arrValues[$key]['Last_Name'] . "</th>";
                $data__table .= "<th colspan='2' style='font-weight:normal;'>" . $emailProblems[$key] . "</th></tr>";
            }
            $data__table.="</table>";
        }
        $data__table.="</div></center>";
        $arrRes['data'] = $data__table;
        $arrRes['status'] = TRUE;
        $arrRes['count'] = $countVal;
        $arrRes['report'] = $report;
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