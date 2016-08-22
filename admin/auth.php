<?php

require_once('./config.php'); //provides $allowed_users

$user = $_SERVER['REMOTE_USER'];

if(!in_array($user, $allowed_users)) {
    echo 'Permission denied.';
    if($user) {
        echo " ($user)";
    }
    exit(1);
}

$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
if($db->connect_errno) {
    echo 'Failed to connect to db. The error was: '.$db->connect_error;
    exit(1);
}

?>
