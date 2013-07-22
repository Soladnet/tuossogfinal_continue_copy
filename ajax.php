<?php

require_once './Config.php';
/* Database Configuration. Add your details below */
$dbOptions = array(
    'db_host' => HOSTNAME,
    'db_user' => USERNAME,
    'db_pass' => PASSWORD,
    'db_name' => DATABASE_NAME
);
/* Database Config End */
error_reporting(E_ALL ^ E_NOTICE);

require "DB.class.php";
require "Chat.class.php";
require "ChatBase.class.php";
require "ChatLine.class.php";
require "ChatUser.class.php";
if (session_id() == "") {
    session_name('GSID');
    session_start();
}

if (get_magic_quotes_gpc()) {
    // If magic quotes is enabled, strip the extra slashes
    array_walk_recursive($_GET, create_function('&$v,$k', '$v = stripslashes($v);'));
    array_walk_recursive($_POST, create_function('&$v,$k', '$v = stripslashes($v);'));
}

try {
    // Connecting to the database
    DB::init($dbOptions);
    $response = array();
    // Handling the supported actions:
    switch ($_GET['action']) {
        case 'login':
            $response = Chat::login($_POST['name'], $_POST['email']);
            break;
        case 'checkLogged':
            $response = Chat::checkLogged($_GET['uid'],$_GET['comid']);
            break;
        case 'logout':
            $response = Chat::logout($_POST['comid'],$_GET['uid']);
            break;
        case 'submitChat':
            $response = Chat::submitChat($_POST['chatText'], $_POST['comid'], $_POST['uid']);
            break;
        case 'getUsers':
            $response = Chat::getUsers($_GET['uid'],$_GET['comid']);
            break;
        case 'getChats':
            $response = Chat::getChats($_GET['lastID'],$_GET['comid']);
            break;
        default:
            throw new Exception('Wrong action');
    }
    echo json_encode($response);
} catch (Exception $e) {
    die(json_encode(array('error' => $e->getMessage())));
}
?>