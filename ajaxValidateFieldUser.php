<?php

include_once './Config.php';

/* RECEIVE VALUE */
$validateValue = $_REQUEST['fieldValue'];
$validateId = $_REQUEST['fieldId'];

/* RETURN VALUE */
$arrayToJs = array();
$arrayToJs[] = $validateId;
if (isValidEmail($validateValue)) {
    $mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
    if ($mysql->connect_errno > 0) {
        $arrayToJs[] = TRUE;
        echo json_encode($arrayToJs);
    } else {
        $sql = "SELECT * FROM `user_personal_info` WHERE email = '" . clean($validateValue) . "'";
        if ($result = $mysql->query($sql)) {
            if ($result->num_rows > 0) {
                $arrayToJs[] = FALSE;
                echo json_encode($arrayToJs);
            } else {
                $arrayToJs[] = TRUE;
                echo json_encode($arrayToJs);
            }
        }
    }
} else {
    $arrayToJs[] = false;
    $arrayToJs['msg'] = "* Email host does not exist";
    echo json_encode($arrayToJs);
}

function isValidEmail($email) {
    //Perform a basic syntax-Check
    //If this check fails, there's no need to continue
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    //extract host
    list($user, $host) = explode("@", $email);
    //check, if host is accessible
    if (!checkdnsrr($host, "MX") && !checkdnsrr($host, "A")) {
        return false;
    }

    return true;
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