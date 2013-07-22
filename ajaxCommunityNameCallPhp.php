<?php

include_once './Config.php';
$pageName = array("index", "home", "communities", "messages", "friends", "login", "login_exec", "settings", "notifications", "signup-personal", "signup-login", "signup-photo", "signup-agreement", "create-community", "community-settings", "password-recovery", "password-recovery-confirm", "password-reset", "tos", "rights", "privacy", "terms", "validate-email","user");
/* RECEIVE VALUE */
$validateValue = $_REQUEST['fieldValue'];
$validateId = $_REQUEST['fieldId'];

/* RETURN VALUE */
$arrayToJs = array();
$arrayToJs[] = $validateId;

$mysql = new mysqli(HOSTNAME, USERNAME, PASSWORD, DATABASE_NAME);
if ($mysql->connect_errno > 0) {
    $arrayToJs[] = TRUE;
    echo json_encode($arrayToJs);
} else {
    $sql = "SELECT * FROM `community` WHERE unique_name = '" . clean($validateValue) . "'";
    if ($result = $mysql->query($sql)) {
        if ($result->num_rows > 0) {
            $arrayToJs[] = FALSE;
            echo json_encode($arrayToJs);
        } else {
            if (preg_match("/[^A-Za-z0-9-]/", $validateValue)) {
                $arrayToJs[] = FALSE;
                $arrayToJs['msg'] = "* Helve Cannot contain special characters except '-'";
            } else {
                if (in_array($validateValue, $pageName)) {
                    $arrayToJs[] = FALSE;
                    $arrayToJs['msg'] = "* '$validateValue' is a reserved word. Try something else";
                } else {
                    $arrayToJs[] = TRUE;
                }
            }
            echo json_encode($arrayToJs);
        }
    }
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